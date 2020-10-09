<?php if(!isset($AccessIndex) || !$AccessIndex) exit('Контент данной страницы для Вас недоступен.'); ?>
<?php

if(!isset($_SESSION['UID']))
    exit('<div class="big-logo"><div class="img"></div></div><p class="page-error">Не удалось определить пользователя.<a href="?logout=yes" class="icon-left-open-big">Вернуться обратно</a></p>');

$arUser = Users::GetUserById($_SESSION['UID']);
$arUser = $arUser[$_SESSION['UID']];

$law1 = Users::UserLawByGroup($arUser['group_id'],"law1"); // Просмотр всех сообщений, зарегистрированных в системе
$law2 = Users::UserLawByGroup($arUser['group_id'],"law2"); // Просмотр всех сообщений, проверенных модератором и опубликованных в системе
$law3 = Users::UserLawByGroup($arUser['group_id'],"law3"); // Возможность изменять статус сообщений
$law4 = Users::UserLawByGroup($arUser['group_id'],"law4"); // Возможность назначать организацию, ответственную за устранение выявленных в сообщении проблем
$law5 = Users::UserLawByGroup($arUser['group_id'],"law5"); // Возможность отправлять сообщения в архив
$law6 = Users::UserLawByGroup($arUser['group_id'],"law6"); // Просмотр общей статистики сообщений по различным критериям
$law7 = Users::UserLawByGroup($arUser['group_id'],"law7"); // Доступ в раздел администрирования. Данное право определяет возможность входа пользователя в раздел '/admin/' (Без указания дополнительных прав будет только просмотр)
$law8 = Users::UserLawByGroup($arUser['group_id'],"law8"); // Возможность определять срок выполнения

$userOptions = Users::getUserOptions($_SESSION['UID']);
$markerCaption = 1;

if(isset($userOptions[2]))
{
   $markerCaption = 0; 
}

if(!$law7)
{
    $_SESSION['SSUID'] = null;
    $_COOKIE['SSUID'] = null;
    exit('<div class="big-logo"><div class="img"></div></div><p class="page-error">Извините, у вас недостаточно прав для доступа к данному разделу системы. Для получения доступа позвоните по номеру 607-506.<a href="/admin/" class="icon-left-open-big">Попробовать еше раз</a></p>');
}

$DepartId = 0;
if(!Users::isSupervisoryByGroup($arUser['group_id']))
{
    $DepartId = $arUser['department_id'];
}

if($arUser['group_id']==2 && $DepartId==0) {
   $_SESSION['SSUID'] = null;
   $_COOKIE['SSUID'] = null;
   exit('<div class="big-logo"><div class="img"></div></div><p class="page-error">Извините, у вас недостаточно прав для доступа к данному разделу системы. Для получения доступа позвоните по номеру 607-506.<a href="/admin/" class="icon-left-open-big">Попробовать еше раз</a></p>');
}

$arMessageCategory = array();
$arMessageStatus = array();
$arDistricts = array();
$arOrg = array();
$arAccessStatus = array(); // прописать ID статуса, который нужно закрыть для выбора

$arMessageCategory = Messages::GetCategory(0,$DepartId);
$arMessageStatus = Messages::GetStatus();
$arDepart = Messages::GetDepartments();

if(Users::isAdmin($arUser['group_id']))
{
    $DepartId = 0;
}

$arDistricts = Messages::GetDistrict(0,$DepartId);

if($law4)
{
    $arOrg = MSystem::GetUsersOrganizationList($DepartId);
}

if(!isset($arUser))
{
    exit('<div class="big-logo"><div class="img"></div></div><p class="page-error">Не удалось определить пользователя.<a href="?logout=yes" class="icon-left-open-big">Вернуться обратно</a></p>');
}

?>

<div id="mapcont">
    <?php require_once('map.php'); ?>
</div>
<?php
    require_once("../plugins/photoswipe/photoSwipeTemp.html");
?>

<script>
var map_height = window.innerHeight;
$('#mapcont,#map').css('height',map_height+'px');
</script>

