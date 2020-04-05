<?php
$con = mysqli_connect('localhost', 'root', 'root', 'social');

if (mysqli_connect_errno()) {
  echo "接続失敗" . mysqli_connect_errno();
}

$query = mysqli_query($con, "INSERT INTO test VALUES('2','Optimus Prime')");
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SNS</title>
</head>
<body>

  hello world
</body>

</html>
