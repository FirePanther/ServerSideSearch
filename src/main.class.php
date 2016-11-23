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
		$this->icons = icons();
		if ($this->isLoggedIn()) {
			$this->setPage('index');
		} else {
			$this->setPage('login');
		}
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
			'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.
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
	private function getHref($newParams = [], $params = null) {
		if ($params === null) {
			$params = $_GET;
		}
		$params = array_merge($params, $newParams);
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
				
				$breadcrumb = '/';
				$pathArr = explode('/', substr($path, 1));
				$last = array_pop($pathArr);
				$dirs = '';
				foreach ($pathArr as $dir) {
					$dirs .= '/'.$dir;
					$breadcrumb .= ' <a href="'.$this->getHref([
						'path' => $dirs
					]).'" class="breadcrumbLink">'.htmlspecialchars($dir).'</a> /';
				}
				if ($last) $breadcrumb .= ' '.htmlspecialchars($last).($isFile ? '' : ' /');
				
				$this->body = '
					<section id="wrapper">
						<header>Index of: '.$breadcrumb.'</header>
						<div class="content">';
				if ($isFile) {
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
				$this->body .= '
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
					<div class="formItem">
						<a href="'.$viewHref.'" class="linkButton">View</a>
					</div>';
				break;
			case 'view':
				$infoHref = $this->getHref(['action' => 'info']);
				$this->body .= '
					<div class="formItem">
						<a href="'.$infoHref.'" class="linkButton">Back</a>
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
								'.highlight_file($path, 1).'
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
		$s = scandir($path);
		if (count($s) == 2) {
			$this->body .= 'Empty Folder';
		} else {
			if ($dirname !== '') {
				$parentFolderHref = $this->getHref([
					'path' => '/'.implode('/', $pathArr)
				]);
				$parentFolderName = array_pop($pathArr);
				if ($parentFolderName == '') $parentFolderName = 'root';
				$this->body .= '
					<div class="listItem">
						<div class="fileIcon">
							<a href="'.$parentFolderHref.'">'.$this->icon('back', '000').'</a>
						</div>
						<div class="fileName">
							<a href="'.$parentFolderHref.'"><span>up to</span> '.htmlspecialchars($parentFolderName).'</a>
						</div>
					</div>';
			}
			foreach ($s as $f) {
				if ($f == '.' || $f == '..') continue;
				$fileHref = $this->getHref([
					'path' => $path.'/'.$f
				]);
				$this->body .= '
					<div class="listItem">
						<div class="fileIcon">
							<a href="'.$fileHref.'">'.$this->iconByFile($path.'/'.$f).'</a>
						</div>
						<div class="fileName">
							<a href="'.$fileHref.'">'.preg_replace('~\.[^.]*$~', '<span>$0</span>', htmlspecialchars($f)).'</a>
						</div>
						<div class="fileActions">
							...
						</div>
					</div>';
			}
		}
	}
}
