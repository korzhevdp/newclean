<?php
ini_set('display_errors', 'Off');
$AccessIndex = true;
// значение $AccessIndex определяет наличие доступа для выполнения загружаемых скриптов
// идея заключается в том, что все подгружаемые модульные скрипты будут доступны для выполнения
// исключительно при обращении к ним главного обрабочика handler.php


$PAGE = 0; 
if(isset($_POST['page']) && is_numeric($_POST['page']))
{
    $PAGE = $_POST['page']; // целочисленное значение данной перменной определяет код подгружаемого скрипта
}


session_start();

// подключение дополнительных функций и классов
include_once("db/db.php");
include_once("functions.php");
include_once("include/classes/MailEvents.php");
include_once("include/classes/MSystem.php");
include_once("include/classes/Messages.php");
include_once("include/classes/Chat.php");
include_once("include/classes/User.php");


if(isset($_GET['recovery_key']))
{
   $PAGE = 36;
}

if(isset($_POST['logout']))
{
    $_SESSION['SSUID'] = null;
    //$_COOKIE['SSUID'] = null;
}

if(($PAGE!=4 && $PAGE!=10 && $PAGE!=11 && $PAGE!=34 && $PAGE!=35 && $PAGE!=36 && $PAGE!=37) && (!isset($_SESSION['SSUID']) ||  $_SESSION['SSUID']==null))
{
    $PAGE = 30; 
}
else
{
    if(isset($_SESSION['UID']))
    {
        $arUser = Users::GetUserById($_SESSION['UID']);
        $arUser = $arUser[$_SESSION['UID']];
    }
}


if(isset($_SESSION['UID']) && !Users::isActiveUser($_SESSION['UID']) && $_SESSION['SSUID']!=null)
{
    $_SESSION['SSUID'] = null;
    $_COOKIE['SSUID'] = null;
    ?>
        <script>$(document).find('.window-loader').remove();</script>
    <?php
    exit('<p style="padding: 10px;">Невозможно выполнить данное действие, т.к. Ваша учетная запись была заблокирована.<br><br><a href="/" class="icon-left-open-big">Назад</a></p>');
}

if(CheckUserAuth() || ($PAGE==10) || ($PAGE==11) || ($PAGE==30)  || ($PAGE==34) || ($PAGE==35) || ($PAGE==36) || ($PAGE==37) || $PAGE==122)
{

    switch ($PAGE)
    {
        case 0:
        case 4:
            require_once('pages/lk.php');
            break;
        case 5:
            require_once('pages/NewMessage_s1.php');
            break;
        case 6:
            require_once('pages/NewMessage_s2.php');
            break;
        case 7:
            require_once('pages/NewMessage_s3.php');
            break;
        case 8:
            require_once('pages/PublicMessage.php');
            break;
        case 9:
            require_once('pages/about.php');
            break;
        case 10:
            require_once('pages/reg.php');
            break;  
        /*case 11:
            require_once('pages/ChangePassword.php');
            break;  */
        case 12:
            require_once('pages/MyMessageList.php');
            break;
        /*case 13:
            require_once('pages/AllMessageList.php');
            break; */
        case 14:
            require_once('pages/MyMessageMap.php');
            break;
        /*case 15:
            require_once('pages/AllMessageMap.php');
            break;
            */
        case 16:
            require_once('pages/OneMessage.php');
            break;
        case 30:
            require_once('pages/SetInterface.php');
            break;
        case 31:
            require_once('pages/feedback.php');
            break;
        case 32:
            require_once('pages/SendFeedback.php');
            break;
        case 122:
            require_once('pages/getAuthByKey.php');
            break;
         case 33:
            require_once('pages/MainMenu.php');
            break;
         case 34:
            require_once('pages/PasswordRecovery.php');
            break;
        case 35:
            require_once('db/action/PasswordRecoveryMessage.php');
            break;
        case 36:
            require_once('pages/ChangePassword.php');
            break;
        case 37:
            require_once('db/action/PasswordRecoveryQuery.php');
            break;
        case 125:
            require_once('db/action/getDistrictByPoint.php');
            break;
        default:
            //require_once('pages/404.php');
            break;
    }

}
else
{
    require_once('pages/auth.php');
}

?>
