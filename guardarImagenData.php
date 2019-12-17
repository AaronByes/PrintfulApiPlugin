<?php
session_start();
if (isset($_GET['width']) && isset($_GET['height']) && isset($_GET['left']) && isset($_GET['top'])) {
    $_SESSION["imageWidth"] = $_GET['width'];
    $_SESSION["imageHeight"] = $_GET['height'];
    $_SESSION["imageLeft"] = $_GET['left'];
    $_SESSION["imageTop"] = $_GET['top'];
    $width = $_GET['width'];
    $height = $_GET['height'];
    $left = $_GET['left'];
    $top = $_GET['top'];
    //echo "alert(\"" . @$width . " - " . @$height . " - " . @$left . " - " . @$top . "\");";
} else {
    //echo "fail";
}

function getImageWidth()
{
    return $_SESSION["imageWidth"];
}

function getImageHeight()
{
    return $_SESSION["imageHeight"];
}

function getImageLeft()
{
    return $_SESSION["imageLeft"];
}

function getImageTop()
{
    return $_SESSION["imageTop"];
}
