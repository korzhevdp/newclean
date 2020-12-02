
<div class="header-panel sticky">
	<a href="<?=base_url();?>/appeal/dashboard" class="icon-left-open-big slide-back">Назад</a>
	<div>Карта всех обращений</div>
</div>

<link rel="stylesheet" type="text/css" href="/styles/leaflet.css">

<div id="LMap" style="height:600px;">

<script src="/scripts/leaflet.js"></script>
<script>
	var map,
		coords = [<?=$coords;?>];
	function mapInit(){
		map = L.map('LMap', {}).setView([64.55,40.56], 12);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Участники OpenStreetMap</a>, &copy; <a title="Администрация МО &quot;Город Архангельск&quot;" href="http://www.arhcity.ru">www.arhcity.ru</a>'
		}).addTo(map);
		//L.marker(coords, {}, {}).addTo(map);
		for (a in coords ) {
			//console.log(coords[a].coords)
			L.marker(coords[a].coords).addTo(map);
		}
	}

	mapInit();
</script>