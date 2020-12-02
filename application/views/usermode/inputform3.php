	<div class="panel-container">
		<div class="header-panel">
			<a href="/appeal/map" class="icon-left-open-big slide-back">Назад</a>
			<div><?=$this->session->userdata("categoryName");?></div>
		</div>

		<div class="container mess_cont">
			<p class="little-text icon-marker address-text">Выбрано местоположение: <br><?=$address;?></p>
			<div class="gray-cont">
				<p class="little-text"><?=$caption;?></p>
				<br>
				<input type="file" id="file1" accept="image/*"><br>
				<input type="file" id="file2" accept="image/*"><br>
				<input type="file" id="file3" accept="image/*"><br>
				<div id="preview-photo" class="photo-cont"><br></div>

			<textarea id="moreInfo" placeholder="Опишите проблему, по возможности укажите точный адрес в тексте"></textarea>
			<span class="little-text">Если Вам известно, какая организация может нести ответственность за нарушение, укажите её в комментарии</span>
			<a href="#" class="btn" id="sendMe">Опубликовать</a>

		</div>
	</div>
	<script>
		if ($(location).attr('href') != "<?=$link;?>") {
			window.history.pushState("", "Чистый город - дополнительно", "<?=$link;?>");
		}
		$("#sendMe").click(function(e){
			e.preventDefault();
			var form_data = new FormData();
				file1 = $('#file1').prop('files')[0];
				file2 = $('#file2').prop('files')[0];
				file3 = $('#file3').prop('files')[0];
				form_data.append('file1', file1);
				form_data.append('file2', file2);
				form_data.append('file3', file3);
				form_data.append('moreInfo', $("#moreInfo").val());
			$.ajax({
				url         : "/appeal/finalizeappeal",
				type        : "POST",
				cache       : false,
				contentType : false,
				processData : false,
				data        : form_data,
				dataType    : 'text',
				success     : function(data) {
					console.log(data);
					window.location = data;
				},
				error       : function( data, stat, err ) {
					console.log([ data, stat, err ].join("\n"));
				}
			});
		});
	</script>
