<?php
/*
#
# vConso BAAS
#
# Copyright (c) 2017 AXIANS Cloud Builder
# Author: Jean-Philippe Levy <jean-philippe.levy@axians.com>
#
*/
?>

<!-- DataTables JavaScript -->
<script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
<script src="/bower_components/datatables-responsive/js/dataTables.responsive.js"></script>

<!-- DateRangePicker JavaScript -->
<script src="/bower_components/moment/min/moment.min.js"></script>
<script src="/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/js/daterangepicker.js"></script>

<!-- Highcharts -->
<script src="/bower_components/highcharts/highcharts.js"></script>
<script src="/bower_components/highcharts/highcharts-more.js"></script>

<!-- Dashboards -->
<script type="text/javascript">
	if($("#container_dashboard").length) {
		ajaxCharts();
		setInterval(function(){ajaxCharts();}, <?php echo $refresh_time * 1000; ?>);
	}
</script>