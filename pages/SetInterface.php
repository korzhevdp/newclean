<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<?php require_once('pages/header.php'); ?>

<div class="container first-page">
	<div class="gray-cont">
		<a href="#" class="btn icon-street-view submt" id="to_auth">Вход для граждан<i>Вы сможете сообщить о проблеме</i></a>
		<a href="/admin/" class="btn icon-lock submt only-for-big-screen">В раздел администрирования<i>Для контролирующих органов, ответственных подраздел. и т.п.</i></a>
		<!--<a href="#" class="btn submt">Электронный референдум<i>Голосования (в разработке)</i></a>-->
		<a href="https://bus.arhcity.ru/" target="_blank" class="btn submt">Автобусы Архангельска<i>отслеживание автобусов online</i></a>
		<a href="http://www.arhcity.ru/?page=2234/2" target="_blank" class="btn submt">Уборочная техника (бета)<i>движение техники online</i></a>
	</div>
	
	<script src="/plugins/chart/сhart.bundle.js"></script>
	<script src="/plugins/chart/Chart.js"></script>
	<script src="/plugins/chart/utils.js"></script>
	<style type="text/css" src="/plugins/chart/style.css"></style>
	
		<div style="width:100%; margin-bottom: 30px;">
				<canvas id="canvas" height="340"></canvas>
		</div>

<script>
<?php
		$arData1 = array();
		$arData2 = array();
		$arData3 = array();
		$arStatusStat = MSystem::statByMessagesStatus();
		$userCount = Users::userCount();
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
		echo "var userCount = ".$userCount.";";
		?>
		
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
				fullWidth: true,
				responsive: true,
				title: {
					display: true,
					text: '',//'Количество сообщений по статусам (всего '+mCount+') (только активные категории)'
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		};

		var ctx = document.getElementById('canvas').getContext('2d');
		window.myLine = new Chart(ctx, config);
		
	</script>
	
	<?php// if($_SERVER['REMOTE_ADDR']=='192.168.51.144' || $_SERVER['REMOTE_ADDR']=='188.170.83.77'): ?>
			<div class="main-policy-info">
				<a href="/policy/" target="_blank">Политика конфиденциальности</a>
			</div>
	<?php// endif; ?>
</div>

<script type="text/javascript">

	document.addEventListener('DOMContentLoaded', function() {
		userAuthByKey();
	}, false);

</script>