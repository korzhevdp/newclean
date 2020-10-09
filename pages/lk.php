<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>


<?php require_once('pages/header.php'); ?>

<div class="container auth">
	<div class="gray-cont">
        <a href="#" class="btn icon-mail" id="message">Сообщить о нарушении</a>
	</div>
</div>

<div class="items gr-border">
    <a href="#" class="icon-right-open-big" id="to_my_message">Мои сообщения</a>
    <?php //<!--<a href="#" class="icon-right-open-big" id="to_all_message">Все сообщения</a>--> ?>
</div>

<div class="container mg20px">
    <a href="#" class="item-link icon-info" id="about">О сервисе</a>
    <a href="#" class="item-link icon-lock" id="logout">Выход</a>
</div>