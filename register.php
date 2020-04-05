<?php
require 'config/config.php';
require 'includes/form_handlers/register_handlers.php';

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
    <?php if(in_array("2文字以上25字以内で入力してください。<br>",$error_array)) echo "2文字以上25字以内で入力してください。<br>";?>


    <input type="text" name="reg_lname" placeholder="名" value="<?php if(isset($_SESSION['reg_lname'])) { echo $_SESSION['reg_lname']; } ?>" required>
    <br>
    <?php if(in_array("2文字以上25字以内で入力してください。<br>",$error_array)) echo "2文字以上25字以内で入力してください。<br>";?>


    <input type="email" name="reg_email" placeholder="メールアドレス" value="<?php if(isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; } ?>" required>
    <br>

    <input type="email2" name="reg_email2" placeholder="メールアドレス確認" value="<?php if(isset($_SESSION['reg_email2'])) { echo $_SESSION['reg_email2']; } ?>" required>
    <br>
    <?php if(in_array("このメールアドレスはすでに使用中です。<br>",$error_array)) echo "このメールアドレスはすでに使用中です。<br>";
    else if(in_array("無効な形式です。<br>",$error_array)) echo "無効な形式です。<br>";
    else if(in_array("メールアドレスが違います<br>",$error_array)) echo "メールアドレスが違います<br>";?>

    <input type="password" name="reg_password" placeholder="パスワード" required>
    <br>
    <input type="password" name="reg_password2" placeholder="パスワード確認" required>
    <br>
    <?php if(in_array("パスワードが違います。<br>",$error_array)) echo "パスワードが違います。<br>";
    else if(in_array("パスワードは英数字で入力してください。<br>",$error_array)) echo "パスワードは英数字で入力してください。<br>";
    else if(in_array("パスワードは5字以上30字以内で入力してください。<br>",$error_array)) echo "パスワードは5字以上30字以内で入力してください。<br>";?>
    <input type="submit" value="登録" name="register_button">
    <br>
    <?php if(in_array("<span style='color:#14c800;'>ログインに成功しました。</span>",$error_array)) echo "<span style='color:#14c800;'>ログインに成功しました。</span>";?>

  </form>
</body>
</html>
