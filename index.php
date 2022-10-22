<link href="style.css" rel="stylesheet" />
<?php
include_once("highlighter.class.php");
$colorObj = new highlighter();
$fileContent = $colorObj->applycolor("highlighter.class.php");
echo $fileContent;
echo "Wikis are enabled by wiki software, otherwise known as wiki engines. A wiki engine, being a form of a content management system, differs from other web-based systems such as blog software";
$colorObj->showfilename(false);
$fileContent = $colorObj->applycolor("index.php");
echo $fileContent;
?>
