<?php

require 'ImagePath.php';
require 'Configuration.php';
require 'Resizer.php';

function sanitize($path) {
	return urldecode($path);
}

function resize($imagePath,$opts=null){
	$path = new ImagePath($imagePath);
	$configuration = new Configuration($opts);

	$resizer = new Resizer($path, $configuration);

	$opts = $configuration->asHash();
	$imagePath = $path->sanitizedPath();

	try {
		$imagePath = $resizer->obtainFilePath();
	} catch (Exception $e) {
		return 'image not found';
	}

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

	if(empty($opts['output-filename']))

	if(false !== $opts['output-filename']) :
		$newPath = $opts['output-filename'];
	else:
        if(!empty($w) and !empty($h)):
            $newPath = $configuration->obtainCache() .$filename.'_w'.$w.'_h'.$h.$cropSignal.$scaleSignal.'.'.$ext;
        elseif(!empty($w)):
            $newPath = $configuration->obtainCache() .$filename.'_w'.$w.'.'.$ext;
        elseif(!empty($h)):
            $newPath = $configuration->obtainCache() .$filename.'_h'.$h.'.'.$ext;
        else:
            return false;
        endif;
	endif;

	$create = true;

    if(file_exists($newPath) == true):
        $create = false;
        $origFileTime = date("YmdHis",filemtime($imagePath));
        $newFileTime = date("YmdHis",filemtime($newPath));
        if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
            $create = true;
        endif;
    endif;

	if($create == true):
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
            return false;
		}
	endif;

	# return cache file path
	return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);
	
}
