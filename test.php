<?php
  session_start();
?>
<html>

<head>
  <link rel="shortcut icon" href="favicon1.ico">
  <link charset="utf-8" title="no title" media="screen" type="text/css" href="css/styles.css" rel="stylesheet">
  <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="js/msg_handler.js"></script>
  
  <script type="text/javascript">
    $(document).ready( function(){
      if( <?php echo isset($_SESSION['userid']) ? 'true' : 'false' ; ?>) {
        $('#login').html('<div>You are logged in as <b><?php echo $_SESSION['username']; ?></b><br /><a href="<?php echo $_SESSION['role']; ?>" style="color:#00F;">&laquo;Go Back</a></div>');
        $('#links a:last').text('Logout').attr('href','cookie.php?action=logout');
        $('#login').append('<a href="changePass.php" style="color:#00F">Change Your Password?</a>');
      } else {
        document.getElementById('username').focus();
      }

      if ( window.location.href.match(/error=true/) ) {
      	MsgHandler.addMsg("Username and password combination not found.");
      	MsgHandler.showError();
      }

      $('input').focus( function(){$(this).addClass('selected');}).blur( function() {$(this).removeClass('selected');});

      $('#frmLogin').submit( function(e){
	      if ( $('#username').val() == "" ) {
	        MsgHandler.addMsg("Please enter a username");
	        MsgHandler.showMsg();
	        return false;
	      } else if ( $('#pwd').val() == "" ) {
	        MsgHandler.addMsg("Please enter a password");
	        MsgHandler.showMsg();
	        return false;
	      }
	      return true;
      });
    });
  </script>
  
  <title>Integrated University Department Information System</title>
</head>

<body>

  <div id="head">
    <div id="right_title" class="title"><a href="http://www.poly.edu">Polytechnic Institute of NYU</a></div>
    <div id="left_title" class="title"><a href="index.php">Integrated University Department Information System</a></div>
  </div>

  <div id="nav_bar">
    <div id="links">
      <a href="index.php" class="selected">Home</a>
      <a href="about.php">About</a>
      <a href="login.html" class="last">Login</a>
    </div>
  </div>

  <div id="msg_box">
    <span>MsgBox</span>
  </div>

  <div id="main">
    <div id="right_col">
      <div id="login">
      <div>Sign into your IUDIS account:</div>
      <div id="loginPanel"> 
        <form method="POST" action="cgi-bin/login.cgi" name="frmLogin" id="frmLogin">
      	  <label for="username">Username:&nbsp;</label><input type="text" name="username" id="username" /><br />
          <label for="pwd">Password:&nbsp;&nbsp;</label><input type="password" name="pwd" id="pwd" /><br />
          <button id="login">Login</button><br/>
          <a href="changePass.php" style="color:#00F">Forgot Your Password?</a>
        </form>
      </div>
      </div>
    </div>
    
    <div id="left_col">
      <p>Welcome to polyIUDIS, an online department wide information system.</p>
    </div>
    
    <div style="clear:right;"></div>
  </div>
  
  <div id="foot">
    -Developed by Group B6-
  </div>

</body>

</html>
