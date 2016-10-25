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

// default filter collapse
$collapse="out";

// create WHERE clause according to parameters
$where_clause = "";
$where_prepare=array();

if(isset($_POST["form"])) {

	$collapse="in";
	extract($_POST);
	
	if($job != ""){ $where_clause .= " AND plugin_name LIKE ?"; $where_prepare[]="%$job%"; }
	if($client != ""){ $where_clause .= " AND client_name LIKE ?"; $where_prepare[]="%$client%"; }
	if($status_code != ""){ $where_clause .= " AND status_code LIKE ?"; $where_prepare[]="%$status_code%"; }
	if($domain_name != ""){ $where_clause .= " AND domain_name LIKE ?"; $where_prepare[]="%$domain_name%"; }
	if($os != ""){ $where_clause .= " AND client_os LIKE ?"; $where_prepare[]="%$os%"; }
		
	// period clause
	if($date != ""){
		$times = explode(" - ", $date);
		$start = date('Y-m-d',strtotime($times[0]));
		$end = date('Y-m-d',strtotime($times[1]));
		$where_clause .= " AND started_ts >= ? AND started_ts < ?"; 
		$where_prepare[]=$start;
		$where_prepare[]=$end;
	}
}

// sql request
$db = getConnexion($db_host, $db_user, $db_pass, $db_name);

$sql = "SELECT *
	FROM $table_activities_tmp
	WHERE ".getFilters().
	$where_clause.
	" ORDER BY started_ts DESC LIMIT $sql_limit";

$stmt = $db->prepare($sql);
$stmt->execute($where_prepare);
$results = $stmt->fetchAll(PDO::FETCH_OBJ);

// conversion en GO
$convertion = 1024*1024*1024;

?>

<link rel="stylesheet" type="text/css" href="css/style.css">

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("menu.link.report_history"); ?></h1>
		</div>
	</div>

	<?php message("", getLabel("label.module_baas.search_limit")." ".$sql_limit." ".getLabel("label.entries"), "");	?>
	
	<div class="panel panel-default">
		<div class="panel-heading" id="headingOne">
			<h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					<i class="fa fa-filter fa-fw"></i></a>
				<?php echo getLabel("label.ged_sorter"); ?>
			</h4>
		</div>
		<div id="collapseOne" class="panel-collapse collapse <?php echo $collapse; ?>" role="tabpanel" aria-labelledby="headingOne">
			<div class="panel-body">
				<form id="logs-form" name="logs-form" method="post">
					<div class="row">
						<div class="form-group col-md-4">
							<label><?php echo getLabel('label.period'); ?></label>
							<div class="input-group">
								<input type="text" class="daterangepicker-eonweb form-control" name="date" value="<?php echo getPostField("date"); ?>">
								<span class="input-group-btn">
									<button name="form" class="btn btn-primary"><?php echo getLabel("action.search"); ?></button>
								</span>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label><?php echo getLabel('label.client'); ?></label>
							<input type="text" class="form-control" name="client" value="<?php echo getPostField("client"); ?>">
						</div>
						<div class="form-group col-md-4">
							<label><?php echo getLabel('label.job'); ?></label>
							<input type="text" id="job" name="job" class="form-control" value="<?php echo getPostField("job"); ?>">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-md-4">
							<label><?php echo getLabel('label.status_code'); ?></label>
							<input type="text" class="form-control" name="status_code" value="<?php echo getPostField("status_code"); ?>">
						</div>
						<div class="form-group col-md-4">
							<label><?php echo getLabel('label.domain_name'); ?></label>
							<input type="text" class="form-control" name="domain_name" value="<?php echo getPostField("domain_name"); ?>">
						</div>
						<div class="form-group col-md-4">
							<label><?php echo getLabel('label.os'); ?></label>
							<input type="text" class="form-control" name="os" value="<?php echo getPostField("os"); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<br>
	
	<!-- Loading -->
	<div class="loading">
		<img src="/images/loader.gif" alt="loading">
	</div>
	
	<!-- Result here ! -->
	<div class="dataTable_wrapper">
		<table class="table table-condensed table-striped table-bordered table-hover datatable-baas">
			<thead>
				<tr>
					<th><?php echo getLabel('label.client'); ?></th>
					<th><?php echo getLabel('label.job'); ?></th>
					<th><?php echo getLabel('label.begin'); ?></th>
					<th><?php echo getLabel('label.end'); ?></th>
					<th><?php echo getLabel('label.status_code'); ?></th>
					<th><?php echo getLabel('label.status_code_summary'); ?></th>
					<th><?php echo getLabel('label.domain_name'); ?></th>
					<th><?php echo getLabel('label.group'); ?></th>
					<th><?php echo getLabel('label.os'); ?></th>
					<th><?php echo getLabel('label.byte_scanned'); ?></th>
					<th><?php echo getLabel('label.byte_transfered'); ?></th>
					<th><?php echo getLabel('label.files'); ?></th>
					<th><?php echo getLabel('label.duration'); ?></th>
					<th><?php echo getLabel('label.err_code'); ?></th>
					<th><?php echo getLabel('label.err_code_summary'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($results as $result) { ?>
					<tr>
						<td><?php echo $result->client_name ?></td>
						<td><?php echo $result->plugin_name ?></td>
						<td><?php echo $result->started_ts ?></td>
						<td><?php echo $result->completed_ts ?></td>
						<td class="<?php echo getCodeClass($result->status_code); ?>"><?php echo $result->status_code ?></td>
						<td><?php echo $result->status_code_summary ?></td>
						<td><?php echo $result->domain_name ?></td>
						<td><?php echo $result->group_name ?></td>
						<td><?php echo $result->client_os ?></td>
						<td><?php echo round( ($result->bytes_scanned/$convertion), 2 ) ?></td>
						<td><?php echo round( ($result->bytes_modified_sent/$convertion), 2 ) ?></td>
						<td><?php echo $result->num_of_files ?></td>
						<td><?php echo $result->duration ?></td>
						<td><?php echo $result->error_code ?></td>
						<td><?php echo $result->error_code_summary ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	
</div>

<?php include("../../footer.php"); ?>
