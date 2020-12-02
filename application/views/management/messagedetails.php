<?//print_r($messageProgressArray);?>

<link rel="stylesheet" type="text/css" href="/styles/leaflet.css">
<div class="messageInfoCard" style="margin-bottom:240px;">

	<h2>Обращение №<?=$messageID?></h2>
	<h4><?=$categoryName?></h4>
	<div class="muted"><?=$statusName;?></div>

	<div class="fixedInfo">
		Дата обращения: <?=$createTime?><br>
		Территориальный округ: <?=$districtName;?><br>
		Время на обработку обращения: <?=$deadline?> дней<br>
		Статус изменился: <?=$updateTime?>
	</div>

	<h4>Автор обращения:</h4>
	<strong><?=$alias?></strong>, E-mail: <?=$email?><?=((strlen($phone)) ? ", тел: ".$phone : "") ?>

	<div class="mainText">
		<h4>Текст сообщения</h4>
		<?=$message;?>
	</div>
	
	<div class="mainText">
		<h5>Фото с места событий</h5>
		<?=$files;?>
	</div>
	
	<div class="mainMap">
		<h5>Схема места</h5>
		<div id="LMap"></div>
	</div>
	
	<? if ( !isset($messageProgressArray[3]) || !$messageProgressArray[3]) { ?>
		<div class="mainText">
			<h5>Валидация задачи</h5>
			<table class="table table-bordered commentary" style="width:100%;margin: 10px 0px;">
			<tr>
				<th id="validityLabel">Задача не валидна</th>
				<td style="width:65px;">
					<label class="switch"><input type="checkbox" id="boxTaskValid"><span class="slider round"></span></label>
				</td>
			</tr>
			</table>
			<div id="statusWord">&nbsp;</div>
			<select id="boxStatusID"><?=$statusList?></select>
			<div id="reasonIfNotVaildLabel">Причина отказа</div>
			<textarea id="reasonIfNotVaild"></textarea><br>
			<button type="button" id="setValidness" class="btn btn-small">Валидация задачи</button>
		</div>
	<? } ?>

	<? if ( !isset($messageProgressArray[4]) || !$messageProgressArray[4]) { ?>
		<div class="mainText">
			<h5>Классификация задачи</h5>
			Категория обращения: <?=$categoryName?><br>
			Подкатегория:<br>
			<select id="boxSubcategoryID"><?=$subcategoriesList;?></select>
			<button type="button" id="setSubcategory" class="btn btn-small">Установить подкатегорию</button>
		</div>
	<? } ?>

	<? if ( !isset($messageProgressArray[5]) || !$messageProgressArray[5]) { ?>
		<div class="mainText">
			<h5>Назначение ответственной организации</h5>
			Ответственная организация:<br>
			<select id="boxOrganizationID"><?=$organizationList;?></select>
			<button type="button" id="setOrganization" class="btn btn-small">Подтвердить организацию</button>
		</div>
	<? } ?>

	<? if ( !isset($messageProgressArray[6]) || !$messageProgressArray[6]) { ?>
		<div class="mainText">
			<h5>Назначение контролирующего субъекта</h5>
			На контроле у:<br>
			<select id="boxControlID"><?=$controlList;?></select>
			<button type="button" id="setController" class="btn btn-small">Подтвердить контролёра</button>
		</div>
	<? } ?>


		<input type="hidden" id="boxMessageID" value="<?=$messageID;?>">
		<!-- <table class="table table-bordered">
		<tr>
			<td>В работе:</td>
			<td style="width:65px;">
				<label class="switch"><input type="checkbox" style="margin-top:20px;" id="boxArchive"<?=(($archive) ? '' : ' checked="checked"');?>><span class="slider round"></span></label>
			</td>
		</tr>
		</table> -->

		<!-- <button class="saveItem btn btn-small">Сохранить</button>-->


		<h4>История операций</h4>
		<table class="table table-bordered commentary">
			<tr>
				<th>Дата и время</th>
				<th>Комментарий</th>
			</tr>
			<?=$historyData;?>
		</table>
		<button type="button" id="clearLocalProgress" class="btn btn-small" style="margin-bottom:40px">Сброс прогресса обработки</button>

		<h4>Комментарии</h4>
		<table class="table table-bordered commentary">
			<tr>
				<th class="col1">Дата и время</th>
				<th>Комментарий</th>
			</tr>
			<?=$commentsData;?>
		</table>
	</div>

	<h4>Прогресс обработки</h4>
	<table class="table table-bordered commentary">
		<th>Этап прохождения</th><th>Оператор</th><th>Cтатус</th>
		<?=$messageProgressTable;?>
	</table>

