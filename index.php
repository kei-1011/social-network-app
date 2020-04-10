<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");
//ページ更新でセッションを破棄する
// session_destroy();

//投稿をDBに送信する
if(isset($_POST['post'])) {
  $post = new Post($con,$userLoggedIn);
  $post->submitPost($_POST['post_text'],'none');
  header("Location:index.php"); // フォームの再送信を防止する
}
?>

    <div class="user_details column">
      <a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $user['profile_pic'];?>" alt=""></a>
      <div class="user_details_left_right">
        <a href="<?php echo $userLoggedIn; ?>">
        <?php
        echo $user['first_name'] . " " . $user['last_name'];
        ?>
        </a>
        <?php
        echo "POSTS:" . $user['num_posts']. "<br>";
        echo "LIKES:" . $user['num_likes'];
        ?>
      </div>
    </div><!-- user_details_column -->

    <div class="main_column column">
      <form action="index.php" method="post" class="post_form">
        <textarea name="post_text" id="post_text" placeholder="言いたいこと"></textarea>
        <input type="submit" value="送信" name="post" id="post_button">
      </form>

        <?php
        // $user_obj = new User($con,$userLoggedIn);
        // echo $user_obj->getFirstAndLastName();
        $post = new Post($con, $userLoggedIn);
        $post->loadPostsFriends();
        //オブジェクトを呼び出す
        ?>
    </div>

  </div><!-- wrapper -->
</body>
</html>
