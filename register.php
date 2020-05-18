<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録フォーム</title>
  <link rel="stylesheet" href="/assets/css/register_style.css">
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="/assets/js/register.js"></script>
</head>

<body>

<?php
if(isset($_POST['register_button'])) {
  echo '
  <script>
  $(document).ready(function(){
      $("#first").hide();
      $("#second").show();
  });
  </script>
  ';
}

?>

<div class="reg_wrap">

  <div class="login_box">
    <div class="login_header">
      <h1>Social Book</h1>
      <p class="text">ログイン又はサインイン</p>
    </div>
    <!-- header -->

    <div id="first">
      <form action="register.php" method="post" class="form">
        <input type="email" name="log_email" placeholder="メールアドレス"  class="form_input" value="<?php if(isset($_SESSION['log_email'])) { echo $_SESSION['log_email']; } ?>" required>
        <br>
        <input type="password" name="log_password" placeholder="パスワード" class="form_input">
        <br>
        <input type="submit" value="ログイン" name="login_button" class="btn_submit">
        <br>
        <?php if(in_array("メールアドレス又はパスワードが正しくありません。<br>",$error_array)) echo "メールアドレス又はパスワードが正しくありません。<br>";?>
        <a href="#" id="signup" class="signup">登録はこちら</a>
      </form>
    </div>
    <!-- first -->

    <div id="second">
      <form action="register.php" method="post" class="form">
        <input type="text" name="reg_fname" placeholder="姓" class="form_input" value="<?php if(isset($_SESSION['reg_fname'])) { echo $_SESSION['reg_fname']; } ?>" required>
        <br>
        <?php if(in_array("2文字以上25字以内で入力してください。<br>",$error_array)) echo "2文字以上25字以内で入力してください。<br>";?>

        <input type="text" name="reg_lname" placeholder="名"  class="form_input" value="<?php if(isset($_SESSION['reg_lname'])) { echo $_SESSION['reg_lname']; } ?>" required>
        <br>
        <?php if(in_array("2文字以上25字以内で入力してください。<br>",$error_array)) echo "2文字以上25字以内で入力してください。<br>";?>


        <input type="email" name="reg_email" placeholder="メールアドレス"  class="form_input" value="<?php if(isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; } ?>" required>
        <br>

        <input type="email2" name="reg_email2" placeholder="メールアドレス確認"  class="form_input" value="<?php if(isset($_SESSION['reg_email2'])) { echo $_SESSION['reg_email2']; } ?>" required>
        <br>
        <?php if(in_array("このメールアドレスはすでに使用中です。<br>",$error_array)) echo "このメールアドレスはすでに使用中です。<br>";
        else if(in_array("無効な形式です。<br>",$error_array)) echo "無効な形式です。<br>";
        else if(in_array("メールアドレスが違います<br>",$error_array)) echo "メールアドレスが違います<br>";?>

        <input type="password" name="reg_password" placeholder="パスワード"  class="form_input" required>
        <br>
        <input type="password" name="reg_password2" placeholder="パスワード確認"  class="form_input" required>
        <br>
        <?php if(in_array("パスワードが違います。<br>",$error_array)) echo "パスワードが違います。<br>";
        else if(in_array("パスワードは英数字で入力してください。<br>",$error_array)) echo "パスワードは英数字で入力してください。<br>";
        else if(in_array("パスワードは5字以上30字以内で入力してください。<br>",$error_array)) echo "パスワードは5字以上30字以内で入力してください。<br>";?>
        <input type="submit" value="登録" name="register_button" class="btn_submit">
        <br>

        <?php if(in_array("<span style='color:#14c800;'>ログインに成功しました。</span>",$error_array)) echo "<span style='color:#14c800;'>ログインに成功しました。</span>";?>

        <a href="#" id="signin" class="signin">サインインはこちら</a>
      </form>
    </div>
    <!-- second -->

  </div>

</div>

</body>
</html>
