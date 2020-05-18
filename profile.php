<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");

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

if(isset($_POST['remove_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->removeFriend($username);
}

if(isset($_POST['add_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->sendRequest($username);
}

if(isset($_POST['respond_request'])) {
  header("Location: requests.php");
}



?>

  <style>
  .wrapper {
    height: 100%;
    margin-left: 0;
    padding-left: 0;
  }
  </style>
  <div class="profile_left">
    <img src="<?php echo $user_array['profile_pic'];?>">

    <div class="profile_info">
      <p><?php echo "投稿数:". $user_array['num_posts'];?></p>
      <p><?php echo "いいね:". $user_array['num_likes'];?></p>
      <p><?php echo "フォロワー:". $num_friends;?></p>
    </div>

    <form action="<?php echo $username; ?>" method="post">
    <?php
    $profile_user_obj = new User($con, $username);

    if($profile_user_obj->isClosed()) {
      header("Location:user_closed.php");
    }

    $logged_in_user_obj = new User($con, $userLoggedIn);

    if($userLoggedIn != $username) {

      if($logged_in_user_obj->isFriend($username)) {
        echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
      } else if ($logged_in_user_obj->didReceiveRequest($username)) {
        echo '<input type="submit" name="respond_request" class="warning" value="Respond to request"><br>';
      } else if ($logged_in_user_obj->didSendRequest($username)) {
        echo '<input type="submit" name="" class="default" value="Request send"><br>';
      } else {
        echo '<input type="submit" name="add_friend" class="success" value="Add friend"><br>';
      }


    }
    ?>
    </form>

  </div>

  <div class="main_column column">
  <?php print($username); ?>
  </div>

  </div><!-- wrapper -->
</body>
</html>
