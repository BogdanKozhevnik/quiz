<!DOCTYPE html>
<html lang="ru">
<?php
include("login/users_login.php");
$servername="db";
$username="root";
$password="root";
$dbname="default";
// подключаемся к серверу
$mysqli = new mysqli($servername, $username, $password, $dbname);
//session_start();

$count_quiz_sql = $mysqli->query("SELECT COUNT(`id_quiz`) FROM `quiz`") or die($mysqli->error . __LINE__);
$count_quiz_arr = mysqli_fetch_array($count_quiz_sql);
$count_quiz = intval($count_quiz_arr[0]);

$quiz_sql = $mysqli->query("SELECT * FROM `quiz`") or die($mysqli->error . __LINE__);;
$quiz_arr = mysqli_fetch_all($quiz_sql);

$k=1;
foreach ($quiz_arr as $item=>$value) {
    $id[$k] =$value[0];
    $title[$k] = $value[1];
    $description[$k] = $value[2];
        $k+=1;
};

?>
<!--вывод на страницу-->
<head>
    <meta charset="UTF-8">
    <title>List of Quiz</title>
    <link rel="stylesheet" type="text/css" href="css/quiz_list.css">
    <script src="js/jquery-3.4.1.min.js"></script>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
</head>

<body>
<form name="quiz_list" class="quiz_list" id="quiz_list" method="post">
    <fieldset class="quiz_list">
        <?PHP
        $i=1;
        while($i<=$count_quiz){
            echo "<fieldset class=\"quiz_list_second\"><p>№".$id[$i]." ".$title[$i]." Описание: ".$description[$i]."<button name='quiz-$id[$i]'>Перейти</button></p></fieldset>";
            $i+=1;
        }
        $i-=1;
        ?>
    </fieldset>

</form>

<?php
//$user =
// переход к тесту
for ($qiuz=1; $qiuz<=$i; $qiuz++) {
    if(isset($_POST['quiz-'.$qiuz])) {
        $drop = $mysqli->query( "TRUNCATE TABLE `quiz_process`");
        header('Location: quiz_process.php?quiz_id=' . $qiuz);
        exit;
    }
}
?>

</body>

</html>
