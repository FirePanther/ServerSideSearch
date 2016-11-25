<?php
/**
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2016 Suat Secmen
 * @license          MIT License
 */
class Main {
	private $content = true, $title = '', $body = '', $icons = [], $htmls = [];
	
	/**
	 * selects the page to show
	 */
	function __construct() {
		$this->config();
		if ($this->isLoggedIn()) {
			$this->setPage('index');
		} else {
			$this->setPage('login');
		}
	}
	
	/**
	 * does some configurations
	 */
	private function config() {
		// load templates
		$this->icons = icons();
		$this->htmls = htmls();
		
		// inspired by githubs colors
		ini_set('highlight.comment', '#969896');
		ini_set('highlight.default', '#333');
		ini_set('highlight.html', '#795da3');
		ini_set('highlight.keyword', '#a71d5d');
		ini_set('highlight.string', '#0086b3');
	}
	
	/**
	 * (boolean) checks if the user is logged in
	 */
	public function isLoggedIn() {
		global $username, $password;
		if (!isset($username, $password))
			throw new Exception('Couldn\'t find username or password variable.');
		$username = strtolower($username);
		return isset($_SESSION['loggedIn'])
			&& (
				$_SESSION['loggedIn'] === md5($username.':'.$password)
				|| $_SESSION['loggedIn'] === md5($username.':'.md5($password))
			);
	}
	
	/**
	 * returns the source of an icon with the color
	 */
	private function icon($name, $forceColor = null) {
		return $this->html('svg', [
			'icon' => str_replace('<path', '<path fill="'.($forceColor !== null ? $forceColor : '#'.$this->icons[$name][1]).'"', $this->icons[$name][0])
		], true);
	}
	
	/**
	 * identifies the icon from the file type
	 */
	private function iconByFile($file, $returnName = false) {
		if (is_link($file)) $icon = 'link';
		elseif (is_dir($file)) $icon = 'folder';
		else {
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			$icon = 'file';
			$icons = [
				'php' => ['php'],
				'html' => ['html', 'htm', 'xml'],
				'image' => ['png', 'bmp', 'gif', 'jpg', 'jpeg'],
				'css' => ['css', 'less', 'sass', 'scss'],
				'js' => ['js', 'coffee', 'ts'],
				'text' => ['txt', 'rtf', 'ini', 'htaccess', 'htpasswd'],
				'archive' => ['zip', 'rar', 'tar', 'gz', 'bz', 'bz2', '7zip']
			];
			foreach ($icons as $i => $exts) {
				if (in_array($ext, $exts)) {
					$icon = $i;
					break;
				}
			}
		}
		return $returnName ? $icon : $this->icon($icon);
	}
	
	/**
	 * parses the variables and returns the html
	 */
	private function html($name, $vars = [], $return = false) {
		if (isset($this->htmls[$name])) {
			$src = $this->htmls[$name];
			foreach ($vars as $k => $v) {
				$src = str_replace(":@$k", $v, $src);
				$src = str_replace(":$k", htmlspecialchars($v), $src);
			}
			if ($return) return $src;
			else $this->body .= $src;
		} else {
			throw new Exception('HTML template "'.htmlspecialchars($name).'" doesn\'t exist.');
		}
	}
	
	/**
	 * redirects to the url and exits the script
	 */
	private function redirect($url) {
		header('location: '.$url);
		exit;
	}
	
	/**
	 * formats a number and adds a unit
	 */
	private function formatSize($i) {
		$unit = 0;
		$units = ['B', 'kB', 'MB', 'GB', 'TB'];
		while ($i > 1000 && $unit < count($units) - 1) {
			$i /= 1024;
			$unit++;
		}
		return number_format($i, 2, '.', ' ').' '.$units[$unit];
	}
	
	/**
	 * determines the file or folder size
	 */
	private function fileSize($file, $starttime) {
		$size = 0;
		$timeout = .2; // in seconds, per folder
		$cancelled = false;
		if ($starttime < microtime(1) - $timeout) return [$size, true]; // timeout
		
		if (is_dir($file)) {
			$s = scandir($file);
			foreach ($s as $f) {
				if ($starttime < microtime(1) - $timeout) return [$size, true]; // timeout
				
				if ($f == '.' || $f == '..') continue;
				elseif (is_dir($file.'/'.$f)) {
					$folderSize = $this->fileSize($file.'/'.$f, $starttime);
					$size += $folderSize[0];
					if ($folderSize[1]) $cancelled = true;
				} else $size += @filesize($file.'/'.$f);
			}
		} else {
			$size += @filesize($file);
		}
		return [$size, $cancelled];
	}
	
