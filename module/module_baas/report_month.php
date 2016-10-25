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
			<h1 class="page-header"><?php echo getLabel('menu.link.report_month'); ?></h1>
		</div>
	</div>

	<?php
		// file not found
		if(isset($_GET["file"])) { message(0, "File not found : ".$_GET["file"], "critical"); }

		// récupération des fichiers biling en BDD
		$db = getConnexion($db_host, $db_user, $db_pass, $db_name);
		$sql = "SELECT *
				FROM billings
				WHERE ".getFilters().
				" ORDER BY bill_date DESC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$files = $stmt->fetchAll(PDO::FETCH_OBJ);
	?>

	<form class="form-inline" action="download.php" method="POST">
		<div class="form-group">
			<?php echo getLabel('label.download_billing'); ?>
			<select class="form-control" name="file" size=1>
				<?php
				foreach ($files as $file) {
					$selected="";
					if(isset($_GET["file"])) {
						if($_GET["file"] == $file->customer_name."/".$file->bill_date) {
							$selected=" selected";
						}
					}
					echo "<option value='".$file->customer_name."/".$file->bill_date."'".$selected.">".$file->customer_name." ".$file->bill_date."</option>";
				}
				?>
			</select>
		</div>
		<button class="btn btn-primary" type="submit" name="action" value="submit"><?php echo getLabel("action.download"); ?></button>
	</form>

</div>

<?php include('../../footer.php'); ?>
