<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

$arUser = array();
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];

if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}


$arStatus = array();
$arStatus = MSystem::GetMessageStatusList();

?>

<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 30%;" data-field="name" data-required>Наименование категории</div>
		<div style="width: 15%;" data-field="icon">Код вект. иконки</div>
		<div style="width: 10%;" data-field="status_color">Цвет маркера</div>
		<div style="width: 10%;" data-field="answer_index" title="Возможность добавлять комментарий при изменении статуса">
			<span>Комментарий</span>
			<div class="hidden-select" id="answer_index_select">
				<select>
					<option value='1'>Да</option>
					<option value='0'>Нет</option>
				</select>
			</div>
		</div>
		<div style="width: 10%;" data-field="file_index" title="Возможность прикрепить файл при изменении статуса">
			<span>Файл</span>
			<div class="hidden-select" id="file_index_select">
				<select>
					<option value='1'>Да</option>
					<option value='0'>Нет</option>
				</select>
			</div>
		</div>
		<div style="width: 10%;" data-field="activity">
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
<div class="table body editable" data-table-id="6">
<?php


foreach($arStatus as $key => $status)
{
	$color = '';
	if($status['activity']==1)
	{
		$color = 'background: #cdf7c2;';
	}
	echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
		echo "<div style='width: 5%; text-align: center;'>".$key."</div>";
		echo "<div style='width: 30%'><a href='#' class='info'>".($status['name']!='' ? $status['name']: '')."</a><input type='text' name='name'/></div>";
		echo "<div style='width: 15%'><a href='#' class='info'>".($status['icon']!='' ? $status['icon']: '')."</a><input type='text' name='icon'/></div>";
		echo "<div style='width: 10%'><a href='#' class='info'>".($status['status_color']!='' ? $status['status_color']: '')."</a><input type='text' name='status_color'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#answer_index_select' class='info' data-id='".$status['answer_index']."'>".($status['answer_index']== 1 ? "Да":"Нет")."</a><input type='hidden' name='answer_index'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#file_index_select' class='info' data-id='".$status['file_index']."'>".($status['file_index']== 1 ? "Да":"Нет")."</a><input type='hidden' name='file_index'/></div>";
		
		echo "<div style='width: 10%; text-align: center;'><a href='#activity_select' class='info' class='info' data-id='".$status['activity']."'>".($status['activity']== 1 ? "Да":"Нет")."</a><input type='hidden' name='activity' value='".$status['activity']."'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
}

?>
</div>
</div>