<div class="admin-panel">
   <?php // print_r($arUser); ?>
    <div class="panel-header">
        <h2><?php if($arUser['org_name']!='') echo $arUser['org_name']; else echo $arUser['group_caption'] ?></h2>
        <h4><?php echo $arUser['user_name'] ?><i><?php echo $arUser['department'];?></i></h4>
        <a href="#" class="icon-help-circled" id="instruction" title="Инструкция"></a>
    </div>
    <div class="white-option">
        <a href="#" class="item-link icon-arrows-ccw" id="map_refresh" title="Обновить данные на карте"></a>
        <a href="#" class="item-link icon-chart-bar" id="statistic">Статистика</a>
        <a href="#" class="item-link icon-widget" id="options">Настройки</a>
        <a href="#" class="item-link icon-lock" id="logout">Выход</a>
    </div>
    <div class="filter">
        <h4>Фильтр сообщений на карте</h4>
        <p>Категории сообщений</p>
        <select id="category" class="filter-object-type" autocomplete="off">
            <option data-id="0" selected>Все категории</option>
            <?php foreach($arMessageCategory as $key => $category): ?>
                <option data-id="<?php echo $key ?>"><?php echo $category['name']//.' ('.$category['message_count'].')' ?></option>
            <?php endforeach;?>
        </select>
        
        <?php if(count($arDistricts)>1)  $category_show = ''; else $category_show = 'display:none;'; ?>
        <div style="<?php echo $category_show ?>">
            <p>Районы города (округа)</p>
            <select id="district" class="filter-object-type" autocomplete="off">
                <option data-id="0" selected>Все районы</option>
                <?php foreach($arDistricts as $key => $district): ?>
                    <option data-id="<?php echo $key ?>"><?php echo $district ?></option>
                <?php endforeach;?>
            </select>
        </div>
        <?php
        //print_r($arMessageStatus);
        ?>
        
        <p>Состояние / статус</p>
        <select id="status" class="filter-object-type" autocomplete="off">
            <option data-id="0" data-icon="0" data-color="0" selected>Все статусы</option>
            <option data-id="overdue" data-icon="0" data-color="0">Просроченные</option>
            <?php foreach($arMessageStatus as $key => $status):
            if(in_array($key,$arAccessStatus)):
                if(!Users::isResponsibleUnit($arUser['group_id'])):
            ?>
                <option data-id="<?php echo $key ?>" data-icon="<?php echo $status['icon'] ?>" data-answer="<?php echo $status['answer'] ?>" data-file="<?php echo $status['file'] ?>" data-color="<?php echo $status['status_color'] ?>"><?php echo $status['name'] ?></option>
            <?php endif;?>
            <?php else:?>
                <option data-id="<?php echo $key ?>" data-icon="<?php echo $status['icon'] ?>" data-answer="<?php echo $status['answer'] ?>" data-file="<?php echo $status['file'] ?>" data-color="<?php echo $status['status_color'] ?>"><?php echo $status['name'] ?></option>
            <?php endif;
            ?>
            
            <?php endforeach;?>
        </select>

        <?php if(count($arOrg)>0): ?>
            <select id="org" class="filter-object-type">
                <?php foreach($arOrg as $key => $org): ?>
                    <option data-id="<?php echo $key ?>"><?php echo (($law4) ? '('.$key.') ':''); ?><?php echo $org['name'] ?></option>
                <?php endforeach;?>
            </select>
        <?php endif; ?>
        
        <?php if(count($arDepart)>0): ?>
            <select id="depart" class="filter-object-type">
                <?php foreach($arDepart as $key => $depart): ?>
                    <option data-id="<?php echo $key ?>"><?php echo (($law4) ? '('.$key.') ':''); ?><?php echo $depart ?></option>
                <?php endforeach;?>
            </select>
        <?php endif; ?>
        
    </div>
    <div class="messages">
        <div class="search">
            <input type="text" placeholder="Поиск по ключевым словам">
            <a href="#"></a>
        </div>
        <div class="message-list"></div>
    </div>
</div>
<script>
    
    var message_cont_height = window.innerHeight-$('.panel-header').height()-$('.white-option').height()-$('.filter').height()-140;
    $('.message-list').css('height',message_cont_height+'px');
    
</script>
<script src="/admin-test/scripts/map.js?v=2.0" type="text/javascript"></script>
<script src="/plugins/photoswipe/chg-photo-init.js" type="text/javascript"></script>