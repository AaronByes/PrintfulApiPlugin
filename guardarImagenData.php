<?php
if (isset($_GET['width'])) {
    $image = $_GET['width'];
    echo "alert(\"" . @$image . "\");";
} else {
    echo "fail";
}

function getImageWidth()
{
    $imageWidth = '149';
    return $imageWidth;
}

function getImageHeight()
{
    $imageHeight = '60';
    return $imageHeight;
}

function getImageLeft()
{
    $imageLeft = '79';
    return $imageLeft;
}

function getImageTop()
{
    $imageTop = '82';
    return $imageTop;
}
