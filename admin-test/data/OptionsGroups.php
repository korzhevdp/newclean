<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
?>
<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 20%;" data-field="name" data-required>Наименование группы</div>
		<div style="width: 21%;" data-field="caption" data-required>Надпись для пользователя</div>
		
		<div style="width: 6%;" data-field="law1" title="Просмотр всех сообщений, зарегистрированных в системе">
			<span>LW1</span>
			<div class="hidden-select" id="law_1_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law2" title="Просмотр всех сообщений, проверенных модератором и опубликованных в системе">
			<span>LW2</span>
			<div class="hidden-select" id="law_2_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law3" title="Возможность изменять статус сообщений">
			<span>LW3</span>
			<div class="hidden-select" id="law_3_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law4" title="Возможность назначать организацию, ответственную за устранение выявленных в сообщении проблем">
			<span>LW4</span>
			<div class="hidden-select" id="law_4_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law8" title="Возможность определять срок выполнения">
			<span>LW8</span>
			<div class="hidden-select" id="law_8_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law5" title="Возможность отправлять сообщения в архив">
			<span>LW5</span>
			<div class="hidden-select" id="law_5_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law6" title="Просмотр общей статистики сообщений по различным критериям">
			<span>LW6</span>
			<div class="hidden-select" id="law_6_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law7" title="Доступ в раздел администрирования. Данное право определяет возможность входа пользователя в раздел '/admin/' (Без указания дополнительных прав будет только просмотр)">
			<span>LW7</span>
			<div class="hidden-select" id="law_7_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<div style="width: 6%;" data-field="law9" title="Доступ к системным настройкам">
			<span>LW9</span>
			<div class="hidden-select" id="law_9_select">
				<select>
					<option value='0'>Нет</option>
					<option value='1'>Да</option>
				</select>
			</div>
		</div>
		<!--<div style="width: 7%;">Удал.</div>-->
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="2">
<?php
$arUsersGroups = array();

$arUsersGroups = MSystem::GetUsersGroupList();

foreach($arUsersGroups as $key => $group)
{
	echo "<div class='table-row' data-row-id='".$key."'>";
	echo "<div style='width: 5%; text-align: center;'>".$key."</div>";
	echo "<div style='width: 20%'><a href='#' class='info'>".($group['name']!='' ? $group['name']: '')."</a><input type='text' name='name'/></div>";
	echo "<div style='width: 21%'><a href='#' class='info'>".($group['caption']!='' ? $group['caption']: '')."</a><input type='text' name='caption'/></div>";
	echo "<div style='width: 6%;'><a href='#law_1_select' class='info color' data-id='".$group['law1']."'>".($group['law1']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law1' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_2_select' class='info color' data-id='".$group['law2']."'>".($group['law2']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law2' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_3_select' class='info color' data-id='".$group['law3']."'>".($group['law3']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law3' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_4_select' class='info color' data-id='".$group['law4']."'>".($group['law4']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law4' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_8_select' class='info color' data-id='".$group['law8']."'>".($group['law8']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law8' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_5_select' class='info color' data-id='".$group['law5']."'>".($group['law5']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law5' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_6_select' class='info color' data-id='".$group['law6']."'>".($group['law6']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law6' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_7_select' class='info color' data-id='".$group['law7']."'>".($group['law7']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law7' value=''/></div>";
	echo "<div style='width: 6%;'><a href='#law_9_select' class='info color' data-id='".$group['law9']."'>".($group['law9']== 1 ? "Да":"Нет")."</a><input type='hidden' name='law9' value=''/></div>";
	//echo "<div style='width: 10%; text-align: center;'><a href='#' class='info' class='info' data-id='".$user['ACTIVE']."'>".($user['ACTIVE']= 1 ? "Да":"Нет")."</a><select name='activity'>";
	//echo "<option value='1' ".($user['ACTIVE'] == 1 ? "selected":"").">Да</option>";
	//echo "<option value='0' ".($user['ACTIVE']!=1  ? "selected":"").">Нет</option>";
	//echo "</select></div>";
	//echo "<div style='width: 7%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
}

?>
</div>
</div>