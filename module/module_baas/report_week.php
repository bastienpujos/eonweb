<?php
/*
#
# vConso BAAS
#
# Copyright (c) 2017 AXIANS Cloud Builder
# Author: Jean-Philippe Levy <jean-philippe.levy@axians.com>
#
*/

include("../../header.php");
include("../../side.php");
include("/srv/eyesofnetwork/vconso-baas/include/config.php");
include("/srv/eyesofnetwork/vconso-baas/include/function.php");

$locale=trim($GLOBALS['langformat']."_".strtoupper($GLOBALS['langformat']));
setlocale(LC_TIME, $locale);
$display_date_from = strftime("%x",strtotime("-7 days"));
$display_date_to = strftime("%x",strtotime("-1 days"));

$date_from = date('y-m-d',strtotime("-7 days"));
$date_to = date('y-m-d',strtotime("-0 days"));
$date = array();
for($i=7; $i>0; $i--){
	$date[]=date('Y m d',strtotime("-".$i." days"));
}
?>

<link rel="stylesheet" type="text/css" href="css/style.css">

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("menu.link.report_week"); ?></h1>
		</div>
	</div>
	
	<?php

	$customer_name = array();
	$domain_name = array();

	// on test si le fichier XML des filtres existe, si oui on le charge, sinon on s'arrete la
	$filepath = "../../cache/".$_COOKIE["user_name"]."-ged.xml";
	if( file_exists($filepath) ){
		$xml = openXml($filepath);

		// vérifie s'il y a un filtre d'activé, sinon c'est terminé
		$default = $xml->default;
		if($default == ''){
			finishHere("message.ged_filter_not_found");
		}

		// on a un filtre utilisé, donc on peut définir le $customer_name
		$customer_name = setCustomerName($xml, $default);
		$domain_name = setDomainName($xml, $default);
	} else {
		finishHere("message.ged_filter_not_found");
	}

	if (count($customer_name) == 0) {
		finishHere("message.ged_filter_not_found");
	}
	
	// filtre sur plusieurs customer_name
	$nbr_of_customer = count($customer_name);

	$clause = "";
	if($nbr_of_customer > 0){
		$clause2 = "AND (";
		for ($i=0; $i < $nbr_of_customer; $i++) { 
			if($i < 1){
				$clause2 .= "customer_name = '".$customer_name[$i]."'";
			} else {
				$clause2 .= " OR customer_name = '".$customer_name[$i]."'";
			}
		}
		$clause2 .= ")";
	}
	
	$clause = "AND client_os NOT LIKE :like1
			AND client_os NOT LIKE :like2
			AND client_os NOT LIKE :like3
			AND client_os NOT LIKE :like4
			AND client_os NOT LIKE :like5";
			
	$clause_status_code = "AND status_code NOT IN (30000, 30005)";
	
	// se connecter a la BDD
	$db = getConnexion($db_host, $db_user, $db_pass, $db_name);
	$sql="SELECT client_name, plugin_name, group_concat(status_code_summary) ,client_os, group_concat(status_code) as status_code, group_concat(DISTINCT concat_ws(':',DATE_FORMAT(started_ts, '%Y %m %d'),status_code )) as started, bytes_scanned, customer_name, domain_name FROM activities_tmp WHERE ".getFilters()." AND started_ts <= '".$date_to."' AND started_ts >= '".$date_from."' ".$clause." group by DATEDIFF('".$date_to."','".$date_from."'), client_name, plugin_name, domain_name, customer_name HAVING group_concat(status_code_summary) NOT LIKE 'Activity completed%,Activity completed%,Activity completed%,Activity completed%,Activity completed%,Activity completed%,Activity completed%'";
	$stmt = $db->prepare($sql);
	$stmt->execute(
		array(
			':like1'  => 'Windows XP%',
			':like2'  => 'Windows Vista%',
			':like3'  => 'Windows 7%',
			':like4'  => 'Windows 8%',
			':like5'  => 'Windows 10%'
		)
	);
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
//-------------------------------------------------------------------
//--------- Connection pour les pc ----------------------------------	
	$clause = "AND (client_os LIKE :like1
			OR client_os LIKE :like2
			OR client_os LIKE :like3
			OR client_os LIKE :like4
			OR client_os LIKE :like5)";
	
	// se connecter a la BDD
	$db = getConnexion($db_host, $db_user, $db_pass, $db_name);
	$sql2="SELECT client_name, plugin_name, group_concat(status_code_summary) ,client_os, group_concat(status_code) as status_code, group_concat(DISTINCT concat_ws(':',DATE_FORMAT(started_ts, '%Y %m %d'),status_code )) as started, bytes_scanned, customer_name, domain_name FROM activities_tmp WHERE ".getFilters()." AND started_ts <= '".$date_to."' AND started_ts >= '".$date_from."' ".$clause." group by DATEDIFF('".$date_to."','".$date_from."'), client_name, plugin_name, domain_name, customer_name HAVING group_concat(status_code_summary) NOT LIKE 'Activity completed%,Activity completed%,Activity completed%,Activity completed%,Activity completed%,Activity completed%,Activity completed%'";
	$stmt2 = $db->prepare($sql2);
	$stmt2->execute(
		array(
			':like1'  => 'Windows XP%',
			':like2'  => 'Windows Vista%',
			':like3'  => 'Windows 7%',
			':like4'  => 'Windows 8%',
			':like5'  => 'Windows 10%'
		)
	);
	$results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
	//var_dump($results);
	?>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo getLabel('label.module_baas.date_serverError_from')." $display_date_from ".getLabel('label.module_baas.date_to')." $display_date_to"; ?>
		</div>
				
		<div class="panel-body">
		
			<div class="loading">
				<img src="/images/loader.gif" alt="loading">
			</div>
		
			<div class="dataTable_wrapper">
				<table class="table table-condensed table-bordered datatable-baas">
					<thead>
						<?php createTableHeader(); ?>
					</thead>
					<tbody>
						<?php
						foreach ($results as $result) {
							$started=array();
							$started_ts=array();
							$started_status=array();
							
							$started=explode(',', $result["started"]);    //séparation des dates et des statut
							foreach($started as $start){
								$started_ts[]=explode(':', $start);
							}

							for($i=0; $i<7; $i=$i+1){ //boucle de traitement si une date n'existe pas
								for($j=0; $j<7; $j=$j+1){
									if($date[$i]==$started_ts[$j][0]){ // boucle de vérification de la présence d'une date
										$started_status[$started_ts[$j][0]]=$started_ts[$j][1];
									}
								}
								if(!array_key_exists($date[$i], $started_status)){ //création d'une date inexistante et fixe le statut à null
									$started_status[$date[$i]]=null;
								}
							}
							
							for($i=0; $i<7; $i++){		// boucle de stockage des résultats dans $activities pour créer le tableau
								$activities[$i]=$result;
								$activities[$i]["status_code"]=$started_status[$date[$i]];
								$activities[$i]["started"]=$date[$i];
							}
							$tmp_good_bkp = 7;
							$last_good_bkp = 0;
							
							createTableRow($result, $activities, "server");
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<?php echo getLabel('label.module_baas.date_pcError_from')." $display_date_from ".getLabel('label.module_baas.date_to')." $display_date_to"; ?>
		</div>
		
		<div class="panel-body">
		
			<div class="loading">
				<img src="/images/loader.gif" alt="loading">
			</div>
		
			<div class="dataTable_wrapper">
				<table class="table table-condensed table-bordered datatable-baas">
					<thead>
						<?php createTableHeader(); ?>
					</thead>
					<tbody>
						<?php
						foreach ($results2 as $result) {
							$started=array();
							$started_ts=array();
							$started_status=array();
							
							$started=explode(',', $result["started"]);     //séparation des dates et des statut
							foreach($started as $start){
								$started_ts[]=explode(':', $start);
							}

							for($i=0; $i<7; $i=$i+1){  			//boucle de traitement si une date n'existe pas
								for($j=0; $j<7; $j=$j+1){
									if($date[$i]==$started_ts[$j][0]){				// boucle de vérification de la présence d'une date
										$started_status[$started_ts[$j][0]]=$started_ts[$j][1];
									}
								}
								if(!array_key_exists($date[$i], $started_status)){		//création d'une date inexistante et fixe le statut à null
									$started_status[$date[$i]]=null;
								}
							}
							
							for($i=0; $i<7; $i++){								// boucle de stockage des résultats dans $activities pour créer le tableau
								$activities[$i]=$result;
								$activities[$i]["status_code"]=$started_status[$date[$i]];
								$activities[$i]["started"]=$date[$i];
							}
							$tmp_good_bkp = 7;
							$last_good_bkp = 0;

							createTableRow($result, $activities, "pc"); 
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Legende des couleurs -->
	<div class="panel panel-info">
		<div class="panel-heading">
			<i class="fa fa-info-circle"> </i> <?php echo getLabel('label.legends'); ?>
		</div>
		<div class="panel-body">
			<table class="table table-condensed table-bordered">
				<tbody>
					<tr>
						<td class="col-sm-1 bg-green"></td>
						<td><?php echo getLabel('label.code_green'); ?></td>
					</tr>
					<tr>
						<td class="col-sm-1 bg-yellow"></td>
						<td> <?php echo getLabel('label.code_yellow'); ?></td>
					</tr>
					<tr>
						<td class="col-sm-1 bg-orange"></td>
						<td><?php echo getLabel('label.code_orange'); ?>
							<br><?php echo getLabel('label.code_orange2'); ?>
						</td>
					</tr>
					<tr>
						<td class="col-sm-1 bg-red"></td>
						<td><?php echo getLabel('label.code_red'); ?></td>
					</tr>
					<tr>
						<td class="col-sm-1 bg-black"></td>
						<td><?php echo getLabel('label.code_black'); ?></td>
					</tr>
					<tr>
						<td class="col-sm-1"></td>
						<td><?php echo getLabel('label.code_white'); ?></td>
					</tr>
				</tbody>
			</table>

			<p>
				<?php echo getLabel('label.star1'); ?>
			</p>
			<p>
				<?php echo getLabel('label.star2'); ?>
			</p>
		</div>
	</div>
</div>

<?php include("../../footer.php"); ?>
