<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}


$arCategories = array();
$arCategories = MSystem::GetCategoriesWithOrg();
$arOrg = MSystem::GetOrganizationList();
?>

<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 10%;" data-field="name" data-required>Наименование категории</div>
		<div style="width: 10%;" data-field="caption">Подпись при выборе</div>
		<div style="width: 10%;" data-field="description">Описание</div>
		<div style="width: 10%;" data-field="icon">Код векторной иконки</div>
		<div style="width: 10%;" data-field="yandex_icon">Тип иконки yandex</div>
		
		<div style="width: 10%; text-align: center;" data-field="deadline">
			<span>Контрольный срок (дней)</span>
			<div class="hidden-select" id="deadline_select">
				<select>
					<option value='0'>0</option>
					<option value='5'>5</option>
					<option value='6'>6</option>
					<option value='7'>7</option>
					<option value='8'>8</option>
					<option value='9'>9</option>
					<option value='10'>10</option>
					<option value='11'>11</option>
					<option value='12'>12</option>
					<option value='13'>13</option>
					<option value='14'>14</option>
					<option value='15'>15</option>
					<option value='16'>16</option>
					<option value='17'>17</option>
					<option value='18'>18</option>
					<option value='19'>19</option>
					<option value='20'>20</option>
					<option value='21'>21</option>
					<option value='22'>22</option>
					<option value='23'>23</option>
					<option value='24'>24</option>
					<option value='25'>25</option>
					<option value='26'>26</option>
					<option value='27'>27</option>
					<option value='28'>28</option>
					<option value='29'>29</option>
					<option value='30'>30</option>
				</select>
			</div>
		</div>
		
		<div style="width: 15%;" data-field="org_id" title="Если к категории привязана закрепленная организация, при создании сообщения в этой категории - ответственная организация будет определена автоматически.">
			<span>Закрепл. организация</span>
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
<div class="table body editable" data-table-id="5">
<?php

foreach($arCategories as $key => $categories)
{
	$color = '';
	if($categories['activity']==1)
	{
		$color = 'background: #cdf7c2;';
	}
	echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
		echo "<div style='width: 5%; text-align: center;'>".$key."</div>";
		echo "<div style='width: 10%'><a href='#' class='info searchable'>".($categories['name']!='' ? $categories['name']: '')."</a><input type='text' name='name'/></div>";
		echo "<div style='width: 10%'><a href='#' class='info'>".($categories['caption']!='' ? $categories['caption']: '')."</a><input type='text' name='caption'/></div>";
		echo "<div style='width: 10%'><a href='#' class='info'>".($categories['description']!='' ? $categories['description']: '')."</a><input type='text' name='description'/></div>";
		echo "<div style='width: 10%'><a href='#' class='info'>".($categories['icon']!='' ? $categories['icon']: '')."</a><input type='text' name='icon'/></div>";
		echo "<div style='width: 10%'><a href='#' class='info'>".($categories['yandex_icon']!='' ? $categories['yandex_icon']: '')."</a><input type='text' name='yandex_icon'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#deadline_select' class='info' data-id='".$categories['deadline']."'>".$categories['deadline']."</a><input type='hidden' name='deadline' value='".$categories['deadline']."'/></div>";
		echo "<div style='width: 15%'><a href='#org_select' class='info' data-id='".$categories['org_id']."'>".($categories['org_name']!='' ? $categories['org_name']: '')."</a><input type='hidden' name='org_id' value='".$categories['org_id']."'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#activity_select' class='info' class='info' data-id='".$categories['activity']."'>".($categories['activity']== 1 ? "Да":"Нет")."</a><input type='hidden' name='activity' value='".$categories['activity']."'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#' class='info icon-cancel option-delete-link'></a></div>";
	echo "</div>";
}

?>
</div>
</div>