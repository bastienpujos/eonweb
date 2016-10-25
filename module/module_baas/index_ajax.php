<?php
/*
#
# vConso BAAS
#
# Copyright (c) 2017 AXIANS Cloud Builder
# Author: Jean-Philippe Levy <jean-philippe.levy@axians.com>
#
*/

include("/srv/eyesofnetwork/vconso-baas/include/config.php");
include("/srv/eyesofnetwork/vconso-baas/include/function.php");

// sql request
$db = getConnexion($db_host, $db_user, $db_pass, $db_name);

$sql = "select a.*,b.sla_infra
from(
	(
		select bill_date,
		sum(backups_ok)/sum(backups_total)*100 as rate_backup_ok_month,
		sum(backups_total) as backups_total,
		sum(backups_ok) as backups_ok,
		sum(billed_volume)/1024/1024/1024/1024 as billed_volume,
		sum(billed_volume_pc)/1024/1024/1024/1024 as billed_volume_pc,
		sum(billed_volume_server)/1024/1024/1024/1024 as billed_volume_server
		from billings
		where ".getFilters()."
		group by bill_date
	)a
	left join
	(
		select *
		from availabilities 
	)b on a.bill_date = b.bill_date
)
where a.bill_date >= curdate() - INTERVAL 1 YEAR
and a.bill_date <= curdate()
order by bill_date desc";

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_OBJ);

header('Content-Type: application/json');
echo json_encode($results,JSON_PRETTY_PRINT);

// Close MySQL connection
$db = null;

?>
