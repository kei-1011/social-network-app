<?php
include("includes/header.php");
//ページ更新でセッションを破棄する
// session_destroy();
?>

  <div class="user_details column">
      <a href="#"><img src="<?php echo $user['profile_pic'];?>" alt=""></a>

      <div class="user_details_left_right">
        <a href="#">
        <?php
        echo $user['first_name'] . " " . $user['last_name'];
        ?>
        </a>
        <?php
        echo "POSTS:" . $user['num_posts']. "<br>";
        echo "LIKES:" . $user['num_likes'];
        ?>
      </div>

    </div>

  </div><!-- wrapper -->

</body>
</html>
