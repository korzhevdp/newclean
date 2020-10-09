<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arRef = array();
$arRef = MSystem::GetReference();

$index = 0;
foreach($arRef as $key => $sec)
{
	$index++;
	echo '<a href="#" data-code="'.$key.'">'.$index.'. '.$sec['caption'].'</a>';
	echo '<div class="one-info" data-code="'.$key.'">'.$sec['text'].'</div>';
}

?>