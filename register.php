<?php
$con = mysqli_connect('localhost', 'root', 'root', 'social');

if (mysqli_connect_errno()) {
  echo "接続失敗" . mysqli_connect_errno();
}

$query = mysqli_query($con, "INSERT INTO test VALUES('2','Optimus Prime')");

//エラーを防ぐための変数の宣言
$fname = "";
$lname = "";
$email = "";
$email2 = "";
$pass = "";
$pass2 = "";
$date = "";
$error_array = "";

if (isset($_POST['register_button'])) {
  //registration from values

  //フォームから受け取った値をエンティティ化け
  $name = htmlspecialchars($_POST['reg_fname'], ENT_QUOTES);
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
    <input type="text" name="reg_frame" placeholder="姓" required>
    <br>
    <input type="text" name="reg_lrame" placeholder="名" required>
    <br>
    <input type="email" name="reg_email" placeholder="メールアドレス" required>
    <br>
    <input type="email2" name="reg_email2" placeholder="メールアドレス確認" required>
    <br>
    <input type="password" name="reg_password" placeholder="パスワード" required>
    <br>
    <input type="password2" name="reg_password2" placeholder="パスワード確認" required>
    <br>
    <input type="submit" value="登録" name="register_button">
  </form>
</body>

</html>