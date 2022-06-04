<?php 
use Feedback\Feedback_Processes;
$fb = new Feedback_Processes();
$token = Token::Create(); 
?>

<main>
    <section id = "Signup">
        <form class = "Common_Form" action = "php/Manager.php" method = "POST">
            <?php $fb -> Give_Feedback('signup'); ?>
            <h2> Kayıt Olunuz </h2>
            <input type = "hidden" name = "Token" value = "<?php echo $token ?>">
            <input type = "text" name = "AccessKey" required = "" placeholder = "Anahtar Giriniz">
            <input type = "text" name = "Username" required = "" placeholder = "Kullanıcı Adınızı Giriniz">
            <input type = "password" name = "Password" required = "" placeholder = "Şifrenizi Giriniz">
            <input type = "password" name = "Password_Repeat" required = "" placeholder = "Şifrenizi Tekrar Giriniz">
            <input type = "submit" name = "Signup" value = "Gönder" class = "submit_button">
        </form>
    </section>

    <section id = "Login"> 
        <form class = "Common_Form" action = "php/Manager.php" method = "POST">
            <?php $fb -> Give_Feedback('login') ?>
            <h1> Giriş Yapınız </h1>
            <input type = "hidden" name = "Token" value = "<?php echo $token ?>">
            <input type = "text" name = "Username" required = "" placeholder = "Kullanıcı Adınızı Giriniz">
            <input type = "password" name = "Password" required = "" placeholder = "Şifrenizi Giriniz">
            <input type = "submit" name = "Login" value = "Giriş" class = "submit_button">
        </form>
    </section>
</main>