	/**
	 * creates a href link, adds and removes parameters
	 */
	private function getHref($newParams = [], $params = null, $remove = []) {
		if ($params === null) {
			$params = $_GET;
		}
		$params = array_merge($params, $newParams);
		if ($remove) {
			foreach ($params as $k => $v) {
				if (in_array($k, $remove)) unset($params[$k]);
			}
		}
		return $_SERVER['PHP_SELF'].'?'.http_build_query($params);
	}
	
	/**
	 * (boolean) has this page any content?
	 */
	public function hasContent() {
		return $this->content ? true : false;
	}
	
	/**
	 * returns the pages title (meta)
	 */
	public function getTitle() {
		return ($this->title ? $this->title.' • ' : '').'FP: ServerSideSearch';
	}
	
	/**
	 * returns the pages body (content)
	 */
	public function getBody() {
		return $this->body;
	}
	
	/**
	 * highlights the content; if it's not a php source, try to highlight it
	 * as well
	 */
	public function highlight($file) {
		$src = file_get_contents($file);
		$addedPhp = false;
		if (strpos($src, '<?') === false) {
			$src = '<? '.$src;
			$addedPhp = true;
		}
		$hSrc = highlight_string($src, 1);
		if ($addedPhp) {
			$hSrc = implode('', explode('&lt;?&nbsp;', $hSrc, 2));
		}
		return $hSrc;
	}
	
	/**
	 * sets the page (defines title and body)
	 */
	private function setPage($name) {
		switch ($name) {
			case 'index':
				$path = getcwd();
				$isFile = false;
				if (isset($_GET['path']) && $_GET['path'][0] == '/') {
					$path = $_GET['path'];
					$isFile = is_file($path);
				}
				if (substr($path, -1) == '/') $path = substr($path, 0, -1);
				if ($path === '') $path = '/';
				
				$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
				
				$breadcrumb = '/';
				$pathArr = explode('/', substr($path, 1));
				
				if ($isFile && $q !== null) {
					$isFIle = false;
					array_pop($pathArr);
				}
				if ($q !== null) $pathArr[] = 'Search';
				
				$last = array_pop($pathArr);
				$dirs = '';
				foreach ($pathArr as $dir) {
					$dirs .= '/'.$dir;
					$breadcrumb .= '<a href="'.$this->getHref([
						'path' => $dirs
					], null, ['q']).'">'.htmlspecialchars($dir).'</a>/';
				}
				if ($last) $breadcrumb .= '<strong>'.htmlspecialchars($last).'</strong>'.($isFile || $q !== null ? '' : '/');
				
				$this->body = $this->html('indexHeader', [
					'search' => ($q !== null ? '' : 
						'<a href="'.$this->getHref([ 'q' => '' ]).'" class="iconButton">'.$this->icon('magnify').'</a>'
					)
				], true);
				$this->html('breadcrumb', [
					'breadcrumb' => $breadcrumb
				]);
				if ($q !== null) {
					$this->search($pathArr, $q);
				} elseif ($isFile) {
					$this->showFileInfo($path, $pathArr, $last);
				} else {
					$this->listFolderContent($path, $pathArr, $last);
				}
				break;
			case 'login':
				$error = '';
				if (isset($_POST['username'], $_POST['password'])) {
					$_SESSION['loggedIn'] = md5(strtolower($_POST['username']).':'.md5($_POST['password']));
					if ($this->isLoggedin()) {
						$this->redirect($this->getHref());
					} else {
						$error = 'Incorrect credentials.';
					}
				}
				$this->title = 'Login';
				$this->body = $this->html('errorMessage', [
					'error' => ($error ? '<div class="error">'.$error.'</div>' : '')
				], true);
				break;
		}
	}
	
