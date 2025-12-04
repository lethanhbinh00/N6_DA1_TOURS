<?php
const DB_HOST = 'localhost';
const DB_NAME = 'travel_erp';
const DB_USER = 'root';
const DB_PASS = '';

define('PATH_ROOT', __DIR__ . '/../'); 

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (Exception $e) {
    die('Lỗi kết nối DB: ' . $e->getMessage());
}
?>
