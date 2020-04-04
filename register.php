<?php
session_start();

$con = mysqli_connect('localhost', 'root', 'root', 'social');

if (mysqli_connect_errno()) {
  echo "接続失敗" . mysqli_connect_errno();
}

$query = mysqli_query($con, "INSERT INTO test VALUES('2','Optimus Prime')");

//エラーを防ぐための変数の宣言
$fname        = "";
$lname        = "";
$email        = "";
$email2       = "";
$pass         = "";
$pass2        = "";
$date         = "";
$error_array  = "";

if (isset($_POST['register_button'])) {
  //registration from values

  /*フォームから受け取った値をエンティティ化*/

  //name
  $fname      = htmlspecialchars($_POST['reg_fname'], ENT_QUOTES);
  $lname      = htmlspecialchars($_POST['reg_lname'], ENT_QUOTES);
  $_SESSION['reg_fname'] = $fname;
  $_SESSION['reg_lname'] = $lname;

  //email
  $email      = htmlspecialchars($_POST['reg_email'], ENT_QUOTES);
  $email2     = htmlspecialchars($_POST['reg_email2'], ENT_QUOTES);
  $_SESSION['reg_email'] = $email;
  $_SESSION['reg_email2'] = $email2;

  //pass
  $password   = htmlspecialchars($_POST['reg_password'], ENT_QUOTES);
  $password2  = htmlspecialchars($_POST['reg_password2'], ENT_QUOTES);

  //date
  $date = date('Y-m-d');  //current date

  //メールが有効な形式かどうかを確認
  if ($email == $email2) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

      $email = filter_var($email, FILTER_VALIDATE_EMAIL);

      //DBにメールが存在するか確認する
      $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");

      //返された行数を数える
      $num_rows = mysqli_num_rows($e_check);

      // 返ってきた行数が０より大きい　→　すでに使用中
      if ($num_rows > 0) {
        echo "このメールアドレスはすでに使用中です。 ";
      }
    } else {
      echo "無効な形式です。";
    }
  } else {
    echo "メールアドレスが違います";
  }

  // 姓名の文字数チェック
  if (strlen($fname) > 25 || strlen($fname) < 2) {
    echo "2文字以上25字以内で入力してください。";
  }
  if (strlen($lname) > 25 || strlen($lname) < 2) {
    echo "2文字以上25字以内で入力してください。";
  }

  //パスワードのチェック
  if ($password != $password2) {
    echo "パスワードが違います。";
  } else {
    if (preg_match('/[^A-Za-z0-9]/', $password)) {
      echo 'パスワードは英数字で入力してください。';
    }
  }

  if (strlen($password) > 30 || strlen($password) < 5) {
    echo 'パスワードは5字以上30字以内で入力してください。';
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録フォーム</title>
</head>

<body>
  <form action="register.php" method="post">
    <input type="text" name="reg_fname" placeholder="姓" value="<?php if(isset($_SESSION['reg_fname'])) { echo $_SESSION['reg_fname']; } ?>" required>
    <br>
    <input type="text" name="reg_lname" placeholder="名" value="<?php if(isset($_SESSION['reg_lname'])) { echo $_SESSION['reg_lname']; } ?>" required>
    <br>
    <input type="email" name="reg_email" placeholder="メールアドレス" value="<?php if(isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; } ?>" required>
    <br>
    <input type="email2" name="reg_email2" placeholder="メールアドレス確認" value="<?php if(isset($_SESSION['reg_email2'])) { echo $_SESSION['reg_email2']; } ?>" required>
    <br>
    <input type="password" name="reg_password" placeholder="パスワード" required>
    <br>
    <input type="password2" name="reg_password2" placeholder="パスワード確認" required>
    <br>
    <input type="submit" value="登録" name="register_button">
  </form>
</body>
</html>
