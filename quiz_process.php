<!DOCTYPE html>
<?php
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

//require_once 'db_connection.php'; // подключаем скрипт

include("login/users_login.php");
$servername="db";
$username="root";
$password="root";
$dbname="default";
// подключаемся к серверу
$mysqli = new mysqli($servername, $username, $password, $dbname);
session_start();
?>

<?php
// получаю get id quiz з сторінки quiz_list
$quiz_idGet = intval($_GET['quiz_id']);

// cheking last quiz id
$quiz_sql = $mysqli->query("SELECT MAX(`id_quiz`) FROM `quiz`");
$quiz_arr = mysqli_fetch_array($quiz_sql);
$quiz_count = intval($quiz_arr[0]);
$quiz = $mysqli->query("SELECT `id_quiz` FROM `quiz`");
$quiz_all = $quiz->fetch_all();

// количество тестов
$maxid_quiz = $mysqli->query("SELECT COUNT(`id_quiz`) FROM `quiz`") or die($mysqli->error . __LINE__);
while ($row = mysqli_fetch_array($maxid_quiz)) {
    $count_quiz = intval($row[0]);
}
// количество вопросов
$question_sql = $mysqli->query("SELECT COUNT(`id_question`) FROM `questions`") or die($mysqli->error . __LINE__);
$question_arr = $question_sql->fetch_array();
$questions = intval($question_arr['0']);

$quest=1;
foreach ($quiz_all as $item => $value) {
    $questions_sql = $mysqli->query("SELECT `question` FROM `questions`");
    $questions_array_all = $questions_sql->fetch_all();
    $questions_sql_group = $mysqli->query("SELECT `id_question`, `question` FROM `questions` where `id_quiz`='$quest'");
    $questions_array_group["quiz".$quest] = $questions_sql_group->fetch_all();
    foreach ($questions_array_group as $qroup=>$question) {
        $count_questions_group[$qroup] = count($question);
    }
    $count_questions_all = count($questions_array_all);
    $quest+=1;
}

// количество ответов
$answ=1;
foreach ($questions_array_all as $item => $value) {
    $count_answer_sql = $mysqli->query("SELECT COUNT(`id_answer`) FROM `answers` WHERE `id_question` = '$answ'");
    $count_answer_arr = $count_answer_sql->fetch_array();
    $count_answer = intval($count_answer_arr[0]);
    $answer_arr['question'.$answ] = $count_answer;
    $answ_count = array_sum($answer_arr);
    $answ+=1;
}

//  количество всех ответов в бд
$count_all_answers_sql = $mysqli->query(" SELECT COUNT(`id_question`) FROM `answers`");
$count_all_answers_arr = mysqli_fetch_array($count_all_answers_sql);
$count_all_answers = intval($count_all_answers_arr['COUNT(`id_question`)']);

//  проходка по вопросам и пренадлежащим ему ответам
for($q=1;$q<=$count_questions_all;$q++) {
//  количество ответов по № вопроса
    $count_answers_group_sql = $mysqli->query(" SELECT COUNT(`id_question`) FROM `answers` where `id_question`='$q'");
    $count_answers_group_arr = mysqli_fetch_all($count_answers_group_sql);
    $count_answers_group[$q] = intval($count_answers_group_arr[0]['0']);
}
// select data of quiz
$data_quiz_sql = $mysqli->query("SELECT * FROM `quiz` INNER JOIN `questions` using(`id_quiz`) INNER JOIN answers USING (`id_question`) WHERE `id_quiz`='$quiz_idGet'");
$data_quiz_arr = mysqli_fetch_all($data_quiz_sql);
$start = $data_quiz_arr[0]['0'];

