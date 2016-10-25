<?php
/*
#
# vConso BAAS
#
# Copyright (c) 2017 AXIANS Cloud Builder
# Author: Jean-Philippe Levy <jean-philippe.levy@axians.com>
#
*/

include("../../include/config.php");
include("../../include/function.php");
include("/srv/eyesofnetwork/vconso-baas/include/config.php");
include("/srv/eyesofnetwork/vconso-baas/include/function.php");

if( isset($_POST["file"]) && $_POST["file"] != '' ){
	getLabel("label.product.name");
	$file_parts = explode("/", $_POST["file"]);
	$customer_name = $file_parts[0];
	
	// date format
	$tmp_date = $file_parts[1];
	$tmp_date_parts = explode("-", $tmp_date);
	$billing_date = $tmp_date_parts[0].$tmp_date_parts[1];

	// report file path
	$file_folder = $vconso_report_path."/".$customer_name;
	$file_name = $billing_date."_".$customer_name."_Billing_".$GLOBALS['langformat'].".pdf";
	$file = $file_folder."/".$file_name;
	
	// create report if not exists
	if( !file_exists($file) ){
		// create customer folder if not exists
		if(!file_exists($file_folder)) { mkdir($file_folder,0777,true); }
		
		// define billing report url
		$report_url = $vconso_report_url."__report=".$vconso_report_billing;
		$report_url.="&__format=pdf";
		$report_url.="&__asattachment=true";
		$report_url.="&__locale=".$GLOBALS['langformat'];
		$report_url.="&year=".$tmp_date_parts[0];
		$report_url.="&month=".$tmp_date_parts[1];
		$report_url.="&Customer=".$customer_name;
		
		// download report
		$ch = curl_init($report_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);
		file_put_contents($file, $data);	
	}
	
	// download report if exists
	if( file_exists($file) ){
		// téléchargement du fichier
		header('Content-Description: File Transfer');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	} else {
		//header("Location: report_month.php?file=".urlencode($file_name));
	}
} else {
	//header("Location: report_month.php");
}
