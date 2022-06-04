<?php namespace Database;
require_once 'Connection.php';
require_once 'Common.php';
use Common\Helpers;
use Connection;
use FFI\Exception;

/*Creating Connection*/
$CONNECTION_OBJ = new Connection();
$CONNECTION = $CONNECTION_OBJ -> Connect();

class Database_Proccesses extends Connection {};

class Get_Data extends Database_Proccesses {
    /*Gets All The Data From List*/
    private static function Get_All_Data($list_name) {
        try {
            global $CONNECTION;
            $data = $CONNECTION -> prepare("SELECT * FROM $list_name");
            $data -> execute();
            return Helpers::Return_Result_Array($data, 1);
        }
        catch (Exception $exception) {return null;};
    }

    /*Gets A Column From List*/
    private static function Get_Column ($list_name, $column_name) {
        try {
            global $CONNECTION;
            $data = $CONNECTION -> prepare("SELECT $column_name FROM $list_name");
            $data -> execute();
            return Helpers::Return_Result_Array($data, 0);
        }
        catch (Exception $exception) {return null;};
    }

    /*Checks All The Data With Condition*/
    private static function Get_All_Data_With_Condition($list_name, $look_as, $val) {
        try {
            global $CONNECTION;
            $data = $CONNECTION -> prepare("SELECT * FROM $list_name WHERE $look_as = ?");
            $data -> bind_param('s', $val);
            $data -> execute();
            return Helpers::Return_Result_Array($data, 1);
        }
        catch (Exception $exception) {return null;};
    }

    private static function Get_Data_With_Limits($list_name, $get = '*', $order, $pre_count, $count) {
        try {
            global $CONNECTION;
            if (is_array($get)) {$get = implode(',', $get);}
            if (is_array($order)) {$order = implode(',', $order);}
            if (is_null($pre_count) or is_null($count)) {
                $data = $CONNECTION -> prepare("SELECT $get FROM $list_name ORDER BY $order");
            }
            else {$data = $CONNECTION -> prepare("SELECT $get FROM $list_name ORDER BY $order LIMIT $count OFFSET $pre_count");}
            $data -> execute();
            return Helpers::Return_Result_Array($data, 1);
        }
        catch(Exception $exception) {return null;}
    }

    /*Overloading Function*/
    public function __call($function, $arg) {
        $args = func_get_args();
        if ($function == "Run") {
            switch (count($args[1])) {
                case 1: {return self::Get_All_Data($args[1][0]);}
                case 2: {return self::Get_Column($args[1][0], $args[1][1]);}
                case 3: {return self::Get_All_Data_With_Condition($args[1][0], $args[1][1], $args[1][2]);}
                case 5: {return self::Get_Data_With_Limits($args[1][0], $args[1][1], $args[1][2], $args[1][3], $args[1][4]);}
                default: {return null;}
            }
        }
    }
}

class Add_Data extends Database_Proccesses {
    /*Inserts Data Into Database*/
    function Run($list_name, $params_1, $params_2) {
        try {
            global $CONNECTION;
            $arr1_count = count($params_1);
            $params_1 = implode(',', $params_1);
            $values = '';
            $types = '';

            for ($_ = 0; $_ < $arr1_count; $_++) {
                if ($_ !== $arr1_count - 1) {$values .= '?,';}
                else {$values .= '?';}
                $types .= 's';
            }

            $data = $CONNECTION -> prepare("INSERT INTO $list_name ($params_1) VALUES ($values)");
            $data -> bind_param($types, ...$params_2);
            $data -> execute();
            return true;
        }
        catch (Exception $exception) {return false;}
    }
}

class User extends Database_Proccesses {
    /*Gets Username From User ID*/
    static function Get_User_From_User_ID($id) {
        $Get_Data = new Get_Data();
        $user = $Get_Data -> Run('Users', 'User_ID', $id);
        if (!empty($user[0]['Username'])) {return $user[0]['Username'];}
        return '[SİLİNMİŞ_KULLANICI]';
    }

    /*Signs User To Database*/
    public function Signup_User($usn, $pw) {
        if (!empty($usn) and !empty($pw)) {
            global $CONNECTION;
            $transfer = $CONNECTION -> prepare("INSERT INTO Users (Username, PW) VALUES (?, ?)");
            $pw = password_hash($pw, PASSWORD_DEFAULT);
            $transfer -> bind_param('ss', $usn, $pw);
            $transfer -> execute();
            return true;
        }
        return false;
    }
}


