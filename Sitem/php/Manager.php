<?php ini_set('display_errors', 1);

require_once 'Settings.php';
require_once 'Modules/Database.php';
require_once 'Modules/Time.php';
use Database\Delete_Data;
use Database\Add_Data;
use Database\Get_Data;
use Database\Files;
use Database\User;

$GET_DATA = new Get_Data();
session_start();

/*PATCH BRUTE FORCE!*/
function Confirm_Token($token) {
    if ($_SESSION['Token'] != $token) {die('Lütfen CSRF Yapma...');}
}

/*Signing Up User*/
if ($_SETTINGS['Signup_Avaible'] == 1 and !empty($_POST['Signup']) and !empty($_POST['Username']) and !empty($_POST['Password']) and !empty($_POST['Password_Repeat']) and !empty($_POST['AccessKey'])) {
    Confirm_Token($_POST['Token']);

    /*Checking If Key Exists*/
    function Check_Access_Key($key) {
        $delete_key = new Delete_Data();
        if ($delete_key -> Delete_Key($key)) {return true;}
        return false;
    }
    
    $database_usernames = $GET_DATA -> Run('Users', 'Username');
    if (!in_array($_POST['Username'], $database_usernames)) {
        $pw = mysqli_real_escape_string($CONNECTION, $_POST['Password']);
        $pwr = mysqli_real_escape_string($CONNECTION, $_POST['Password_Repeat']);
        if ($pw == $pwr) {
            if (Check_Access_Key($_POST['AccessKey'])) {
                $Signup = new User();
                if ($Signup -> Signup_User($_POST['Username'], $_POST['Password'])) {
                    $profile = $GET_DATA -> Run('Users', 'Username', $_POST['Username']);
                    $_SESSION['User_ID'] = $profile[0]['User_ID'];
                    $_SESSION['Username'] = $profile[0]['Username'];
                    $_SESSION['Password'] = $profile[0]['PW'];
                    $_SESSION['IPAdress'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['HTTPAgent'] = $_SERVER['HTTP_USER_AGENT'];
                    header('Location: ../index.php?$signup=0');
                } else {header('Location: ../index.php?$Page=LoS&$signup=1');}
            } else {header('Location: ../index.php?$Page=LoS&$signup=2');}
        } else {header('Location: ../index.php?$Page=LoS&$signup=3');}
    } else {header('Location: ../index.php?$Page=LoS&$signup=4');}
} 

/*Logging In User*/ 
else if ($_SETTINGS['Login_Avaible'] == 1 and !empty($_POST['Login']) and !empty($_POST['Username']) or !empty($_POST['Password'])) {
    Confirm_Token($_POST['Token']);
    
    function Check_Login($database_vals, $usn, $pw) {
        if (!is_null($database_vals) and $database_vals[0]['Username'] == $usn and password_verify($pw, $database_vals[0]['PW'])) {return true;}
        return false;
    }
    
    $profile = $GET_DATA -> Run('Users', 'Username', $_POST['Username']);
    if (Check_Login($profile, $_POST['Username'], $_POST['Password'])) {
        $_SESSION['User_ID'] = $profile[0]['User_ID'];
        $_SESSION['Username'] = $profile[0]['Username'];
        $_SESSION['Password'] = $profile[0]['PW'];
        $_SESSION['IPAdress'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['HTTPAgent'] = $_SERVER['HTTP_USER_AGENT'];
        header('Location: ../index.php');
    } else {header('Location: ../index.php?$Page=LoS&$login=1');}
}

/*User Sending Post*/ 
else if ($_SETTINGS['Uploading_Avaible'] == 1 and isset($_POST['Post'])) {
    Confirm_Token($_POST['Token']);
    $add_data = new Add_Data();

    if ($_FILES['Upload']['size'][0] > 0) {
        $files = new Files();
        $file_upload = $files -> Upload_File($_SESSION['User_ID'], 'Post', $_FILES['Upload'], true, array('png', 'jpg', 'jpeg'));
        if (is_array($file_upload)) {
            $file_upload = implode(',', $file_upload);
            if ($add_data -> Run('Posts', array('Poster_ID', 'Date_And_Time', 'Title', 'Referances', 'Content'), array($_SESSION['User_ID'], $CURRENT_DATE, $_POST['Title'], $file_upload, $_POST['Content']))) {
                header('Location: ../index.php?$post=0');
            } else {header('Location: ../index.php?$post=1');}
        } else {header('Location: ../index.php?$post=2');}
    }
    else {
        if ($add_data -> Run('Posts', array('Poster_ID', 'Date_And_Time', 'Title', 'Content'), array($_SESSION['User_ID'], $CURRENT_DATE, $_POST['Title'], $_POST['Content']))) {
            header('Location: ../index.php?$post=0');
        } else {header('Location: ../index.php?$upload=1');}
    }
}

/*User Updating Profile Image*/
else if (isset($_POST['Update'])) {
    Confirm_Token($_POST['Token']);
    $files = new Files(); 
    
    if ($files -> Upload_File($_SESSION['User_ID'], 'Profile', $_FILES['Update_Profile'], false, array('png', 'jpg', 'jpeg'))) {
        header('Location: ../index.php?$updateprofileimg=0');
    } else {header('Location: ../index.php?$updateprofileimg=1');}
}

/*Logout*/ 
else if (isset($_POST['Logout'])) {
    session_destroy();
    header('Location: ../index.php');
}

/*Changing Pages*/
else if (isset($_POST['Homepage'])) {header('Location: ../index.php');}
else if (isset($_POST['LoginOrSignup'])) {header('Location: ../index.php?$Page=LoS');}

/*Post Loading*/
else if (isset($_POST['LoadPosts'])) {
    header('Location: ../index.php?$LoadPosts=2');
}

/*Manager Finishing Work*/ 
else {header('Location: ../index.php?$CURRENTLY_NOT_AVAIBLE=true');}
/*Not sure if i should close connection or not..*/
