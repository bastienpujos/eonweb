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
?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel('menu.link.dashboard'); ?></h1>
		</div>
	</div>
	
	<div id="container_dashboard">
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-pie-chart fa-fw"></i>
						<?php echo getLabel("label.module_baas.sla_infra"); ?>
					</div>
					<div class="panel-body">
						<div id="container_sla_infra"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-pie-chart fa-fw"></i>
						<?php echo getLabel("label.module_baas.sla_backup"); ?>
					</div>
					<div class="panel-body">
						<div id="container_sla_backup"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-pie-chart fa-fw"></i>
						<?php echo getLabel("label.module_baas.vol_billed"); ?>
					</div>
					<div class="panel-body">
						<div id="container_vol_billed"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-pie-chart fa-fw"></i>
						<?php echo getLabel("label.module_baas.result_backup"); ?>
					</div>
					<div class="panel-body">
						<div id="container_result_backup"></div>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-line-chart fa-fw"></i>
						<?php echo getLabel("label.module_baas.vol_graph"); ?>
					</div>
					<div class="panel-body">
						<div id="container_vol_graph"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-area-chart fa-fw"></i>
						<?php echo getLabel("label.module_baas.sla_graph"); ?>
					</div>
					<div class="panel-body">
						<div id="container_sla_graph"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>

<?php include('../../footer.php'); ?>
