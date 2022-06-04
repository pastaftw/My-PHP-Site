<?php class Connection {
    public function Connect($TEST_SERVER_MODE_ENABLED = true) {
        
        /*Test Server Connection*/
        if ($TEST_SERVER_MODE_ENABLED) {
            $CONNECTION = new mysqli(
                "localhost",
                'root',
                '12345678',
                'CoreDatabase',
            );
        }

        /*Original Server Connection*/
        else {
            $CONNECTION = new mysqli(
                'HOST_NAME',
                'DATABASE_NAME',
                'PASSWORD',
                'LIST',
            ); 
        }   
        
        if ($CONNECTION -> connect_error) {die('BAĞLANTI HATASI: Lütfen daha sonra tekrar deneyiniz.');} 
        return $CONNECTION;
    } 
}
?>
