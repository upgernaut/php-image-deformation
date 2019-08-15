<?php


function distort($width,$height,$img,$img2)
{
	// случайные параметры (можно поэкспериментировать с коэффициентами):
	// частоты
$rand1 = mt_rand(700000, 1000000) / 15000000;
$rand2 = mt_rand(700000, 1000000) / 15000000;
$rand3 = mt_rand(700000, 1000000) / 15000000;
$rand4 = mt_rand(700000, 1000000) / 15000000;
// фазы
$rand5 = mt_rand(0, 3141592) / 1000000;
$rand6 = mt_rand(0, 3141592) / 1000000;
$rand7 = mt_rand(0, 3141592) / 1000000;
$rand8 = mt_rand(0, 3141592) / 1000000;
// амплитуды
$rand9 = mt_rand(400, 600) / 100;
$rand10 = mt_rand(400, 600) / 100;
	 
	for($x = 0; $x < $width; $x++){
	  for($y = 0; $y < $height; $y++){
		// координаты пикселя-первообраза.
		$sx = $x + ( sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6) ) * $rand9;
		$sy = $y + ( sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8) ) * $rand10;
	 
		// первообраз за пределами изображения
		if($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1){ 
		  $color = 0;
		  $color_x = 0;
		  $color_y = 0;
		  $color_xy = 0;
		}else{ // цвета основного пикселя и его 3-х соседей для лучшего антиалиасинга
		  $color = (imagecolorat($img, $sx, $sy) >> 16) & 0xFF;
		  $color_x = (imagecolorat($img, $sx + 1, $sy) >> 16) & 0xFF;
		  $color_y = (imagecolorat($img, $sx, $sy + 1) >> 16) & 0xFF;
		  $color_xy = (imagecolorat($img, $sx + 1, $sy + 1) >> 16) & 0xFF;
		}



		// сглаживаем только точки, цвета соседей которых отличается
		if($color == $color_x && $color == $color_y && $color == $color_xy){
		  $newcolor=$color;
		}else{
		  $frsx = $sx - floor($sx); //отклонение координат первообраза от целого
		  $frsy = $sy - floor($sy);
		  $frsx1 = 1 - $frsx;
		  $frsy1 = 1 - $frsy;

		  // вычисление цвета нового пикселя как пропорции от цвета основного пикселя и его соседей
		  $newcolor = floor( $color    * $frsx1 * $frsy1 +
							 $color_x  * $frsx  * $frsy1 +
							 $color_y  * $frsx1 * $frsy  +
							 $color_xy * $frsx  * $frsy );
		}
		imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));
	  }
	}	
	return $img2;
	
}

$wordwrap = 60;
$fontPath = "ARIALUNI.TTF";
$textRotation = 0;
$backgroundColor = false; 
$fontColor = "000000";


use PHPImageWorkshop\ImageWorkshop;

require_once('PHPImageWorkshop/ImageWorkshop.php'); 



for($i=1; $i<10; $i++) {

	// / Initialization of layers you need
	$pinguLayer = ImageWorkshop::initFromPath(__DIR__.'/original_image.jpg');
	$emptyForDistort = imagecreate($pinguLayer->getWidth(), $pinguLayer->getHeight());
	$emptyForDistort = ImageWorkshop::initFromResourceVar($emptyForDistort);
	$imgDisplay = distort($pinguLayer->getWidth(),$pinguLayer->getHeight(),$pinguLayer->getResult(),$emptyForDistort->getResult());
	$pinguLayer = ImageWorkshop::initFromResourceVar($imgDisplay);
	// Saving the result in a folder
	$pinguLayer->save(__DIR__."/", "result_image".$i.".jpg", true, null, 95);
	
	?>
		<img src="result_image<?php echo $i; ?>.jpg">
	<?php 
}


?>




