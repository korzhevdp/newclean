<div class="big-logo">
	<?php if(Users::isAuthorized()): ?>
		<a href="#" class="top-menu-btn icon-bars"></a>
		<a href="#" class="top-feedback-btn icon-mail"></a>
	<?php endif; ?>
	<div class="img"></div>
</div>
<p class="center gray little-text">Система работает в тестовом режиме<br>Запущен процесс подключения управляющих компаний<p>
<?php if(Users::isAuthorized()): ?>
	<p class="center gray little-text">Если у Вас возникли проблемы при работе,<br>пожалуйста, <a href="#" class="default" id="feedback">сообщите об этом</a><p>
<?php endif; ?>