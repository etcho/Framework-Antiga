<?php
Error_Reporting(E_ALL & ~E_NOTICE);
//header("Pragma: no-cache");
//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>EXIF</title>
<style> 
<!--
html {
	color:#000000;
	background-color:#FFFFFF;
	}
body {
	background:url(images/exif_background.gif) #FFFFFF no-repeat;
	padding:0px;
	margin:0px;
	height:424px;
	width:572px;
	}
textarea {
	position:absolute;
	top:55px;
	left:45px;
	width:210px; 
	height:280px; 
	padding:0px;
	border:0px;
	font:11px verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif; 
	color:#000000; 
	background: transparent;
	}
-->
</style>
</head>
<body>
<textarea readonly="readonly" style="overflow-x:hidden;overflow-y:hidden"><?php
if (isset($_GET['image']))
{
$im=$_GET['image'];
if (strlen($im)>5) {
if ($im{0}.$im{1}.$im{2}.$im{3}.$im{4}.$im{5}.$im{6} == 'http://'){
	$im = str_replace('http://','',$im);
	$im = str_replace($_SERVER['HTTP_HOST'],'',$im);
	}
}
if ($im{0} == '/'){
	$im = $_SERVER['DOCUMENT_ROOT'].$im;
}else{
	$im = getcwd()."/".$im;
}

	require_once("exif_functions.php");
	$exif=@exif_php_read_data($im);



    if ($exif===false)
        $exif=array();

    $data=array();
    $output='';
	//Start Model
    if (isset($exif['Model']))
    	$output.="Model: ".$exif['Model']."\n";
	//Finish Model
    //Start Date
    $date="";
    if (isset($exif['DateTimeOriginal']))
        $date=$exif['DateTimeOriginal'];
    if (empty($date) && isset($exif['DateTime']))
        $date=$exif['DateTime'];

    if (!empty($date))
    {
        $date=split(':',str_replace(' ',':',$date));
        $date="{$date[0]}-{$date[1]}-{$date[2]} {$date[3]}:{$date[4]}:{$date[5]}";
        $date=strftime("%d %B %Y",strtotime($date));
        $output.="Date: ".$date."\n";
    }
	//Finish Date
    //Start ISO
    $iso="";
    if (isset($exif['ISOSpeedRatings']))
        $iso=$exif['ISOSpeedRatings'];
    else if (isset($exif['ModeArray']))
    {
        // Add ISO for PowerShot cameras
        switch (@$exif['ModeArray'][16])
        {
            case 15: $iso="auto";break;
            case 16: $iso="50";break;
            case 17: $iso="100";break;
            case 18: $iso="200";break;
            case 19: $iso="400";break;
        }
    }

    if (!empty($iso))
        $output.="ISO value: ".$iso."\n";
   // Finish ISO
   // Start Aperture
    if (isset($exif['COMPUTED']['ApertureFNumber']))
    {
        $output.="Aperture: ".$exif['COMPUTED']['ApertureFNumber']."\n";
    }
  	// Finish Aperture
  	//Start Exposure
    $shutter=0;
    if (isset($exif['ExposureTime']))
    {
        list($d1,$d2)=split('/',$exif['ExposureTime']);
        if ($d1>0 && $d2>0)
            $shutter=$d1/$d2;
        else
            $shutter=$exif['ExposureTime'];

        if ($shutter<1 && $shutter>0)
            $e="1/" . round(1/$shutter,0) . " s";
        else
            $e=round($shutter,1) ." s";

        $output.="Exposure: ".$e."\n";
    }
    //Finish Exposure
	//Start Flash
    if (isset($exif['Flash']))
    {
        $e=($exif['Flash']&1)?'Yes':'No';
        $output.="Flash: ".$e."\n";
    }
	//Finish Flash
	//Start MeteringMode
    if (isset($exif['MeteringMode']))
    {
    	switch ($exif['MeteringMode'])
        {
            case 0: $meterimgmode="Unknown";break;
            case 1: $meterimgmode="Average";break;
            case 2: $meterimgmode="CenterWeightedAverage";break;
            case 3: $meterimgmode="Spot";break;
            case 4: $meterimgmode="MultiSpot";break;
            case 5: $meterimgmode="Pattern";break;
            case 6: $meterimgmode="Partial";break;
            case 255: $meterimgmode="Other";break;
            default: $meterimgmode="Unknown";
        }
    $output.="Metering Mode: ".$meterimgmode."\n";
    }
	//Finish MeteringMode
   // Start Width
    if (isset($exif['COMPUTED']['Width']))
    	$width = $exif['COMPUTED']['Width'];
    elseif (isset($exif['ExifImageWidth']))
    	$width = $exif['ExifImageWidth'];
	else
		$width = "Unknown";  

  	// Finish Width
   // Start Height
    if (isset($exif['COMPUTED']['Height']))
    	$height = $exif['COMPUTED']['Height'];
    elseif (isset($exif['ExifImageLength']))
    	$height = $exif['ExifImageLength'];
	else
		$height = "Unknown";  

	$output.="Image Resolution: ".$height." x ".$width." px\n";
  	// Finish Height
    
   echo $output;

/*  
    if (isset($exif['FocalLength']))
    {
        list($d1,$d2)=split('/',$exif['FocalLength']);
        if ($d1>0 && $d2>0)
        {
            $e=round($d1/$d2,1);
            $data[]="F:|$e mm";
        }
    }
*/
}
else 
{
	echo "This page is showing image EXIF information. Image is taking from GET \"image\" variable";
}
?></textarea>
<div style="position:absolute;top:75px;left:325px;width:195px;height:80px;text-align:center; font-size:16pt">Image EXIF<br/>Information</div>
</body>
</html>
