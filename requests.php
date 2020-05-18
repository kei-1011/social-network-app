<?php
include("includes/header.php");

?>

<div class="main_column colum" id="main_column">
  <h4>Friend Request</h4>

  <?php
  $query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_from='$userLoggedIn'");
  if(mysqli_num_rows($query) == 0) {
    echo 'You have no friend requests at this time!';
  } else {
    while($row = mysqli_fetch_array($query)) {
      $user_from = $row['user_from'];
      $user_from_obj = new User($con,$user_from);

      echo $user_from_obj->getFirstAndLastName() . "sent you a friend request";

      $user_from_friend_array = $user_from_obj->getFriendArray();

      if(isset($_POST['accept_request'] . $user_from)) {
      }
      if(isset($_POST['ignore_request'] . $user_from)) {
      }
    }
  }
  ?>
</div>
