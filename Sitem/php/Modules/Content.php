<?php namespace Content;

require_once 'Database.php';
use Database\User;
use Database\Get_Data;
$Get_Data = new Get_Data();

class Content_Proccesses {
    static function Content_From_Array($arr, $val_type, $val) {
        if (!empty($arr)) {
            for ($_ = 0; $_ < count($arr); $_++) {
                if ($arr[$_][$val_type] == $val) {
                    return $arr[$_]['Referance'];
                }
            }
        }
        return false;
    }

    static function Get_User_Content($id, $level, $purpose, $content_type) {
        global $Get_Data;
        $path_start = '';
        $uploads_folder = 'uploads/';
        for ($_ = 0; $_ < $level; $_++) {$path_start .= '../';}

        /*Get Content*/
        if ($content_type == 'IMAGE') {
            if (!empty($id) and $_SESSION['User_ID']) {
                $profile = $Get_Data -> Run('Uploads', 'Uploader_ID', $_SESSION['User_ID']);
                $profile = self::Content_From_Array($profile, $purpose, 'Profile');
                if (is_string($profile) and file_exists($path_start.$uploads_folder.$profile)) {
                    return $path_start.$uploads_folder.$profile;
                }
            }
            return $path_start.'img/Unknown.png';
        }
    }
}


class Post {
    static function Post_Images($from, $referances) {
        $referances = explode(',', $referances);
        $total_images = count($referances); 
        for ($_ = 0; $_ < $total_images; $_++) {
            if (!empty($referances[$_])) {
                $content = $from."/".$referances[$_];
                if (!file_exists($content)) {$content = 'img/Unknown.png';}
                echo "<div> <img src = '".$content."'></div>";
            }
        }
    }

    static function Create_Post($poster, $current_date, $post_title, $post_content, $referances) {
        echo "<div class = 'Post'> 
        <h2> Konu: $post_title | Tarafından: $poster | $current_date </h2> <br>
        <p> $post_content </p>";
        if (!is_null($referances) or !$referances = '') {
            echo "<div class = 'Image_Holder'>";
            self::Post_Images('uploads', $referances);
            echo "</div>";
        }
        echo "</div> <br>";
    }

    static function List_Posts() {
        global $Get_Data;
        $posts = $Get_Data -> Run('Posts', array('Poster_ID', 'Date_And_Time', 'Title', 'Referances', 'Content'), array('Date_And_Time DESC'), null, null);
        if (is_null($posts)) { die('death'); return;}
        for ($_ = 0; $_ < count($posts); $_++) {
            $poster = User::Get_User_From_User_ID($posts[$_]['Poster_ID']);
            $post_date_and_time = $posts[$_]['Date_And_Time'];
            $post_date_and_time = date("d/m/Y | h:i:s", strtotime($post_date_and_time));
            $post_title = $posts[$_]['Title'];
            $post_content = $posts[$_]['Content'];
            $post_referances = $posts[$_]['Referances'];
            self::Create_Post($poster, $post_date_and_time, $post_title, $post_content, $post_referances);
        }
    }
}

?>