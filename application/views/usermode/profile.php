<?=$header;?>

	<div class="panel-container">
		<div class="header-panel">
			<a href="/" class="icon-left-open-big slide-back">Назад</a>
			<div>Профиль пользователя</div>
		</div>

		<div class="container mess_cont">
			<div class="gray-cont">
				<table>
					<tr>
						<td>Фамилия</td>
						<td class="form"><input type="text" id="lastname" value="<?=$lastname?>"></td>
					</tr>
					<tr>
						<td style="width:175px;">Имя</td>
						<td class="form"><input type="text" id="firstname" value="<?=$firstname?>"></td>
					</tr>

					<tr>
						<td>Отчество</td>
						<td class="form"><input type="text" id="secondname" value="<?=$secondname?>"></td>
					</tr>
					<tr>
						<td>Телефон</td>
						<td class="form"><input type="text" id="phone" value="<?=$phone?>"></td>
					</tr>
					<tr>
						<td>Имя для системы</td>
						<td class="form"><input type="text" id="alias" value="<?=$alias?>"></td>
					</tr>
					<tr>
						<td>Email/login</td>
						<td class="form"><input type="text" value="<?=$email?>" disabled></td>
					</tr>
				</table>
			</div>
			<span class="btn" id="sendMe">Обновить</span>
			<hr>
			<h4>Сменить пароль</h4>
			<div class="gray-cont">
				<table>
				<tr>
					<td style="width:175px;">Старый пароль</td>
					<td class="form"><input type="password" id="currentPassword" value=""></td>
				</tr>
				<tr>
					<td>Новый пароль</td>
					<td class="form"><input type="password" id="newPassword" value=""></td>
				</tr>
				<tr>
					<td>Повторите пароль</td>
					<td class="form"><input type="password" id="newPasswordCheck" value=""></td>
				</tr>
				</table>
			</div>
			<span class="btn" id="changeMe">Сменить пароль</span>
		</div>
	</div>


<script>
	if ($(location).attr('href') != "<?=base_url();?>profile") {
		window.history.pushState("", "Чистый город - профиль пользователя", "<?=base_url();?>profile");
	}

	$("#changeMe").click(function(e){
		e.preventDefault();
		$.ajax({
			url      : '/login/changepassword',
			type     : "POST",
			data     : {
				currentPassword  : $("#currentPassword").val(),
				newPassword      : $("#newPassword").val(),
				newPasswordCheck : $("#newPasswordCheck").val()
			},
			dataType : "html",
			success  : function(data) {
				retrievePage(requestUrl);
			},
			error: function(data,stat,err) {
				console.log(data,stat,err);
			}
		});
	});

	$("#sendMe").click(function(e){
		e.preventDefault();
		$.ajax({
			url      : '/profile/saveprofile',
			type     : "POST",
			data     : {
				firstname  : $("#firstname").val(),
				lastname   : $("#lastname").val(),
				secondname : $("#secondname").val(),
				phone      : $("#phone").val(),
				alias      : $("#alias").val(),
			},
			dataType : "html",
			success  : function(data) {
				retrievePage(requestUrl);
			},
			error: function(data,stat,err) {
				console.log(data,stat,err);
			}
		});
	});
</script>