<?php
include("includes/header.php");
/*
http://192.168.11.8:10000/aaaa_bbbbb_1
getで「aaaa_bbbbb_1」のユーザー名を取得。
ユーザー名を元に、ユーザー情報を取得する。
URLは、htaccessで設定してあり、内部的には以下のURLになっていることになる。
http://192.168.11.8:10000/profile.php?profile_username=aaaa_bbbbb_1
*/

if(isset($_GET['profile_username'])) {
  $username = $_GET['profile_username'];
  $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
  $user_array = mysqli_fetch_array($user_details_query);

  $num_friends = (substr_count($user_array['friend_array'],",")) - 1;  // '," を数える？
}

?>

  <div class="profile_left">
    <img src="<?php echo $user_array['profile_pic'];?>">

    <div class="profile_info">
      <p><?php echo "投稿数:". $user_array['num_posts'];?></p>
      <p><?php echo "いいね:". $user_array['num_likes'];?></p>
      <p><?php echo "フォロワー:". $num_friends;?></p>
    </div>
  </div>

  <div class="main_column column">
  <?php print($username); ?>
  </div>

  </div><!-- wrapper -->
</body>
</html>
