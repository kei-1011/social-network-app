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

    //文字列を別の文字列に書き換える　str_replace
    $body = str_replace('\r\n','\n',$body);
    $body = nl2br($body); //<br>を挿入


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

  public function loadPostsFriends($data,$limit) {

    $page = $data['page'];
    $userLoggedIn = $this->user_obj->getUsername();

    if($page == 1) {
      $start = 0;
    } else {
      $start = ($page -1) * $limit;
    }

    $str = "";
    $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

    if(mysqli_num_rows($data_query) > 0) {

      $num_iterations = 0; // ロードした結果の数
      $count = 1;

      while($row = mysqli_fetch_array($data_query)) {
        $id = $row['id'];
        $body = $row['body'];
        $added_by = $row['added_by'];
        $date_time = $row['date_added'];

        //prepare user_to string so it can be include even if not posted to a yser
        if($row['user_to'] == 'none') {
          $user_to = "";
        } else {
          $user_to_obj = new User($this->con, $row['user_to']);
          $user_to_name = $user_to_obj->getFirstAndLastName();
          $user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
        }

        //投稿したアカウントのユーザーが閉鎖されているかを確認する
        $added_by_obj = new User($this->con,$row['user_to']);
        if($added_by_obj->isClosed()) {
          continue;
        }

        if($num_iterations++ < $start)
          continue;

        //10の投稿が読み込まれた時点でbreak
        if($count > $limit) {
          break;
        } else {
          $count++;
        }

        $user_details_query = mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
        $user_row = mysqli_fetch_array($user_details_query);
        $first_name = $user_row['first_name'];
        $last_name = $user_row['last_name'];
        $profile_pic = $user_row['profile_pic'];


        //タイムスタンプの取得
        $date_time_now = date("Y-m-d H:i:s");         //現在日時
        $start_date = new DateTime($date_time);       //投稿日時
        $end_date = new DateTime($date_time_now);     //現在日時
        $interval = $start_date->diff($end_date);     //経過日時

        if($interval->y >= 1) { //->で1経過年を取得する
          if($interval == 1) {
            $time_message = $interval->y . " year ago";    //1年前
          } else {
            $time_message = $interval->y . " years ago";   //数年前
          }
        } else if ($interval->m >=1 ) {     //->mで月
          if($interval->d ==0) {
            $days = " ago";
          } else if($interval->d ==1) {     //　->dで日を取得する
            $days = $interval->d . " day ago";
          } else {
            $days = $interval->d . " days ago";
          }

          if($interval->m ==1) {
            $time_message = $interval->m . " month". $days;
          } else {
            $time_message = $interval->m . " months". $days;
          }


        } else if($interval->d >=1) {
          if($interval->d == 1) {     //　->dで日を取得する
            $time_message = "Yesterday";
          } else {
            $time_message = $interval->d . " days ago";
          }
        } else if($interval->h >=1) {
          if($interval->h ==1) {     //　->hで時間を取得する
            $time_message = $interval->d . " hour ago";
          } else {
            $time_message = $interval->d . " hours ago";
          }
        } else if($interval->i >=1) {
          if($interval->i ==1) {     //　->hで時間を取得する
            $time_message = $interval->i . " minute ago";
          } else {
            $time_message = $interval->i . " minutes ago";
          }
        } else {
          if($interval->s < 30) {     //　->hで時間を取得する
            $time_message = "Just now";
          } else {
            $time_message = $interval->s . " seconds ago";
          }
        }

        $str .= "<div class='status_post'>
                    <div class='post_profile_pic'>
                      <img src='$profile_pic' width='50'>
                    </div>

                    <div class='posted_by' style='color:#acacac;'>
                      <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                    </div>
                    <div id='post_body'>
                      $body
                      <br>
                    </div>
                </div>
                <hr>";
      }
    }

    echo $str;
  }
}
