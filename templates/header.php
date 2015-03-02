<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="pt-br" />
    <meta http-equiv="X-UA-Compatible" content="IE=100" />
    <meta type="title" content="<?php echo $_GET["title"] ?>" />
    <meta property="og:title" content="<?php echo $_GET["title"] ?>" />
    <?php if (file_exists("assets/images/favicon.png")){ ?>
    	<link rel="icon" href="<?php echo URL ?>assets/images/favicon.png" />
    <?php } elseif (file_exists("assets/images/favicon.ico")){ ?>
    	<link rel="icon" href="<?php echo URL ?>assets/images/favicon.ico" />
    <?php } ?>
	<title><?php echo $_GET["title"] ?></title>
    <?php if (!vazio($_GET["description"])){ ?>
		<meta type="description" content="<?php echo $_GET["description"] ?>" />
        <meta name="description" content="<?php echo $_GET["description"] ?>" />
        <meta property="og:description" content="<?php echo $_GET["description"] ?>" />
    <?php } ?>
    <?php if (!vazio($_GET["keywords"])){ ?>
        <meta name="keywords" content="<?php echo $_GET["keywords"] ?>" />
    <?php } ?>
    <?php if (!vazio($_GET["author"])){ ?>
        <meta name="author" content="<?php echo $_GET["author"] ?>" />
    <?php } ?>
    <?php if (!vazio($_GET["og:url"])){ ?>
        <meta property="og:url" content="<?php echo $_GET["og:url"] ?>" />
    <?php } ?>
    <?php if (!vazio($_GET["og:image"])){ ?>
        <meta property="og:image" content="<?php echo $_GET["og:image"] ?>" />
    <?php } ?>
    <?php echo css_include("template") ?>
    <?php echo css_include("template_print", "print") ?>
    <?php echo javascript_include("jquery") ?>
	<?php echo javascript_include("jquery.placeholder") ?>
	<?php echo javascript_include("js_functions") ?>
	<?php echo javascript_include("mascaras") ?>
    <?php echo javascript_include("jquery.maskedinput") ?>
	<link type="text/css" rel="stylesheet" href="<?php echo URL ?>lib/calendar/source/dhtmlgoodies_calendar.css" media="screen"></link>
	<script type="text/javascript" src="<?php echo URL ?>lib/calendar/source/dhtmlgoodies_calendar.js"></script>
    <?php echo javascript_include("jquery.tipTip") ?>
    <?php echo css_include("jquery.tipTip") ?>
    <script> var URL = "<?php echo URL ?>" </script>
    <script>
		$(document).ready(function(){
            $("[placeholder]").placeholder()
			$(".tipTip").tipTip()
        })
	</script>