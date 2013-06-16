<?php
//This file takes a couple parameters and gives back a KML Circle.
// example URL: circle_gen.php?lat=47.99147681&lng=-102.69587137&color=green&mi=15&name=thetest352

function mostPrecision($firstCoordinate, $secondCoordinate, $radius) {
  $firstprecision = 0;
// Return an integer between two and nine as the best precision of the 
// two given Coordinates together with the Radius precision in meters 
// (assume more than six decimal precision of a Coordinate is bogus)
  $exploded = explode(".", $firstCoordinate);
  if (count($exploded) < 2) { 
    $firstPrecision = 0; 
  } else {
    $firstPrecision = strlen($exploded[1]);
    if ($firstprecision > 6) { $firstPrecision = 6; }  
  }
  $exploded = explode(".", $secondCoordinate);
  if (count($exploded) < 2) { 
    $secondPrecision = 0; 
  } else {
    $secondPrecision = strlen($exploded[1]);
    if ($secondPrecision > 6) { $secondPrecision = 6; }  
  }
  if ($firstPrecision > $secondPrecision) {
    $coordPrecision = $firstPrecision;
  } else { 
    $coordPrecision = $secondPrecision;
  }
// To make precise divisions of a polygon with dozens to 
// hundreds of sides requires two additional decimal places.
  $coordPrecision = $coordPrecision + 2;
// A circle with radius greater than 10,000 kilometers covers 
// a hemsiphere and needs only whole number coordinates (zero 
// decimal places) to precisely divide a polygon with dozens 
// to hundreds of sides. As the circle radius decreases, one 
// needs correspondingly higher precision. For radii less than 
// ten meters, a precision of seven decimal places is needed.  
  if ($radius > 10000000) { $radiusPrecision = 0;
  } elseif ($radius > 1000000) {  $radiusPrecision = 1;
  } elseif ($radius > 100000) {  $radiusPrecision = 2;
  } elseif ($radius > 10000) {  $radiusPrecision = 3;
  } elseif ($radius > 1000) {  $radiusPrecision = 4;
  } elseif ($radius > 100) {  $radiusPrecision = 5;
  } elseif ($radius > 10) {  $radiusPrecision = 6;
  } else { $radiusPrecision = 7;
  }
  if ($radiusPrecision > $coordPrecision) {
    return $radiusPrecision;
  } else { 
    return $coordPrecision;
  }
}

//header('Content-type: application/vnd.google-earth.kml+xml');
$circleLabel = htmlspecialchars(rawurldecode($_GET["name"]));
$circleLat = $_GET["lat"];
$circleLon = $_GET["lng"];

$mi = $_GET["mi"];
$name = $circleLabel." ".$mi." Mile Radius";

$circleRadius = $mi / 0.00062137;

$color = $_GET["color"];

if($color=="red"){
	$fcolor = "ff0000ff";
} else if($color=="green"){
	$fcolor = "ff00ff00";
} else if($color=="blue"){
	$fcolor = "ffff0000";
} else if($color=="yellow"){
	$fcolor = "ff00ffff";
} else if($color=="white"){
	$fcolor = "ffffffff";
} else {
	$fcolor = "ff0000ff";
}

$circlePrecision = mostPrecision($circleLat, $circleLon, $circleRadius);
$coordinatesList = "";
// Code based on Google Earth community posting by 'ink_polaroid'
// http://bbs.keyhole.com/ubb/showflat.php/Cat/0/Number/23634/an//page//vc/1
$num_points = 36;
$delta_pts = 360/$num_points;
// convert coordinates to radians
$lat = deg2rad($circleLat);
$lon = deg2rad($circleLon);
$d = $circleRadius;
$d_rad = $d/6378137;
// loop around the compass, appending coordinates of each vertex
for($i=0; $i<=$num_points; $i++) {
  $radial = deg2rad($i*$delta_pts);
  $lat_rad = asin(sin($lat)*cos($d_rad) + cos($lat)*sin($d_rad)*cos($radial));
  $dlon_rad = atan2(sin($radial)*sin($d_rad)*cos($lat), cos($d_rad)-sin($lat)*sin($lat_rad));
  $lon_rad = fmod(($lon+$dlon_rad + M_PI), 2*M_PI) - M_PI;
  $coordinatesList .= Round(rad2deg($lon_rad),$circlePrecision).",".Round(rad2deg($lat_rad),$circlePrecision).",0 ";
}
$kml = "<?xml version='1.0' encoding='UTF-8' ?>\n";
$kml .= "<kml xmlns='http://earth.google.com/kml/2.0'>\n";
$kml .= "<Document>\n";
$kml .= " <Folder>\n";
$kml .= "   <name>".$name."</name>\n";
$kml .= "   <visibility>1</visibility>\n";
$kml .= "   <Placemark>\n";
$kml .= "    <name>".$circleLabel."</name>\n";
$kml .= "    <visibility>1</visibility>\n";
$kml .= "    <Style>\n";
$kml .= "     <geomColor>".$fcolor."</geomColor>\n";
$kml .= "     <geomScale>1</geomScale>\n";
$kml .= "     <LineStyle>\n";
$kml .= "      <width>2</width>\n";
$kml .= "      <color>".$fcolor."</color>\n";
$kml .= "     </LineStyle>\n";
$kml .= "     <PolyStyle id='defaultPolyStyle'>\n";
$kml .= "      <color>".$fcolor."</color>\n";
$kml .= "      <fill>0</fill>\n";
$kml .= "      <outline>1</outline>\n";
$kml .= "     </PolyStyle>\n";
$kml .= "    </Style>\n";
$kml .= "    <Polygon>\n";
$kml .= "     <outerBoundaryIs>\n";
$kml .= "      <LinearRing>\n";
$kml .= "       <coordinates>".$coordinatesList."</coordinates>\n";
$kml .= "      </LinearRing>\n";
$kml .= "     </outerBoundaryIs>\n";
$kml .= "    </Polygon>\n";
$kml .= "   </Placemark>\n";
$kml .= "   <Placemark>\n";
$kml .= "    <name>".$circleLabel."</name>\n";
$kml .= "    <visibility>1</visibility>\n";
$kml .= "    <Style id='".$color.".png'>\n";
$kml .= "     <IconStyle>\n";
$kml .= "      <Icon>\n";
$kml .= "       <href>http://ndrigs.s3.amazonaws.com/pincons/".$color.".png</href>\n";
$kml .= "      </Icon>\n";
$kml .= "     </IconStyle>\n";
$kml .= "     <LabelStyle>\n";
$kml .= "      <scale>1.1</scale>\n";
$kml .= "      <color>".$fcolor."</color>\n";
$kml .= "     </LabelStyle>\n";
$kml .= "    </Style>\n";
$kml .= "    <Point>\n";
$kml .= "       <coordinates>".$circleLon.",".$circleLat."</coordinates>\n";
$kml .= "    </Point>\n";
$kml .= "   </Placemark>\n";
$kml .= "  </Folder>\n";
$kml .= " </Document>\n";
$kml .= "</kml>\n";
header("Content-Type: application/vnd.google-earth.kml+xml;");
header("Content-Disposition: attachment; filename:'circlegen.kml'");
echo $kml;
exit;
?>