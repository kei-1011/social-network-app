<?php
ob_start(); //出力のバッファリングを有効にする
session_start();

// ini_set('display_errors', "On");

$timezone = date_default_timezone_set("Asia/Tokyo");

$con = mysqli_connect('localhost', 'root', 'root', 'social');

if (mysqli_connect_errno()) {
  echo "接続失敗" . mysqli_connect_errno();
}
