<!DOCTYPE html>
<html lang="ru">
<?php
//require_once 'db_connection.php'; // подключаем скрипт
//include 'db_connection.php';
include("login/users_login.php");
$servername="db";
$username="root";
$password="root";
$dbname="default";
// подключаемся к серверу
$mysqli = new mysqli($servername, $username, $password, $dbname);
error_reporting(0);
?>

<head>
    <meta charset="UTF-8">
    <title>Result Quiz</title>
    <link rel="stylesheet" type="text/css" href="css/quiz_result.css">
    <script src="js/jquery-3.4.1.min.js"></script>
</head>

<body>
<fieldset class="ramka">

<?php

// обчисление результатов
@$count_result_sql = $mysqli->query("SELECT COUNT(`id`) FROM `quiz_result`") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($count_result_sql)) {
    $count_result = intval($row[0]);
}
@$count_true_result_sql = $mysqli->query("SELECT COUNT(`correct_answer`) FROM `quiz_result` WHERE `correct_answer`='correct'") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($count_true_result_sql)) {
    $count_true_result = intval($row[0]);
}
$count_false_result = $count_result-$count_true_result;
$result = round((100/$count_result)*$count_true_result,1);

// isset(), is_null(), empty()
// вывод на страницу
if (isset($count_false_result) and isset($result)) {
    echo "<h2 class='itog'>Вы ответили правельно на  " . $count_true_result . " из " . $count_result . " вопросов</h2>";
    echo "<p><h1 class='result'>Ваш результат = " . $result . "%</h1></p>";
    echo "<p><h3 class='wish'>Желаем успехов!</h3></p>";
} else {
    echo "<h2 class='itog'>Вы не отвечали на вопросы!</h2>";
    echo "<p><h3 class='wish'>Выберите тест, и пройдите заново!</h3></p>";
}
?>

</fieldset>
<button class="back" name="" id="" onclick='javascript=location.href="quiz_list.php";' value="">Выбрать тест</button>
</body>
</html>
