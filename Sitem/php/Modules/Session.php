<?php namespace Session;
use Content;
use Common\Helpers;

class Session_Proccesses {
    static function Check_If_Logged_In() {
        if (!empty($_SESSION['User_ID'])) {return true;}
        return false;
    }

    function Check_Session() {
        if (!empty($_SESSION['IPAdress']) and !empty($_SESSION['HTTPAgent'])) {
            /*Check if ipaddress matches with session.*/
            if ($_SERVER['REMOTE_ADDR'] != $_SESSION['IPAdress']) {
                session_destroy();
                header('Location: ../../index.php?$session=fail');
            }
            /*Check if browser info matches with session.*/
            if ($_SERVER['HTTP_USER_AGENT'] != $_SESSION['HTTPAgent']) {
                session_destroy();
                header('Location: ../../index.php?$session=fail');
            }
            /*SON GİRME ZAMANINI HESAPLA VE EKLE*/

            if(!empty($_SESSION['User_ID']) and 
            !empty($_SESSION['Username']) and 
            !empty($_SESSION['Password']) and
            !empty($_SESSION['IPAdress']) and 
            !empty($_SESSION['HTTPAgent'])) 
            {return true;}
        }
        session_destroy();
        header('Location: ../../index.php?$session=fail');
        return false;
    }

    function Claim_User_Profile() {
        require_once 'Connection.php';
        if (!empty($_SESSION['User_ID'])) {
            if (!empty($_SESSION['Username']) and !empty($_SESSION['Password'])) {
                $check = $CONNECTION -> prepare("SELECT * FROM Users WHERE User_ID = ? AND Username = ? AND PW = ?");
                $check -> bind_param('sss', $_SESSION['User_ID'], $_SESSION['Username'], $_SESSION['Password']);
                $check -> execute();
                $get_result = Helpers::Return_Result_Array($check);
                if (sizeof($get_result) == 1) {return true;}
            }
        }
        return false;
    }
}
?>