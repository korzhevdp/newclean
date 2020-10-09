<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<?php
if(!isset($_POST['coord_x']) || !isset($_POST['coord_y']))
	exit('Не удалось определить координаты места.');

$address = '';
$district = '';
if(isset($_POST['address'])) 
    $address = $_POST['address'];
	
if(isset($_POST['district']) && $_POST['district']!='')
{
    $district = $_POST['district'];
	if($address!='')
	{
		$address = $address.', '.$district; 
	}
	else
	{
		$address = $district;
	}
}

if(!filter_var($_POST['coord_x'],FILTER_VALIDATE_FLOAT) || !filter_var($_POST['coord_y'],FILTER_VALIDATE_FLOAT)) 
    exit('Переданные координаты места некорректны.');
    
if(!isset($_POST['category']) || !is_numeric($_POST['category']))
	exit('Категория сообщения неопределена.');

?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back" data-id="<?php echo $category?>" id="back_map">Назад</a>
    <div>Новое сообщение</div>
</div>

<div class="container mess_cont">
	<?php if($address!=''): ?>
		<p class="little-text icon-marker address-text">Выбрано местоположение: <br><?php echo $address ?></p>
	<?php else: ?>
		<p class="little-text icon-exclamation address-text">Не удалось определить адрес. Рекомендуем уточнить на карте<?php echo $address ?></p>
	<?php endif;?>
	<div class="gray-cont">
		<p class="little-text">Не более 3 фото (до 5 Мб)</p>
		<div id="preview-photo" class="photo-cont">

		</div>
		<a href="#" id="getfile" class="btn icon-plus">Добавить фото</a>
		<input id="inputfile" type="file" accept="image/*">
	</input>

	<textarea type="text" name="message" placeholder="Введите информацию о выявленном нарушение, укажите точный адрес в тексте" name="message-text"></textarea>
	<span class="little-text">Если Вам известно, какая организация должна нести ответственность за выявленное нарушение, укажите данную информацию в комментарии.</span>
	<a href="#" class="btn" id="public">Опубликовать</a>

</div>

<script src="plugins/uploadphoto.js?q=2.2"></script>
