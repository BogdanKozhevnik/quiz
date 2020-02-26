<?php
$servername="db";
$username="root";
$password="root";
$dbname="default";
// подключаемся к серверу
$mysqli = new mysqli($servername, $username, $password, $dbname);

######################
# SETTINGS START
######################

// Add login/password pairs below, like described above
// NOTE: all rows except last must have comma "," at the end of line
//$check_sql = $mysqli->query("SELECT `login`, `password` FROM `users` UNION SELECT `login`, `password` FROM `admin`");
//$check_arr = $check_sql->fetch_all();
    $check_admin_sql = $mysqli->query("SELECT `email`, `password` FROM `admin`");
    $check_admin = $check_admin_sql->fetch_all();
    $admin = [$check_admin[0][0]=>$check_admin[0][1]];
    $check_users_sql = $mysqli->query("SELECT `email`, `password` FROM `users`");
    $check_users = $check_users_sql->fetch_all();
    $check_arr = array_merge($check_admin, $check_users);
if (empty($check_arr)) {
        echo ("Пользователей нет. база пуста!");
    } else {

    foreach ($check_arr as $item=> $value) {
        $email_db = $check_arr[$item]['0'];
        $password_db = $check_arr[$item]['1'];

        $LOGIN_INFORMATION[$email_db] = $password_db;
    }
}

// request login? true - show login and password boxes, false - password box only
define('USE_USERNAME', true);

// User will be redirected to this page after logout
define('LOGOUT_URL', 'http://www.example.com/');

// time out after NN minutes of inactivity. Set to 0 to not timeout
define('TIMEOUT_MINUTES', 0);

// This parameter is only useful when TIMEOUT_MINUTES is not zero
// true - timeout time from last activity, false - timeout time from login
define('TIMEOUT_CHECK_ACTIVITY', true);

###################
# SETTINGS END

// show usage example
if(isset($_GET['help'])) {
    die('Include following code into every page you would like to protect, at the very beginning (first line):<br>&lt;?php include("' . str_replace('\\','\\\\',__FILE__) . '"); ?&gt;');
}

// timeout in seconds
$timeout = (TIMEOUT_MINUTES == 0 ? 0 : time() + TIMEOUT_MINUTES * 60);

// logout?
if(isset($_GET['logout'])) {
    setcookie("verify", '', $timeout, '/'); // clear password;
    header('Location: ' . LOGOUT_URL);
    exit();
}

if(!function_exists('showLoginPasswordProtect')) {
// show login form
    function showLoginPasswordProtect($error_msg) {
        ?>
        <html>
        <head>
            <title>Для доступа к странице введите пароль</title>
            <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
            <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
        </head>
        <body>
        <style>
            input { border: 1px solid black; }
        </style>
        <div style="width:500px; margin-left:auto; margin-right:auto; text-align:center">
            <form method="post">
                <h3>Введите имя пользователя и пароль:</h3>
                <font color="red"><?php echo $error_msg; ?></font><br />Пользователь:<br />
                <input type="input" name="access_login" /><br />E-mail:<br />
                <?php if (USE_USERNAME) echo '<input type="email" name="access_email" required="required" /><br />Пароль:<br />' ;?>
                <input type="password" name="access_password" required="required" /><p></p>
                <input type="submit" name="Submit" value="Войти" />
                <input type="submit" name="register" value="Зарегистрироваться" />
            </form>
            <br />
        </div>
        </body>
        </html>

        <?php
// stop at this point
        die();
    }
}

if (isset($_POST['register'])) {
    $login = isset($_POST['access_login']);
    $eMail = isset($_POST['access_email']) ? $_POST['access_email'] : '';
    $pass = $_POST['access_password'];
    $reset_auto_increment = $mysqli->query("ALTER TABLE `users` AUTO_INCREMENT = 1");
    $register = $mysqli->query("INSERT INTO `users` (`id`, `login`, `password`) VALUES  ( NULL, `$login`, '$eMail', '$pass')");
    showLoginPasswordProtect("");
}

// user provided password
if (isset($_POST['access_password']) && isset($_POST['access_email'])) {

    $eMail = isset($_POST['access_email']) ? $_POST['access_email'] : '';
    $pass = $_POST['access_password'];

    if (empty($check_arr)) {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        showLoginPasswordProtect("Вы не зарегистрированы, зарегистрируйтесь!");
    }
    else {
        foreach ($LOGIN_INFORMATION as $user=>$password ) {
            if ($eMail == $user) {
                $email_db = $user;
                $password_db = $password;
                $true = array_key_exists($eMail, $LOGIN_INFORMATION);
            }
        }
//        $password_db = array_search($pass, $LOGIN_INFORMATION); // return value, need key
    }

    $LOGIN_INFORMATION = array(
        "$email_db" => "$password_db");

    if (USE_USERNAME && ( !array_key_exists($eMail, $LOGIN_INFORMATION))) {
//    (!USE_USERNAME && !in_array($pass, $LOGIN_INFORMATION) || (USE_USERNAME && ( !array_key_exists($login, $LOGIN_INFORMATION) || $LOGIN_INFORMATION[$login] != $pass ) )
        showLoginPasswordProtect("Пользователя с таким e-mail нет!");
    }
    else if (!USE_USERNAME && !in_array($pass, $LOGIN_INFORMATION) || $LOGIN_INFORMATION[$eMail] != $pass) {
        showLoginPasswordProtect("Е-mail и пароль не совпадают!");
        } else {
        // set cookie if password was validated
        setcookie("verify", md5($eMail.'%'.$pass), $timeout, '/');
        // Some programs (like Form1 Bilder) check $_POST array to see if parameters passed
        // So need to clear password protector variables
        unset($_POST['access_email']);
        unset($_POST['access_password']);
        unset($_POST['Submit']);
    }
}
else {
    // check if password cookie is set
    if (!isset($_COOKIE['verify'])) {
        showLoginPasswordProtect("");
    }
    // check if cookie is good
    $found = false;
    foreach($LOGIN_INFORMATION as $key=>$val) {
        $lp = (USE_USERNAME ? $key : '') .'%'.$val;
        if ($_COOKIE['verify'] == md5($lp)) {
            $found = true;
            // prolong timeout
            if (TIMEOUT_CHECK_ACTIVITY) {
                setcookie("verify", md5($lp), $timeout, '/');
            }
            break;
        }
    }
    if (!$found) {
        showLoginPasswordProtect("");
    }
}

?>
