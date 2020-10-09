<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроек на стадии разработки и доступна только системным администраторам</div>');
?>
<div class="table header">
	<div class="table-row">
		<div style="width: 6%; text-align: center;">#</div>
		<div style="width: 84%;" data-field="name">Наименование подразделения</div>
		<div style="width: 10%;">Удал.</div>
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="3">
<?php
$arDepartments = array();

$arDepartments = MSystem::GetDepartmentsList();

foreach($arDepartments as $key => $department)
{
	echo "<div class='table-row' data-row-id='".$key."'>";
	echo "<div style='width: 6%; text-align: center;'>".$key."</div>";
	echo "<div style='width: 84%'><a href='#' class='info'>".($department['name']!='' ? $department['name']: '')."</a><input type='text' name='name'/></div>";
	
	//echo "<div style='width: 10%; text-align: center;'><a href='#' class='info' class='info' data-id='".$user['ACTIVE']."'>".($user['ACTIVE']= 1 ? "Да":"Нет")."</a><select name='activity'>";
	//echo "<option value='1' ".($user['ACTIVE'] == 1 ? "selected":"").">Да</option>";
	//echo "<option value='0' ".($user['ACTIVE']!=1  ? "selected":"").">Нет</option>";
	//echo "</select></div>";
	echo "<div style='width: 10%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
}

?>
</div>
</div>
