<?php
//エラーを防ぐための変数の宣言
$fname        = "";
$lname        = "";
$email        = "";
$email2       = "";
$pass         = "";
$pass2        = "";
$date         = "";
$error_array  = array();
//array_pushでエラー文字列を配列に格納していく

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
        array_push($error_array,"このメールアドレスはすでに使用中です。<br>");
      }
    } else {
      array_push($error_array,"無効な形式です。<br>");
    }
  } else {
    array_push($error_array,"メールアドレスが違います<br>");
  }

  // 姓名の文字数チェック
  if (strlen($fname) > 25 || strlen($fname) < 2) {
    array_push($error_array,"2文字以上25字以内で入力してください。<br>");
  }
  if (strlen($lname) > 25 || strlen($lname) < 2) {
    array_push($error_array,"2文字以上25字以内で入力してください。<br>");
  }

  //パスワードのチェック
  if ($password != $password2) {
    array_push($error_array,"パスワードが違います。<br>");
  } else {
    if (preg_match('/[^A-Za-z0-9]/', $password)) {
      array_push($error_array,'パスワードは英数字で入力してください。<br>');
    }
  }

  if (strlen($password) > 30 || strlen($password) < 5) {
    array_push($error_array,'パスワードは5字以上30字以内で入力してください。<br>');
  }

  /*
  エラーがない場合
  */
  if(empty($error_array)) {
    $password = md5($password);

    //ユーザーネーム(last_name と first_nameの連結)
    $username = strtolower($fname . "_" . $lname);
    //同姓同名をチェックする
    $check_username_query = mysqli_query($con,"SELECT username FROM users WHERE username='$username'");

    /*
    user_name
    重複していた場合↓
    user_name_1
    user_name_2...
    連番で追加していく
    */

    $i = 0;
    // ユーザー名が存在する場合、ユーザー名に番号を追加
    while(mysqli_num_rows($check_username_query) != 0) {
      $i++;
      $username = $username . "_" . $i;
      $check_username_query = mysqli_query($con,"SELECT username FROM users WHERE username='$username'");
    }

    //プロフィール写真の割り当て
    $rand = rand(1,2);    //1と2の間でランダムにする

    //プロフィール写真をランダムで変数に格納する
    if($rand == 1 ) {
      $profile_pic = "/assets/images/profile_pics/defaults/head_deep_blue.png";
    } else if($rand == 2 ){
      $profile_pic = "/assets/images/profile_pics/defaults/head_emerald.png";
    }

    //データベースに送信する
		$query = mysqli_query($con, "INSERT INTO users VALUES (NULL, '$fname', '$lname', '$username', '$email', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

    //メッセージ出力
    array_push($error_array,"<span style='color:#14c800;'>ログインに成功しました。</span>");

    //セッションを空にする
    $_SESSION['reg_fname'] = "";
    $_SESSION['reg_lname'] = "";
    $_SESSION['reg_email'] = "";
    $_SESSION['reg_email2'] = "";
  }
}?>
