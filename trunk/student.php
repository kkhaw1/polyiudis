<?php
  session_start();
  // Check to see if they are logged in
  if( !isset($_SESSION['userid']) ) {
    header('Location:http://pdc-amd01.poly.edu/~kkhawa01/polyiudis/login.html');
  }
  // Check to see if they have access to the current page
  preg_match('/(?<fname>[a-zA-Z]+).php/', getenv('SCRIPT_NAME'), $matches);
  if( $_SESSION['role'] != $matches['fname'] ) {
    // They should not be here, so send them back
    header('Location:'. ( (!$_SESSION['role'])?'login.html':$_SESSION['role'] . '.php') );
  }
?>

<html>
  <head>
    <?
      echo "<title>Polytechnic IUDIS - Student Home: ". $_SESSION['username'] ."</title>";
    ?>
    <link rel="shortcut icon" href="favicon1.ico">
    <link charset="utf-8" title="no title" media="screen" type="text/css" href="css/styles.css" rel="stylesheet">
    <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="js/application.js"></script>
    <script type="text/javascript" charset="utf-8" src="js/msg_handler.js"></script>
    <script type="text/javascript" charset="utf-8">
      $(document).ready( function() {
        ApplicationObj.init( <?php echo $_SESSION['userid'] .',\''. $_SESSION['role'] .'\',\''. $_SESSION['username'] .'\''; ?> );
        $('#classes').trigger('click');
      });
    </script>
  </head>

<body>
  <div id="head">
    <div id="right_title" class="title"><a href="http://www.poly.edu">Polytechnic Institute of NYU</a></div>
    <div id="left_title" class="title"><a href="index.php">Integrated University Department Information System</a></div>
  </div>

  <div id="nav_bar">
    <div id="links">
      <a href="index.php">Home</a>
      <a href="#" id="classes">View Current Classes</a>
      <a href="#" id="grades">View Grades</a>
      <a href="#" id="p_info">View Personal Information</a>
      <a href="#" id="grad">View Graduation Requirements</a>
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
      <div id="content"></div>
    </div>
    
    <div style="clear:right;"></div>
  </div>
  
  <div id="foot">
    -Developed by Group B6-
  </div>

</html>
