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
  public function getUsername() {
    return $this->user["username"];
  }

  public function getNumPosts() {
    $username = $this->user['username'];
    $query = mysqli_query($this->con,"SELECT num_posts FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['num_posts'];
  }

  public function getFirstAndLastName() {
    //名前を呼び出すメソッド
    $username = $this->user['username'];
    $query = mysqli_query($this->con,"SELECT first_name,last_name FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['first_name'] . " " . $row['last_name'];
  }


  //プロフィール写真を取得
  public function getProfilePic() {
    $username = $this->user['username'];
    $query = mysqli_query($this->con,"SELECT profile_pic FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    return $row['profile_pic'];
  }

  public function isClosed() {
    $username = $this->user['username'];
    $query = mysqli_query($this->con,"SELECT user_closed FROM users WHERE username='$username'");
    $row = mysqli_fetch_array($query);

    if($row['user_closed'] == 'yes') {
      return true;
    } else {
      return false;
    }
  }

  public function isFriend($username_to_check) {
    $usernameComma = "," . $username_to_check . ",";

    // フォローしているユーザー または　自分かどうかをチェックする
    if(strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username']) {
      return true;
    } else {
      return false;
    }
  }
}
