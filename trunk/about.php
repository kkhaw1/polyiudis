<?php
  session_start();
?>

<html>
<head>
  <title>Polytechnic IUDIS - About Us</title>
  <link rel="shortcut icon" href="favicon1.ico">
  <link charset="utf-8" title="no title" media="screen" type="text/css" href="css/styles.css" rel="stylesheet">
  <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
</head>

<body>
  <div id="head">
    <div id="right_title" class="title"><a href="http://www.poly.edu">Polytechnic Institute of NYU</a></div>
    <div id="left_title" class="title"><a href="index.php">Integrated University Department Information System</a></div>
  </div>

  <div id="nav_bar">
    <div id="links">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="#" id="logout" class="last">Logout</a>
    </div>
  </div>

  <div id="msg_box">
    <span>MsgBox</span>
  </div>

  <div id="main">
    <div id="right_col">
      <i>Coming Soon...<br />
      Department Wide Announcements</i>
    </div>
    
    <div id="left_col">
      <div style="font-size:85%;">Signed in as <strong>&laquo;<?php echo $_SESSION['username']; ?>&raquo;</strong>.</div>
      <div id="content">
        <p></p>
      </div>
    </div>
    
    <div style="clear:right;"></div>
  </div>
  
  <div id="foot">
    -Developed by Group B6-
  </div>
</body>

</html>
