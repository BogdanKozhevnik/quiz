<!DOCTYPE html>
<html lang="en">
<?php
include("login/admin_access.php");
//include 'db_connection.php';
//require_once 'db_connection.php'; // подключаем скрипт
$servername="db";
$username="root";
$password="root";
$dbname="default";
// подключаемся к серверу
$mysqli = new mysqli($servername, $username, $password, $dbname);
if (!$mysqli) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
echo "Соединение с MySQL установлено!" . PHP_EOL;
echo "Информация о сервере: " . mysqli_get_host_info($mysqli) . PHP_EOL;

?>
<head>
    <meta charset="UTF-8">
    <title>Edit Quiz</title>
    <link rel="stylesheet" type="text/css" href="css/edit_quiz_list.css">
    <script src="js/jquery-3.4.1.min.js"></script>
</head>
<body>
<?php
// select data
$data_quiz_sql = $mysqli->query("SELECT * FROM `quiz` INNER JOIN `questions` using(`id_quiz`) INNER JOIN answers USING (`id_question`)");
//    $data_quiz_sql = $mysqli->query("SELECT `id_question`,`question` FROM `quiz` INNER JOIN `questions` using(`id_quiz`) INNER JOIN answers USING (`id_question`) WHERE `id_quiz`='$quiz_idGet'");
$data_quiz_arr = mysqli_fetch_all($data_quiz_sql);

// количество тестов
$maxid_quiz = $mysqli->query("SELECT COUNT(`id_quiz`) FROM `quiz`") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($maxid_quiz)) {
    $count_quiz = intval($row[0]);
}

// кількість питань для кожного тесту
for($q=1; $q<=$count_quiz; $q++) {
    $count_question_sql = $mysqli->query("SELECT COUNT(`id_question`) FROM `questions`");
    $count_question_arr = mysqli_fetch_array($count_question_sql);
    $count_question = intval($count_question_arr[0]);
    $count_question_group_sql = $mysqli->query("SELECT COUNT(`id_question`) FROM `questions` WHERE `id_quiz` = '$q'");
    $count_question_qroup_arr = mysqli_fetch_array($count_question_group_sql);
    $count_question_group[$q] = intval($count_question_qroup_arr[0]);
}

// достаю всі відповіді до тестів
$all_answers_sql = $mysqli->query("SELECT count(`id_answer`) from `answers`");
$all_answers_arr= $all_answers_sql->fetch_array();
$all_answers = intval($all_answers_arr['0']);

// id quiz
$id_quiz_sql = $mysqli->query("SELECT `id_quiz` FROM `quiz`") or die($mysqli->error . __LINE__);
$id_quiz_arr = $id_quiz_sql->fetch_all();

// count questions
$id_question_sql = $mysqli->query("SELECT `id_question` FROM  `questions`");
$id_question_arr = $id_question_sql->fetch_all();
$count_all_questions = count($id_question_arr);

// count answers
$id_answer_sql = $mysqli->query("SELECT `id_answer` FROM `answers`");
$id_answer_arr = $id_answer_sql->fetch_all();
$count_all_answers = count($id_answer_arr);

// вивід на сторінку
echo "<form class='save_all' method='post'>
<button class='add_new_quiz' name='add_new_quiz'>Дoбавить новый тест</button>
</form> ";
$quiz_index = 1; // покажчик кількості тестів

