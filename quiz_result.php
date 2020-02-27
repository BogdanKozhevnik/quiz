<!DOCTYPE html>
<html lang="ru">
<?php
//require_once 'db_connection.php'; // подключаем скрипт
include 'db_connection.php';
$new_msql = new ConnectDB;
// looking for who is active user now
$hashMsqlLP = $new_msql->hashMysqlLogPass();
foreach ($hashMsqlLP as $email=>$hash) {
    if ($hash == $_COOKIE['verify']) {
        $activeUserEmail = $email;
    }
}
// get active user data from db
$LOGIN_INFORMATION = $new_msql->getActiveUser($activeUserEmail);
$activeUserLogin = $LOGIN_INFORMATION[$activeUserEmail];

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
@$count_result_sql = $mysqli->query("SELECT COUNT(`id`) FROM `quiz_process`") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($count_result_sql)) {
    $count_result = intval($row[0]);
}
@$count_true_result_sql = $mysqli->query("SELECT COUNT(`correct_answer`) FROM `quiz_process` WHERE `correct_answer`='correct'") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($count_true_result_sql)) {
    $count_true_result = intval($row[0]);
}
$count_false_result = $count_result-$count_true_result;
$result = round((100/$count_result)*$count_true_result,1);

// get trying of user to do the test
$quizId = $_GET['quiz_id'];
$quiz_sql = $mysqli->query("SELECT `title` FROM `quiz` where `id_quiz`='$quizId'");
$quiz_arr = mysqli_fetch_array($quiz_sql);
$thiQuizTitle = $quiz_arr[0];
$try_sql = $mysqli->query("SELECT `try` FROM `quiz_result` where `quiz_title`='$thiQuizTitle' and `login`='$activeUserLogin'");
$try_arr = $try_sql->fetch_array();
$try = $try_arr[0];
// all tries
$allQuiz_sql = $mysqli->query("SELECT `quiz_title` FROM `quiz_result`");
$allQuiz_arr = $allQuiz_sql->fetch_all();
foreach ($allQuiz_arr as $item=>$value) {
$tests[] = $value[0];
}

// вывод на страницу
if (isset($count_false_result) and isset($result)) {
    echo "<h2 class='itog'>Вы ответили правельно на  " . $count_true_result . " из " . $count_result . " вопросов</h2>";
    echo "<p><h1 class='result'>Ваш результат = " . $result . "%</h1></p>";
    echo "<p><h3 class='wish'>Желаем успехов!</h3></p>";
    $res = "" .$count_true_result . " из " . $count_result . " вопросов верно => ".$result. "%";

    $fillResult = $mysqli->query("UPDATE `quiz_result` SET `result`='$res', `try`='$try' WHERE `quiz_title`='$thiQuizTitle' and `login`='$activeUserLogin';");

} else {
    echo "<h2 class='itog'>Вы не отвечали на вопросы!</h2>";
    echo "<p><h3 class='wish'>Выберите тест, и пройдите заново!</h3></p>";
}
?>

</fieldset>
<button class="back" name="" id="" onclick='javascript=location.href="quiz_list.php";' value="">Выбрать тест</button>
</body>
</html>
