<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Тех. поддержка</div>
</div>
<div class="container form feedback-form">
    <p>Вы можете задать вопрос технической поддержке или сообщить о проблемах, возникающих при работе с сервисом</p>
    <input type="text" placeholder="Тема обращения" name="subject"/>
	<textarea name="message" placeholder="Опишите суть вашего вопроса или проблему. Вы можете, дополнительно, указать контактные данные."></textarea>
	<p>Рекомендуем прикрепить скриншот</p>
	<div class="gray-cont">
		<a href="#" id="screenshot" class="bl-link icon-plus">Прикрепить файл</a>
		<input id="inputfile" type="file" name="inputfile">
		<input type="hidden" name="filedata">
	</div>
	<br>
	<a href="#" class="btn" id="send-feedback">Отправить сообщение</a>

</div>