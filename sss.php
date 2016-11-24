<?php
/**
 * This file was generated. You can find the clean (uncompressed) source here:
 * https://github.com/FirePanther/ServerSideSearch/tree/master/src
 */
/**
 * A little server side searching script. It allows you to quickly check out
 * your files and folders and gives you the ability to do some quick actions.
 *
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2012-2016 Suat Secmen
 * @license          MIT License
 */

// login data, required
$username = 'FirePanther';
$password = ''; // this can also be md5'ed once (optional)

// -----

$style = 'body { font-family: \'Open Sans\', sans-serif; background-color: #FAFAFA; font-weight: 300; margin: 0; padding: 0; }

a img { border: 0; }

header { background-color: #5390F5; color: #FFF; padding: 20px; font-size: 20px; }
header .iconButton { float: right; }

.loginBox { position: absolute; left: 50%; top: 50%; margin-left: -260px; margin-top: -210px; width: 520px; height: 420px; background-color: #FFF; box-shadow: 10px 10px 50px rgba(0, 0, 0, 0.1); }
.loginBox header { padding: 60px 40px 10px; font-size: 40px; }
.loginBox .content { padding: 40px; }

.searchBox { background-color: #FFF; box-shadow: 10px 10px 50px rgba(0, 0, 0, 0.1); margin: 20px; }
.searchBox .content { padding: 40px; }

.right { text-align: right; }

.error { color: red; }

.notice { margin: 20px; }

.breadcrumb { padding: 10px 15px; background-color: #3C59D6; color: #CDF; font-size: 11px; }
.breadcrumb a { font-size: 13px; color: #CDF; font-weight: bold; padding: 0 5px; }
.breadcrumb a:hover { text-decoration: none; }
.breadcrumb strong { font-size: 13px; padding: 0 5px; }

.listItem { background-color: #FFF; border-bottom: 1px solid #D8D8D8; }
.listItem:after { content: ""; display: block; clear: both; }
.listItem .fileIcon { display: inline-block; padding: 15px 0 0 15px; float: left; }
.listItem .fileName { display: inline-block; padding: 15px; }
.listItem .fileName a { color: #000; text-decoration: none; }
.listItem .fileName span { opacity: .5; }
.listItem .fileActions { display: inline-block; padding: 15px; float: right; }

.listTable { width: 100%; background-color: #FFF; border-bottom: 1px solid #D8D8D8; }
.listTable td { padding: 10px 15px; }
.listTable td.key { font-weight: bold; }
.listTable td.value { text-align: right; }

.code { box-sizing: border-box; overflow-x: scroll; padding: 10px; background-color: #FFF; margin: 10px 0 0; }

.label { padding: 15px 15px 5px; font-weight: bold; }

.imagePreview { max-width: calc(100% - 20px); max-height: calc(100% - 20px); box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2); margin: 10px; }

/* Form */
.formElement { padding: 25px 0 15px; }

.buttonsContainer { padding: 15px; }

.button { background-color: #5390F5; border: 0; color: #FFF; text-transform: uppercase; padding: 15px 25px; cursor: pointer; font-family: \'Open Sans\', sans-serif; font-size: 13px; transition: all 200ms linear; outline: 0; text-decoration: none; }
.button:hover { background-color: #3C59D6; }
.button:active { background-color: #1F75CF; }

.linkButton { padding: 15px 25px; color: #5390F5; font-weight: bold; text-transform: uppercase; text-decoration: none; font-size: 13px; }
.linkButton:hover { text-decoration: underline; color: #3C59D6; }
.linkButton:active { text-decoration: none; color: #1F75CF; }

.input.number { width: 40px; text-align: right; }

.hasPlaceholder, .input { outline: none; padding: 5px 0; width: 100%; box-sizing: border-box; font-size: 14px; border: 0; box-shadow: 0 1px 0px #777; transition: box-shadow 200ms linear; }
.hasPlaceholder:focus, .input:focus { box-shadow: 0 2px 0px #5390F5; }

.isPlaceholder { position: absolute; color: gray; transition: all 200ms ease-in-out; pointer-events: none; }

.hasPlaceholder:invalid + .isPlaceholder { margin: -25px 0 0; font-size: 14px; }
.hasPlaceholder:focus + .isPlaceholder, .hasPlaceholder:valid + .isPlaceholder { margin: -45px 0 0; font-size: 10px; color: #5390F5; }

/** These lines fixes a problem where the placeholder is in the input while the input field is autofilled: https://github.com/FirePanther/ServerSideSearch/issues/1  @source: http://stackoverflow.com/a/37432260/2284993 */
@-webkit-keyframes autofill { to { background: #FFF; } }
input:-webkit-autofill { -webkit-animation-name: autofill; -webkit-animation-fill-mode: both; }

/** https://codepen.io/andreasstorm/pen/ZGjNwZ */
.checkbox { display: inline-block; }
.checkbox label { position: relative; display: block; height: 20px; width: 44px; background: #898989; border-radius: 100px; cursor: pointer; transition: all 0.3s ease; }
.checkbox label:after { position: absolute; left: -2px; top: -3px; display: block; width: 26px; height: 26px; border-radius: 100px; background: #fff; box-shadow: 0px 2px 3px rgba(0, 0, 0, 0.5); content: \'\'; transition: all 0.3s ease; }
.checkbox label:active:after { transform: scale(1.15, 0.85); }
.checkbox input { display: none; }
.checkbox input:checked ~ label { background: #5390F5; }
.checkbox input:checked ~ label:after { left: 20px; background: #3C59D6; }
.checkbox input:disabled ~ label { background: #ddd; pointer-events: none; }
.checkbox input:disabled ~ label:after { background: #bbb; }
';

header('content-type: text/html; charset=utf-8');
session_start();

$data = new Main();
if ($data->hasContent()) {
	echo '<!doctype html>
<html>
	<head>
		<title>'.$data->getTitle().'</title>
		<meta charset="utf-8">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
	</head>
	<body>
		<style>'.$style.'</style>
		'.$data->getBody().'
	</body>
</html>
';
}

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
		$regex = isset($_POST['regex']) && $_POST['regex'] ? true : false;
		$case = isset($_POST['case']) && $_POST['case'] ? true : false;
		$maxSize = isset($_POST['maxSize']) ? $_POST['maxSize'] : 2;
		$this->html('searchBox', [
			'q' => $q,
			'regexChecked' => $regex ? ' chechked' : '',
			'caseChecked' => $case ? ' chechked' : '',
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
				$list = $this->startSearch('/'.implode('/', $pathArr), $q, $regex, $case, $maxSize * 1024 * 1024);
				$results = count($list);
				$this->body .= '<div class="label" id="results">'.($results >= 100 ? '99+' : $results).' Result'.($results == 1 ? '' : 's').'</div>';
				foreach ($list as $f) {
					$fileHref = $this->getHref([
						'path' => $f
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
	 * shows information of the file after opening it, also has a preview of the
	 * file.
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
function icons() { return [
	'archive' => ['<path d="M14,17H12V15H10V13H12V15H14M14,9H12V11H14V13H12V11H10V9H12V7H10V5H12V7H14M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3Z" />', '000'],
	'back' => ['<path d="M21,11H6.83L10.41,7.41L9,6L3,12L9,18L10.41,16.58L6.83,13H21V11Z" />', '000'],
	'css' => ['<path d="M5,3L4.35,6.34H17.94L17.5,8.5H3.92L3.26,11.83H16.85L16.09,15.64L10.61,17.45L5.86,15.64L6.19,14H2.85L2.06,18L9.91,21L18.96,18L20.16,11.97L20.4,10.76L21.94,3H5Z" />', '4b86e4'],
	'file' => ['<path d="M13,9V3.5L18.5,9M6,2C4.89,2 4,2.89 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2H6Z" />', '666'],
	'folder' => ['<path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z" />', '8f8f8f'],
	'html' => ['<path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M6.12,15.5L9.86,19.24L11.28,17.83L8.95,15.5L11.28,13.17L9.86,11.76L6.12,15.5M17.28,15.5L13.54,11.76L12.12,13.17L14.45,15.5L12.12,17.83L13.54,19.24L17.28,15.5Z" />', 'feae51'],
	'image' => ['<path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M6,20H15L18,20V12L14,16L12,14L6,20M8,9A2,2 0 0,0 6,11A2,2 0 0,0 8,13A2,2 0 0,0 10,11A2,2 0 0,0 8,9Z" />', '1fa666'],
	'js' => ['<path d="M3,3H21V21H3V3M7.73,18.04C8.13,18.89 8.92,19.59 10.27,19.59C11.77,19.59 12.8,18.79 12.8,17.04V11.26H11.1V17C11.1,17.86 10.75,18.08 10.2,18.08C9.62,18.08 9.38,17.68 9.11,17.21L7.73,18.04M13.71,17.86C14.21,18.84 15.22,19.59 16.8,19.59C18.4,19.59 19.6,18.76 19.6,17.23C19.6,15.82 18.79,15.19 17.35,14.57L16.93,14.39C16.2,14.08 15.89,13.87 15.89,13.37C15.89,12.96 16.2,12.64 16.7,12.64C17.18,12.64 17.5,12.85 17.79,13.37L19.1,12.5C18.55,11.54 17.77,11.17 16.7,11.17C15.19,11.17 14.22,12.13 14.22,13.4C14.22,14.78 15.03,15.43 16.25,15.95L16.67,16.13C17.45,16.47 17.91,16.68 17.91,17.26C17.91,17.74 17.46,18.09 16.76,18.09C15.93,18.09 15.45,17.66 15.09,17.06L13.71,17.86Z" />', 'f9d16d'],
	'link' => ['<path d="M10.59,13.41C11,13.8 11,14.44 10.59,14.83C10.2,15.22 9.56,15.22 9.17,14.83C7.22,12.88 7.22,9.71 9.17,7.76V7.76L12.71,4.22C14.66,2.27 17.83,2.27 19.78,4.22C21.73,6.17 21.73,9.34 19.78,11.29L18.29,12.78C18.3,11.96 18.17,11.14 17.89,10.36L18.36,9.88C19.54,8.71 19.54,6.81 18.36,5.64C17.19,4.46 15.29,4.46 14.12,5.64L10.59,9.17C9.41,10.34 9.41,12.24 10.59,13.41M13.41,9.17C13.8,8.78 14.44,8.78 14.83,9.17C16.78,11.12 16.78,14.29 14.83,16.24V16.24L11.29,19.78C9.34,21.73 6.17,21.73 4.22,19.78C2.27,17.83 2.27,14.66 4.22,12.71L5.71,11.22C5.7,12.04 5.83,12.86 6.11,13.65L5.64,14.12C4.46,15.29 4.46,17.19 5.64,18.36C6.81,19.54 8.71,19.54 9.88,18.36L13.41,14.83C14.59,13.66 14.59,11.76 13.41,10.59C13,10.2 13,9.56 13.41,9.17Z" />', 'bbb'],
	'magnify' => ['<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />', 'fff'],
	'pdf' => ['<path d="M14,9H19.5L14,3.5V9M7,2H15L21,8V20A2,2 0 0,1 19,22H7C5.89,22 5,21.1 5,20V4A2,2 0 0,1 7,2M11.93,12.44C12.34,13.34 12.86,14.08 13.46,14.59L13.87,14.91C13,15.07 11.8,15.35 10.53,15.84V15.84L10.42,15.88L10.92,14.84C11.37,13.97 11.7,13.18 11.93,12.44M18.41,16.25C18.59,16.07 18.68,15.84 18.69,15.59C18.72,15.39 18.67,15.2 18.57,15.04C18.28,14.57 17.53,14.35 16.29,14.35L15,14.42L14.13,13.84C13.5,13.32 12.93,12.41 12.53,11.28L12.57,11.14C12.9,9.81 13.21,8.2 12.55,7.54C12.39,7.38 12.17,7.3 11.94,7.3H11.7C11.33,7.3 11,7.69 10.91,8.07C10.54,9.4 10.76,10.13 11.13,11.34V11.35C10.88,12.23 10.56,13.25 10.05,14.28L9.09,16.08L8.2,16.57C7,17.32 6.43,18.16 6.32,18.69C6.28,18.88 6.3,19.05 6.37,19.23L6.4,19.28L6.88,19.59L7.32,19.7C8.13,19.7 9.05,18.75 10.29,16.63L10.47,16.56C11.5,16.23 12.78,16 14.5,15.81C15.53,16.32 16.74,16.55 17.5,16.55C17.94,16.55 18.24,16.44 18.41,16.25M18,15.54L18.09,15.65C18.08,15.75 18.05,15.76 18,15.78H17.96L17.77,15.8C17.31,15.8 16.6,15.61 15.87,15.29C15.96,15.19 16,15.19 16.1,15.19C17.5,15.19 17.9,15.44 18,15.54M8.83,17C8.18,18.19 7.59,18.85 7.14,19C7.19,18.62 7.64,17.96 8.35,17.31L8.83,17M11.85,10.09C11.62,9.19 11.61,8.46 11.78,8.04L11.85,7.92L12,7.97C12.17,8.21 12.19,8.53 12.09,9.07L12.06,9.23L11.9,10.05L11.85,10.09Z" />', 'f85b45'],
	'php' => ['<path d="M12,18.08C5.37,18.08 0,15.36 0,12C0,8.64 5.37,5.92 12,5.92C18.63,5.92 24,8.64 24,12C24,15.36 18.63,18.08 12,18.08M6.81,10.13C7.35,10.13 7.72,10.23 7.9,10.44C8.08,10.64 8.12,11 8.03,11.47C7.93,12 7.74,12.34 7.45,12.56C7.17,12.78 6.74,12.89 6.16,12.89H5.29L5.82,10.13H6.81M3.31,15.68H4.75L5.09,13.93H6.32C6.86,13.93 7.3,13.87 7.65,13.76C8,13.64 8.32,13.45 8.61,13.18C8.85,12.96 9.04,12.72 9.19,12.45C9.34,12.19 9.45,11.89 9.5,11.57C9.66,10.79 9.55,10.18 9.17,9.75C8.78,9.31 8.18,9.1 7.35,9.1H4.59L3.31,15.68M10.56,7.35L9.28,13.93H10.7L11.44,10.16H12.58C12.94,10.16 13.18,10.22 13.29,10.34C13.4,10.46 13.42,10.68 13.36,11L12.79,13.93H14.24L14.83,10.86C14.96,10.24 14.86,9.79 14.56,9.5C14.26,9.23 13.71,9.1 12.91,9.1H11.64L12,7.35H10.56M18,10.13C18.55,10.13 18.91,10.23 19.09,10.44C19.27,10.64 19.31,11 19.22,11.47C19.12,12 18.93,12.34 18.65,12.56C18.36,12.78 17.93,12.89 17.35,12.89H16.5L17,10.13H18M14.5,15.68H15.94L16.28,13.93H17.5C18.05,13.93 18.5,13.87 18.85,13.76C19.2,13.64 19.5,13.45 19.8,13.18C20.04,12.96 20.24,12.72 20.38,12.45C20.53,12.19 20.64,11.89 20.7,11.57C20.85,10.79 20.74,10.18 20.36,9.75C20,9.31 19.37,9.1 18.54,9.1H15.79L14.5,15.68Z" />', 'b99afc'],
	'text' => ['<path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z" />', 'fff'],
]; }
function htmls() { return [
	'breadcrumb' => '<div class="listItem">'.PHP_EOL.'	<div class="breadcrumb">'.PHP_EOL.'		:@breadcrumb'.PHP_EOL.'	</div>'.PHP_EOL.'</div>',
	'code' => '<div class="code">'.PHP_EOL.'	:code'.PHP_EOL.'</div>',
	'emptyFolderMessage' => '<div class="notice">Empty Folder</div>',
	'errorMessage' => '<section id="wrapper">'.PHP_EOL.'	<form method="post">'.PHP_EOL.'		<div class="loginBox">'.PHP_EOL.'			<header>Login</header>'.PHP_EOL.'			<div class="content">'.PHP_EOL.'				:@error'.PHP_EOL.'				<div class="formElement">'.PHP_EOL.'					<input type="text" required class="hasPlaceholder" name="username">'.PHP_EOL.'					<div class="isPlaceholder">Username</div>'.PHP_EOL.'				</div>'.PHP_EOL.'				<div class="formElement">'.PHP_EOL.'					<input type="password" required class="hasPlaceholder" name="password">'.PHP_EOL.'					<div class="isPlaceholder">Password</div>'.PHP_EOL.'				</div>'.PHP_EOL.'				<div class="formElement right">'.PHP_EOL.'					<input type="submit" value="Login" class="button">'.PHP_EOL.'				</div>'.PHP_EOL.'			</div>'.PHP_EOL.'		</div>'.PHP_EOL.'	</form>'.PHP_EOL.'</section>',
	'fileInfoButtons' => '<div class="buttonsContainer">'.PHP_EOL.'	<a href=":viewHref" class="button">View</a>'.PHP_EOL.'</div>',
	'fileViewButtons' => '<div class="buttonsContainer">'.PHP_EOL.'	<a href=":infoHref" class="button">Back</a>'.PHP_EOL.'</div>',
	'image' => '<img src="data::mime;base64,:base64" alt=":alt" class=":class">',
	'indexHeader' => '<section id="wrapper">'.PHP_EOL.'	<header>'.PHP_EOL.'		<span>Index</span>'.PHP_EOL.'		:@search'.PHP_EOL.'	</header>'.PHP_EOL.'	<div class="content">',
	'listItem' => '<div class="listItem">'.PHP_EOL.'	<div class="fileIcon">'.PHP_EOL.'		<a href=":fileHref">:@fileIcon</a>'.PHP_EOL.'	</div>'.PHP_EOL.'	<div class="fileName">'.PHP_EOL.'		<a href=":fileHref">:fileName<span>:fileExt</span></a>'.PHP_EOL.'	</div>'.PHP_EOL.'	<div class="fileActions">'.PHP_EOL.'		:gt:fileSize'.PHP_EOL.'	</div>'.PHP_EOL.'</div>',
	'listItemArchive' => '<div class="listItem">'.PHP_EOL.'	<div class="fileIcon">'.PHP_EOL.'		:@fileIcon'.PHP_EOL.'	</div>'.PHP_EOL.'	<div class="fileName">'.PHP_EOL.'		:fileName<span>:fileExt</span>'.PHP_EOL.'	</div>'.PHP_EOL.'</div>',
	'listItemBack' => '<div class="listItem">'.PHP_EOL.'	<div class="fileIcon">'.PHP_EOL.'		<a href=":parentFolderHref">:@icon</a>'.PHP_EOL.'	</div>'.PHP_EOL.'	<div class="fileName">'.PHP_EOL.'		<a href=":parentFolderHref"><span>:label</span> :parentFolderName</a>'.PHP_EOL.'	</div>'.PHP_EOL.'</div>'.PHP_EOL.'',
	'listTableEnd' => '</table>',
	'listTableRow' => '<tr>'.PHP_EOL.'	<td class="key">:key</td>'.PHP_EOL.'	<td class="value">:value</td>'.PHP_EOL.'</tr>',
	'listTableStart' => '<table class="listTable">',
	'searchBox' => '<div class="searchBox">'.PHP_EOL.'	<header>Search</header>'.PHP_EOL.'	<div class="content">'.PHP_EOL.'		<form method="post" action="#results">'.PHP_EOL.'			<div class="formElement">'.PHP_EOL.'				<input type="search" required name="q" value=":q" class="hasPlaceholder">'.PHP_EOL.'				<div class="isPlaceholder">Search</div>'.PHP_EOL.'			</div>'.PHP_EOL.'			<div class="formElement">'.PHP_EOL.'				<table class="listTable">'.PHP_EOL.'					<tr>'.PHP_EOL.'						<td class="key">Regular Expressions</td>'.PHP_EOL.'						<td class="value">'.PHP_EOL.'							<div class="checkbox">'.PHP_EOL.'								<input type="checkbox" name="regex" id="regex":regexChecked>'.PHP_EOL.'								<label for="regex"></label>'.PHP_EOL.'							</div>'.PHP_EOL.'						</td>'.PHP_EOL.'					</tr>'.PHP_EOL.'					<tr>'.PHP_EOL.'						<td class="key">Case sensitive</td>'.PHP_EOL.'						<td class="value">'.PHP_EOL.'							<div class="checkbox">'.PHP_EOL.'								<input type="checkbox" name="case" id="case":caseChecked>'.PHP_EOL.'								<label for="case"></label>'.PHP_EOL.'							</div>'.PHP_EOL.'						</td>'.PHP_EOL.'					</tr>'.PHP_EOL.'					<tr>'.PHP_EOL.'						<td class="key">Ignore larger files</td>'.PHP_EOL.'						<td class="value">'.PHP_EOL.'							<input class="input number" type="number" value=":maxSize" name="maxSize" step=".1"> MB+'.PHP_EOL.'						</td>'.PHP_EOL.'					</tr>'.PHP_EOL.'				</table>'.PHP_EOL.'			</div>'.PHP_EOL.'			<div class="formElement right">'.PHP_EOL.'				<input type="submit" class="button" value="Search">'.PHP_EOL.'			</div>'.PHP_EOL.'		</form>'.PHP_EOL.'	</div>'.PHP_EOL.'</div>',
	'svg' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24">'.PHP_EOL.'	:@icon'.PHP_EOL.'</svg>',
]; }
