<?php if(!isset($AccessIndex)) exit('Контент данной страницы для вас недоступен.'); ?>

<br>
<h2>Восстановление пароля</h2>
<div class="gray-cont">
	<p>Для восстановления пароля введите Ваш email, указанный при регистрации</p>
	<input type="text" placeholder="Email" data-type="email" name="email"/>
	<div class="form-error"></div>
	<a href="#" class="btn submt" id="repass">Восстановить</a>
</div>
<p>На указанный Email будет выслана ссылка для восстановления пароля</p>

<a href="/admin/" class="bl-link icon-cancel">Отменить восстановление</a>