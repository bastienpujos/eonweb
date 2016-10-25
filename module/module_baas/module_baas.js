/*
#
# vConso BAAS
#
# Copyright (c) 2017 AXIANS Cloud Builder
# Author: Jean-Philippe Levy <jean-philippe.levy@axians.com>
#
*/

/**
 * draw the gauge chart with selected title, values, in a HTML target
 *
 * @param div_id 	-> (String) the HTML target's id
 * @param title 	-> (String) the title of the Chart
 * @param datas 	-> (Json) Chart's values in a json array, that's the Ajax response
 */
function drawGaugeChart(div_id, title, data, column_type, unit)
{	
	if(unit == '%') {
		GaugeChartData = [[title,parseFloat(data)],[100-parseFloat(data)]]
	} else {
		GaugeChartData = [[title,parseFloat(data)]]
	}
	
	$('#'+div_id).highcharts({
		chart: {
			renderTo: 'container_dashboard',
			type: 'pie',
		},
		tooltip: {
			enabled: false,
		},
		plotOptions: {
			pie: {
				slicedOffset: 0,
				dataLabels: {
					enabled: false
				}
			}
		},
		title: {
			text: (Math.round(parseFloat(data)*100)/100).toString().replace('.',dictionnary['label.module_baas.decimalpoint']) + ' ' + unit,
			align: 'center',
			verticalAlign: 'middle',
			y: 5
		},      
		credits: {
		   enabled: false
		},
		series: [{
			name: column_type,
			data: GaugeChartData,
			innerSize: '70%',
			showInLegend:false,
			dataLabels: {
				enabled: false
			},
			states:{
				hover: {
					enabled: false
				}
			}
		}]
    });
}

/**
 * draw the pie chart with selected title, values, in a HTML target
 *
 * @param div_id 	-> (String) the HTML target's id
 * @param datas 	-> (Json) Chart's values in a json array, that's the Ajax response
 */
function drawPieChart(div_id, datas)
{	
	var chart_datas = [];
	var data_object = {
		name: dictionnary['label.module_baas.success'],
		y: parseFloat(datas[0].backups_ok)
	}
	chart_datas.push(data_object);
	var data_object2 = {
		name: dictionnary['label.module_baas.failures'],
		y: parseFloat((datas[0].backups_total)-(datas[0].backups_ok))
	}
	chart_datas.push(data_object2);
	
	$('#'+div_id).highcharts({
          chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
		credits: {
			enabled: false
		},
		colors: ['limegreen', 'red'],
        tooltip: {
            pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.percentage:.2f} %</b><br/>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.y:.0f}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dictionnary['label.module_baas.result_backup'],
            colorByPoint: true,
            data: chart_datas
                
		}]
    });
}

/**
 * draw the line chart with selected title, values, in a HTML target
 *
 * @param div_id 	-> (String) the HTML target's id
 * @param datas 	-> (Json) Chart's values in a json array, that's the Ajax response
 */
function drawLineChart(div_id, datas)
{	
	var categories_object1 = [];
	var length = datas.length;
	var chart_datas = [];
	for(i = length-1; i >= 0; i--){
		var data_object = {
			y: parseFloat(datas[i].billed_volume)
		}
		chart_datas.push(data_object);
		categories_object1.push(String(datas[i].bill_date));
	} 
	
	$('#'+div_id).highcharts({
		title: {
			text: ''
		},
		credits: {
			enabled: false
		},
        xAxis: {
            categories: categories_object1
        },
        yAxis: {
            title: {
                text: ''
			},
			labels: {                
				formatter: function () {
				return this.value + ' ' + dictionnary['label.module_baas.tb'];
				}
			}
        },
		tooltip: {
			valueSuffix: ' ' + dictionnary['label.module_baas.tb']
		},
        series: [{
            name: dictionnary['label.module_baas.vol_billed'],
			data: chart_datas
        }]
    });
}

/**
 * draw the area chart with selected title, values, in a HTML target
 *
 * @param div_id 	-> (String) the HTML target's id
 * @param datas 	-> (Json) Chart's values in a json array, that's the Ajax response
 */
