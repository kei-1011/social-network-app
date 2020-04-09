<?php
class Post {
  private $user_obj;
  private $con;

  public function __construct($con,$user){
    $this->con = $con;
    $this->user_obj = new User($con,$user);
    //配列に格納
  }
  public function submitPost($body,$user_to) {
    $body = strip_tags($body);
    $body = mysqli_real_escape_string($this->con,$body);
    $check_empty = preg_replace('/\s+/','',$body);  //空白文字を削除

    if($check_empty != "") {

      //current data and time
      $date_added = date("Y-m-d H:i:s");

      //ユーザー名を取得
      $added_by = $this->user_obj->getUsername();

      //プロフィールページが表示されている場合、user_toはnoneにする
      if($user_to == $added_by) {
        $user_to = "none";
      }

      //DBに登録
      $query = mysqli_query($this->con,"INSERT INTO posts VALUES(NULL,'$body','$added_by','$user_to','$date_added','no','no','0')");
      $returned_id = mysqli_insert_id($this->con);

      //update post
      $num_posts = $this->user_obj->getNumPosts();
      $num_posts++;
      $update_query = mysqli_query($this->con,"UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
    }
  }
}