	/**
	 * search for files (+ their content) and folders
	 */
	private function search($pathArr, $q) {
		$regex = isset($_REQUEST['regex']) && $_REQUEST['regex'] !== '0' ? true : false;
		$case = isset($_REQUEST['case']) && $_REQUEST['case'] !== '0' ? true : false;
		$maxSize = isset($_REQUEST['maxSize']) && $_REQUEST['maxSize'] ? (float)$_REQUEST['maxSize'] : 2;
		$this->html('searchBox', [
			'q' => $q,
			'regexChecked' => $regex ? ' checked' : '',
			'caseChecked' => $case ? ' checked' : '',
			'maxSize' => $maxSize
		]);
		
		if (strlen($q)) {
			global $regexErrorTest;
			$regexErrorTest = false;
			if ($regex) {
				set_error_handler(function($errno, $errstr) {
					global $regexErrorTest;
					$regexErrorTest = $errstr;
					if (substr($regexErrorTest, 0, 14) == 'preg_match(): ') $regexErrorTest = substr($regexErrorTest, 14);
				});
				preg_match('/'.$q.'/i', 'test');
				restore_error_handler();
			}
			if (!$regexErrorTest) {
				$searchPath = '/'.implode('/', $pathArr);
				$list = $this->startSearch($searchPath, $q, $regex, $case, $maxSize * 1024 * 1024);
				$results = count($list);
				$this->body .= '<div class="label" id="results">'.($results >= 100 ? '99+' : $results).' Result'.($results == 1 ? '' : 's').'</div>';
				foreach ($list as $f) {
					$fileHref = $this->getHref([
						'path' => $f,
						'pb' => $searchPath,
						'qb' => $q,
						'regex' => $regex,
						'case' => $case,
						'maxSize' => $maxSize
					], null, ['q']);
					$size = $this->fileSize($f, microtime(1));
					$ext = pathinfo(basename($f), PATHINFO_EXTENSION);
					if ($ext) $ext = '.'.$ext;
					$this->html('listItem', [
						'fileHref' => $fileHref,
						'fileIcon' => $this->iconByFile($f),
						'fileName' => basename($f, $ext),
						'fileExt' => $ext,
						'gt' => ($size[1] ? '&gt; ' : ''),
						'fileSize' => $this->formatSize($size[0])
					]);
				}
			} else {
				$this->body .= '<div class="notice error">'.htmlspecialchars($regexErrorTest).'</div>';
			}
		}
	}
	
	/**
	 * starts the (folder recursive) search
	 */
	private function startSearch($path, $q, $regex, $case, $maxSize) {
		$limitResults = 100;
		$queue = [];
		$found = [];
		$s = scandir($path);
		foreach ($s as $f) {
			if ($f == '.' || $f == '..') continue;
			elseif (is_dir($path.'/'.$f)) {
				// also find folders (if the name matches)
				if ($regex && preg_match('/'.$q.'/'.($case ? '' : 'i'), $f) ||
					!$regex && (
						$case && strpos($f, $q) !== false ||
						!$case && stripos($f, $q) !== false
					)) {
					$found[] = $path.'/'.$f;
				}
				
				$queue[] = $path.'/'.$f;
			} else {
				if (@filesize($path.'/'.$f) <= $maxSize) {
					// check source of file and file name
					$src = $f.PHP_EOL.file_get_contents($path.'/'.$f);
					
					// I'm sorry, it's 2am
					if ($regex && preg_match('/'.$q.'/'.($case ? '' : 'i'), $src) ||
						!$regex && (
							$case && strpos($src, $q) !== false ||
							!$case && stripos($src, $q) !== false
						)) {
						$found[] = $path.'/'.$f;
					}
				}
			}
			if (count($found) > $limitResults) break;
		}
		
		if (count($found) < $limitResults && $queue) {
			foreach ($queue as $dir) {
				$found = array_merge($found, $this->startSearch($dir, $q, $regex, $case, $maxSize));
				if (count($found) > $limitResults) break;
			}
		}
		return $found;
	}
	
