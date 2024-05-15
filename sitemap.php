<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: text/xml; charset=UTF-8");
 
$sitemapPath = __DIR__ . '/../sitemap.xml';
 
if(file_exists($sitemapPath)){
    $courses = file_get_contents($sitemapPath);
    echo $courses;
} else {
    echo "&lt;?xml version=\"1.0\"?>&lt;Error>404 - File not found!&lt;/Error>";
}