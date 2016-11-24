<?php
/**
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2016 Suat Secmen
 * @license          MIT License
 */
class Main {
	private $content = true, $title = '', $body = '', $icons = [];
	
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
	 * (boolean) has this page any content?
	 */
	private function icon($name, $forceColor = null) {
		return
			'<?xml version="1.0" encoding="UTF-8"?'.'>'.
			//'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.
			'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24">'.
				str_replace('<path', '<path fill="'.($forceColor !== null ? $forceColor : '#'.$this->icons[$name][1]).'"', $this->icons[$name][0]).
			'</svg>';
	}
	
	/**
	 * (boolean) has this page any content?
	 */
	private function iconByFile($file, $returnName = false) {
		if (is_link($file)) $icon = 'link';
		elseif (is_dir($file)) $icon = 'folder';
		else {
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			switch ($ext) {
				case 'php':
				case 'phtml': // lol
				case 'php3':
				case 'php4':
				case 'ph3':
				case 'ph4':
					$icon = 'php';
					break;
				case 'html':
				case 'htm':
				case 'xml':
					$icon = 'html';
					break;
				case 'png':
				case 'bmp':
				case 'gif':
				case 'jpg':
				case 'jpeg':
					$icon = 'image';
					break;
				case 'css':
					$icon = 'css';
					break;
				case 'js':
					$icon = 'js';
					break;
				case 'txt':
				case 'rtf':
				case 'ini':
				case 'htaccess':
				case 'htpasswd':
					$icon = 'text';
					break;
				case 'zip':
				case 'rar':
				case 'tar':
				case 'gz':
				case 'bz':
				case 'bz2':
					$icon = 'archive';
					break;
				default:
					$icon = 'file';
			}
		}
		return $returnName ? $icon : $this->icon($icon);
	}
	
	/**
	 * (boolean) has this page any content?
	 */
	private function redirect($url) {
		header('location: '.$url);
		exit;
	}
	
