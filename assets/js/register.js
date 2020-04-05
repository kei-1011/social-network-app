$(document).ready(function () {

  // サインアップクリック時、登録フォームを表示
  $('#signup').on('click', function () {
    $('#first').slideUp("",function () {
      $('#second').slideDown('');
    })
  })

  // サインインクリック時、登録フォームを非表示
  $('#signin').on('click', function () {
    $('#second').slideUp("",function () {
      $('#first').slideDown('');
    })
  })

});