</div>

<script src="/scripts/leaflet.js"></script>
<script type="text/javascript">
	var defaultCenter = { lat: 64.543110, lng : 40.537736 },
		taskValid     = <?=$taskValid;?>,
		normalZoom    = 15,
		coords        = <?=$coords;?>,
		layers,
		map,
		messageStatus = (taskValid) ? "Комментарий к продолжению обработки" : "Комментарий к завершению обработки";
		messageID     = '<?=$messageID;?>';
		
	$(".saveItem").unbind().click(function() {
		$.ajax({
			url      : "/admin/saveadmmessagedetails",
			type     : "POST",
			data     : {
				organizationID : $("#boxOrganizationID").val(),
				controlID      : $("#boxControlID").val(),
				statusID       : $("#boxStatusID").val(),
				messageID      : $("#boxMessageID").val(),
				archive        : $("#boxArchive").prop("checked") ? 0 : 1
			},
			dataType : 'html',
			success  : function(data) {
				
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});

	$("#setValidness").unbind().click(function() {
		$.ajax({
			url      : "/admin/setValidateInfoOnAMessage",
			type     : "POST",
			data     : {
				text      : $("#reasonIfNotVaild").val(),
				validness : ($("#boxTaskValid").prop("checked")) ? 1 : 0,
				messageID : $("#boxMessageID").val()
			},
			dataType : 'html',
			success  : function(data) {
				window.location.reload();
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});

	$("#setSubcategory").unbind().click(function() {
		$.ajax({
			url      : "/admin/setSubcategory",
			type     : "POST",
			data     : {
				subcategory : $("#boxSubcategoryID").val(),
				messageID   : $("#boxMessageID").val()
			},
			dataType : 'html',
			success  : function(data) {
				window.location.reload();
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});

	$("#setOrganization").unbind().click(function() {
		$.ajax({
			url      : "/admin/validateOrganization",
			type     : "POST",
			data     : {
				organizationID : $("#boxOrganizationID").val(),
				messageID      : $("#boxMessageID").val()
			},
			dataType : 'html',
			success  : function(data) {
				window.location.reload();
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});

	$("#setController").unbind().click(function() {
		$.ajax({
			url      : "/admin/validateController",
			type     : "POST",
			data     : {
				controllerID : $("#boxControlID").val(),
				messageID    : $("#boxMessageID").val()
			},
			dataType : 'html',
			success  : function(data) {
				window.location.reload();
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});

	$("#clearLocalProgress").unbind().click(function() {
		$.ajax({
			url      : "/processes/clearprogress/" + $("#boxMessageID").val(),
			type     : "GET",
			dataType : 'html',
			success  : function(data) {
				window.location.reload();
			},
			error    : function( data, stat, err ) {
				console.log([ data, stat, err ].join("\n"));
			}
		});
	});

	$("#boxTaskValid").click(function(){
		//console.log($(this).prop('checked'))
		markAvailableStatii();
		if ($(this).prop('checked')) {
			$("#validityLabel").html("Задача валидна");
			$("#reasonIfNotVaildLabel").html('Комментарий к исполнению');
			$("#statusWord").html("Комментарий к продолжению обработки");
			return true;
		}
		$("#reasonIfNotVaildLabel").html('Статус завершения работы');
		$("#validityLabel").html("Задача не валидна");
		$("#statusWord").html("Комментарий к завершению обработки");
	});

	function markAvailableStatii() {
		$("#boxStatusID option").removeClass("hide");
		$("#boxStatusID option").each(function(){
			if ($(this).attr("final") == $("#boxTaskValid").prop("checked")) {
				$(this).addClass("hide");
			}
		});
		$("#boxStatusID option:first").prop("selected", true);
	}

	function mapInit() {
		map = L.map('LMap').setView(coords, normalZoom);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">Участники OpenStreetMap</a>, &copy; <a title="Администрация МО &quot;Город Архангельск&quot;" href="https://www.arhcity.ru">www.arhcity.ru</a>'
		}).addTo(map);
		layers = L.featureGroup().addTo(map);
		L.marker(coords).addTo(layers);
	}

	$("#statusWord").html(messageStatus);

	markAvailableStatii();
	mapInit();
</script>