<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Мои сообщения</div>
    <a href="#" class="add-btn" id="message"></a>
</div>
<div class="search icon-search">
    <input type="text" placeholder="Начните вводить..."/>
</div>

<?php
if(!isset($_SESSION['UID']))
    exit('Пользователь неопределен'); 

$arMessages =  Messages::UsersMessagesList($_SESSION['UID'],0);
$mess_count = count($arMessages);

if($mess_count > 0): ?>
    <div class="container">
        <br/>
        <a href="#" class="btn icon-pin" id="my_message_map">Все на карте (<?php echo $mess_count; ?>)</a>
    </div>
    
    <div class="messages items gr-border">
    <?php
        foreach($arMessages as $key => $message)
        {
            $message_text = $message['message'];
            $mstatus_id = $message['status_id'];
            
            $status_ok = '';
            $disabled = '';
            
            if($mstatus_id == 2)
            {
                $status_ok = ' green-mlink';
            }
            
            if(strlen($message_text)>17)
                $message_text = substr_unicode($message_text, 0,22).'...';
            if(!in_array($mstatus_id,array(5,6)))
            {
                $disabled = 'disabled';
            }
            echo '<div><a href="#" class="mlink icon-mail-1 one_message_panel '.$status_ok.'" data-id="'.$key.'"><b>'.$key.'</b> '.$message_text.'</a><a href="#" class="delete '.$disabled.' '.$status_ok.'" data-status-id="'.$mstatus_id.'" data-delete-id="'.$key.'"></a></div>';
        }
    ?>
    </div>
<?php else: ?>
    <p class="empty-page">Вы еще не создавали сообщение</p>
    <div class="container">
        <a href="#" class="btn icon-mail" id="message">Создать сообщение</a>
    </div>
<?php endif; ?>