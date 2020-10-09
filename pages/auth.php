<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<?php require_once('pages/header.php'); ?>

<div class="container auth">
	<div class="gray-cont">
		<!--<p>Для входа введите Ваш логин и пароль</p>-->
		<input type="text" placeholder="Email / Логин" data-type="email" name="login" autocomplete/>
		<input type="password" class="mr-1" placeholder="Пароль" name="password" autocomplete/>
		<div class="form-error"></div>
		<a href="#" class="btn icon-key submt" id="auth">Войти</a>
		<a href="#" class="little-link" id="password_recovery">Забыли пароль?</a>
	</div>
	<a href="#" class="bl-link icon-users" id="to_reg">Регистрация пользователя</a>
	<!--<a href="#" class="bl-link icon-question" id="to_repass">Не могу &nbsp;вспомнить пароль</a>-->
	<a href="/" class="bl-link icon-bars">Перейти в главный раздел</a>
	<br/>
</div>