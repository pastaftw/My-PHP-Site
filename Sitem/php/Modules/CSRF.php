<?php class Token {
    static function Create() {
        $token = md5(uniqid(rand(), true));
        $_SESSION['Token'] = $token;
        return $token;
    }
}
?>