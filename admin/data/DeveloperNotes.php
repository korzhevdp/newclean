<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}


$arNotes = array();
$arNotes = MSystem::GetDeveloperNotes();

?>

<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 35%;" data-field="name" data-required>Описание</div>
		<div style="width: 10%; text-align: center;" data-field="done">
			<span>Выполнено</span>
			<div class="hidden-select" id="done_select">
				<select>
					<option value='1'>Да</option>
					<option value='0' selected>Нет</option>
				</select>
			</div>
		</div>
		<div style="width: 10%; text-align: center;" data-field="priority">
			<span>Приоритет</span>
			<div class="hidden-select" id="priority_select">
				<select>
					<option value='1'>Да</option>
					<option value='0' selected>Нет</option>
				</select>
			</div>
		</div>
		<div style="width: 25%; text-align: center;" data-field="sort">Индекс сортировки</div>
		<div style='width: 15%; text-align: center;'>Удал.</div>
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="7">
<?php

foreach($arNotes as $key => $notes)
{
	$color = '';
	if($notes['done']==1)
	{
		$color = 'background: #c0e8bb;';
	}
	else
	{
		if($notes['priority']==1)
		{
			$color = 'background: #f7e0c2;';
		}
	}
	
	echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
		echo "<div style='width: 5%; text-align: center;'>".$key."</div>";
		echo "<div style='width: 35%'><a href='#' class='info searchable'>".($notes['name']!='' ? $notes['name']: '')."</a><input type='text' name='name'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#done_select' class='info' class='info' data-id='".$notes['done']."'>".($notes['done']== 1 ? "Да":"Нет")."</a><input type='hidden' name='done' value='".$notes['done']."'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#priority_select' class='info' class='info' data-id='".$notes['priority']."'>".($notes['priority']== 1 ? "Да":"Нет")."</a><input type='hidden' name='priority' value='".$notes['priority']."'/></div>";
		echo "<div style='width: 25%; text-align: center;'><a href='#' class='info'>".($notes['sort']!='' ? $notes['sort']: '')."</a><input type='text' name='sort'/></div>";
		echo "<div style='width: 15%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
}


?>
</div>
</div>