function drawAreaChart(div_id, datas)
{	
	var categories_object1 = [];
	var length = datas.length;
	var chart_datas4 = [];
	var chart_datas5 = [];
	
	for(i = length-1; i >= 0; i--){
		var data_object = {
			y: parseFloat(datas[i].sla_infra)
		}
		var data_object2 = {
			y: parseFloat(datas[i].rate_backup_ok_month)
		}
		chart_datas4.push(data_object);
		chart_datas5.push(data_object2);
		categories_object1.push(String(datas[i].bill_date));
	}
	
	$('#'+div_id).highcharts({
        chart: {
            type: 'area'
        },
        title: {
            text: ''
        },
        credits: {
			enabled: false
		},
        xAxis: {
            categories: categories_object1
        },
        yAxis: {
            title: {
                text: ''
            },
			min: 75,
			max: 100,
			labels: {                
				formatter: function () {
				return this.value + ' %';
				}
			}
        },
		tooltip: {
			valueSuffix: ' %'
		},
		tooltip: {
			valueSuffix: ' %'
		},
		colors: ['palevioletred', 'slateblue'],
		plotOptions: {
            column: {
                stacking: 'percent'
            }
		},
        series: [{
            name: dictionnary['label.module_baas.sla_infra'],
            data: chart_datas4
        },
		{
            name: dictionnary['label.module_baas.sla_backup'],
            data: chart_datas5
        }]
    });
}

/**
 * draw the graphs with all parameters
 *
 * @param link 	-> (String) if link in graph
 */
function ajaxCharts() {
	
	// get the number of host ordered by state (first pie chart)
	$.ajax({
		url: "/module/module_baas/index_ajax.php",
		dataType: "JSON",
		success: function(response){
			Highcharts.setOptions({
				lang: {
					decimalPoint: dictionnary['label.module_baas.decimalpoint'],
					thousandsSep: dictionnary['label.module_baas.thousandssep']
				},
				tooltip: {
					valueDecimals: 2
				}
			});
			drawGaugeChart("container_sla_infra", "SLA Infra", response[0]["sla_infra"], "sla_infra", "%");
			drawGaugeChart("container_sla_backup", "SLA Backup", response[0]["rate_backup_ok_month"], "rate_backup_ok_month", "%");
			drawGaugeChart("container_vol_billed", "Volume", response[0]["billed_volume"], "billed_volume", dictionnary['label.module_baas.tb']);
			drawPieChart("container_result_backup", response);
			drawLineChart("container_vol_graph", response);
			drawAreaChart("container_sla_graph", response);
			$("#menu-toggle").click(function(){
				$('#container_sla_infra').highcharts().reflow(); 
				$('#container_sla_backup').highcharts().reflow(); 
				$('#container_vol_billed').highcharts().reflow(); 
				$('#container_result_backup').highcharts().reflow(); 
				$('#container_vol_graph').highcharts().reflow(); 
				$('#container_sla_graph').highcharts().reflow();
			});
		},
		error: function(){}
	});
	
}

$(document).ready(function() {
	// pages with datatables
	if($(".datatable-baas").length) {
		var datatable = $('.datatable-baas').DataTable({
			responsive: true,
			lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, dictionnary['label.all']] ],
			language: {
				lengthMenu: dictionnary['action.display'] + " _MENU_ " + dictionnary['label.entries'],
				search: dictionnary['action.search']+":",
				paginate: {
					first:      dictionnary['action.first'],
					previous:   dictionnary['action.previous'],
					next:       dictionnary['action.next'],
					last:       dictionnary['action.last']
				},
				info:           dictionnary['label.datatable.info'],
				infoEmpty:      dictionnary['label.datatable.infoempty'],
				infoFiltered:   dictionnary['label.datatable.infofiltered'],
				zeroRecords:    dictionnary['label.datatable.zerorecords']
			},
			columnDefs: [ {
				"targets"  : 'no-sort',
				"orderable": false,
			}],
			aaSorting: [],
			initComplete: function( settings, json ) {
				$('div.loading').remove();
				$('.datatable-baas').show();
			}
		});
		
		if($("th.is-sort").length) {
			datatable.order([[$("th.is-sort").index(),'desc' ]]).draw();
		}
	}
});
