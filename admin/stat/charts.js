/*
Name: 			UI Elements / Charts - Examples
Written by: 	Okler Themes - (http://www.okler.net)
Theme Version: 	1.4.1
*/

(function( $ ) {

	'use strict';
	/*
	Morris: Line
	*/
	Morris.Line({
		resize: true,
		element: 'morrisLine',
		data: morrisLineData,
		xkey: 'y',
		ykeys: ['a', 'b'],
		labels: ['Зарегистрировано пользователей', 'Добавлено сообщений'],
		hideHover: true,
		lineColors: ['#0088cc', '#734ba9'],
	});
	/*
	Flot: Pie
	*/

		var plot = $.plot('#flotPie', flotPieData, {
			series: {
				pie: {
					show: true,
					combine: {
						color: '#999',
						threshold: 0.1
					}
				}
			},
			legend: {
				show: true
			},
			grid: {
				hoverable: true,
				clickable: true
			}
		});
	
	

}).apply( this, [ jQuery ]);