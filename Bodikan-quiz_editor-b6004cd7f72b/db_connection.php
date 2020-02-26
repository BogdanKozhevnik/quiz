<?php
$servername="db";
$username="root";
$password="root";
$dbname="default";

$mysqli = new mysqli($servername, $username, $password, $dbname);
if (!$mysqli) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
//if (mysqli_connect_error()) {
//    die('Connect Error (' . mysqli_connect_errno() . ') '
//        . mysqli_connect_error());
//}
//if ($mysqli->connect_error) {
//    die('Ошибка : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
//}
echo "Соединение с MySQL установлено!" . PHP_EOL;
echo "Информация о сервере: " . mysqli_get_host_info($mysqli) . PHP_EOL;
mysqli_close($mysqli);