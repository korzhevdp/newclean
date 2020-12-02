<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
	<meta name="MobileOptimzied" content="width">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="Архангельск чистый город">
	<meta name="mobile-web-app-title" content="Архангельск чистый город">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<meta name="keywords" content="">
	<meta name="description" content="">
	
	<link rel="apple-touch-icon-precomposed" href="/img/favicon-x128.png">
	<link rel="touch-icon-precomposed" href="/img/favicon-x128.png">
	<title>Чистый город</title>

	<link rel="stylesheet" type="text/css" href="/styles/leaflet.css">
	<link rel="stylesheet" type="text/css" href="/styles/admin.css">
</head>
<body>
	<script type="text/javascript" src="/scripts/jquery.js"></script>

	<table class="admMain">
		<tr>
			<td>
				<div class="admOrg">
					<?=(strlen($userdata['organizationName'])) ? $userdata['organizationName'] : $userdata['groupName'];?>
				</div>

				<div class="admUsername" title="<?=$userdata['departmentName'];?>">
					<?=$userdata['alias'] ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="admFilterForm">
				Территориальный округ
				<select id="districtID" class="messageSelector">
					<option value="0">Выберите территориальный округ</option>
					<?=$districts;?>
				</select>
				Категории
				<select id="categoryID" class="messageSelector">
					<option value="0">Все категории</option>
					<?=$categories;?>
				</select>
				Состояние / статус
				<select id="statusID" class="messageSelector">
					<option value="0">Все статусы</option>
					<?=$statii;?>
				</select>
				<hr>
				Организации
				<select id="organizationID" class="messageSelector">
					<option value="0">Выберите организацию-исполнителя</option>
					<?=$organizations;?>
				</select>
				Подразделения
				<select id="departmentID" class="messageSelector">
					<option value="0">Выберите курирующее подразделение</option>
					<?=$departments;?>
				</select>

			</td>
		</tr>
		<tr>
			<td class="messageSectionHeader">
				<h4>Обращения</h4>
			</td>
		</tr>
		<tr>
			<td id="LMap" style="height:500px;">
			</td>
		</tr>
		<tr>
			<td id="messagesContainer">
				<?=$messages;?>
			</td>
		</tr>
		<tr>
			<td id="returnToList" class="returnToList">
				< Обратно к списку
			</td>
		</tr>
		<tr>
			<td id="messageData" style="height:500px;">
			</td>
		</tr>

	</table>

	<script src="/scripts/leaflet.js"></script>
	<script type="text/javascript">
		var defaultCenter = { lat: 64.543110, lng : 40.537736 },
			normalZoom    = 15,
			coords,
			layers,
			map,
			messageID = '<?=$messageID;?>';

		function mapInit() {
			map = L.map('LMap').setView(defaultCenter, normalZoom);
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Участники OpenStreetMap</a>, &copy; <a title="Администрация МО &quot;Город Архангельск&quot;" href="http://www.arhcity.ru">www.arhcity.ru</a>'
			}).addTo(map);
			layers = L.featureGroup().addTo(map);
		}

		function setListener() {
			$(".messageItem").unbind().click(function(){
				ref = $(this).attr('ref');
				getMessageDetails(ref);
			});
		}

		function getMessageDetails(ref){
			//console.log(ref);
			if (ref == "0") {
				$("#returnToList").css('display', 'none');
				$("#messagesContainer").css('display', 'block');
				layers.clearLayers();
				return false;
			}
			$.ajax({
				url      : "/admin/getAdmMessageDetails/" + ref,
				type     : "GET",
				dataType : 'html',
				success  : function(data) {
					var URL = '<?=base_url();?>admin/appeal/' + ref;
					//console.log(data);
					$("#messageData").empty().html(data);
					$("#messageData").css('display', 'table-cell');
					//$("#LMap").css('display', 'table-cell');

					$("#returnToList").css('display', 'block');
					$("#messagesContainer").css('display', 'none');
					layers.clearLayers();

					if (coords === undefined) {
						map.setView(defaultCenter, 15);
						if ($(location).attr('href') != '<?=base_url();?>admin') {
							window.history.pushState("", "Чистый город - локация", '<?=base_url();?>admin');
						}
						return false;
					}
					if ($(location).attr('href') != URL) {
						window.history.pushState("", "Чистый город - локация", URL);
					}
					map.setView(coords, 15);
					L.marker(coords).addTo(layers);
				},
				error    : function( data, stat, err ) {
					console.log([ data, stat, err ].join("\n"));
				}
			});
		}

		$("#returnToList").click(function(){
			window.history.pushState("", "Чистый город - локация", '<?=base_url();?>admin');
			$("#returnToList").css('display', 'none');
			$("#messagesContainer").css('display', 'block');
			$("#messageData").empty().css('display', 'none');
			layers.clearLayers();
			map.setView(defaultCenter, normalZoom);
			//$("#LMap").css('display', 'none');
		});

		$(".messageSelector").change(function(){
			$.ajax({
				url      : "/admin/getFilteredMessages",
				type     : "POST",
				data     : {
					categoryID     : $("#categoryID").val(),
					statusID       : $("#statusID").val(),
					organizationID : $("#organizationID").val(),
					departmentID   : $("#departmentID").val(),
					districtID     : $("#districtID").val(),
				},
				dataType : 'html',
				success  : function(data) {
					$("#messagesContainer").empty().html(data);
					setListener();
				},
				error    : function( data, stat, err ) {
					console.log([ data, stat, err ].join("\n"));
				}
			});
		});
		mapInit();

		setListener();
		if ( messageID ) {
			getMessageDetails(messageID);
		}
	</script>
</body>
</html>