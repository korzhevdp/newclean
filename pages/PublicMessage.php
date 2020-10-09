<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
if(!isset($_POST['coord_x']) || !isset($_POST['coord_y']))
	exit('Не удалось определить координаты места. Попробуйте опубликовать сообщение повторно.');
if(!isset($_POST['category']) || !is_numeric($_POST['category']))
	exit('Категория сообщения неопределена.');
if(!filter_var($_POST['coord_x'],FILTER_VALIDATE_FLOAT) || !filter_var($_POST['coord_y'],FILTER_VALIDATE_FLOAT)) 
    exit('Переданные координаты места некорректны. Попробуйте опубликовать сообщение повторно.');
if(!isset($_POST['address']) || !SymbolSecur($_POST['address']))
	exit('Ориентировочный адрес местоположения некорректен');
if(!isset($_POST['district']) || !SymbolSecur($_POST['district']))
	exit('Район города некорректен');
if(!isset($_POST['message']) || !SymbolSecur($_POST['message']))
	exit('Текст сообщения не найден или некорректен.');
if(!isset($_POST['files']) || count($_POST['files'])==0)
	exit('При попытке публикации сообщения не удалось обнаружить ни одной фотографии.');

$coord_x = $_POST['coord_x'];
$coord_y = $_POST['coord_y'];
$category = $_POST['category'];
$message = CharacterFilter($_POST['message']);
$address = CharacterFilter($_POST['address']);
$post_district = CharacterFilter($_POST['district']);
$arFiles = $_POST['files'];
$success = false;
$district_id = 0;
$org_id = 0;

if(isset($_SESSION['UID']))
{
    $file_result = SaveFiles($arFiles);
	$arDistricts = Messages::GetDistrict();
	$arOrg = MSystem::GetOrgByCategoryId($category);
	$org_id = 0;
	$depart_id = 0;
	if(isset($arOrg['org_id']) && $arOrg['org_id']>0) // автоматическое определение ответственной организации, если присутствует привязка к категории
	{
		$org_id = $arOrg['org_id'];
	}
	
	if(isset($arOrg['depart_id']) && $arOrg['depart_id']>0) // автоматическое определение ответственной организации, если присутствует привязка к категории
	{
		$depart_id = $arOrg['depart_id'];
	}
	   
    $errorMessage = '';

    $district_id = 0;
    if(count($arDistricts)>0)
	{
		foreach($arDistricts as $key => $district)
		{
			$findDistrict = stripos($district, $post_district);
			if($findDistrict!==false)
				$district_id = $key;
		}
    }
    //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", var_export($file_result, true), FILE_APPEND);
	if($file_result['status'])
	{
		if($files = json_encode($file_result['content']))
		{
			$arData = array(
				"user_id" => $_SESSION['UID'],
				"coord_x" => $coord_x,
				"coord_y" => $coord_y,
				"address" => $address,
				"district_id" => $district_id,
				"depart_id" => $depart_id,
				"org_id" => $org_id,
				"message" => $message,
				"files" => $files,
				"category_id" => $category
			);
		
			$NewMessResult = Messages::NewMessage($arData);
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
		$errorMessage = 'Не получилось определить набор изображений';
	}
	else
	$errorMessage = $file_result['message'];
}
else
exit('Пользователь не определен');
?>

<?php if($success): ?>
<div class="panels active">
	<div class="panel-container">
		<div class="header-panel sticky">
			<a href="#" class="icon-left-open-big slide-back" id="back_lk">Кабинет</a>
			<div>Сообщ. принято</div>
		</div>
		
		<div class="container">
			<div class="green-blk">
				<b>Ваше сообщение принято.</b> 
			</div>
			<div class="gray-cont">
				<a href="#" id="info-after-message" class="bl-link icon-question">Что дальше?</a>
			</div>
			<div class="info-after-message">
				<div>К системе подключены все администрации территориальных округов города Архангельска.</div>
				<div>1. После проверки Вашего сообщения будет назначено ответственное подразделение и ответственная организация (управляющая компания, ресурсоснабжающая организация).</div>
				<div>2. В личном кабинете Вы можете просматривать свои сообщения в списке или на карте, осуществляя контроль за их текущим статусом.</div>
				<div>3. В результате устранения замечания или отклонения ответственное подразделение изменит статус сообщения.</div>
			</div>
		</div>
	</div>
</div>
<?php else:?>
	<div class="header-panel">
		<a href="#" class="icon-left-open-big slide-back" id="back_lk">Кабинет</a>
		<div>Ошибка!</div>
	</div>
	<div class="main_error">
		<b><?php echo $errorMessage ?></b><br/> Попробуйте повторить публикацию еще раз. 
	</div>
	<div class="container">
		<div class="gray-cont">
			<a href="#" id="message" class="bl-link icon-bars">Повторить еще раз</a>
		</div>
	</div>
<?php endif; ?>


