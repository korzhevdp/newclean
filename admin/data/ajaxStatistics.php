<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?
ob_start();


$html = ob_get_contents();
ob_end_clean();

//die();
?>