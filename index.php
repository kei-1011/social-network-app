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

         <!-- ajaxで読み込んだ投稿を挿入する -->
        <div class="posts_area"></div>
        <img id="loading" src="/assets/images/icons/loading.gif">
    </div>


    <!-- 非同期投稿読み込み -->
    <script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(function() {

      $('#loading').show();

      //最初の投稿を読み込むためのajax
      $.ajax({
        url: 'includes/handlers/ajax_load_posts.php',
        type: 'POST',
        data: 'page=1&userLoggedIn=' + userLoggedIn,
        cache:false,

        success: function(data) {
          console.log(data);
          $('#loading').hide();
          $('.posts_area').html(data);
        }
      });

      $(window).scroll(function() {
        var height      = $('.posts_area').height(); //posts_areaの高さ
        var scroll_top  = $(this).scrollTop();       // topからのスクロール
        var page        = $('.posts_area').find('.nextPage').val();
        var noMorePosts = $('.posts_area').find('.noMorePosts').val();

        if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
          $('#loading').show();

          //最初の投稿を読み込むためのajax
          var ajaxReq = $.ajax({
            url: 'includes/handlers/ajax_load_posts.php',
            type: 'POST',
            data: 'page=' + page +'&userLoggedIn=' + userLoggedIn,
            cache:false,

            success: function(response) {
              $('.posts_area').find('.nextPage').remove();
              $('.posts_area').find('.noMorePosts').remove();
              $('#loading').hide();
              $('.posts_area').append(response);  // 既存の投稿のしたに追加
            }
          });//ajax

        } //endif

      }); //scroll

    }); // jquery

    </script>

  </div><!-- wrapper -->
</body>
</html>
