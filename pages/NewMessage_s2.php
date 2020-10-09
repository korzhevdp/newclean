<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
if(isset($_POST['category']) && is_numeric($_POST['category']))
{
    $arCategory =  Messages::GetCategory($_POST['category']);
    $arCategory = $arCategory[$_POST['category']];
    $caption = 'нарушение или проблему';
    if(isset($arCategory['caption']) && $arCategory['caption']!='')
    {
        $caption = $arCategory['caption'];
    }
    
    $Caption = 'Укажите на карте, где Вы зафиксировали '.$caption.'. Добавьте или перетащите маркер в нужную точку.';
}
else
{
    exit('Идентификатор категории неопределен. Обновите страницу и попробуйте создать сообщение еще раз.');
}
?>

<div class="header-panel">
    <a href="#" class="icon-left-open-big slide-back" id="back_category">Назад</a>
    <div>Местоположение</div>
</div>

<p class="block little-text"><?php echo $Caption;  ?></p>
<input type="hidden" name="coord_x" value=""/>
<input type="hidden" name="coord_y" value=""/>
<input type="hidden" name="address" value=""/>
<input type="hidden" name="district" value=""/>
<input type="hidden" name="category" value="<?php echo $_POST['category']?>"/>
<div id="map"></div>
<div class="container map-btn">
    <a href="#" class="btn" id="message_info">Далее</a>
</div>
<script src="scripts/mapPoint.js?v=1.96"></script>