class Files extends Database_Proccesses {
    /*Control Function For Files*/
    private static function Upload_Control($file_name, $file_size, $file_type, $file_error, $white_list_extensions) {
        $allowed_size = 700000;
        $common_type_filter = array(
            'image/gif', 'image/jpeg', 'image/png', 'image/tiff', 'audio/mpeg', 'video/mp4', 'video/mpeg', 
            'application/vnd.ms-excel', 'application/zip', 'application/x-7z-compressed', 'audio/wav',
            'application/vnd.ms-powerpoint', 'application/vnd.rar', 'application/vnd.visio', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation1',
        );
            
        $control_count = 0;
        $explode_name = explode('.', $file_name);
        if (!$file_error) {$control_count += 1;}
        if ($file_size <= $allowed_size) {$control_count += 1;}
        if (in_array($file_type, $common_type_filter)) {$control_count += 1;}
        if (in_array(end($explode_name), $white_list_extensions)) {$control_count += 1;}
        if ($control_count == 4) {return true;}
        else {return false;}
    }

    /*Upload File*/
    private static function Proccess($id, $file_purpose, $repeatable, $white_list_extensions, $file_name, $file_size, $file_type, $file_error, $file_tmpn, $file_new_name) {
        global $CONNECTION;
        $upload_to = 'Uploads';
        $file_path = "../uploads/";

        if (Files::Upload_Control($file_name, $file_size, $file_type, $file_error, $white_list_extensions)) {
        $check = $CONNECTION -> query("SELECT * FROM $upload_to WHERE Uploader_ID = $id AND Purpose = '$file_purpose'");
            
            if (!$repeatable and $check -> num_rows == 1) {
                $Get_Data = new Get_Data();
                $profile = $Get_Data -> Run('Uploads', 'Uploader_ID', $id);
                $existing_file = $file_path.$profile[0]['Referance'];
    
                if (file_exists($existing_file)) {unlink($existing_file);}
                if (move_uploaded_file($file_tmpn, $file_path.$file_new_name)) {
                    $update_file = $CONNECTION -> prepare("UPDATE $upload_to SET Referance = ? WHERE Uploader_ID = $id");
                    $update_file -> bind_param('s', $file_new_name);
                    $update_file -> execute();
                    return true;    
                }
            }
            else if (move_uploaded_file($file_tmpn, $file_path.$file_new_name)) {
                    $transfer = $CONNECTION -> prepare("INSERT INTO $upload_to (Uploader_ID, Purpose, Referance) VALUES (?, ?, ?)");
                    $transfer -> bind_param('sss', $id, $file_purpose, $file_new_name);
                    $transfer -> execute();
                    return true;
            }
        }    
        return false;
    }

    static function Upload_File($id, $file_purpose, $file, $repeatable, $white_list_extensions) {
        if (is_null($file)) {return false;}
        if (is_array($file['name'])) {
            $file_count = count($file['name']);
            $accepted_file_count = 0;
            $file_names = array();

            for ($file_index = 0; $file_index < $file_count; $file_index++) {
                $file_name = $file['name'][$file_index];
                $file_size = $file['size'][$file_index];
                $file_type = $file['type'][$file_index];
                $file_error = $file['error'][$file_index];
                $file_tmpn = $file['tmp_name'][$file_index];
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $file_rename = 'Uploaded-'.$id.'-'.uniqid();
                $file_new_name = $file_rename.'.'.$file_extension;
                if (self::Proccess($id, $file_purpose, $repeatable, $white_list_extensions, $file_name, $file_size, $file_type, $file_error, $file_tmpn, $file_new_name)) {
                    array_push($file_names, $file_new_name);
                    $accepted_file_count++;
                }
            }
            if ($accepted_file_count > 0) {return $file_names;}
            return false;
        }
        else {
            $file_name = $file['name'];
            $file_size = $file['size'];
            $file_type = $file['type'];
            $file_error = $file['error'];
            $file_tmpn = $file['tmp_name'];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_rename = 'Uploaded-'.$id.'-'.uniqid();
            $file_new_name = $file_rename.'.'.$file_extension;
            if (self::Proccess($id, $file_purpose, $repeatable, $white_list_extensions, $file_name, $file_size, $file_type, $file_error, $file_tmpn, $file_new_name)) {
                return array($file_new_name);
            }
        }
        return false;
    }
}

class Delete_Data extends Database_Proccesses {
    /*Deletes Data (UPDATE LATER!!) */
    function Delete_Key($key) {
        try {
            $Get_Data = new Get_Data();
            if (in_array($key, $Get_Data -> Run('GoldenKeys', 'Key_Content'))) {
                global $CONNECTION;
                $data = $CONNECTION -> prepare("DELETE FROM GoldenKeys WHERE Key_Content = ?");
                $data -> bind_param('s', $key);
                $data -> execute();
                return true;
            }
            return false;
        }
        catch (Exception $exception) {return false;}
    }
}


?>