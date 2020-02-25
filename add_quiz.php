<!DOCTYPE html>
<html lang="ru">
<?php
include("login/admin_access.php");
//require_once 'db_connection.php'; // подключаем скрипт
//include 'db_connection.php';
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

?>
<head>
    <meta charset="UTF-8">
    <title>Add Quiz</title>
    <link rel="stylesheet" type="text/css" href="css/add_quiz.css">
    <script src="js/jquery-3.4.1.min.js"></script>
</head>

<body><br>
<form method="post" class="quiz_form" id="quiz_form" name="quiz_form">
    <label><b>Номер последнего опросника = №<?php echo $quiz_count ?></b></label>
    <fieldset>
    <label class="label_quiz">ID:
        <input type="text" class="id_quiz" title="ID" name="id_quiz" readonly="readonly" value="<?php echo ($quiz_count+1) ?>"/>
    </label>
    <label for='quiz-title-text' class="quiz-title">Тема опросника:</label><p><input type="text" id='quiz-title-text' name="quiz-title-text" class="quiz-title-text" placeholder="Тема*"/></p>
    <label for='quiz-description-text' class="quiz-description">Описание к опроснику:<p><textarea name="quiz-description-text" id="quiz-description-text" class="quiz-description-text" placeholder="Описание*"></textarea></p>
    </fieldset>
        <button class="save-quiz" name="save-quiz" id="save-quiz" >Добавить опросник </button>
</form>

<?php
if(isset($_POST['save-quiz'])) {
    $quiz_title = $_POST['quiz-title-text'];
    $quiz_description = $_POST['quiz-description-text'];
    // сброс ауто инкремента на последнюю позицию в бд
    $reset_auto_increment = $mysqli->query("ALTER TABLE `quiz` AUTO_INCREMENT = 1");
    // end

    $add_quiz = $mysqli->query("INSERT INTO `quiz` ( `title`, `description`) VALUES ( '$quiz_title', '$quiz_description');");
    $quiz_id = $mysqli->insert_id;
                if ($add_quiz == true){
                   echo "Информация занесена в базу данных";
                }else{
                    echo "Опросник не занесен в базу данных";
                    die('Error: ' . mysqli_error($mysqli));
                }
    header('Location: edit_quiz.php?quiz_id='.$quiz_id);
    exit;
}
?>

</body>
</html>