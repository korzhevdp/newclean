<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Все сообщения</div>
    <a href="#" class="add-btn" id="message"></a>
</div>
<div class="search icon-search">
    <input type="text" placeholder="Начните вводить..."/>
</div>

<?php
$arMessages =  Messages::UsersMessagesList(0,0);
$mess_count = count($arMessages);

if($mess_count > 0): ?>
    <div class="container">
        <br/>
        <a href="#" class="btn icon-pin" id="all_message_map">На карте (<?php echo $mess_count; ?>)</a>
    </div>
    
    <div class="items">
    <?php
        foreach($arMessages as $key => $message)
        {
            if(strlen($message)>50)
                $message = substr_unicode($message, 0,50).'...';
            echo '<a href="#" class="icon-mail-1 one_message_panel" data-id="'.$key.'">'.$message.'</a>';
        }
    ?>
    </div>
<?php else: ?>
    <p class="empty-page">Пока никто не создавал сообщение</p>
    <div class="container">
        <a href="#" class="btn icon-mail" id="message">Создать сообщение</a>
    </div>
<?php endif; ?>