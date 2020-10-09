<?php if(!isset($AccessIndex)) exit('Контент данной страницы для вас недоступен.'); ?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Восстанов. пароля</div>
</div>

<div class="container change-password form">
	<p>Для восстановления пароля введите Ваш email, указанный при регистрации</p>
	<div class="gray-cont">
		<input type="text" placeholder="Email" data-type="email" name="email"/>
		<div class="form-error"></div>
		<a href="#" class="btn submt" id="repass">Восстановить</a>
	</div>
	
	<p>На указанный Email будет выслана ссылка для восстановления пароля</p>
	
</div>