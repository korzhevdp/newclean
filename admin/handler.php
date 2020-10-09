<?php
ini_set('display_errors', 'Off');
$AccessIndex = true;
session_start();

include_once("functions.php");
include_once("../db/db.php");
include_once("../include/classes/MailEvents.php");
include_once("../include/classes/Messages.php");
include_once("../include/classes/User.php");
include_once("../include/classes/MSystem.php");
include_once("../include/classes/Chat.php");


$PAGE = 0;
if((isset($_POST['page'])) && is_numeric($_POST['page']))
{
    $PAGE = $_POST['page'];
}

if(isset($_GET['recovery_key']))
{
   $PAGE = 36;
}

if(isset($_POST['logout']) || isset($_GET['logout']))
{
    $_SESSION['SSUID'] = null;
    //$_COOKIE['SSUID'] = null;
}


if(isset($_SESSION['UID']))
{
    $arUser = Users::GetUserById($_SESSION['UID']);
    if(isset($arUser[$_SESSION['UID']]))
    {
        $arUser = $arUser[$_SESSION['UID']];
        $law1 = Users::UserLawByGroup($arUser['group_id'],"law1"); // Просмотр всех сообщений, зарегистрированных в системе
        $law2 = Users::UserLawByGroup($arUser['group_id'],"law2"); // Просмотр всех сообщений, проверенных модератором и опубликованных в системе
        $law3 = Users::UserLawByGroup($arUser['group_id'],"law3"); // Возможность изменять статус сообщений
        $law4 = Users::UserLawByGroup($arUser['group_id'],"law4"); // Возможность назначать организацию, ответственную за устранение выявленных в сообщении проблем
        $law4_1 = Users::UserLawByGroup($arUser['group_id'],"law4_1"); // Возможность назначать департамент
        $law5 = Users::UserLawByGroup($arUser['group_id'],"law5"); // Возможность отправлять сообщения в архив
        $law6 = Users::UserLawByGroup($arUser['group_id'],"law6"); // Просмотр общей статистики сообщений по различным критериям
        $law7 = Users::UserLawByGroup($arUser['group_id'],"law7"); // Доступ в раздел администрирования. Данное право определяет возможность входа пользователя в раздел '/admin/' (Без указания дополнительных прав будет только просмотр)
        $law8 = Users::UserLawByGroup($arUser['group_id'],"law8"); // Возможность определять срок выполнения
        $law9 = Users::UserLawByGroup($arUser['group_id'],"law9"); // Доступ к системным настройкам
        $UserOrganization = Users::isOrganization($arUser['group_id']);
    }
    // лучше потом переделать, но пока ТАК!
}
    
if(CheckUserAuth() || ($PAGE==10) || ($PAGE==11) || ($PAGE==2) || ($PAGE==122) || ($PAGE==34) || ($PAGE==35) || ($PAGE==36) || ($PAGE==37))
{
    if(isset($_SESSION['UID']) && !Users::isActiveUser($_SESSION['UID']) && $_SESSION['SSUID']!=null)
    //if(!Users::isActiveUser($_SESSION['UID']) && $_SESSION['SSUID']!=null)
    {
        $_SESSION['SSUID'] = null;
        $_COOKIE['SSUID'] = null;
        exit('<p class="page-error">Невозможно выполнить данное действие, т.к. Ваша учетная запись была заблокирована администратором.<br><br><a href="/admin/" class="icon-left-open-big">Назад</a></p>');
    }

    switch ($PAGE)
    {
        case 0:
            require_once('pages/main.php');
            break;
        case 2:
            require_once('pages/reg.php');
            break;
        case 5:
            require_once('data/SaveOption.php'); // сохраняет данные и возвращает новый элемент в контейнер
            break;
        case 7:
            require_once('data/ToArchive.php');
            break;
        case 6:
            require_once('data/Reference.php');
            break;
        case 8:
            require_once('data/Statistic.php');
            break;
        case 9:
            require_once('data/Options.php');
            break;
        case 34:
            require_once('data/PasswordRecovery.php');
            break;
        case 35:
            require_once('../db/action/PasswordRecoveryMessage.php');
            break;
        case 36:
            require_once('data/ChangePassword_recovery.php');
            break;
        case 37:
            require_once('../db/action/PasswordRecoveryQuery.php');
            break;
        case 91:
            require_once('data/OptionsUsers.php');
            break;
        case 92:
            require_once('data/OptionsGroups.php');
            break;
        case 94:
            require_once('data/PersonalOptions.php');
            break;
        case 95:
            require_once('data/OptionsDepartments.php');
            break;
        case 96:
            require_once('data/OptionsOrganizations.php');
            break;
        case 97:
            require_once('data/OptionsCategories.php');
            break;
        case 99:
            require_once('data/OptionsStatus.php');
            break;
        case 98:
            require_once('data/OptionsAddDataRow.php');
            break;
        case 100:
            require_once('data/SystemHistory.php');
            break;
        case 10:
            require_once('data/SetTableOption.php');
            break;
        case 101:
            require_once('data/GetMessageJSONData.php');
            break;
        case 102:
            require_once('data/setSubOrganization.php');
            break;
        case 103:
            require_once('data/getSubOrganization.php');
            break;
        case 104:
            require_once('data/changePassword.php');
            break;
        case 105:
            require_once('data/setUserOptions.php');
            break;
        case 106:
            require_once('data/DeveloperNotes.php');
            break;
        case 107:
            require_once('data/MessagesList.php');
            break;
        case 108:
            require_once('data/MessageEvents.php');
            break;
        case 109:
            require_once('data/FeedbackMessages.php');
            break;
        case 122:
            require_once('data/getAuthByKey.php');
            break;
        case 125:
            require_once('data/getOneMessage.php');
            break;
        case 130:
            require_once('data/Chat.php');
            break;
        case 135:
            require_once('data/sendChatMessage.php');
            break;
        case 136:
            require_once('data/chatActivation.php');
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