$go = 0;
$next = 0;
foreach ($data_quiz_arr as $key =>$value) {
    $end = count($data_quiz_arr);
}
$step=1;
// обработчик сессии
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}
if (!isset($_SESSION['next_question'])) {
    $_SESSION['next_question'] = 0;
}

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
// обработчик перехода на след. вопрос
if (isset($_POST['next_question'])) {
    if($_SESSION['score']<=$count_questions_group["quiz".$quiz_idGet]) {
        @$_SESSION['next_question'] += 1;
        @$question_id = intval($_SESSION['next_question']);
        @$_SESSION['score'] += 1 ;
        @$question_idarr = intval($_SESSION['score']);
        @$cag=$count_answers_group[$question_idarr];

        foreach ($_POST as $key => $value) {
            $key_value = intval($key);
            if ($key ='on' && $value != "Дальше") {
                $reset_auto_increment = $mysqli->query("ALTER TABLE `quiz_process` AUTO_INCREMENT = 1");
                $reset_auto_increment = $mysqli->query("ALTER TABLE `quiz_result` AUTO_INCREMENT = 1");
                $user_answer_sql = $mysqli->query("SELECT `id_answer`,`answer`,`value` FROM `answers` where `id_answer`='$key_value'");
                $user_answer_arr = $user_answer_sql->fetch_array();

                $user_answer = $user_answer_arr['answer'];
                $answer_check = $user_answer_arr['value'];

                // find title of question
                $questionTitle = 0;
                if ($question_idarr > 1 ) {
                    $questionTitle = $questionTitle+$count_answers_group[$question_idarr];
                }
                $titleQuestion = $data_quiz_arr[$questionTitle][4];  // title of question

                // insert resultates in quiz_result
                $res_process = $mysqli->query( "INSERT INTO `quiz_process` (`login`, `email`, `quiz_title`, `title_question`, `user_answer`, `correct_answer`) VALUES ('".$activeUserLogin."','".$activeUserEmail."','".$data_quiz_arr[0][2]."','".$titleQuestion."','".$user_answer."','".$answer_check."')");
                if ($question_idarr<2) {
                    if (empty($try)) {
                        $try=$try+1;
                       $res_result = $mysqli->query( "INSERT INTO `quiz_result` (`login`, `email`, `quiz_title`, `try`) VALUES ('".$activeUserLogin."','".$activeUserEmail."','".$data_quiz_arr[0][2]."','".$try."')");

                    }
                    else {
                        $try=$try+1;
                        $fillResult = $mysqli->query("UPDATE `quiz_result` SET  `try`='$try' WHERE `quiz_title`='$thiQuizTitle' and `login`='$activeUserLogin';");

                    }
                }
            }

        }
    }
}

?>

<head>
    <title>Quiz</title>
    <script src="js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/quiz_process.css">
</head>
<body>
<form name="quiz_list" class="quiz_list" id="quiz_list" method="post">
    <div>
        <fieldset class="quiz_fieldset">

            <!--        вивод еллементов на страницу -->
            <?php
            $i = 0;
            echo "<label class='quizz'><b>№" . $data_quiz_arr[0][0] . "</b></label>";
            echo "<p class='quizz'><label><b>Тема: " . $data_quiz_arr[$i][2] . "</b></label></p>";
            echo "<p class='quizz'><label><b>Описание: " . $data_quiz_arr[0][3] . "</b></label></p>";

            echo "<fieldset class='quiz_fieldset2'>";
            echo "<span><u>колличество вопросов: <b>".$count_questions_group["quiz".$quiz_idGet]."</b></u></span>";

            if($_SESSION['next_question'] == 0) {
                echo "<ul>&nbsp;&nbsp;<b>".$data_quiz_arr[0][4]."</b>";
            }
            else {

                @$next_step = ($cag); // вивод названия вопроса
                echo "<ul>&nbsp;&nbsp;<b>".@$data_quiz_arr[$next_step][4]."</b>";
            }

            $session_id = intval($_SESSION['score']+$start   );
            foreach ($data_quiz_arr as $item) {
                if ($item['0'] == $session_id) { // тут сешн ід стартує з номера першої відповіді
                    echo "<li>" . $item[6] . "<input type='checkbox' class='' id='' name='$item[5]'></li>";
                }
            }
            // переход на страницу результатов
            if ($_SESSION['score']>=$count_questions_group["quiz".$quiz_idGet]) {
                $_SESSION['score'] = 0;
                $_SESSION['next_question'] = 0;
                $qiuz = $_GET['quiz_id'];
                header('Location: quiz_result.php?quiz_id=' . $qiuz);
                exit;
            }
            echo "</ul>";
            echo "</fieldset>";
            ?>
        </fieldset>
    </div>
    <input type="submit" name="next_question" id="next_question" class="next" value="Дальше"/>
</form>

</body>
</html>
