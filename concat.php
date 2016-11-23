<?php
/**
 * This file concats the files from the src folder into a single file.
 *
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2016 Suat Secmen
 * @license          MIT License
 */
chdir(__DIR__);
$source = '<?php'.
	getPhp('src/config.php').
	loadAsVar([
		'style' => 'src/style.css'
	]).
	getPhp('src/header.php').
	getPhp('src/main.class.php').
	generateIconsSrc();

file_put_contents('sss.php', $source);

/**
 * get the content of the php file 
 */
function getPhp($file) {
	if (!file_exists($file)) throw new Exception('File doesn\'t exist: '.$file);
	$src = trim(preg_replace('~^\s*<\?(php)?\s*~i', '', file_get_contents($file)));
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
	return $iconsPhpSrc.PHP_EOL.']; }';
}