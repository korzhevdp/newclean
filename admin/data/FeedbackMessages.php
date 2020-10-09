<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];
if(!Users::isAdmin($arUser['group_id']))
{
	exit('<div class="pg10px">Извините, у Вас недостаточно прав для доступа к данному разделу.  Панель настроек на стадии разработки и доступна только системным администраторам.</div>');
}


$arMessages = array();
$arMessages = MSystem::GetFeedbackMessages();

//print_r($arMessages);
?>

<div class="table header">
	<div class="table-row">
		<div style="width: 5%; text-align: center;">#</div>
		<div style="width: 15%;">Тема</div>
		<div style="width: 20%;">Комментарий</div>
		<div style="width: 15%;">Имя пользователя</div>
		<div style="width: 10%;">Email</div>
		<div style="width: 22%;">Скриншот</div>
		<div style="width: 7%;">Время отправки</div>
		<div style="width: 6%; text-align: center;" data-field="answered">
			<span>Отвечено</span>
			<div class="hidden-select" id="answered_select">
				<select>
					<option value='1'>Да</option>
					<option value='0'>Нет</option>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="table-scroller">
<div class="table body editable" data-table-id="10">
<?php


foreach($arMessages as $key => $message)
{
	$color = '';
	if($message['answered']==1)
	{
		$color = 'background: #cdf7c2;';
	}
	
	echo "<div class='table-row' data-row-id='".$key."' style='".$color."'>";
		echo "<div style='width: 5%; text-align: center;'><a href='#' class='info searchable'>".$key."</a></div>";
		echo "<div style='width: 15%'><a href='#' class='info searchable'>".($message['subject']!='' ? $message['subject']: '')."</a></div>";
		echo "<div style='width: 20%'><a href='#' class='info searchable'>".($message['text']!='' ? $message['text']: '')."</a></div>";
		echo "<div style='width: 15%'><a href='#' class='info searchable'>".($message['alias']!='' ? $message['alias']: '')."</a></div>";
		echo "<div style='width: 10%'><a href='#' class='info searchable'>".($message['email']!='' ? $message['email'].' ('.$message['user_id'].')': '')."</a></div>";
		echo "<div style='width: 22%'><a href='#' class='info searchable'>".($message['file_path']!='' ? '<img style="max-width: 200px;" src="/'.$message['file_path'].'">': '')."</a></div>";
		echo "<div style='width: 7%'><a href='#' class='info'>".($message['create_date']!='' ? $message['create_date']: '')."</a></div>";
		echo "<div style='width: 6%; text-align: center;'><a href='#answered_select' class='info' data-id='".$message['answered']."'>".($message['answered']== 1 ? "Да":"Нет")."</a><input type='hidden' name='answered' value='".$message['answered']."'/></div>";
	echo "</div>";
}


?>
</div>
</div>
<script>
	$(function() {
		$('.mwindow-empty-container').html('(Всего обращений - <?php echo count($arMessages) ?>)');
	});
</script>
