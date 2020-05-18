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
        <a href="<?php echo $userLoggedIn; // htaccess でurlを制御?>">
        <?php
        echo $user['first_name'] . " " . $user['last_name'];
        ?>
        </a>
        <?php
        echo "<p>投稿数:" . $user['num_posts']. "</p>";
        echo "<p>いいね:" . $user['num_likes']. "</p>";
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
    $(function(){

      var userLoggedIn = '<?php echo $userLoggedIn; ?>';
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
          url: "includes/handlers/ajax_load_posts.php",
          type: "POST",
          data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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

  </div><!-- wrapper -->
</body>
</html>
