<?php namespace Common; class Helpers {
    static function Return_Result_Array($statement, $style = 1) {
        $result = array();
        $statement -> store_result();
        $statement_rows = $statement -> num_rows;
        if ($statement_rows) {
            for ($_ = 0; $_ < $statement_rows; $_++) {
                $metadata = $statement -> result_metadata();
                $params = array();
                while ($field = $metadata -> fetch_field()) {
                    if ($style === 0) {$params[] = &$result[$_];}
                    else {$params[] = &$result[$_][$field -> name];}
                }
                call_user_func_array(array($statement, 'bind_result'), $params);
                $statement -> fetch();
            }
        } 
        return $result;
    }
}
?>