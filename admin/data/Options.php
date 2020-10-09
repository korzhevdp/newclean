<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.');

if(!$law9 && !$law7)
{
	exit('<div class="pg10px">Системные настройки для Вас недоступны. Обратитесь к администратору системы.</div>');
}


$userOptions = Users::getUserOptions($_SESSION['UID']);


?>

<div class="pd-panel-cont options-panel active">
<?php
//echo $_SERVER["HTTP_USER_AGENT"];
/*echo "<div class='line-option'><div class='capt wth80'>Направлять уведомления обо всех операциях пользователей администратору</div><div class='action checkbox'><a href='#' class='icon-check'></a></div></div>";*/
if($law7 || $law9)
{
	echo "<a class='line-item lnk' data-type='94'>Персональные настройки<i class='icon-external-link'></i></a>";
	
	
	
	if($law9)
	{
		echo "<div class='line-caption'>Системные настройки</div>";
		echo "<a class='line-item lnk' data-type='91' data-width='80'>Пользователи<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='92' data-width='80'>Группы пользователей<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		//echo "<a class='line-item lnk' data-type='93'>Права пользователей<i class='icon-external-link'></i></a>";
		//echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='95' data-width='50'>Ответственные подразделения<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='96'>Организации<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='97' data-width='80'>Категории сообщений<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='99'>Статусы<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='108'>Настройка почтовых событий<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='107'>Сообщения в системе<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='109'>Обращения в техническую поддержку<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<a class='line-item lnk' data-type='100'>История операций<i class='icon-external-link'></i></a>";
		//echo "<div class='line-option'><div class='capt wth80'>Разрешить просмотр истории действий для ответственных подразделений</div><div class='action checkbox'><a href='#' class='icon-check'></a></div></div>";
	}
	
	
	
	echo "<div class='line-caption'>Настройки отображения объектов (в разработке)</div>";
	if($law1)
	{
		echo "<div class='line-option'><div class='capt wth80' title='Будут показаны только те сообщения, у которых не назначено ответственное подразделение'>Показывать только нераспределенные сообщения</div><div class='action checkbox'><a href='#' class='icon-check ".(isset($userOptions[3]) ? 'active':'')."' data-option-id='3'></a></div></div>";
		echo "<div class='separ'></div>";
	}
	
	if($law1 || $UserOrganization)
	{
		echo "<div class='line-option'><div class='capt wth80' title=''>Показывать границы округов на карте</div><div class='action checkbox'><a href='#' class='icon-check ".(isset($userOptions[4]) ? 'active':'')."' data-option-id='4'></a></div></div>";
		echo "<div class='separ'></div>";
	}
	echo "<div class='line-option'><div class='capt wth80' title=''>Не окрашивать сообщения в правой колонке в соответствии с цветом статуса</div><div class='action checkbox'><a href='#' class='icon-check ".(isset($userOptions[1]) ? 'active':'')."' data-option-id='1'></a></div></div>";
	echo "<div class='separ'></div>";
	echo "<div class='line-option'><div class='capt wth80' title=''>Не выводить номер сообщения и дополнительную информацию у каждого маркера на карте</div><div class='action checkbox'><a href='#' class='icon-check ".(isset($userOptions[2]) ? 'active':'')."' data-option-id='2'></a></div></div>";
	if($law9)
	{
		echo "<div class='separ'></div>";
		echo "<div class='line-option'><div class='capt wth80' title=''>Показать только отправленные в архив (при выбранной опции ряд других настроек может временно утратить активность)</div><div class='action checkbox'><a href='#' class='icon-check ".(isset($userOptions[2]) ? 'active':'')."' data-option-id='2'></a></div></div>";
	}
	
	if($law9)
	{
		echo "<div class='line-caption'>Дополнительно</div>";
		echo "<a class='line-item lnk' data-type='106'>Заметки зазрботчика<i class='icon-external-link'></i></a>";
		echo "<div class='separ'></div>";
		echo "<div class='line-option'><div class='capt wth80'>Временно заблокировать доступ к публичной части системы (для простых пользователей)</div><div class='action checkbox'><a href='#' class='icon-check'></a></div></div>";
		echo "<div class='line-option'><div class='capt wth80'>Временно заблокировать доступ к административной части системы (для всех, кроме системных администраторов)</div><div class='action checkbox'><a href='#' class='icon-check'></a></div></div>";
	}
	
}
?>
</div>
