<?php

class ConnectDB {

   public function msql() {
       $servername = "db";
       $username = "root";
       $password = "root";
       $dbname = "default";

       $mysqli = new mysqli($servername, $username, $password, $dbname);
    if (!$mysqli) {
//        echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
//        echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
//        echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

//       echo "Соединение с MySQL установлено!" . PHP_EOL;
//       echo "Информация о сервере: " . mysqli_get_host_info($mysqli) . PHP_EOL;
       return $mysqli;
}

    public  function mySqlQuery($request) {
      $query =  $this->msql()->query($request);
      return $query;
    }

    public function getActiveUser($email) {
        $check_users_sql = $this->msql()->query("SELECT `login`, '$email' FROM `users` where `email`='$email' UNION SELECT `login`, '$email' FROM `admin` where `email`='$email'");
        $check_users = $check_users_sql->fetch_all();
        if (empty($check_users)) {
            echo ("Пользователей нет. база пуста!");
        } else {
            foreach ($check_users as $item=> $value) {
                $login_db = $check_users[$item]['0'];
                $email_db = $check_users[$item]['1'];

                $LOGIN_INFORMATION[$email_db] = $login_db;
            }
        }
        return $LOGIN_INFORMATION;
    }

    // add all loggins and passwords to hash for compare with active user hash and find who is active now
    public function hashMysqlLogPass() {
        $check_sql = $this->msql()->query("SELECT `email`, `password` FROM `users` UNION SELECT `email`, `password` FROM `admin`");
        $check_arr = $check_sql->fetch_all();
        foreach ($check_arr as $item=> $value) {
            $email_db = $check_arr[$item]['0'];
            $password_db = $check_arr[$item]['1'];

            $LOGIN_INFORMATION[$email_db] = $password_db;
        }
        // check if cookie is good
        $s = 0;
        foreach($LOGIN_INFORMATION as $key=>$val) {
            $name = stristr($key, '@', true);
//        $name = $new_msql->getUserName($eMail); // get user name from db using email

            $loginPassword[$key] = md5($key .'%'.$val);
        $s++;
        }
        return $loginPassword;
    }

//    public function getUserName ($eMail) {
//
//        $name_sql = $this->msql()->query("SELECT `login` FROM `users`  WHERE `email`='$eMail' UNION  SELECT `login` FROM `admin` WHERE `email`='$eMail'");
//        $name_array = $name_sql->fetch_array();
//        $name = $name_array['login'];
//        return $name;
//    }

//    mysqli_close($mysqli);
}
