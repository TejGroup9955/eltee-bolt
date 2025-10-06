<?php

//  ini_set('display_errors', '1');
//  ini_set('display_startup_errors', '1');
//  error_reporting(E_ALL);

date_default_timezone_set('Asia/Kolkata'); 
$dbHost ="localhost";
$dbUsername="root";
$dbPassword="";
$dbName="eltee_dmcc_new1";
$connect = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

try {
  $dsn = "mysql:host=localhost;dbname=eltee_dmcc_new1";
  $username = "root";
  $password = "";

  $pdo = new PDO($dsn, $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
$config_url = "http://10.10.50.82/ELTEE_DMCC/";

if(!function_exists('safeString')) {
    function safeString($connect, $str=NULL) {
        $str = htmlentities($str, ENT_QUOTES, 'UTF-8'); // convert funky chars to html entities
        $str = mysqli_real_escape_string($connect,$str);
        return $str;
    }
}
if(!function_exists('sanitizeInput')) {
  function sanitizeInput($input, $conn) {
    if (is_array($input)) {
        return array_map(function($item) use ($conn) {
            return mysqli_real_escape_string($conn, $item);
        }, $input);
    } else {
        return mysqli_real_escape_string($conn, $input);
    }
  }
}
if(!function_exists('sanitizeInputWithoutArray')) {
  function sanitizeInputWithoutArray($input, $conn) {
    if (is_array($input)) {
        return $input;
    } else {
        return mysqli_real_escape_string($conn, $input);
    }
  }
}
?>