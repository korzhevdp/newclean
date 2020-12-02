<?=$header;?>

<div class="container auth">
	<div class="gray-cont">
	  <h2>Регистрация</h2>
	  <input type="text" placeholder="Имя" name="user_name">
		<input type="text" placeholder="Email" data-type="email" id="email">
		<!--<input type="tel" placeholder="" data-type="phone" name="phone"/>-->
		<!--<div class="form-info-message">На указанный email будет отправлено письмо для подтверждения Вашей учетной записи.</div>-->
		<input type="password" class="mr-1" placeholder="Пароль" id="password1">
		<input type="password" class="mr-1" placeholder="Повторить пароль" id="password2">
		<!--<div class="g-recaptcha" data-sitekey="6LcOjUkUAAAAAHevUTJKkWYLNUGqUhDs_RNmD3VE"></div>-->
		
		<div class="reg-captcha">
			<p>Введите код с изображения:</p>
			<img src="//gorod.arhcity.ru/captcha/"/>
			<input type="text" placeholder="Код" name="captcha">
		</div>
		
		<div class="form-error"></div>
	
		<a href="#" class="btn" id="regMe">Зарегистрироваться</a>
	</div>
	<a href="/login" ref="/login" class="bl-link icon-key submit">У меня есть учетная запись</a>
	<a href="/" ref="/welcome" class="bl-link icon-bars submit">Вернуться в главный раздел</a>
	<br/>
</div>
<!-- <script type="text/javascript" src="/plugins/inputmask/jquery.inputmask.js"></script> -->
<script>
	window.history.pushState("", "Чистый Город - Регистрация", "<?=base_url();?>registration");
	$(function() {
		$('input[data-type="phone"]').inputmask("+7 (999) 999-99-99", {
			clearMaskOnLostFocus: false
		});

	});
	$("#regMe").unbind().click(function(e) {
		e.preventDefault();
		$.ajax({
			url      : '/login/registration',
			type     : "POST",
			data     : {
				email     : $("#email").val(),
				password1 : $("#password1").val(),
				password2 : $("#password2").val()
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
	});
</script>

