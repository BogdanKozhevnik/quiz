<!DOCTYPE html>
<html lang="ru">

<?php
include("login/admin_access.php");
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

// cheking last quiz id
$quiz_sql = $mysqli->query("SELECT MAX(`id_quiz`) FROM `quiz`");
$quiz_arr = mysqli_fetch_array($quiz_sql);
$quiz_count = intval($quiz_arr[0]);

// получаю get id quiz з сторінки add_quiz
$quiz_idGet = intval($_GET['quiz_id']);
$quiz_idRequest = intval($_REQUEST['quiz_id']);
$quiz_id = $mysqli->query("SELECT `id_quiz` FROM `quiz`");

// количество тестов
$maxid_quiz = $mysqli->query("SELECT COUNT(`id_quiz`) FROM `quiz`") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($maxid_quiz)) {
    $count_quiz = intval($row[0]);
}
// количество вопросов
$question_sql = $mysqli->query("SELECT COUNT(`id_question`) FROM `questions`") or die($mysqli->error . __LINE__);
$question_arr = mysqli_fetch_array($question_sql);
$questions_count = intval($question_arr['0']);
// количество ответов
$answer_sql = $mysqli->query("SELECT COUNT(`id_answer`) FROM `answers`") or die($mysqli->error . __LINE__);
$answer_arr = mysqli_fetch_array($answer_sql);
$answers_count = intval($answer_arr['0']);
// end
$count_question = ($questions_count+1);
$count_answer = ($answers_count+1);
$count = 1;
$ans = 1;

if(isset($_POST['save_result'])) {
    $str = strval($count);
    $question_title = ('question_num');
    $question_id = $question_title.$str;

    // взяти тільки масив потрібних елементів
    foreach ($_POST as $key => $value) {
        $quiz = $_POST['questions'];
    }

    foreach ($quiz as $key => $value) {
        $question=$quiz[$count]['question'];
        $answers=$quiz[$count]['answers'];
        $checkbox = $quiz[$count]['checkbox'];
        $number_checkbox = intval($_POST['count_checkbox']);
        // add checked, unchecked when name of checkbox = radio_№
        $number_answ_checkbox = intval($_POST['count_checkbox']);
        // array all the answers
        $all_answers = $number_answ_checkbox;
        // end
        if($quiz[$count]='question') {
            // сброс ауто инкремента на последнюю позицию в бд
            $reset_auto_increment = $mysqli->query("ALTER TABLE `questions` AUTO_INCREMENT = 1");
            // end
            $question_add = $mysqli->query("INSERT INTO `questions` (`id_quiz`, `id_question`, `question`) VALUES ('$count_quiz', '$count_question', '$question')");

            foreach ($answers as $answ => $answer) {
                if ($checkbox[$ans]=='on'){
                    // сброс ауто инкремента на последнюю позицию в бд
                    $reset_auto_increment = $mysqli->query("ALTER TABLE `answers` AUTO_INCREMENT = 1");
                    // end
                $answer_add = $mysqli->query("INSERT INTO `answers` (`id_question`, `id_answer`, `answer`, `correct_answer`, `value`) VALUES ('$count_question', '$count_answer', '$answer', '$answer', 'correct')");
                $ans+=1;
                $count_answer+=1;
                }

                else {
                    // сброс ауто инкремента на последнюю позицию в бд
                    $reset_auto_increment = $mysqli->query("ALTER TABLE `answers` AUTO_INCREMENT = 1");
                    // end
                    $answer_add = $mysqli->query("INSERT INTO `answers` (`id_question`, `id_answer`, `answer`, `correct_answer`, `value`) VALUES ('$count_question', '$count_answer', '$answer', '', 'wrong')");
                    $ans+=1;
                    $count_answer+=1;
                }
            }
        }

        $count+=1;
        $count_question+=1;
    }
    if ($question_add == true) {
        echo "Вопросы занесены в базу данных! ";
    }else {
        echo "Вопросы не занесены в базу данных! ";
        die('Error: ' . mysqli_error($mysqli));
    }
    if ($answer_add == true) {
        echo "Ответы занесенs в базу данных! ";
    }else {
        echo "Ответы не занесены в базу данных! ";
        die('Error: ' . mysqli_error($mysqli));
    }
    // закрываем подключение
    mysqli_close($mysqli);
    // go to edit
    header('Location: edit_quiz_list.php');
    exit;
}
?>
<!--вивід на сторінку-->
<head>
    <meta charset="UTF-8">
    <title>Admin Quiz</title>
    <link rel="stylesheet" type="text/css" href="css/edit_quiz.css">
    <script src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/add_quest_answ.js"></script>
</head>

<body>
<form class="form_admin" method="post" action="edit_quiz.php">
    <label class="label_quiz">Quiz №:
        <input type="text" class="id_quiz" title="ID" name="id_quiz" readonly="readonly" value="<?php echo $quiz_idGet ?>"/>
    </label>
    <fieldset>
    <ul id='questions'></ul>
    <label for='question-input'></label><input type="text" id='question-input' name="question-input" placeholder="Вопрос*"/>
    <button class="question-btn" name="question-btn" id="question-btn" onclick="addInputQuestion()">Добавить вопрос</button>

    </fieldset>
    <br>
    <div class="admin_tools">
        <input type="submit" class="save_result" title="" name="save_result" id="save_result" value="Сохранить"/>
    </div>
    <input type=hidden value="" id="countquests">
    <input type=hidden value="" id="countcheckbox">
    <input type=hidden value="" id="countchecked">
    <input type=hidden value="" id="countunchecked">
</form>
<script>
    function stopDefAction(evt) {
        evt.preventDefault();
        evt.stopPropagation();
    }
    document.querySelector('button').addEventListener(
        'click', stopDefAction, false);
</script>

</body>
</html>
