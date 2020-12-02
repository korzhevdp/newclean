<?=$header?>
<link rel="stylesheet" type="text/css" href="/styles/leaflet.css">




<script src="/scripts/leaflet.js"></script>
<div class="messageInfoCard">
	<h2>Информация по обращению №<?=$messageID?></h2>
	Категория обращения: <?=$categoryName?><br>
	Время обработки обращения: <?=$deadline?> дней<br>
	Дата обращения: <?=$createTime?><br>
	Категория обращения: <?=$categoryName?><br>
	Территориальный округ: <?=$districtName;?><br>
	Ответственная организация: <?=$organizationName;?><br>
	На контроле у : <?=$departmentName;?><br><br>
	
	<?=$message;?><hr>
	<?=$files;?>

	<div id="LMap" style="height:200px;"></div><br><br>

	Статус: <?=$statusName?><br>
	Статус изменился: <?=$updateTime?><br><br>

	<?=($archive) ? "Архивное" : "В работе";?>
</div>
<a href="/appeal/dashboard" class="bl-link icon-bars">Вернуться к моим обращениям</a>
<script>
	var map,
		coords = <?=$coords;?>;
	function mapInit(){
		map = L.map('LMap', {dragging: false, scrollWheelZoom: false, zoomControl: false }).setView(coords, 12);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Участники OpenStreetMap</a>, &copy; <a title="Администрация МО &quot;Город Архангельск&quot;" href="http://www.arhcity.ru">www.arhcity.ru</a>'
		}).addTo(map);
		L.marker(coords, {}, {}).addTo(map);
	}
	mapInit();
</script>