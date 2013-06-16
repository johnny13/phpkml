<?php

function generatekml($color,$aa){
	/*
	Convert Hex to KML Color
	The order of expression is aabbggrr, 
	where aa=alpha (00 to ff); bb=blue (00 to ff); gg=green (00 to ff); rr=red (00 to ff).
	*/
	$rr = substr($color, 0, 2);
	$gg = substr($color, 2, 2);
	$bb = substr($color, 4, 2);
	return $aa.$bb.$gg.$rr;
}


function random_color_part() {
	/* generate random 0 - 255 number and conver to hex. */
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
	$color = random_color_part() . random_color_part() . random_color_part();
	return $color;
}


if(isset($_GET["total"])&&is_numeric($_GET["total"])){
	
	//Setup Check Passed
	//output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=data.csv');
	
	//Open CSV File from Output & Write 1st row as Name
	$output = fopen('php://output', 'w');
	if(!isset($_GET["kml"])){
		fputcsv($output, array('Colors'));
	} else {
		fputcsv($output, array('Colors','KMLColor'));
	}
	
	$i = 0;
	$total=$_GET["total"];
	$colors = array();
	
	if(!isset($_GET["kml"])){
		while($i <= $total){
			$colors[] = '#'.random_color();
			$i++;
		}
		//Debug
		//print_r($colors);
		//exit;
	} else {
		/* alpha opacity value */
		$ALPHA = $_GET["kml"];
		
		while($i <= $total){
			$base = random_color();
			$color = '#'.$base;
			$kmlbase = generatekml($base,$ALPHA);
			$kml = '#'.$kmlbase;
			$colors[] = array($color, $kml);
			$i++;
		}
	}
	
	foreach($colors as $color){
		if(!is_array($color)){
			fputcsv($output, array($color), ',', '"');
		} else {
			fputcsv($output, $color, ',', '"');
		}
	}
	
	fclose($output);
	
} else {
	//Setup Check Failed
	print_r("parameter error "."<br/>");
	print_r("?total={number value}"."<br/>");
	print_r("optional: &kml={opacity value}"."<br/>");
	exit;
}
?>