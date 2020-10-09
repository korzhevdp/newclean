<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php
if(!isset($_POST['message_id']) || !is_numeric($_POST['message_id']))
{
    exit('Не удалось получить информацию о данном сообщении');
}
    
$message_id = $_POST['message_id'];
$arMessages =  Messages::getOneMessage($_SESSION['UID'],$message_id,1);
$arMessages = $arMessages[$message_id];

if(isset($arMessages['org_id']))
{
    $arOrgData = MSystem::getOrganizationData($arMessages['org_id']);
}


?>

<div class="header-panel sticky">
    <a href="#" class="icon-left-open-big slide-back">Назад</a>
    <div>Сообщение № <?php echo $arMessages['id'] ?></div>
</div>
<div class="container">
    <?php
        echo '<p>Изменено '.$arMessages["create_time"].'</p>';
        echo '<div class="status '.$arMessages["status_icon"].' '.$arMessages["status_color"].'">'.$arMessages["status"];
        echo '</div>';
        if($arMessages['answer']!='')
        {
            echo '<div class="answer-text"><b>Ответ для Вас от исполнителя:</b><br/>'.$arMessages['answer'];
            if($arMessages['answer_file_path']!='')
            {
                $arMessages['answer_file_path'] = 'admin/'.$arMessages['answer_file_path'];
                echo '<div class="preview-answer-img" style="background-image: url(\''.$arMessages['answer_file_path'].'\');"></div>';
            }
            echo '</div>';
        }
        

        if($arMessages['responsible']!='')
        {
            echo '<p>'.$arMessages['responsible'].'</p><div class="separ"></div>';
        }
        
        if($arMessages['depart_name']!='')
        {
            echo 'Контролирующий департамент:';
            echo '<div>'.$arMessages['depart_name'].'</div><div class="separ"></div>';
        }
        
        if($arMessages['org_name']!='')
        {
            echo 'Ответственная организация: <br>';
            if(isset($arMessages['org_id']) && $arMessages['org_id']!='')
            {
                echo '<span class="qinfo">информация не проверена</span>';
            }
            
            echo '<div>'.$arMessages['org_name'].'</div>';
            if(is_array($arOrgData) && count($arOrgData)> 0)
            {
                echo '<div>';
                if(isset($arOrgData['inn']) && $arOrgData['inn']!='' && $arOrgData['inn']!='null')
                {
                    echo '<b>ИНН</b>: '.$arOrgData['inn'].'<br/>';
                }
                if(isset($arOrgData['address']) && $arOrgData['address']!='' && $arOrgData['address']!='null')
                {
                    echo '<b>Адрес</b>: '.$arOrgData['address'].'<br/>';
                }
                if(isset($arOrgData['phone']) && $arOrgData['phone']!='' && $arOrgData['phone']!='null')
                {
                    echo '<b>Тел.</b>: '.$arOrgData['phone'].'<br/>';
                }
                if(isset($arOrgData['email']) && $arOrgData['email']!='' && $arOrgData['email']!='null')
                {
                    echo '<b>Email</b>: '.$arOrgData['email'].'<br/>';
                }
                if(isset($arOrgData['house_count']) && $arOrgData['house_count']!='0' && $arOrgData['house_count']!='null')
                {
                    echo '<b>Домов в обслуживании</b>: '.$arOrgData['house_count'].'<br/>';
                }
                echo '</div>';
            }
            echo '<div class="separ"></div>';
        }
        
        if($arMessages['result_time']!='')
        {
            echo 'Устранить до:';
            echo '<div><b>'.str_replace(" в 00:00","",$arMessages['result_time']).'</b></div><div class="separ"></div>';
        }
            
        echo '<p class="mgtop20px"><b>'.$arMessages['category'].'</b></p>';
        echo $arMessages['text'];
        echo '<div class="mess-address">'.$arMessages['address'].'</div>';
    ?>
    <div class="photo-files">
        <?php
        $file_count = 0;
        foreach($arMessages['files'] as $key => $file)
        {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$file['path']))
            {
                echo '<a href="'.$file['path'].'" class="ph-item" data-w="'.$file['w'].'" data-h="'.$file['h'].'" style="background-image: url('.$file['path'].')"></a>';
                $file_count++;
            }
        }
        if($file_count===0)
          echo '<div class="empty-block">Не удалось загрузить фотографии места</div>';
        ?>
    </div>
</div>
<?php
    require_once("plugins/photoswipe/photoSwipeTemp.html");
?>