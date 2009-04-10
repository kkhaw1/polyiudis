<?php
session_start();

if( isset($_GET['action']) && $_GET['action'] == "logout") {
  session_destroy();
  header('Location: login.html');
  exit;
} else {
  $_SESSION['userid'] = $_GET['userid'];
  $_SESSION['username'] = $_GET['username'];
  $_SESSION['roleid'] = $_GET['roleid'];
  $_SESSION['role'] = $_GET['role'];

  header('Location: '.$_SESSION['role'].'');
}

