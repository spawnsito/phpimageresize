<?php

require 'ImagePath.php';
require 'Configuration.php';
require 'Resizer.php';

function sanitize($path) {
	return urldecode($path);
}

function isInCache($path, $imagePath) {
	$isInCache = false;
	if(file_exists($path) == true):
		$isInCache = true;
		$origFileTime = date("YmdHis",filemtime($imagePath));
		$newFileTime = date("YmdHis",filemtime($path));
		if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
			$isInCache = false;
		endif;
	endif;

	return $isInCache;
}

function composeNewPath($imagePath, $configuration) {
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();
	$filename = md5_file($imagePath);
	$finfo = pathinfo($imagePath);
	$ext = $finfo['extension'];

	$cropSignal = isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "";
	$scaleSignal = isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "";
	$widthSignal = !empty($w) ? '_w'.$w : '';
	$heightSignal = !empty($h) ? '_h'.$h : '';
	$extension = '.'.$ext;

	$newPath = $configuration->obtainCache() .$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

	if($opts['output-filename']) {
		$newPath = $opts['output-filename'];
	}

	return $newPath;
}

function doResize($imagePath, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();

	if(!empty($w) and !empty($h)):

		list($width,$height) = getimagesize($imagePath);
		$resize = $w;

		if($width > $height):
			$resize = $w;
			if(true === $opts['crop']):
				$resize = "x".$h;
			endif;
		else:
			$resize = "x".$h;
			if(true === $opts['crop']):
				$resize = $w;
			endif;
		endif;

		if(true === $opts['scale']):
			$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
				" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
		else:
			$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
				" -size ". escapeshellarg($w ."x". $h) .
				" xc:". escapeshellarg($opts['canvas-color']) .
				" +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
		endif;

	else:
		$cmd = $configuration->obtainConvertPath() ." " . escapeshellarg($imagePath) .
			" -thumbnail ". (!empty($h) ? 'x':'') . $w ."".
			(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") .
			" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);
	endif;

	$c = exec($cmd, $output, $return_code);
	if($return_code != 0) {
		error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
		throw new RuntimeException();
	}
}

function resize($imagePath,$opts=null){


	$path = new ImagePath($imagePath);
	$configuration = new Configuration($opts);

	$resizer = new Resizer($path, $configuration);

	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();

	if(empty($opts['output-filename']) && empty($w) && empty($h)) {
		return 'cannot resize the image';
	}

	$imagePath = $path->sanitizedPath();

	try {
		$imagePath = $resizer->obtainFilePath();
	} catch (Exception $e) {
		return 'image not found';
	}

	$newPath = composeNewPath($imagePath, $configuration);

	if(empty($opts['output-filename']) && empty($w) && empty($h)) {
		return 'cannot resize the image';
	}

    $create = !isInCache($newPath, $imagePath);

	if($create == true):
		
	endif;

	# return cache file path
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
	
}
