<!DOCTYPE html>
<html lang="en">
<?php
include("login/users_login.php");
include 'db_connection.php';
$new_msql = new ConnectDB;
$msql = $new_msql->msql();
// looking for who is active user now
$hashMsqlLP = $new_msql->hashMysqlLogPass();
foreach ($hashMsqlLP as $email=>$hash) {
    if ($hash == $_COOKIE['verify']) {
        $activeUserEmail = $email;
    }
}
// get active user data from db
$LOGIN_INFORMATION = $new_msql->getActiveUser($email);
$activeUserLogin = $LOGIN_INFORMATION[$activeUserEmail];

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
$adminResultSql = $msql->query("SELECT `login`, `email`, `quiz_title`, `title_question`, `user_answer`, `correct_answer` FROM `quiz_result`");
$adminResultArray = $adminResultSql->fetch_all();

?>
<head>
    <meta charset="UTF-8">
    <title>Results</title>
</head>
<body>

<?php

// make users croup array with results
if ($LOGIN_INFORMATION == $admin) {
    foreach ($names as $key=>$value) {
        $userResults_sql = $msql->query("SELECT `login`, `email`, `quiz_title`, `title_question`, `user_answer`, `correct_answer` FROM `quiz_result` WHERE `login`='$value'");
        $userResults_array[$value] = $userResults_sql->fetch_all();
    }
    foreach ($userResults_array as $key=>$value) {
        $countItems = count($userResults_array[$key]);
        if (!empty($value)) {
            for ($i=0; $i<$countItems; $i++) {
                $login = $value[$i][0];
                $mail = $value[$i][1];
                $quiz = $value[$i][2];
                $question = $value[$i][3];
                $answer = $value[$i][4];
                $check = $value[$i][5];

                echo "<fieldset><b>$login</b></br>
                <table>
                  <tr>
                    <th>е-мейл</th>
                    <th>Название теста</th>
                    <th></th>
                  </tr>
                  <tr>
                    <td>$mail</td>
                    <td>$quiz</td>
                    <td></td>
                  </tr>
                </table>
                </fieldset>";

//                echo "htj5";
            }
        }
    }
}
?>

</body>
</html>