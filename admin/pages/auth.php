<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<?php require_once('pages/header.php'); ?>

<div class="container auth">
	<div class="gray-cont">
		<input type="text" placeholder="Email / Логин" data-type="email" name="login"/>
		<input type="password" class="mr-1" placeholder="Пароль" name="password"/>
		<div class="btn-block">
			<a href="#" class="btn submt" id="reg">Регистрация</a>
			<a href="#" class="btn icon-key submt right-position" id="auth">Войти</a>
		</div>
		<a href="#" class="little-link" id="password_recovery">Забыли пароль?</a>
	</div>
	
	<p class="center gray">Вход в административный раздел системы (Контролирующие органы, Ответственные подразделения)</p>
	<br/>
	<a href="/" class="bl-link icon-bars">Перейти в общий раздел</a>
</div>
<div class="copyright">© МУ &laquo;Центр информационных технологий&raquo; 2019</div>
<script type="text/javascript">

	document.addEventListener('DOMContentLoaded', function() {
   userAuthByKey();
	}, false);

</script>