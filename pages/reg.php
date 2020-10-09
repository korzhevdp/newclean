<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<!--<script src='https://www.google.com/recaptcha/api.js'></script>-->

<?php require_once('pages/header.php'); ?>

<div class="container auth">
	<div class="gray-cont">
	  <h2>Регистрация</h2>
	  <input type="text" placeholder="Имя" name="user_name"/>
		<input type="text" placeholder="Email" data-type="email" name="email"/>
		<!--<input type="tel" placeholder="" data-type="phone" name="phone"/>-->
		<!--<div class="form-info-message">На указанный email будет отправлено письмо для подтверждения Вашей учетной записи.</div>-->
		<input type="password" class="mr-1" placeholder="Пароль" name="password1"/>
		<input type="password" class="mr-1" placeholder="Повторить пароль" name="password2"/>
		<!--<div class="g-recaptcha" data-sitekey="6LcOjUkUAAAAAHevUTJKkWYLNUGqUhDs_RNmD3VE"></div>-->
		
		<div class="reg-captcha">
			<p>Введете код с изображения:</p>
			<img src="//gorod.arhcity.ru/captcha/"/>
			<input type="text" placeholder="Код" name="captcha"/>
		</div>
		
		<div class="form-error"></div>
	
		<a href="#" class="btn submt" id="reg_user">Зарегистрироваться</a>
	</div>
	<a href="#" class="bl-link icon-key" id="to_auth">У меня есть учетная запись</a>
	<a href="/" class="bl-link icon-bars">Вернуться в главный раздел</a>
	<br/>
</div>
<script type="text/javascript" src="plugins/inputmask/jquery.inputmask.js"></script>
<script>
	$(function() {
		$('input[data-type="phone"]').inputmask("+7 (999) 999-99-99", {
			clearMaskOnLostFocus: false
		});

	});
</script>
