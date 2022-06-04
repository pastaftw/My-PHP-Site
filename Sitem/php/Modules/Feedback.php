<?php namespace Feedback;

class Feedback_Processes {
    function Give_Info ($style, $success, $info) {
        if ($style == 'span') {
            if ($success == 1 or $success == true) {$color = 'success';}
            else if ($success == 0 or $success == false) {$color = 'fail';}
            else {$color = 'unknown';}
            echo "<span class = \"info_box $color\"> $info </span>";
        }
        else if ($style == 'div') {
            if ($success == 1 or $success == true) {$color = 'alert';}
            else if ($success == 0 or $success == false) {$color = 'fail';}
            else {$color = 'unknown';}
            echo "<div class = \"info_box $color\"> $info </div>";
        }
    }

    /*Durum Dönütleri*/
    function Give_Feedback($type) {
        $type = "\$".$type;
        
        if ($type == '$signup') {
            if (isset($_GET[$type])) {
                $type = $_GET[$type];
                if ($type == 0) {self::Give_Info('span', 1, 'Kayıt tamamlandı.');}
                else if ($type == 1) {self::Give_Info('span', 0, 'Kayıt işlemi tamamlanamadı.');}
                else if ($type == 2) {self::Give_Info('span', 0, 'Geçersiz anahtar girildi.');}
                else if ($type == 3) {self::Give_Info('span', 0, 'Şifreler eşleşmedi.');}
                else if ($type == 4) {self::Give_Info('span', 0, 'Kullanıcı adı zaten kullanımda.');}
                else {self::Give_Info('span', 2, '?');}
            }
        }
        else if ($type == '$login') {
            if (isset($_GET[$type])) {
                $type = $_GET[$type];
                if ($type == '1') {self::Give_Info('span', 0, 'Kullanıcı adı veya şifre yanlış.');}
                else {self::Give_Info('span', 2, '?');}
            }
        }
        else if($type == '$post') {
            if (isset($_GET[$type])) {
                $type = $_GET[$type];
                if ($type == '0') {self::Give_Info('span', 1, 'Gönderi paylaşıldı.');}
                else if ($type == '1') {self::Give_Info('span', 2, 'Beklenmedi hata oluştu.');}
                else if ($type == '2') {self::Give_Info('span', 0, 'Bu gönderi paylaşılamaz.');}
                else {self::Give_Info('span', 2, '?');}
            }
        }
        else if($type == '$updateprofileimg') {
            if (isset($_GET[$type])) {
                $type = $_GET[$type];
                if ($type == '0') {self::Give_Info('span', 1, 'Profil resminiz güncellendi.');}
                else if ($type == '1') {self::Give_Info('span', 0, 'Bu resim yüklemeye uygun değildir.');}
                else {self::Give_Info('span', 2, '?');}
            }
        }
        else if($type == '$CURRENTLY_NOT_AVAIBLE') {
            if (isset($_GET[$type])) {
                $type = $_GET[$type];
                if ($type == true) {self::Give_Info('div', 1, 'Şu anda bu işlem gerçekleştirilemiyor, lütfen daha sonra tekrar deneyiniz.');}
            }
        }
    }
}
?>