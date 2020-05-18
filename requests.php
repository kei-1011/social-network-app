<?php
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");

?>

<div class="main_column column" id="main_column">
  <h4>友達リクエスト</h4>

  <?php
  $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
  if(mysqli_num_rows($query) == 0) {
    echo '友達リクエストはありません!';
  } else {
    while($row = mysqli_fetch_array($query)) {
      $user_from = $row['user_from'];
      $user_from_obj = new User($con,$user_from);

      echo $user_from_obj->getFirstAndLastName() . "さんから友達申請が届いています";

      $user_from_friend_array = $user_from_obj->getFriendArray();


      if(isset($_POST['accept_request'.$user_from ])) {
        $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
        $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

        $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
        echo '現在、あなたの友達です';
        header('Location: requests.php');
      }

      if(isset($_POST['ignore_request'.$user_from ])) {
        $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
        echo '友達申請を無視';
        header('Location: requests.php');
      }
      ?>
      <form action="requests.php" method="post">
        <input type="submit" value="受け入れる"" name="accept_request<?php echo $user_from;?>" id="accept_button">
        <input type="submit" value="無視" name="ignore_request<?php echo $user_from;?>" id="ignore_button">
      </form>
      <?php
    }
  }
  ?>
</div>
