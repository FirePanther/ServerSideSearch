<?php
/**
 * This file concats the files from the src folder into a single file.
 *
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2016 Suat Secmen
 * @license          MIT License
 */
chdir(__DIR__);

$dev = $argc ? $argv[1] == 'dev' : false;

$source = '<?php'.
	getPhp('src/config.php', $dev).
	loadAsVar([
		'style' => 'src/style.css'
	]).
	getPhp('src/header.php').
	getPhp('src/main.class.php').
	generateIconsSrc().
	generateHtmlSrc();

file_put_contents('sss.php', $source);

/**
 * get the content of the php file 
 */
function getPhp($file, $dev = false) {
	if (!file_exists($file)) throw new Exception('File doesn\'t exist: '.$file);
	$src = file_get_contents($file);
	if (!$dev) {
		$src = preg_replace('~(\$password\s*=\s*)(\'|")(.*?)\\2~', '$1$2$2', $src);
	}
	$src = trim(preg_replace('~^\s*<\?(php)?\s*~i', '', $src));
	return PHP_EOL.$src.PHP_EOL;
}

/**
 * load the files into php variables
 */
function loadAsVar($inc) {
	$src = PHP_EOL;
	
	foreach ($inc as $var => $f) {
		if (!file_exists($f)) throw new Exception('File doesn\'t exist: '.$f);
		$fSrc = file_get_contents($f);
		$addVar = '$'.$var.' = \''.str_replace('\'', '\\\'', $fSrc).'\';';
		$src .= $addVar.PHP_EOL;
	}
	
	return $src;
}

/**
 * generates a php function with all svg icons from src/icons/
 */
function generateIconsSrc() {
	$iconsPhpSrc = 'function icons() { return [';
	
	$svgs = glob('src/icons/*.svg');
	foreach ($svgs as $svg) {
		$filename = basename($svg, '.svg');
		if (preg_match('~^(.*?)\-([a-f0-9]{3}|[a-f0-9]{6})$~i', $filename, $m)) {
			$name = $m[1];
			$color = $m[2];
			
			$src = file_get_contents($svg);
			if (preg_match('~<svg[^>]*>(.*)<\/svg>~i', $src, $m)) {
				$iconsPhpSrc .= PHP_EOL.'	\''.$name.'\' => ['.
					'\''.str_replace('\'', '\\\'', $m[1]).'\', \''.$color.'\''.
				'],';
			}
		}
	}
	return $iconsPhpSrc.PHP_EOL.']; }'.PHP_EOL;
}

/**
 * generates a php function with all svg icons from src/icons/
 */
function generateHtmlSrc() {
	$htmlPhpSrc = 'function htmls() { return [';
	
	$htmls = glob('src/html/*.html');
	foreach ($htmls as $html) {
		$name = str_replace('\'', '\\\'', basename($html, '.html'));
		
		$src = str_replace('\'', '\\\'', file_get_contents($html));
		$src = str_replace(["\r\n", "\r", "\n"], '\'.PHP_EOL.\'', $src);
		$htmlPhpSrc .= PHP_EOL.'	\''.$name.'\' => '.
			'\''.$src.'\',';
	}
	return $htmlPhpSrc.PHP_EOL.']; }'.PHP_EOL;
}