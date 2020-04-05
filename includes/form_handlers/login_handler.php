<?php

//ログインボタンが押された時
if(isset($_POST['login_button'])) {

  //値が妥当な e-mail アドレスであるかどうかを検証
  $email = filter_var($_POST['log_email'], FILTER_VALIDATE_EMAIL);;

  //セッションにメールアドレスを格納
  $_SESSION['log_email'] = $email;

  //パスワードをmd5に
  $password = md5($_POST['log_password']);

  //入力された値とDBテーブルのメールアドレス、パスワードが一致するものがあるか確認する
  $check_database_query = mysqli_query($con,"SELECT * FROM users WHERE email='$email' AND password='$password'");

  //上記で取得できた行数を数える
  $check_login_query = mysqli_num_rows($check_database_query);

  if($check_login_query == 1) {
    $row = mysqli_fetch_array($check_database_query); //連想配列で取得する
    $username = $row['username'];

    $_SESSION['username'] = $username;

    header("Location:index.php");
    exit();
  } else {
    array_push($error_array,"メールアドレス又はパスワードが正しくありません。<br>");
  }
}
