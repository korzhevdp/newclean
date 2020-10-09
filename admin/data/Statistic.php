<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<div class="pd-panel-cont active">
<?php

if(!$law6)
{
	exit('<div class="pg10px">Общая статистика сообщений для вас недоступна. Обратитесь к администратору системы.</div>');
}

$arStat = array();
$arDistr = array();
$arStatus = array();
$arCat = array();

if(!($law6))
	exit('<div class="line-caption">В модуле статистики ведутся работы по оптимизации и корректировке алгоритмов анализа статистических показателей. Информация будет доступна позже.</div>');


if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{    
  include("ajaxStatistics.php");  
}

$arDepart = MSystem::GetUserListByOrgId(586);
//print_r($arDepart);

$arStat = MSystem::statByMounthAddActive();
$userCount = Users::userCount();
$arData1 = array();
$arData2 = array();
$arData3 = array();
foreach($arStat as $key => $value)
{
	$arData1[] = $key;
	$arData2[] = $value['messages'];
	$arData3[] = $value['users'];
}

?>
	<script src="/plugins/xlsx.full.min.js"></script>
	<script src="/plugins/chart/Chart.js"></script>
	<script src="/plugins/chart/utils.js"></script>
	<script src="/plugins/chart/utils.js"></script>
	<style type="text/css" src="/plugins/chart/style.css"></style>

	<div style="width:100%; padding: 10px;">
		<div id="report">
			<span toglle="off" onclick="toglle(this);">Сохранить отчет в exel</span>
			<div class="inner"  style="display:none;">
				
			</div>

		</div>
		<canvas id="canvas"></canvas>
		<div class="separ"></div>
		<canvas id="canvas1"></canvas>
		<div class="separ"></div>
		<canvas id="canvas2" style="display: none;"></canvas>
	</div>
	
			
		<?
		
		$arDistrStat = MSystem::statDistrictActivity();
		//$actualCategories = MSystem::getActualCategories();
		//$actualStatusMessages = MSystem::getActualStatusMessages();
	
		$allStat = MSystem::getAllStatisticsByCategories();
		  //echo "<pre>";print_r($t);echo "</pre>";
		?>
		
	
	<script>
		function getTable(reg, cat, status,statistics)
		{
			let col_reg = Object.keys(reg).length;
			let col_cat = Object.keys(cat).length;
			let col_stat = Object.keys(status).length;
			var stat_obj = new Object();
			var cat_obj = new Object();

			let table = "";
			let body = "<tbody>";
			let header = 
				"<table width='100%' style='border: solid 2px gray;' id='data-table'>" +
				"<thead><tr>" +
		     	"<td align='center' rowspan='3'><b>Округа</b></td>" +
		        "<td align='center' colspan='" + (col_cat*col_stat) + "'><b>Категории<b></td>" +
		        "<td align='center' rowspan='2' colspan='"+ col_stat +"'><b>Всего</b></td></tr>";

		    
		    header += "<tr>";
		    for(let key in cat)
			{
				header += "<td align='center' colspan='3'><b>"+ cat[key].replace(/\s/g, "</br>")+"</b></td>";
				cat_obj[key] = {};
			}
			cat_obj["total"] = {};
			
			header += "</tr>";
			header += "<tr>";
			
			let str_stat = "";			
			for(let key in status)
			{
				str_stat += "<td align='center'><i>"+status[key].replace(/\s/g, "</br>")+"</i></td>";
			}

		    for(let i=0; i<col_cat; i++)
			{
				header += str_stat;
			}

			for(let key in status)
			{
				header += "<td align='center'>Всего <br>"+status[key].replace(/\s/g, "</br>")+"</td>";
			}

			str_body = "";


			for(let key in statistics)
			{
				//var stat_obj = new Object();
				let summ = 0;
				str_body +="<tr>";
				for(let k in statistics[key])
				{
					
					if(k == "dist") str_body += "<td align='center'><b>"+reg[statistics[key][k]].replace(/\s/g, "</br>")+"</b></td>";
					else 
						{
						
							let stat = parseInt(k.replace("category_", "").split("_")[1]);
							let cat = parseInt(k.replace("category_", "").split("_")[0]);

							if(cat_obj[cat][stat]===undefined) cat_obj[cat][stat] = 0;
							if(stat_obj[stat]===undefined) stat_obj[stat] = 0;
							if(cat_obj["total"][stat]===undefined) cat_obj["total"][stat] = 0;

							if(statistics[key][k] != null) 
								{
									str_body += "<td class='td_"+stat+"' align='center'>"+statistics[key][k]+"</td>";
									stat_obj[stat] += Number(statistics[key][k]);
									cat_obj[cat][stat] +=  Number(statistics[key][k]);
									cat_obj["total"][stat] +=  Number(statistics[key][k]);
								}
							else 
								{
									str_body += "<td class='td_"+stat+"' align='center'>0</td>";
								}
						}
				}
				//console.log(cat_obj);
				for(st in stat_obj)
				{
					str_body +="<td align='center' class='td_"+ st+" all_stat_"+ st+"'>"+ stat_obj[st] +"</td>";
					stat_obj[st] = 0;
				}
				
				str_body +="</tr>";

				body += str_body;
				str_body ="";
			}
			
			str_body +="<tr><td align='center'>Всего по  <br>категориям</td>";
			for(ct in cat_obj)
			{
				for(st in cat_obj[ct])
				{
					str_body +="<td align='center' class='td_"+ st+" all_cat_"+ ct+"_"+st+"'>"+ cat_obj[ct][st] +"</td>";
					
				}
				
			}

			str_body +="</tr>";

			body += str_body;

			header += "</tr></thead>";
			body += "</tbody>";
			table += header;
			table += body;
			table += "</table>";
			return  table;
		}
		function toglle(state)
		{
			
			let attr = state.getAttribute("toglle");
			let inner = document.querySelector("#report .inner");
			
			if(attr == "off") {inner.style.display = "block"; state.setAttribute("toglle", "on");}
			else 
				{
					inner.style.display = "none";
					state.setAttribute("toglle", "off");
				}
		}

		function createFilter(stat){

			let region = stat["reg"];
			let statuses = stat["statuses"];
			let categories = stat["categories"];
			let statistics = stat["stat"];
			let selectCategories  ="<select id='categories' multiple style='display:inline-block;width:30%; margin-right: 10px;'>";
			

			for(let key in categories)
			{
				selectCategories += "<option selected value='"+ key+"'>"+ categories[key]+"</option>"
				
			}
			selectCategories +="</select>";
			let selectRegions  ="<select id='regions' multiple style='display:inline-block;width:30%; margin-right: 10px;'> ";
			
			for(let key in region)
			{
				selectRegions += "<option selected value='"+ key+"'>"+ region[key]+"</option>"
			
			}
			selectRegions +="</select>";
			var inner = document.querySelector(".inner");

			inner.innerHTML += "<form action='' method='POST'>" + selectRegions;
			inner.innerHTML += selectCategories;
			inner.innerHTML += getTable(region, categories, statuses,statistics)+ "</form>";

			inner.innerHTML += "<div><p id='xportxlsx' class='xport'>" +
			"<input type='submit' value='Export to XLSX!' onclick=" + "doit('xlsx');" +">" +
			"</p></div>";
			//console.log(categories);

		}
		function doit(type, fn, dl) {
			var elt = document.getElementById('data-table');
			var wb = XLSX.utils.table_to_book(elt, {sheet:"Sheet JS"});
			return dl ?
				XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'}) :
				XLSX.writeFile(wb, fn || ('SheetJSTableExport.' + (type || 'xlsx')));
		}
		<?php
		echo "var data1 = ".json_encode($arData1).";";
		echo "var data2 = ".json_encode($arData2).";";
		echo "var data3 = ".json_encode($arData3).";";
		echo "var userCount = ".$userCount.";";
		echo "var districtsStat = ".json_encode($arDistrStat).";";
		//echo "var actualCategories = ".json_encode($actualCategories).";";
		//echo "var actualStatusMessages = ".json_encode($actualStatusMessages).";";
		echo "var allStat = ".json_encode($allStat).";";
		?>
		
		createFilter(allStat);
		Chart.pluginService.register({
		beforeDraw: function (chart) {
			if (chart.config.options.elements.center) {
        //Get ctx from string
        var ctx = chart.chart.ctx;
        
				//Get options from the center object in options
        var centerConfig = chart.config.options.elements.center;
      	var fontStyle = centerConfig.fontStyle || 'Arial';
				var txt = centerConfig.text;
        var color = centerConfig.color || '#000';
        var sidePadding = centerConfig.sidePadding || 20;
        var sidePaddingCalculated = (sidePadding/100) * (chart.innerRadius * 2)
        //Start with a base font of 30px
        ctx.font = "30px " + fontStyle;
        
				//Get the width of the string and also the width of the element minus 10 to give it 5px side padding
        var stringWidth = ctx.measureText(txt).width;
        var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

        // Find out how much the font can grow in width.
        var widthRatio = elementWidth / stringWidth;
        var newFontSize = Math.floor(30 * widthRatio);
        var elementHeight = (chart.innerRadius * 2);

        // Pick a new font size so it will not be larger than the height of label.
        var fontSizeToUse = Math.min(newFontSize, elementHeight);

				//Set font settings to draw it correctly.
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
        var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
        ctx.font = fontSizeToUse+"px " + fontStyle;
        ctx.fillStyle = color;
        
        //Draw text in center
        ctx.fillText(txt, centerX, centerY);
			}
		}
	});
		var config = {
			type: 'line',
			data: {
				labels: data1,
				datasets: [{
					label: 'Добавлено сообщений', // наименование набора данных
					backgroundColor: '#15cc5e',
					borderColor: '#15cc5e',
					data: data2,
					fill: false,
				},
				{
					label: 'Зарегистрировалось пользователей', // наименование набора данных
					backgroundColor: '#247ad2',
					borderColor: '#247ad2',
					data: data3,
					fill: false,
				}]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Активность появления новых сообщений по месяцам в соотношении с кол-вом зарегистрированных пользователей'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Месяца с момента запуска системы' //Подпись горизонтальной оси
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Количество' //Подпись горизонтальной оси
						}
					}]
				}
			}
		};
		
		<?php
		$arData1 = array();
		$arData2 = array();
		$arData3 = array();
		$arStatusStat = MSystem::statByMessagesStatus();
		$messageCount = 0;
		foreach($arStatusStat as $key => $value)
		{
			$arData1[] = $key;
			$arData2[] = $value['count'];
			$arData3[] = $value['color'];
			$messageCount+=$value['count'];
		}
		echo "var data1 = ".json_encode($arData1).";";
		echo "var data2 = ".json_encode($arData2).";";
		echo "var data3 = ".json_encode($arData3).";";
		echo "var mCount = ".$messageCount.";";
		?>
		

		
		var config1 = {
			type: 'doughnut',
			data: {
				labels: data1,
				datasets: [{
					label: 'Зарегистрировалось пользователей', // наименование набора данных
					data: data2,
					backgroundColor: data3,
				}]
			},
			options: {
				elements: {
					center: {
						text: 'Пользователей '+userCount,
						color: '#000', // Default is #000000
						fontStyle: 'Arial', // Default is Arial
						sidePadding: 20 // Defualt is 20 (as a percentage)
					}
				},
				responsive: true,
				title: {
					display: true,
					text: 'Количество сообщений по статусам (всего '+mCount+') (только активные категории)'
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		};
		
		console.log(districtsStat);
		
		
		var distrNameList =  [];
		$.each(districtsStat, function(i, val) {
		  distrNameList.push(val.short_name);
		});
		console.log(distrNameList);
		
		var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [{
				label: 'Dataset 1',
				backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
				borderColor: window.chartColors.red,
				borderWidth: 1,
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}, {
				label: 'Dataset 2',
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				borderWidth: 1,
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}]

		};
		
		var config2 = {
			type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Показатели по округам'
					}
				}
		};
		
		
		
		//window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myLine = new Chart(ctx, config);
			var ctx1 = document.getElementById('canvas1').getContext('2d');
			window.myLine1 = new Chart(ctx1, config1);
			
			var ctx2 = document.getElementById('canvas2').getContext('2d');
			window.myLine2 = new Chart(ctx2, config2);
		//};

	</script>

</div>