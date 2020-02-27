<!DOCTYPE html>
<html lang="en">
<?php
include("login/users_login.php");
include 'db_connection.php';
$new_msql = new ConnectDB;
$msql = $new_msql->msql();
// looking for who is active user now
@$hashMsqlLP = $new_msql->hashMysqlLogPass();
foreach ($hashMsqlLP as $email=>$hash) {
    if (@$hash == @$_COOKIE['verify']) {
        @$activeUserEmail = $email;
    }
}
// get active user data from db
$LOGIN_INFORMATION = $new_msql->getActiveUser($activeUserEmail);
@$activeUserLogin = $LOGIN_INFORMATION[$activeUserEmail];

// get admin data for compare if current user is admin
$admin_sql = $msql->query("SELECT `email`, `password` FROM `admin`");
$admin_arr = $admin_sql->fetch_all();
$admin = [$admin_arr[0][0]=>$admin_arr[0][1]];

//get all users name
$allNames_sql = $msql->query("SELECT `login` FROM `users` UNION SELECT `login` FROM `admin`");
$allNames_array = $allNames_sql->fetch_all();
foreach ($allNames_array as $key=>$value) {
    $names[] = $value[0];
}

// get all results for admin wathing
$adminResultSql = $msql->query("SELECT `login`, `email`, `quiz_title`, `result` FROM `quiz_result`");
$adminResultArray = $adminResultSql->fetch_all();

?>
<head>
    <meta charset="UTF-8">
    <title>Results</title>
    <link rel="stylesheet" type="text/css" href="css/user_score.css">
</head>
<body>
<input type="submit" name="back_question" class="back_question" value="Назад к тестам"  onclick="location.href='quiz_list.php'"/>
<?php

// make users croup array with results
if ($LOGIN_INFORMATION == $admin) {
    foreach ($names as $key=>$value) {
        $userResults_sql = $msql->query("SELECT `login`, `email`, `quiz_title`, `result`, `try` FROM `quiz_result` WHERE `login`='$value'");
        $userResults_array[$value] = $userResults_sql->fetch_all();
    }
    foreach ($userResults_array as $key=>$value) {
        $countItems = count($userResults_array[$key]);
        if (!empty($value)) {
            for ($i=0; $i<$countItems; $i++) {
                $login = $value[$i][0];
                $mail = $value[$i][1];
                $quiz = $value[$i][2];
                $result = $value[$i][3];
                $try = $value[$i][4];

    echo "<fieldset class='score'><b>$login</b></br>
                <table class='tb-score'>
                  <tr>
                    <th>E-мейл</th>
                    <th>Название теста</th>
                    <th>Результат:</th>
                    <th>Попытка:</th>
                    <th></th>
                  </tr>
                  <tr>
                    <td>$mail</td>
                    <td class='quiz-name'>$quiz</td>
                    <td>$result</td>
                    <td class='quiz-name'>$try</td>
                    <td></td>
                  </tr>
                </table>
                </fieldset>";
            }
        }
    }
} else {
    $userResults_sql = $msql->query("SELECT `login`, `email`, `quiz_title`, `result` , `try` FROM `quiz_result` WHERE `login`='$activeUserLogin'");
    $userResults_array[] = $userResults_sql->fetch_all();
    foreach ($userResults_array as $key=>$value) {
        $countItems = count($userResults_array[$key]);
        if (!empty($value)) {
            for ($i=0; $i<$countItems; $i++) {
                $login = $value[$i][0];
                $mail = $value[$i][1];
                $quiz = $value[$i][2];
                $result = $value[$i][3];
                $try = $value[$i][4];

                echo "<fieldset class='score'><b>$login</b></br>
                <table class='tb-score'>
                  <tr>
                    <th>E-мейл</th>
                    <th>Название теста</th>
                    <th>Результат:</th>
                    <th>Попытка:</th>
                    <th></th>
                  </tr>
                  <tr>
                    <td>$mail</td>
                    <td class='quiz-name'>$quiz</td>
                    <td>$result</td>
                    <td class='quiz-name'>$try</td>
                    <td></td>
                  </tr>
                </table>
                </fieldset>";
            }
        }
    }
}
?>

</body>
</html>