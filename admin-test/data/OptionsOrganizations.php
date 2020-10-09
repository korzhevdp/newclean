<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
if(isset($_SESSION['UID']))
{
	$arUser = Users::GetUserById($_SESSION['UID']);
	$arUser = $arUser[$_SESSION['UID']];
}

if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}

$arOrg = array();
$arDepartments = array();

$arOrg = MSystem::GetOrganizationList();
$arDepartments = MSystem::GetDepartmentsList();

//print_r($arOrg);

?>
<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 15%;" data-field="name" data-required>Наименование</div>
		<div style="width: 25%;" data-field="address">Адрес</div>
		<div style="width: 10%;" data-field="house_count">Кол-во домов</div>
		<div style="width: 25%;" data-field="department_id">
			<span>Контролирующее подразделение</span>
			<div class="hidden-select" id="department_select_more">
				<div class="select multiple-select">
						<?php
							foreach($arDepartments as $key => $department)
							{
								echo "<a href='#' data-depart-id='".$key."'>".$department['name']."</a>";
							}
						?>
				</div>
			</div>
		</div>
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
<div class="table body editable" data-table-id="4">
<?php
foreach($arOrg as $key => $org)
{
	echo "<div class='table-row' data-row-id='".$key."'>";
	echo "<div style='width: 5%; text-align: center;'>".$key."</div>";
	echo "<div style='width: 15%'><a href='#' class='info searchable'>".($org['name']!='' ? $org['name']: '')."</a><input type='text' name='name'/></div>";
	echo "<div style='width: 25%'><a href='#' class='info'>".($org['address']!='' ? $org['address']: '')."</a><input type='text' name='address'/></div>";
	echo "<div style='width: 10%; text-align: center;'><a href='#' class='info'>".($org['house_count']!='' ? $org['house_count']: '')."</a><input type='text' name='house_count'/></div>";
	
	$strDepartments = '';
	if(count($org['departments']) > 0)
	{
		foreach($org['departments'] as $key => $departName)
		{
			if($key>0) $strDepartments .=", ";
			if($departName!='')
			{
				$strDepartments .= "<b>(".($key+1).")</b>&nbsp;".$departName;
			}
		}
	}
	echo "<div style='width: 25%'><a href='#department_select_more' class='info' data-type='multiple'>".($strDepartments!='' ? $strDepartments : '')."</a></div>";
	
	echo "<div style='width: 10%; text-align: center;'><a href='#activity_select' class='info' class='info' data-id='".$org['activity']."'>".($org['activity']== 1 ? "Да":"Нет")."</a><input type='hidden' name='activity' value='".$org['activity']."'/></div>";
	echo "<div style='width: 10%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
}

?>
<script>
	$(function() {
		$('.mwindow-empty-container').html('(Всего организаций - <?php echo count($arOrg) ?>)');
	});
</script>
</div>
</div>