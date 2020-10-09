<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arResult = array();
$arResult['status'] = false;
$arResult['message'] = 'При выполнении запроса произошла неизвестная ошибка';


echo json_encode($arResult);

?>