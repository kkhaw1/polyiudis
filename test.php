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
      <p style="text-align:left;">polyIUDIS, the Integrated University Department Information System, was
       developed as solution to handling all of a University's Computer Science and 
       Engineering Department wide needs.  PolyIUDIS utilizes a clean and simple layout to allow easy navigation.</p>
      <div id="list_box">
      <h4>Manage Courses</h4>
        <li>Add new classes</li>
        <li>Assign who will teach courses</li>
        <li>View Rosters</li>
      <h4>Manage Course Grades</h4>
        <li>View Grades</li>
        <li>Assign Grades</li>
      <h4>Manage Student Records</h4>
        <li>Create Student Accounts</li>
        <li>View Student Records</li>
        <li>Register for Classes</li>
      <h4>Manage Invoices</h4>
        <li>Filing Invoices</li>
        <li>Approving Speding</li>
        <li>Budgets</li>
      </div>
    </div>
    
    <div style="clear:right;"></div>
  </div>
  
  <div id="foot">
    -Developed by Group B6-
  </div>

</body>

</html>