// скрипт
foreach ($id_quiz_arr as $quiz => $index) { // покажчик id теста
    $cq = intval($id_quiz_arr[$quiz]['0']);
    $quiz_id = $cq;

$quiz_sql = $mysqli->query("SELECT `title`,`description` FROM  `quiz` WHERE `id_quiz`='$cq'");
$quiz_arr = $quiz_sql->fetch_array();
$quiz_title = $quiz_arr['title'];
$quiz_description = $quiz_arr['description'];

    $count_question_all_sql = $mysqli->query("SELECT count(`id_question`) FROM  `questions`");
    $count_question_all_qrr = $count_question_all_sql->fetch_array();
    $count_question_all = intval($count_question_all_qrr[0]['0']);

$questions2_sql = $mysqli->query("SELECT `id_question`, `question` FROM `questions` WHERE `id_quiz`='$quiz_id'");
$questions2_arr = $questions2_sql->fetch_all();

    echo "<form method='post'>
<div><fieldset class='fieldset'>
<ul class='id_quiz_$quiz_id'>$quiz_id <button name='del_quiz_$quiz_id'>Delete quiz</button>
<br><input type='text' class='quiz' value='$quiz_title'/>
<p><input type='text' value='$quiz_description'/></p>";

    foreach ($questions2_arr as $item => $value) {
        $question_id2 = $questions2_arr[$item]['0'];
        $question2 = $questions2_arr[$item]['1'];
        $answers_sql = $mysqli->query("SELECT `id_answer`,`answer` FROM `answers` WHERE `id_question`='$question_id2'");
        $answers_arr2 = $answers_sql->fetch_all();
    echo "<ul><li class='question' type='disc'>$question_id2<input type='text' value='$question2'/>
              <button name='del_question_$question_id2'>delete</button></li></ul>";

    foreach ($answers_arr2 as $answ => $ans){
        $answer_id = $answers_arr2[$answ]['0'];
        $answer = $answers_arr2[$answ]['1'];

    echo "<ul><li class='answer' type='circle'><input type='text' value='$answer'/><input type='checkbox' name='$answer_id'/></li></ul>";
    }
    echo "<input type='text' name='add_text_answer_$question_id2' class='add_text_answer'/><input type='checkbox' name='check_answer' class='check_answer'/>correct/wrong  
          <br><button name='add_answer_$question_id2' class='add_answer'>add answer</button><button name='delete_answer_$question_id2' class='delete_answer'>delete answer</button>";
    }
    $add_next_question = $count_question_group[$quiz_index]+1;
    echo"</ul>
<div class='add_new_question'>
<input type='text' class='add_text_question' name='add_text_question_$quiz_id'/>
<button name='add_question_$quiz_id' class='add_question'>add question</button>
</div>
</div></fieldset>";

echo "</form>";

// delete quiz
    foreach ($id_quiz_arr as $id =>$quiz) {
        $del_quiz = $id_quiz_arr[$id]['0'];
        foreach ($_POST as $item => $value) {
            if (isset($_POST['del_quiz_' . $del_quiz])) {
                $delete_quiz = $mysqli->query("DELETE FROM `quiz` WHERE `id_quiz`='$del_quiz'");
                $reset_auto_increment = $mysqli->query("ALTER TABLE `questions` AUTO_INCREMENT = 1");
                header('refresh: 0');
            }
        }
        clearstatcache();
    }

// delete question
    foreach ($id_question_arr as $id => $question) {
        $del_question = $id_question_arr[$id]['0'];
        foreach ($_POST as $item => $value) {
            if (isset($_POST['del_question_' . $del_question])) {
                $delete_question = $mysqli->query("DELETE FROM `questions` WHERE `id_question`='$del_question'");
                $reset_auto_increment = $mysqli->query("ALTER TABLE `questions` AUTO_INCREMENT = 1");
                header('refresh: 0');
            }
        }
        clearstatcache();
    }

// add question
    foreach ($id_quiz_arr as $id => $quiz) {
        $quiz_id = $id_quiz_arr[$id]['0'];
            if (isset($_POST['add_question_' . $quiz_id]) && $quiz_index<$count_quiz) {
                $add_new_question_txt = $_POST['add_text_question_' . $quiz_id];
                $reset_auto_increment = $mysqli->query("ALTER TABLE `questions` AUTO_INCREMENT = 1");
                $add_new_question = $mysqli->query("INSERT INTO `questions` (`id_quiz`, `id_question`, `question`) VALUES ('$quiz_id', NULL, '$add_new_question_txt')");
                header('refresh: 0');
            }
            clearstatcache();
        }

// add answer
    foreach ($id_question_arr as $id => $question) {
        $question_id = $id_question_arr[$id]['0'];
            if (isset($_POST['add_answer_' . $question_id]) && $quiz_index<$count_quiz) {
                if (isset($_POST['check_answer'])) {
                    $checked = $_POST['check_answer'];
                    if ($checked == 'on') {

                        $add_new_answer_txt = $_POST['add_text_answer_' . $question_id];
                        $reset_auto_increment = $mysqli->query("ALTER TABLE `answers` AUTO_INCREMENT = 1");
                        $add_new_answer = $mysqli->query("INSERT INTO `answers` (`id_question`, `id_answer`, `answer`, `correct_answer`, `value`) VALUES  ('$question_id', NULL, '$add_new_answer_txt', '', 'correct')");
                    header('refresh: 0');
                    }
                } else {
                        $add_new_answer_txt = $_POST['add_text_answer_' . $question_id];
                        $reset_auto_increment = $mysqli->query("ALTER TABLE `answers` AUTO_INCREMENT = 1");
                        $add_new_answer = $mysqli->query("INSERT INTO `answers` (`id_question`, `id_answer`, `answer`, `correct_answer`, `value`) VALUES  ('$question_id', NULL, '$add_new_answer_txt', '', 'wrong')");
                    header('refresh: 0');

                }
            }
        }

    // delete answer
    foreach ($id_question_arr as $id => $question) {
        $question_id = $id_question_arr[$id]['0'];
        if (isset($_POST['delete_answer_' . $question_id]) && $quiz_index < $count_quiz) {
            foreach ($_POST as $del_answ => $del_answ) {
                if(is_numeric($del_answ)){
                    $delete_answer = $mysqli->query("DELETE FROM `answers` WHERE `id_answer`='$del_answ'");
                    header('refresh: 0');
                }
            }
        }
    }

    $quiz_index+=1;
// add new quiz
if (isset($_POST['add_new_quiz'])) {
    clearstatcache();
    header('Location: add_quiz.php');
    exit;
}
}
?>

</body>
</html>