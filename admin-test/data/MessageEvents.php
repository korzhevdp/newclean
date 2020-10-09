<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}


$arEvents = array();
$arEvents = MSystem::GetMailEventsList();

?>

<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 10%;" data-field="event_name" data-required>Наименование события</div>
		<div style="width: 10%;" data-field="subject" data-required>Тема письма</div>
		<div style="width: 20%;" data-field="text" data-required>Текст пиьсма</div>
		<div style="width: 15%;" data-field="link">Ссылка в письме</div>
		<div style="width: 10%;" data-field="link_text">Текст ссылки</div>
		<div style="width: 10%;" data-field="from_email">Email отправителя</div>
		<div style="width: 10%; text-align: center;" data-field="activity">
			<span>Актив.</span>
			<div class="hidden-select" id="activity_select">
				<select>
					<option value='1'>Да</option>
					<option value='0'>Нет</option>
				</select>
			</div>
		</div>
		<div style='width: 10%; text-align: center;'>Удал.</div>
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="9">
<?php

if(is_array($arEvents) && count($arEvents)>0)
{
	
	foreach($arEvents as $key => $events)
	{
		$color = '';
		if($events['activity']==0)
		{
			$color = 'background: #f7e0c2;';
		}
		
		echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
			echo "<div style='width: 5%; text-align: center;'>".$key."</div>";
			echo "<div style='width: 10%'><a href='#' class='info'>".($events['event_name']!='' ? $events['event_name']: '')."</a><input type='text' name='event_name'/></div>";
			echo "<div style='width: 10%'><a href='#' class='info'>".($events['subject']!='' ? $events['subject']: '')."</a><input type='text' name='subject'/></div>";
			echo "<div style='width: 20%'><a href='#' class='info'>".($events['text']!='' ? $events['text']: '')."</a><input type='text' name='text'/></div>";
			echo "<div style='width: 15%'><a href='#' class='info'>".($events['link']!='' ? $events['link']: '')."</a><input type='text' name='link'/></div>";
			echo "<div style='width: 10%'><a href='#' class='info'>".($events['link_text']!='' ? $events['link_text']: '')."</a><input type='text' name='link_text'/></div>";
			echo "<div style='width: 10%'><a href='#' class='info'>".($events['from_email']!='' ? $events['from_email']: '')."</a><input type='text' name='from_email'/></div>";
			echo "<div style='width: 10%; text-align: center;'><a href='#activity_select' class='info' class='info' data-id='".$events['activity']."'>".($events['activity']== 1 ? "Да":"Нет")."</a><input type='hidden' name='activity' value='".$events['activity']."'/></div>";
			echo "<div style='width: 10%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
		echo "</div>";
	}
	
}
else
{
	echo "<div class='sys-table-info'>Ни одной записи не было найдено</div>";
}

?>
</div>
</div>