	/**
	 * shows information of the file after opening it, also has a preview of the
	 * file.
	 */
	private function showFileInfo($path, $pathArr, $filename) {
		$action = 'info';
		if (isset($_GET['action']) && in_array($_GET['action'], ['view', 'edit'])) {
			$action = $_GET['action'];
		}
		
		// search query backup (back to search)
		if (isset($_GET['qb'])) {
			$searchHref = $this->getHref([
				'q' => $_GET['qb'],
				'path' => $_GET['pb']
			], null, ['qb', 'pb', 'action']);
			$this->html('listItemBack', [
				'parentFolderHref' => $searchHref,
				'icon' => $this->icon('magnify', '000'),
				'label' => 'back to',
				'parentFolderName' => 'Search "'.$_GET['qb'].'"'
			]);
		}
		
		switch ($action) {
			case 'info':
				$fileType = $this->iconByFile($path, true);
				
				$parentFolderHref = $this->getHref([
					'path' => '/'.implode('/', $pathArr)
				]);
				$parentFolderName = array_pop($pathArr);
				if ($parentFolderName == '') $parentFolderName = 'root';
				
				$this->html('listItemBack', [
					'parentFolderHref' => $parentFolderHref,
					'icon' => $this->icon('back'),
					'label' => 'back to',
					'parentFolderName' => $parentFolderName
				]);
				$this->html('listTableStart');
				$this->html('listTableRow', [
					'key' => 'File Name',
					'value' => $filename
				]);
				if (is_link($path)) {
					$this->html('listTableRow', [
						'key' => 'Original Location',
						'value' => readlink($path)
					]);
				}
				$this->html('listTableRow', [
					'key' => 'File Size',
					'value' => $this->formatSize(filesize($path))
				]);
				$this->html('listTableRow', [
					'key' => 'File Create Time',
					'value' => date('Y-m-d, H:i (s)', filectime($path))
				]);
				$this->html('listTableRow', [
					'key' => 'File Modification Time',
					'value' => date('Y-m-d, H:i (s)', filemtime($path))
				]);
				$this->html('listTableRow', [
					'key' => 'File Access Time',
					'value' => date('Y-m-d, H:i (s)', fileatime($path))
				]);
				if ($fileType == 'archive') {
					$zip = @zip_open($path);
					if (is_resource($zip)) {
						$num = 0;
						$originalSize = 0;
						while ($zipFile = zip_read($zip)) {
							$num++;
							$originalSize += zip_entry_filesize($zipFile);
						}
						$this->html('listTableRow', [
							'key' => 'Archive Contains',
							'value' => number_format($num, 0, '.', ' ').' File'.($num == 1 ? '' : 's')
						]);
						$this->html('listTableRow', [
							'key' => 'Original Size',
							'value' => $this->formatSize($originalSize)
						]);
					}
				} elseif ($fileType == 'image') {
					list($width, $height, $type, $attr) = @getimagesize($path);
					if ($width && $height) {
						$this->html('listTableRow', [
							'key' => 'Dimensions',
							'value' => $width.' x '.$height
						]);
					}
					if ($type) {
						$this->html('listTableRow', [
							'key' => 'Mime Type',
							'value' => image_type_to_mime_type($type)
						]);
					}
				}
				$this->html('listTableEnd');
				$this->html('fileInfoButtons', [
					'viewHref' => $this->getHref(['action' => 'view'])
				]);
				break;
			case 'view':
				$this->html('fileViewButtons', [
					'infoHref' => $this->getHref(['action' => 'info'])
				]);
				switch ($this->iconByFile($path, true)) {
					case 'archive':
						$this->body .= '<div class="label">Archive Content:</div>';
						$zip = zip_open($path);
						if (is_resource($zip)) {
							while ($zipFile = zip_read($zip)) {
								$zipName = zip_entry_name($zipFile);
								$ext = pathinfo($zipName, PATHINFO_EXTENSION);
								if ($ext) $ext = '.'.$ext;
								$this->html('listItemArchive', [
									'fileIcon' => $this->iconByFile($zipName),
									'fileName' => basename($zipName, $ext),
									'fileExt' => $ext
								]);
							}
						}
						break;
					case 'image':
						list($width, $height, $type, $attr) = @getimagesize($path);
						if ($type) {
							$mime = image_type_to_mime_type($type);
							$this->html('image', [
								'mime' => $mime,
								'base64' => base64_encode(file_get_contents($path)),
								'alt' => 'preview of '.basename($path),
								'class' => 'imagePreview'
							]);
						}
						break;
					default:
						$this->html('code', [
							'code' => $this->highlight($path)
						]);
						break;
				}
				break;
		}
	}
	
	/**
	 * list the content (files and folders) of a folder
	 */
	private function listFolderContent($path, $pathArr, $dirname) {
		// search query backup (back to search)
		if (isset($_GET['qb'])) {
			$searchHref = $this->getHref([
				'q' => $_GET['qb'],
				'path' => $_GET['pb']
			], null, ['qb', 'pb']);
			$this->html('listItemBack', [
				'parentFolderHref' => $searchHref,
				'icon' => $this->icon('magnify', '000'),
				'label' => 'back to',
				'parentFolderName' => 'Search "'.$_GET['qb'].'"'
			]);
		}
		
		if ($dirname !== '') {
			$parentFolderHref = $this->getHref([
				'path' => '/'.implode('/', $pathArr)
			]);
			$parentFolderName = array_pop($pathArr);
			if ($parentFolderName == '') $parentFolderName = 'root';
			
			$this->html('listItemBack', [
				'parentFolderHref' => $parentFolderHref,
				'icon' => $this->icon('back'),
				'label' => 'up to',
				'parentFolderName' => $parentFolderName
			]);
		}
		
		$s = scandir($path);
		if (count($s) == 2) {
			$this->html('emptyFolderMessage');
		} else {
			foreach ($s as $f) {
				if ($f == '.' || $f == '..') continue;
				$fileHref = $this->getHref([
					'path' => $path.'/'.$f
				]);
				$size = $this->fileSize($path.'/'.$f, microtime(1));
				
				$ext = pathinfo($f, PATHINFO_EXTENSION);
				if ($ext) $ext = '.'.$ext;
				
				$this->html('listItem', [
					'fileHref' => $fileHref,
					'fileIcon' => $this->iconByFile($path.'/'.$f),
					'fileName' => basename($f, $ext),
					'fileExt' => $ext,
					'gt' => ($size[1] ? '&gt; ' : ''),
					'fileSize' => $this->formatSize($size[0])
				]);
			}
		}
	}
}
