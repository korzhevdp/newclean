<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у вас недостаточно прав для доступа к данному разделу.  Панель настроект находится на стадии разработки и доступна только системным администраторам</div>');
}


$arMessages = array();
$arMessages = Messages::GetFullMessagesList();

//print_r($arMessages);
?>

<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 30%;" data-field="message" data-required>Описание</div>
		<div style="width: 10%;" data-field="user_id">ИД пользователя</div>
		<div style="width: 20%;" data-field="address">Адрес</div>
		
		<div style="width: 10%;" data-field="coord_x">Коорд. X</div>
		<div style="width: 10%;" data-field="coord_y">Коорд. Y</div>
		
		<div style="width: 15%; text-align: center;" data-field="archive">
			<span>В архиве</span>
			<div class="hidden-select" id="archive_select">
				<select>
					<option value='1'>Да</option>
					<option value='0'>Нет</option>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="8">
<?php

$archive_count = 0;
foreach($arMessages as $key => $message)
{
	$color = '';
	if($message['archive']==1)
	{
		$archive_count++;
		$color = 'background: #f7e0c2;';
	}
	
	
	echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
		echo "<div style='width: 5%; text-align: center;'><a href='#' class='info searchable'>".$key."</a></div>";
		echo "<div style='width: 30%'><a href='#' class='info searchable'>".($message['message']!='' ? $message['message']: '')."</a><input type='text' name='message'/></div>";
		echo "<div style='width: 10%; text-align: center;'><a href='#' class='info searchable' class='info' title='".$message['user_alias']."'>".($message['user_id']!='' ? $message['user_id']: '')."</a></div>";
		echo "<div style='width: 20%;'><a href='#' class='info'>".($message['address']!='' ? $message['address']: '')."</a><input type='text' name='address'/></div>";

		echo "<div style='width: 10%;'><a href='#' class='info'>".($message['coord_x']!='' ? $message['coord_x']: '')."</a><input type='text' name='coord_x'/></div>";
		echo "<div style='width: 10%;'><a href='#' class='info'>".($message['coord_y']!='' ? $message['coord_y']: '')."</a><input type='text' name='coord_y'/></div>";
		echo "<div style='width: 15%; text-align: center;'><a href='#archive_select' class='info' data-id='".$message['archive']."'>".($message['archive']== 1 ? "Да":"Нет")."</a><input type='hidden' name='archive' value='".$message['archive']."'/></div>";
	echo "</div>";
}


?>
</div>
</div>
<script>
	$(function() {
		$('.mwindow-empty-container').html('(Всего сообщений - <?php echo count($arMessages) ?>, из них в архиве <?php echo $archive_count ?>)');
	});
</script>