<?php
/* 
Uses the KML Class to easily assemble a KML File.

In this case the file we are generating is 

TODO: make Circle_Gen.php Do This.
*/
include "./kml.class.php";
// Creates the Document.
$newGP = array();
$newGP["lng"] = "4.1234";
$newGP["lat"] = "1.4321";
$newGP["full_name"] = "Example Point";
$newGP["deets"] = "This is some sample information about the location<br/>you can use html to format it.";
$newGP["custom_map"] = "./images/acte_p.png";


$kml = new KML($title);
$document = new KMLDocument($title, $title);

/**
  * Style definitions
  Icons from http://ndrigs.s3.amazonaws.com/pincons
*/
$path = "./images/";
$url = "http://mysite.com/pincons/";

$color = $newGP["custom_map"].".png";
$style = new KMLStyle($color);
$style->setIconStyle($url.$color, 'ffffffff', 'normal', 1);
$style->setLineStyle('ffffffff', 'normal', 2);
$document->addStyle($style);

$portFolder = new KMLFolder('', 'Locations');

//Build Point Information
$lng = $newGP["lng"];
$lat = $newGP["lat"];
$name  = $newGP["full_name"];
$comment = $newGP["deets"];

//Create KML PlaceMark
$port = new KMLPlaceMark('', $name, '', true);
$port->setGeometry(new KMLPoint($lng, $lat, 0));
$port->setStyleUrl('#'.$color);

//Sample Popup Balloon. $[geDirections] is hard coded to google earth.
$style = new KMLStyle();
$style->setBalloonStyle ("<h2>".$name."</h2><div style='padding:7px;'>".$comment."</div><h3>$[geDirections]</h3>");
$port->addStyle($style);

$portFolder->addFeature($port);

$document->addFeature($portFolder);
$kml->setFeature($document);
echo $kml->output('S');
?>