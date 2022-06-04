<?php use Content\Content_Proccesses; ?>
<nav>
    <ul>
        <li class = "Site_Name"> <?php echo date('d / m') ?> </li> 
        <li class = "Empty"></li> 

        <form action = "php/Manager.php" method = "POST"> 
            <input class = "Nav_Button" type = 'submit' name = "Homepage" value = "Ana Sayfa">
            <input class = "Nav_Button" type = 'submit' name = "LoginOrSignup" value = "Giriş & Kayıt">
        </form>

        <?php 
        if ($Check_User_ID) { 
            $profile_picture = Content_Proccesses::Get_User_Content($_SESSION['User_ID'], 0, 'Purpose', 'IMAGE');
            $profile_name = $_SESSION['Username'];
        ?>
        <li class = "Profile"> 
            <img src = "<?php echo $profile_picture ?>"> 
            <div>
                <p> <?php echo $profile_name ?> </p>
                <form action = "php/Manager.php" method = "POST"> <input id = "Logout_Button" type = "submit" name = "Logout" value = "Çıkış Yap"> </form> 
            </div>
        </li>
        <?php } ?>
    </ul>
</nav>