	/**
	 * (boolean) has this page any content?
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
	 * (boolean) has this page any content?
	 */
	private function fileSize($file, $starttime) {
		$size = 0;
		$timeout = 1; // in seconds, per folder
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
	 * (boolean) has this page any content?
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
	 * returns the pages body (content)
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
				
				$this->body = '
					<section id="wrapper">
						<header>
							<span>Index</span>
							'.($q !== null ? '' : 
								'<a href="'.$this->getHref([ 'q' => '' ]).'" class="iconButton">'.$this->icon('magnify').'</a>'
							).'
						</header>
						<div class="content">';
				$this->body .= '
					<div class="listItem">
						<div class="breadcrumb">
							'.$breadcrumb.'
						</div>
					</div>';
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
				$this->body = '
					<section id="wrapper">
						<form method="post">
							<div class="loginBox">
								<header>Login</header>
								<div class="content">
									'.($error ? '<div class="error">'.$error.'</div>' : '').'
									<div class="formElement">
										<input type="text" required class="hasPlaceholder" name="username">
										<div class="isPlaceholder">Username</div>
									</div>
									<div class="formElement">
										<input type="password" required class="hasPlaceholder" name="password">
										<div class="isPlaceholder">Password</div>
									</div>
									<div class="formElement right">
										<input type="submit" value="Login" class="button">
									</div>
								</div>
							</div>
						</form>
					</section>';
				break;
		}
	}
	
	/**
	 * search for files (+ their content) and folders
	 */
	private function search($pathArr, $q) {
		$regex = isset($_POST['regex']) && $_POST['regex'] ? true : false;
		$case = isset($_POST['case']) && $_POST['case'] ? true : false;
		$maxSize = isset($_POST['maxSize']) ? $_POST['maxSize'] : 2;
		$this->body .= '
			<div class="searchBox">
				<header>Search</header>
				<div class="content">
					<form method="post" action="#results">
						<div class="formElement">
							<input type="search" required name="q" value="'.htmlspecialchars($q).'" class="hasPlaceholder">
							<div class="isPlaceholder">Search</div>
						</div>
						<div class="formElement">
							<table class="listTable">
								<tr>
									<td class="key">Regular Expressions</td>
									<td class="value">
										<div class="checkbox">
											<input type="checkbox" name="regex" id="regex"'.($regex ? ' checked' : '').'>
											<label for="regex"></label>
										</div>
									</td>
								</tr>
								<tr>
									<td class="key">Case sensitive</td>
									<td class="value">
										<div class="checkbox">
											<input type="checkbox" name="case" id="case"'.($case ? ' checked' : '').'>
											<label for="case"></label>
										</div>
									</td>
								</tr>
								<tr>
									<td class="key">Ignore larger files</td>
									<td class="value">
										<input class="input number" type="number" value="'.$maxSize.'" name="maxSize" step=".1"> MB+
									</td>
								</tr>
							</table>
						</div>
						<div class="formElement right">
							<input type="submit" class="button" value="Search">
						</div>
					</form>
				</div>
			</div>';
		
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
				$list = $this->startSearch('/'.implode('/', $pathArr), $q, $regex, $case, $maxSize * 1024 * 1024);
				$results = count($list);
				$this->body .= '<div class="label" id="results">'.($results >= 100 ? '99+' : $results).' Result'.($results == 1 ? '' : 's').'</div>';
				foreach ($list as $f) {
					$fileHref = $this->getHref([
						'path' => $f
					], null, ['q']);
					$size = $this->fileSize($f, microtime(1));
					$this->body .= '
						<div class="listItem">
							<div class="fileIcon">
								<a href="'.$fileHref.'">'.$this->iconByFile($f).'</a>
							</div>
							<div class="fileName">
								<a href="'.$fileHref.'">'.preg_replace('~\.[^.]*$~', '<span>$0</span>', htmlspecialchars(basename($f))).'</a>
							</div>
							<div class="fileActions">
								'.($size[1] ? '&gt; ' : '').$this->formatSize($size[0]).'
							</div>
						</div>';
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
			elseif (is_dir($path.'/'.$f)) $queue[] = $path.'/'.$f;
			else {
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
	 * list the content (files and folders) of a folder
	 */
	private function showFileInfo($path, $pathArr, $filename) {
		$action = 'info';
		if (isset($_GET['action']) && in_array($_GET['action'], ['view', 'edit'])) {
			$action = $_GET['action'];
		}
		
		switch ($action) {
			case 'info':
				$fileType = $this->iconByFile($path, true);
				
				$parentFolderHref = $this->getHref([
					'path' => '/'.implode('/', $pathArr)
				]);
				$parentFolderName = array_pop($pathArr);
				if ($parentFolderName == '') $parentFolderName = 'root';
				
				$this->body .= '
					<div class="listItem">
						<div class="fileIcon">
							<a href="'.$parentFolderHref.'">'.$this->icon('back').'</a>
						</div>
						<div class="fileName">
							<a href="'.$parentFolderHref.'"><span>back to</span> '.htmlspecialchars($parentFolderName).'</a>
						</div>
					</div>
					<table class="listTable">
						<tr>
							<td class="key">File Name</td>
							<td class="value">'.htmlspecialchars($filename).'</td>
						</tr>
						<tr>
							<td class="key">File Size</td>
							<td class="value">'.$this->formatSize(filesize($path)).'</td>
						</tr>
						<tr>
							<td class="key">File Create Time</td>
							<td class="value">'.date('Y-m-d, H:i (s)', filectime($path)).'</td>
						</tr>
						<tr>
							<td class="key">File Modification Time</td>
							<td class="value">'.date('Y-m-d, H:i (s)', filemtime($path)).'</td>
						</tr>
						<tr>
							<td class="key">File Access Time</td>
							<td class="value">'.date('Y-m-d, H:i (s)', fileatime($path)).'</td>
						</tr>';
				if ($fileType == 'archive') {
					$zip = @zip_open($path);
					if (is_resource($zip)) {
						$num = 0;
						$originalSize = 0;
						while ($zipFile = zip_read($zip)) {
							$num++;
							$originalSize += zip_entry_filesize($zipFile);
						}
						$this->body .= '
							<tr>
								<td class="key">Archive Contains</td>
								<td class="value">'.number_format($num, 0, '.', ' ').' File'.($num == 1 ? '' : 's').'</td>
							</tr>';
						$this->body .= '
							<tr>
								<td class="key">Original Size</td>
								<td class="value">'.$this->formatSize($originalSize).'</td>
							</tr>';
					}
				} elseif ($fileType == 'image') {
					list($width, $height, $type, $attr) = @getimagesize($path);
					if ($width && $height) {
						$this->body .= '
							<tr>
								<td class="key">Dimensions</td>
								<td class="value">'.$width.' x '.$height.' px</td>
							</tr>';
					}
					if ($type) {
						$this->body .= '
							<tr>
								<td class="key">Mime Type</td>
								<td class="value">'.image_type_to_mime_type($type).'</td>
							</tr>';
					}
				}
				$this->body .= '</table>';
				
				$viewHref = $this->getHref(['action' => 'view']);
				$this->body .= '
					<div class="buttonsContainer">
						<a href="'.$viewHref.'" class="button">View</a>
					</div>';
				break;
			case 'view':
				$infoHref = $this->getHref(['action' => 'info']);
				$this->body .= '
					<div class="buttonsContainer">
						<a href="'.$infoHref.'" class="button">Back</a>
					</div>';
				switch ($this->iconByFile($path, true)) {
					case 'archive':
						$this->body .= '<div class="label">Archive Content:</div>';
						$zip = zip_open($path);
						if (is_resource($zip)) {
							while ($zipFile = zip_read($zip)) {
								$zipName = zip_entry_name($zipFile);
								$this->body .= '
									<div class="listItem">
										<div class="fileIcon">
											'.$this->iconByFile($zipName).'
										</div>
										<div class="fileName">
											'.htmlspecialchars($zipName).'
										</div>
									</div>';
							}
						}
						break;
					case 'image':
						list($width, $height, $type, $attr) = @getimagesize($path);
						if ($type) {
							$mime = image_type_to_mime_type($type);
							$this->body .= '<img src="data:'.$mime.';base64,'.base64_encode(file_get_contents($path)).'" class="imagePreview">';
						}
						break;
					default:
						$this->body .= '
							<div class="code">
								'.$this->highlight($path).'
							</div>';
						break;
				}
				break;
		}
	}
	
	/**
	 * list the content (files and folders) of a folder
	 */
	private function listFolderContent($path, $pathArr, $dirname) {
		if ($dirname !== '') {
			$parentFolderHref = $this->getHref([
				'path' => '/'.implode('/', $pathArr)
			]);
			$parentFolderName = array_pop($pathArr);
			if ($parentFolderName == '') $parentFolderName = 'root';
			$this->body .= '
				<div class="listItem">
					<div class="fileIcon">
						<a href="'.$parentFolderHref.'">'.$this->icon('back').'</a>
					</div>
					<div class="fileName">
						<a href="'.$parentFolderHref.'"><span>up to</span> '.htmlspecialchars($parentFolderName).'</a>
					</div>
				</div>';
		}
		
		$s = scandir($path);
		if (count($s) == 2) {
			$this->body .= '<div class="notice">Empty Folder</div>';
		} else {
			foreach ($s as $f) {
				if ($f == '.' || $f == '..') continue;
				$fileHref = $this->getHref([
					'path' => $path.'/'.$f
				]);
				$size = $this->fileSize($path.'/'.$f, microtime(1));
				$this->body .= '
					<div class="listItem">
						<div class="fileIcon">
							<a href="'.$fileHref.'">'.$this->iconByFile($path.'/'.$f).'</a>
						</div>
						<div class="fileName">
							<a href="'.$fileHref.'">'.preg_replace('~\.[^.]*$~', '<span>$0</span>', htmlspecialchars($f)).'</a>
						</div>
						<div class="fileActions">
							'.($size[1] ? '&gt; ' : '').$this->formatSize($size[0]).'
						</div>
					</div>';
			}
		}
	}
}
