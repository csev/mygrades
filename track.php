<?php

require_once "../config.php";

// The Tsugi PHP API Documentation is available at:
// http://do1.dr-chuck.com/tsugi/phpdoc
use \Tsugi\Util\Net;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\Blob\BlobUtil;

if (BlobUtil::emptyPostSessionLost()) {
    die('Error: Maximum size of ' . BlobUtil::maxUpload() . 'MB exceeded.');
}

// No parameter means we require CONTEXT, USER, and LINK
$LAUNCH = LTIX::requireData();

// Model
$p = $CFG->dbprefix;

//Defining the environment
$im     = imagecreatefrompng("images/track.png");
$orange = imagecolorallocate($im, 220, 210, 60);
$red=imagecolorallocate($im,255,0,0);
$blue=imagecolorallocate($im,0,0,255);
$black = imagecolorallocate($im,0,0,0);
$width=700;
$height=300;
$left=60;
$right=690;
$top=10;
$bottom=250;
$diameter=15;
imageline($im,$left,$top,$right,$top,$black);
imageline($im,$left,$bottom,$right,$bottom,$black);
imagedashedline($im,$left,$top,$left,$bottom,$black);
$textline=$bottom+15;
imagestring($im,3,$left,$textline,'100%',$black);
imagestring($im,3,intval($left+($right-$left)*0.25),$textline,'75%',$black);
imagestring($im,3,intval($left+($right-$left)*0.5),$textline,'50%',$black);
imagestring($im,3,intval($left+($right-$left)*0.75),$textline,'25%',$black);

header("Content-type: image/png");

if ($USER->instructor) {
    $grades_array=$_SESSION['grades'];
    $names_array=$_SESSION['names'];

    for ($i=1;$i<count($grades_array);$i++) {
        $gx=intval($right-($right-$left)*$grades_array[$i]);
        $gy=rand($top+$diameter,$bottom-$diameter);
        imagestring($im,3,$gx,$gy,$names_array[$i],$red);
    }
} else {
    $grades_array=$_SESSION['grades'];
    $user_grade=$_SESSION['userGrade'];

    foreach ($grades_array as $g) {
        $gx=intval($right-($right-$left)*$g);
        $gy=rand($top+$diameter,$bottom-$diameter);
        imagefilledellipse($im,$gx,$gy,$diameter,$diameter,$red);
    }

    $ix=intval($right-($right-$left)*$user_grade);
    $iy=intval(($bottom-$top)/2);
    imagefilledellipse($im,$ix,$iy,$diameter,$diameter,$blue);
}



imagepng($im);
imagedestroy($im);

?>