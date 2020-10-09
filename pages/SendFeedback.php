<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
if(!isset($_POST['subject']) || !SymbolSecur($_POST['subject'])
	|| !isset($_POST['message']) || !SymbolSecur($_POST['message'])
	|| !isset($_POST['filedata']) || !SymbolSecur($_POST['filedata']) )
{
	exit('Отправленные данные некорректны.');
}

$subject = $_POST['subject'];
$text = $_POST['message'];
$file = array();
$file[]['value'] = $_POST['filedata'];
$file_path = '';
$success = false;
$errorMessage = '';

if(isset($_SESSION['UID']))
{
    
	$arData = array(
		"subject" => $subject,
		"text" => $text,
	);
	
	if($_POST['filedata']!='')
	{
		$file_result = SaveFiles($file);
		if($file_result['status'])
		{
			if(isset($file_result['content'][0]))
			{
				$file_path = $file_result['content'][0];
			}
		}
	}
	//CharacterFilter
	
	$NewMessResult = MSystem::SendFeedback($_SESSION['UID'],CharacterFilter($subject),CharacterFilter($text),$file_path);
	if($NewMessResult['status'])
	{
		$success = true;
	}
	else
	{
		$errorMessage = $NewMessResult['message'];
	}
			
}
else
{
	exit('Пользователь не определен');
}
?>

<?php if($success): ?>
	<div class="header-panel">
		<a href="#" class="icon-left-open-big slide-back" id="back_lk">Кабинет</a>
		<div>Отправлено</div>
	</div>
	
	<div class="container">
		<div class="green-blk">
			<b>Ваше сообщение отправлено в техническую поддержку пользователей.</b>
		</div>
	</div>
<?php else:?>
	<div class="header-panel">
		<a href="#" class="icon-left-open-big slide-back" id="back_lk">Кабинет</a>
		<div>Ошибка!</div>
	</div>
	<div class="main_error">
		<b><?php echo $errorMessage ?></b><br/>Попробуйте повторить отправку еще раз. 
	</div>
<?php endif; ?>


