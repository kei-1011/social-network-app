<?php
  require 'config/config.php';
  include("includes/classes/User.php");
  include("includes/classes/Post.php");

  if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];

    //ログインしているユーザに関する情報を取得
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
  } else {
    header("Location:register.php");
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <link rel="stylesheet" href="/assets/css/style.css">

  <style>
  * {
    font-size: 14px;
    font-family: Arial, Helvetica, sans-serif;
  }
  body {
    background-color:#eee;
  }
  </style>
</head>
<body>
  <script>
    function toggle() {
      var element = document.getElementById('comment_section');

      if(element.style.display == "block"){
        element.style.display = "none";
      } else {
        element.style.display = "block";
      }
    }
  </script>

  <?php
  // get id of post
  if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
  }

  $user_query = mysqli_query($con, "SELECT added_by,user_to FROM posts WHERE id='$post_id'");
  $row = mysqli_fetch_array($user_query);

  $posted_to = $row['added_by'];

  if(isset($_POST['postComment'.$post_id])) {
    $post_body = $_POST['post_body'];
    $post_body = mysqli_escape_string($con, $post_body);
    $date_time_now = date("Y-m-d H:i:s");
    $insert_post = mysqli_query($con, "INSERT INTO comments VALUES(NULL,'$post_body', '$userLoggedIn','$posted_to','$date_time_now','no','$post_id')");
    echo "<p>Comment Posted!</p>";
  }
?>

  <form action="comment_frame.php?post_id=<?php echo $post_id;?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="post">
    <textarea name="post_body"></textarea>
    <input type="submit" name="postComment<?php echo $post_id;?>" value="Post">
  </form>

<!-- Load comments -->

<?php
$get_comments = mysqli_query($con,"SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
$count = mysqli_num_rows($get_comments);

if($count != 0) {
  while($comment = mysqli_fetch_array($get_comments)) {

    $comment_body = $comment['post_body'];
    $posted_to = $comment['posted_to'];
    $posted_by = $comment['posted_by'];
    $date_added = $comment['date_added'];
    $removed = $comment['removed'];

    //タイムスタンプの取得
    $date_time_now = date("Y-m-d H:i:s");         //現在日時
    $start_date = new DateTime($date_added);       //投稿日時
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
    $user_obj = new User($con,$posted_by);
    ?>
      <div class="comment_section">
      <!-- target _parentで親要素にリンクさせる -->
        <a href="<?php echo $posted_by;?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic();?>" title="<?php echo $posted_by;?>" style="float:left;" height="30"></a>
        <a href="<?php echo $posted_by;?>" target="_parent"><b> <?php echo $user_obj->getFirstAndLastName();?> </b></a>
        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo $time_message . "<br>";?>
        <span><?php echo nl2br($comment_body); ?></span>
        <hr>
      </div>
    <?php
  }
} else {
  echo "<center><br><br>No Comments to Show!</center>";
}
?>
</body>
</html>
