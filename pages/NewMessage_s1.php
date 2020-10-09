<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

    $arCategory =  Messages::GetCategory();
    
    $count = count($arCategory);
    $caption = 'Выберите категорию сообщения в соответствии с правилами.';
    if($count==1)
    {
        $caption = 'На данный момент доступна только одна категория';
    }
?>



<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Выбор категории</div>
</div>
<?php if(count($arCategory)<=0) exit('<p class="empty-page">Нет доступных для выбора категорий</p>'); ?>
<div class="container">
    <p><?php echo $caption ?></p>
</div>
<div class="items gr-border">
    <?php foreach($arCategory as $key => $category): ?>
        <a href="#" data-id="<?php echo $key?>" data-description="<?php echo trim($category['description']) ?>" class="mes-category<?php echo $category['icon'] ?>">
            <?php echo $category['name'] ?>
        </a>
        <?php if(trim($category['description'])!=''): ?>
            <div class="mess-cat-description" data-cat="<?php echo $key; ?>">
                <?php echo trim($category['description']) ?>
                <a href="#" class="btn cat-selection" data-id="<?php echo $key?>">Выбрать</a>
            </div>
        <?php endif; ?>
        
    <?php endforeach; ?>
</div>
