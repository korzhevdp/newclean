<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}

$arUsers = array();
$arDepartments = array();
$arGroups = array();

$arUsers = MSystem::GetUsersList();
$arDepartments = MSystem::GetDepartmentsList();
$arOrg = MSystem::GetOrganizationList();
$arGroups = MSystem::GetGroupsList();

?>
<div class="table header">
	<div class="table-row">
		<div style="width: 3%;">#</div>
		<div style="width: 15%;" data-field="email" data-required>E-mail / логин</div>
		<div style="width: 10%;">Посл. аутентиф.</div>
		<div style="width: 15%;" data-field="alias" data-required>Имя пользователя</div>
		<div style="width: 7%;  text-align: center;">Кол-во сообщ.</div>
		<div style="width: 13%;" data-field="group_id">
			<span>Группа</span>
			<div class="hidden-select" id="group_select">
				<select>
				<?php
					foreach($arGroups as $key => $group)
					{
						echo "<option value='".$key."'>".$group['name']."</option>";
					}
				?>
				</select>
			</div>
		</div>
		<div style="width: 15%;" data-field="department_id">
			<span>Подразделение</span>
			<div class="hidden-select" id="department_select">
				<select>
				<?php
					echo "<option value='0'></option>";
					foreach($arDepartments as $key => $department)
					{
						echo "<option value='".$key."'>".$department['name']."</option>";
					}
				?>
				</select>
			</div>
		</div>
		<div style="width: 10%;" data-field="org_id">
			<span>Организация</span>
			<div class="hidden-select" id="org_select">
				<select>
				<?php
					echo "<option value='0'></option>";
					foreach($arOrg as $key => $org)
					{
						echo "<option value='".$key."'>".$org['name']."</option>";
					}
				?>
				</select>
			</div>
		</div>
		<div style="width: 5%;  text-align: center;" data-field="activity">
			<span>Актив.</span>
			<div class="hidden-select" id="activity_select">
				<select>
					<option value='1'>Да</option>
					<option value='0'>Нет</option>
				</select>
			</div>
		</div>
		<div style='width: 7%; text-align: center;'>Удал.</div>
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="1">
<?php

$simple_users = 0;
foreach($arUsers as $key => $user)
{
	$color = '';
	if($user['GROUP_ID']>1)
	{
		$color = 'background: #f7e0c2;';
	}
	echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
		echo "<div style='width: 3%'><a href='#' class='info searchable'>".$key."</a></div>";
		echo "<div style='width: 15%'><a href='#' class='info searchable' title='".$user['PHONE']."'>".($user['EMAIL']!='' ? $user['EMAIL']: '')."</a><input type='text' name='email'/></div>";
		echo "<div style='width: 10%'><a href='#' class='info'>".($user['AUTH_DATE']!='' ? $user['AUTH_DATE']: '')."</a></div>";
		echo "<div style='width: 15%'><a href='#' class='info searchable'>".($user['ALIAS']!='' ? $user['ALIAS']: '')."</a><input type='text' name='alias'/></div>";
		
		echo "<div style='width: 7%;  text-align: center;'><a href='#' class='info'>".($user['MESSAGE_COUNT']!=0 ? "<b style='color:#19922f; font-size: 1.5em;'>".$user['MESSAGE_COUNT']."</b>": "")."</a></div>";
		
		echo "<div style='width: 13%'><a href='#group_select' class='info' data-id='".$user['GROUP_ID']."'>".($user['GROUP_NAME']!='' ? $user['GROUP_NAME']: '')."</a><input type='hidden' name='group_id' value='".$user['GROUP_ID']."'/></div>";
		echo "<div style='width: 15%'><a href='#department_select' class='info' data-id='".$user['DEPARTMENT_ID']."'>".($user['DEPARTMENT_NAME']!='' ? $user['DEPARTMENT_NAME']: '')."</a><input type='hidden' name='department_id' value='".$user['DEPARTMENT_ID']."'/></div>";
		echo "<div style='width: 10%'><a href='#org_select' class='info' data-id='".$user['ORG_ID']."'>".($user['ORG_NAME']!='' ? $user['ORG_NAME']: '')."</a><input type='hidden' name='org_id' value='".$user['ORG_ID']."'/></div>";
		echo "<div style='width: 5%; text-align: center;'><a href='#activity_select' class='info' class='info' data-id='".$user['ACTIVE']."'>".($user['ACTIVE']== 1 ? "Да":"Нет")."</a><input type='hidden' name='activity' value='".$user['ACTIVE']."'/></div>";
		echo "<div style='width: 7%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
	if($user['GROUP_ID']==1)
	{
		$simple_users++;
	}
}
?>
</div>
</div>
<script type="text/javascript" src="/plugins/inputmask/jquery.inputmask.js"></script>
<script>
	$(function() {
		$('input[name="phone"]').inputmask("+7 (999) 999-99-99", {
			clearMaskOnLostFocus: false
		});
		$('.mwindow-empty-container').html('(Всего пользователей - <?php echo count($arUsers) ?>, граждан <?php echo $simple_users ?>)');
	});
</script>