<?php 
use Feedback\Feedback_Processes;
$token = Token::Create(); 
$fb = new Feedback_Processes();
?>

<main> 
    <section>
        <form class = "Common_Form" action = "php/Manager.php" method = "POST" enctype = "multipart/form-data">
            <?php $fb -> Give_Feedback('post'); ?>
            <h2> Paylaşım Yapınız </h2>
            <input type = "hidden" name = "Token" value = "<?php echo $token ?>">
            <input required type = "text" name = "Title">
            <textarea required class = "PrePost"  name = "Content"> </textarea>
            <input multiple type = "file" name = "Upload[]">
            <input type = "submit" name = "Post" value = "Paylaş">
        </form>
    </section>

    <section>
        <form class = "Common_Form" action = "php/Manager.php" method = "POST" enctype = "multipart/form-data">
            <?php $fb -> Give_Feedback('updateprofileimg'); ?>
            <h2> Profil Resmi Güncelleme </h2>
            <input type = "hidden" name = "Token" value = "<?php echo $token ?>">
            <input type = "file" name = "Update_Profile">
            <input type = "submit" name = "Update" value = "Güncelle">
        </form>
    </section>
</main>