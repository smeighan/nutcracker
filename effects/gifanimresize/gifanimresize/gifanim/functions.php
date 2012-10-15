<?
function scaleImageFile($fileSrc, $w, $h, $saveTo, $resizemethod = 1){
	$delays = array(5);
	
	if(file_exists($fileSrc) && is_numeric($w) && is_numeric($h)) {
		if(list($width, $height, $type, $attr) = getimagesize($fileSrc)){
	

			if($type == 1 && is_ani($fileSrc)){
				$gif = new GIFDecoder(file_get_contents($fileSrc));
				$delays = $gif->GIFGetDelays();
				$oldimg_a = $gif->GIFGetFrames();
				if(sizeof($oldimg_a) <= 0) return false;
				
				for($i = 0; $i < sizeof($oldimg_a); $i++){
					$oldimg_a[$i] = imagecreatefromstring($oldimg_a[$i]);
				}
				
			}else{
			    if(! ($oldimg = loadImage($fileSrc, $type))) return false;
				$oldimg_a = array($oldimg);
			}
			$newimg_a = array();
			
			
			foreach($oldimg_a as $oldimg){
				$newimg = null;
	
				if($resizemethod == 4){
					$ratio = 1.0;
					$ratio_w = $width / $w;
					$ratio_h = $height / $h;
					$ratio = ($ratio_h < $ratio_w ? $ratio_h : $ratio_w);
					$neww = intval($width / $ratio);
					$newh = intval($height / $ratio);
					$tempimg = imagecreatetruecolor($neww, $newh);
					imagecopyresampled($tempimg, $oldimg, 0, 0, 0, 0, $neww, $newh, $width, $height);
					$clipw = 0; $cliph = 0;
					if($neww > $w) $clipw = $neww - $w;
					if($newh > $h) $cliph = $newh - $h;
	
	
					$cliptop = floor($cliph / 2);
					$clipleft = floor($clipw / 2);
					$newimg = imagecreatetruecolor($w, $h);
					imagecopy($newimg, $tempimg, 0, 0, $clipleft, $cliptop, $w, $h);
				}else if($resizemethod == 3){
					$newimg = imagecreatetruecolor($w, $h);
					imagecopyresampled($newimg, $oldimg, 0, 0, 0, 0, $w, $h, $width, $height);
				}else if($resizemethod == 2){
					$ratio = 1.0;
					$ratio_w = $width / $w;
					$ratio_h = $height / $h;
					$ratio = ($ratio_h > $ratio_w ? $ratio_h : $ratio_w);
					$newimg = imagecreatetruecolor(intval($width / $ratio), intval($height / $ratio));
					imagecopyresampled($newimg, $oldimg, 0, 0, 0, 0, intval($width / $ratio), intval($height / $ratio), $width, $height);
				}else{
					$ratio = 1.0;
					if($width > $w || $height > $h){
						$ratio = $width / $w;
						if(($height / $h) > $ratio) $ratio = $height / $h;	
					}
					$newimg = imagecreatetruecolor(intval($width / $ratio), intval($height / $ratio));
					imagecopyresampled($newimg, $oldimg, 0, 0, 0, 0, intval($width / $ratio), intval($height / $ratio), $width, $height);
				}
				array_push($newimg_a, $newimg);
			}

			if(sizeof($newimg_a) > 1){
				$newa = array();
				foreach($newimg_a as $i){
					ob_start();
					imagegif($i);
					$gifdata = ob_get_clean();
					array_push($newa, $gifdata);
				}

				$gifmerge = new GIFEncoder	(
							$newa,
							$delays,
							999,
							2,
							0, 0, 0,
							"bin"
					);	
				FWrite ( FOpen ( $saveTo, "wb" ), $gifmerge->GetAnimation ( ) );
	        }else{
		        outputImage($newimg, $saveTo);
			}            

            
	        foreach($newimg_a as $newimg){
	        	imagedestroy($newimg);
	        }
            return true;
            
		} else return false;
	}
	return false;
}


function loadImage($fileSrc, $imgType){
    switch ($imgType){
        case 1:   //   gif
            return imagecreatefromgif($fileSrc);
        case 2:   //   jpeg
            return imagecreatefromjpeg($fileSrc);
        case 3:  //   png
            return imagecreatefrompng($fileSrc);
    }
    return false;
}

function outputImage($img, $saveTo){
    if(strlen($saveTo) > 0){
        imagejpeg($img, $saveTo, 90);
    }

    return true;
}


function is_ani($filename) {
	return (bool)preg_match('/\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)/s', file_get_contents($filename), $m);
}