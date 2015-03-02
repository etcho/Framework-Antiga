<?php
    switch ($pacote){
    case "lytebox":
?>
        <script type="text/javascript" src="<?php echo URL ?>assets/js/lytebox/lytebox.js"></script>
        <link rel="stylesheet" href="<?php echo URL ?>assets/js/lytebox/lytebox.css" type="text/css" media="screen" />
<?php
        break;
    case "swfupload":
?>
        <link href="<?php echo URL ?>lib/swfupload/css/default.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo URL ?>lib/swfupload/swfupload.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>lib/swfupload/js/swfupload.queue.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>lib/swfupload/js/fileprogress.js"></script>
        <script type="text/javascript" src="<?php echo URL ?>lib/swfupload/js/handlers.js"></script>
<?php
        break;
    case "config":
        require_once("lib/config.php");
        break;
    case "xajax":
        require_once("lib/xajax/xajax_core/xajax.inc.php");
        break;
    case "html2fpdf":
        require_once("lib/html2fpdf/html2fpdf.php");
        break;
	case "recaptcha":
		require_once("lib/recaptcha/recaptchalib.php");
        $_GET["publickey"] = "6LfHhdgSAAAAAJzS5a_5CXBUroU0WFeIUQCFDH_G";
        $_GET["privatekey"] = "6LfHhdgSAAAAAEaFqBpf1LpenGd17mlSIgd4p6UU";
		break;
    }
?>
