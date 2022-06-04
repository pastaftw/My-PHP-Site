<?php ini_set('display_errors', 1);

/* Insert Settings & Check Server Status BRUTE FORCE VE CSRF!!!!!*/
require_once 'php/Settings.php';

/*Checking Server Status*/
if ($_SETTINGS['Status'] != 1) {
    echo '<h1 style = "text-align: center; color: white; background-color: black;"> 
            Site Şu Anda Bakımdadır :( 
          </h1>';
    exit;
}

/* Main Scripts */
require_once 'php/Modules/Database.php';
require_once 'php/Modules/Session.php';
require_once 'php/Modules/Content.php';
require_once 'php/Modules/CSRF.php';
require_once 'php/Modules/Feedback.php';
require_once 'php/Modules/Time.php';
use Session\Session_Proccesses;
use Feedback\Feedback_Processes;
use Content\Post;
use Content\Content_Proccesses;
session_start();

/*Functions*/
function Update_Page($page_name) {
    $upd_page = 'php/Content/'.$page_name;
    require_once $upd_page;
}

/*Variables*/
$Check_User_ID = Session_Proccesses::Check_If_Logged_In();
$Current_Post = 2;

/*Content*/
require_once 'php/Content/Head.php';
require_once 'php/Content/Navbar.php';
if (isset($_GET['$CURRENTLY_NOT_AVAIBLE'])) {
    Feedback_Processes::Give_Feedback('CURRENTLY_NOT_AVAIBLE');
}
?>

<body> 
    <?php
        /*Page Content*/
        if (isset($_GET['$Page'])) {Update_Page('LoS.php');}
        else if (!$Check_User_ID) {
            Update_Page('Home_NLI.php'); 
            Post::List_Posts();
        } 
        else {Update_Page('Home.php');}
    ?>
</body>
</html>