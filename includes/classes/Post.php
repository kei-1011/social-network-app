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

        //ユーザーに投稿されていなくてもインクルードできるように user_to の文字列を用意する
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

        // フォローしているユーザ、自分の投稿を取得
        $user_logged_obj = new User($this->con, $userLoggedIn);
        if($user_logged_obj->isFriend($added_by)) {

          if($num_iterations++ < $start) {
            continue;
          }

          //10の投稿が読み込まれた時点でbreak
          if($count > $limit) {
            break;
          } else {
            $count++;
          }

          if($userLoggedIn == $added_by) {
            $delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
          }else {
            $delete_button = "";
          }

          $user_details_query = mysqli_query($this->con,"SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
          $user_row = mysqli_fetch_array($user_details_query);
          $first_name = $user_row['first_name'];
          $last_name = $user_row['last_name'];
          $profile_pic = $user_row['profile_pic'];

          ?>
          <script>
          function toggle<?php echo $id; ?>() {
            var target = $(event.target);

            if(!target.is("a")) { // ユーザーリンクをクリックしたとき、コメント欄を表示しない
              var element = document.getElementById('toggleComment<?php echo $id;?>');
              if(element.style.display == "block"){
                element.style.display = "none";
              } else {
                element.style.display = "block";
              }
            }
          }
          </script>
          <?php
          $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
          $comments_check_num = mysqli_num_rows($comments_check);

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

          $str .= "<div class='status_post' onClick='javascript:toggle$id()'>
                      <div class='post_profile_pic'>
                        <img src='$profile_pic' width='50'>
                      </div>

                      <div class='posted_by' style='color:#acacac;'>
                        <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                        $delete_button
                      </div>
                      <div id='post_body'>
                      ".nl2br($body)."
                        <br>
                        <br>
                      </div>

                      <div class='newsfeedPostOptions'>
                        コメント($comments_check_num)&nbsp;&nbsp;&nbsp;
                        <iframe src='like.php?post_id=$id'></iframe>
                      </div>

                  </div>
                  <div class='post_comment' id='toggleComment$id' style='display:none;'>
                  <iframe src='comment_frame.php?post_id=$id' id='comment_frame' frameborder='0'></iframe>
                  </div>
                  <hr>";
        }
        ?>
        <script>

        $(function() {

          $('$post<?php echo $id;?>').on('click',function() {
            bootbox.confirm("この投稿を削除しますか？", function(result) {
              $.post("/includes/form_handlers/delete_post.php?post_id=<?php echo $id;?>", {result:result});

              if(result) {
                location.reload();
              }
            });
          });

        });


        </script>
        <?php
      } // End while loop

      if($count > $limit) {
        $str .= "
        <input type='hidden' class='nextPage' value='". ($page + 1) ."'>
        <input type='hidden' class='noMorePosts' value='false'>";
      } else {
        $str .= "
        <input type='hidden' class='noMorePosts' value='true'>
        <p style='text-align:center;'>No more posts</p>
        ";
      }

    }

    echo $str;
  }
}
