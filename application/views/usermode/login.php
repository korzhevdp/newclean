
<?=$header;?>

<div class="container auth">
	<div class="gray-cont">
		<!--<p>Для входа введите Ваш логин и пароль</p>-->
		<form method="post" action="">
			<input type="text"      placeholder="Email / Логин" data-type="email" id="email">
			<input type="password"  placeholder="Пароль"class="mr-1" id="password">
			<input type="hidden" name="">
		</form>

		<div class="form-error"></div>
		<a href="#" class="btn icon-key login" id="logMe">Войти</a>
		<a href="#" ref="/passwordrestore" class="little-link submit">Забыли пароль?</a>
	</div>
	<a href="#" ref="/registration" class="bl-link icon-users submit">Регистрация пользователя</a>
	<!--<a href="#" class="bl-link icon-question" id="to_repass">Не могу &nbsp;вспомнить пароль</a>-->
	<a href="#" ref="/welcome" class="bl-link icon-bars submit">Перейти в главный раздел</a>
	<br/>
</div>

<script type="text/javascript">
	if ($(location).attr('href') != "<?=base_url();?>login") {
		window.history.pushState("", "Чистый город - логин", "<?=base_url();?>login");
	}
	$("#logMe").unbind().click(function(e) {
		e.preventDefault();
		$.ajax({
			url      : '/login/authenticate',
			type     : "POST",
			data     : {
				email    : $("#email").val(),
				password : $("#password").val(),
			},
			dataType : "json",
			success  : function(data) {
				if (data.status) {
					if (data.redirect.length) {
						window.location.href = data.redirect;
						return false;
					}
					window.location.href = "/admin";
					return false;
				}
			},
			error: function(data,stat,err) {
				console.log(data,stat,err);
			}
		});

	});
</script>