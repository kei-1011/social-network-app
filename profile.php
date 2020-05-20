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

// リクエスト
if(isset($_POST['add_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->sendRequest($username);
}

//
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
        echo '<input type="submit" name="remove_friend" class="danger" value="友達を削除する"><br>';
      } else if ($logged_in_user_obj->didReceiveRequest($username)) {
        echo '<input type="submit" name="respond_request" class="warning" value="リクエストに応じる"><br>';
      } else if ($logged_in_user_obj->didSendRequest($username)) {
        echo '<input type="submit" name="" class="default" value="リクエスト送信"><br>';
      } else {
        echo '<input type="submit" name="add_friend" class="success" value="友達追加"><br>';
      }
    }
    ?>
    </form>
    <div class="text-center">
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#post_form">投稿する</button>
    </div>

    <?php
    if($userLoggedIn != $username) {
      echo '<div class="profile_info_bottom">';
      echo $logged_in_user_obj->getMutualFriends($username) . "Mutual friends";
      echo '</div>';
    }

    ?>
  </div>

  <div class="main_column column">
    <!-- ajaxで読み込んだ投稿を挿入する -->
    <div class="posts_area"></div>
  <img id="loading" src="/assets/images/icons/loading.gif">


  <!-- Modal -->
  <div class="modal fade" id="post_form" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">投稿する</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <p>これは、ユーザーのプロフィールページやニュースフィードに表示され、お友達にも見てもらえるようになります。</p>
        </div>

        <form class="profile_post" action="" method="post">
          <div class="form-group">
            <textarea class="form-control" name="post_body"></textarea>
            <input type="hidden" name="user_from" value="<?php echo $userLoggedIn;?>">
            <input type="hidden" name="user_to" value="<?php echo $username;?>">
          </div>
        </form>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">閉じる</button>
          <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">投稿</button>
        </div>
      </div>
    </div>
  </div><!--modal-->

    <!-- 非同期投稿読み込み -->
    <script>
    $(function(){

      var userLoggedIn = '<?php echo $userLoggedIn; ?>';
      var profileUsername = '<?php echo $username; ?>';
      var inProgress = false;

      loadPosts(); // 最初の投稿を読み込む

        $(window).scroll(function() {
          var bottomElement = $(".status_post").last();
          var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            // isElementInViewportではgetBoundingClientRect()を使用、
            //これはjQueryオブジェクトではなくHTML DOMオブジェクトを必要とします。jQueryの等価は以下のように[0]を使用
            if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                loadPosts();
            }
        });

        function loadPosts() {
            if(inProgress) {
              //すでにいくつかの記事をロードしている場合
          return;
        }

        inProgress = true;
        $('#loading').show();

        var page = $('.posts_area').find('.nextPage').val() || 1;
        //.nextPageが見つからなかった場合は、まだページ上にないはず(記事の読み込みが初めての場合)、値'1'を使用する

        $.ajax({
          url: "includes/handlers/ajax_load_profile_posts.php",
          type: "POST",
          data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUser=" + profileUsername,
          cache:false,

          success: function(response) {
            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
            $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage

            setTimeout(function(){
              $('#loading').hide();
              $(".posts_area").append(response);
            },300);

            inProgress = false;
          }
        });
        }

        //isElementInView関数　要素が表示されているかどうかを検出
        //要素が下部にあるかどうかではなく、画面上にあるかどうかを確認します。
        function isElementInView (el) {
            var rect = el.getBoundingClientRect();

            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <= (window.innerWidth || document.documentElement.clientWidth) //  or $(window).width()
            );
        }
    });

    </script>
  </div>

  </div><!-- wrapper -->
</body>
</html>
