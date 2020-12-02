<div class="header-panel">
	<a href="<?=base_url();?>appeal/category" class="icon-left-open-big slide-back">Назад</a>
	<div>Местоположение <?=$categoryName;?></div>
</div>

<p class="block little-text" id="locCaption"><?=$caption;?></p>
<link rel="stylesheet" type="text/css" href="/styles/leaflet.css">

<div id="LMap"></div>

<div class="container map-btn">
	<a href="#" class="btn" id="postCoords">Далее</a>
</div>


<script src="/scripts/leaflet.js"></script>
<script>
	$("#LMap").height($(window).height() - 210 + 'px');


	var coords    = { <?=$coords;?> },
		allowPost = false,
		address   = "",
		district  = 0,
		map,
		responsible,
		layers,
		dCoordinates = {<?=$dCoordinates;?>};

	if ($(location).attr('href') != "<?=$link;?>") {
		window.history.pushState("", "Чистый город - локация", "<?=$link;?>");
	}

	function isMarkerInsideLPolygon(marker, poly) {
		// честно позаимствовано в интернете. Чистый лифлет.
		var polyPoints = poly.getLatLngs(),
			x = marker.getLatLng().lat,
			y = marker.getLatLng().lng,
			inside = false;

		for (var i = 0, j = polyPoints.length - 1; i < polyPoints.length; j = i++) {
			var xi = polyPoints[i].lat, yi = polyPoints[i].lng;
			var xj = polyPoints[j].lat, yj = polyPoints[j].lng;

			var intersect = ((yi > y) != (yj > y))
				&& (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
			if (intersect) inside = !inside;
		}
		return inside;
	};

	function isMarkerInsideGPolygon(marker, polyPoints) {
		// слегка модифицировано под наши задачи. Определение принадлежности к полигону.
		var x = marker.lat,
			y = marker.lng,
			inside = false;

		for (var i = 0, j = polyPoints.length - 1; i < polyPoints.length; j = i++) {
			var xi = polyPoints[i][0],
				yi = polyPoints[i][1],
				xj = polyPoints[j][0],
				yj = polyPoints[j][1],
				intersect = ((yi > y) != (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
			inside = (intersect) ? !inside : inside;
		}
		return inside;
	};

	function defineDistrict(marker) {
		//Адрес ближайшего дома из адресного плана
		$.ajax({
			url      : "/appeal/getnearestaddress",
			type     : "POST",
			data     : {
				lat  : coords.lat,
				lng  : coords.lng
			},
			dataType : 'text',
			success  : function(data) {
				address = data;
				$("#locCaption").html(address);
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
		//перебираем полигоны 
		for (a in dCoordinates ) {
			if ( isMarkerInsideGPolygon(marker, dCoordinates[a].coords) ) {
				district    = a;
				responsible = dCoordinates[a].responsible;
				name        = dCoordinates[a].name;
				allowPost = true;
				return true;
			}
		}
		return false;
	}

	function geoError() {
		console.log ("Не удалось установить местоположение");
	}

	function geoSuccess(position) {
		coords = { lat : position.coords.latitude, lng : position.coords.longitude };
		defineDistrict(coords);
		L.marker(coords, {draggable: true}, {})
		.on("dragend", function(event){
			coords = event.target.getLatLng();
			console.log(coords)
			defineDistrict(coords);
		})
		.addTo(layers);
		defineDistrict(coords);
		allowPost = true;
	}

	function mapInit(){
		map = L.map('LMap')
		.setView([64.543110, 40.537736], 12)
		.on("click", function(e){
			layers.clearLayers();
			coords    = e.latlng;
			console.log("onclick", coords)
			defineDistrict(coords);
			L.marker(coords, {draggable: true}, {})
			.on("dragend", function(event){
				coords = event.target.getLatLng();
				defineDistrict(coords);
				console.log("dragend after click",coords)
				allowPost = true;
			})
			.addTo(layers);
			allowPost = true;
		});
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Участники OpenStreetMap</a>, &copy; <a title="Администрация МО &quot;Город Архангельск&quot;" href="http://www.arhcity.ru">www.arhcity.ru</a>'
		}).addTo(map);
		layers = L.featureGroup().addTo(map);

		if (coords.lat !== undefined && coords.lng !== undefined) {
			console.log("from session", coords)
			L.marker(coords, {draggable: true}, {})
			.on("dragend", function(event){
				coords = event.target.getLatLng();
				console.log("dragend from session", coords)
				defineDistrict(coords);
				allowPost = true;
			})
			.addTo(layers);
			defineDistrict(coords);
			allowPost = true;
			return true;
		}
		navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
	}

	$("#postCoords").unbind().click(function(e) {
		e.preventDefault();
		if (!allowPost) {
			alert("Укажите локацию");
			return false;
		}
		console.log("pre save", coords)
		$.ajax({
			url      : '/appeal/setlocation',
			type     : "POST",
			data     : {
				districtID   : district,
				responsible  : responsible,
				lat          : coords.lat,// не передаёт сложный объект coords вида {lat:float, lng:float}
				lng          : coords.lng,// не передаёт сложный объект coords вида {lat:float, lng:float}
				districtName : name,
				address      : address
			},
			dataType : "html",
			success  : function(data) {
				$("#appContent").html(data);
				setListener();
			},
			error: function(data,stat,err) {
				console.log(data,stat,err);
			}
		});
		return true;
		//console.log('Blocked');
	});

	mapInit();
</script>

