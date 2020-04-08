<?php
class User {
  private $user;
  private $con;

  public function __construct($con,$user){
    //クラスが呼び出されたらまず実行される
    $this->con = $con;
    // ログインユーザーの情報を取得する
    $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username='$user'");
    $this->user = mysqli_fetch_array($user_details_query);
    //配列に格納
  }
  public function getFirstAndLastName() {
    //名前を呼び出すメソッド
    $username = $this->user['username'];
    $query = mysqli_query($this->con,"SELECT first_name,last_name FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['first_name'] . " " . $row['last_name'];
  }
}
