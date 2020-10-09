<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
if(!isset($_SESSION['UID'])) exit('Пользователь неопределен');

$arMessages = array();
$arMessages = Messages::GetMessages($_SESSION['UID'],0);

?>   

<div class="header-panel">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Мои сообщения</div>
	<!--<a href="#" class="add-btn" id="message"></a>-->
</div>
<p class="block">На карте отмечены все сообщения, которые вы создавали</p>
<div id="map"></div>

<script src="//api-maps.yandex.ru/2.1/?lang=ru-RU" type="text/javascript"></script>
<script src="//yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>

<script>
<?php
	echo "var messages = false;\n";
	if(count($arMessages)>0)
	echo "messages = ".json_encode($arMessages).";\n";
?>
	map_height = window.innerHeight-$('.header-panel').height()-$('p.block').height()-12;
	$('#map').css('height',map_height+'px');
</script>
<script src="scripts/usersMapMessages.js?q=1.4"></script>