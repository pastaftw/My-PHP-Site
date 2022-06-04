<?php

require_once 'Database.php';
use Database\Get_Data;
$a = new Get_Data();
$s = $a -> Run("Posts");
echo "<pre>";
print_r($s);
echo "</pre>";

?>