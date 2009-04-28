<?php
  session_start();
?>

<html>
<head>
  <?
    echo "<title>Polytechnic IUDIS - Change Your Password</title>";
  ?>
  <link rel="shortcut icon" href="favicon1.ico">
  <link charset="utf-8" title="no title" media="screen" type="text/css" href="css/styles.css" rel="stylesheet">
  <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="js/application.js"></script>
  <script type="text/javascript" charset="utf-8" src="js/msg_handler.js"></script>
  <script type="text/javascript" charset="utf-8">
    $(document).ready( function() {
      $('#frmPass').submit(function(e) {
        e.preventDefault();
        var error=0;
        $(this).find('input').each(function(){
          if( $(this).val()=='' ) {
            error=1;
            alert('Please enter a value for ' + $(this).attr('id'));
          }
        });
        if ( $(this).find('#pass1').val() != $(this).find('#pass2').val() ) {
          error=1;
          alert('Passwords do not match.');
        }
        if( !error ) {
          var qry1 = "SELECT username, email from users where username='" +$(this).find('#username').val()+ "' and email='" + $(this).find('#email').val() + "'";
          var qry2 = "UPDATE users SET password='" +$(this).find('#pass1').val()+ "' where username='" +$(this).find('#username').val()+ "' and email='" + $(this).find('#email').val() + "'";
          $.post('cgi-bin/query2.cgi', {query:qry1}, function(data) {
            $('#content').append(data);
            if ( qry_result != null ) {
              $.post('cgi-bin/query.cgi', {query:qry2}, function() {
                MsgHandler.addMsg('Successfully changed password.');
                MsgHandler.showMsg();
                setTimeout(function() {
                  window.location.href="index.php";
                }, 2000);
              });
            } else {
              MsgHandler.addMsg('Make sure that you correctly typed in your username and email address.');
              MsgHandler.showMsg();  
            }
          });
        }

        return false;
      });
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
          <button id="login">Login</button>
        </form>
      </div>
      </div>
    </div>
    
    <div id="left_col">
      <div style="font-size:105%;padding:3px;font-style:italic;">Have you forgotten your password?<br>Simply want to change your password?<br>Create a new password here.</div>
      <div id="content">
        <form id="frmPass" action="cgi-bin/query.cgi" method="POST">
          <table>
            <tr><td><label for="username">Username:</label></td><td><input type="text" id="username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" /></td></tr>
            <tr><td><label for="email">Email:</label></td><td><input type="text" id="email" /></td></tr>
            <tr><td><label for="pass1">New Password:</label></td><td><input type="password" id="pass1" /></td></tr>
            <tr><td><label for="pass2">Confirm Password:</label></td><td><input type="password" id="pass2" /></td></tr>
            <tr><td>&nbsp;</td><td><input type="submit" id="btnNewPass" value="Create New Password" /></td></tr>
          </table>
        </form>
      </div>
    </div>
    
    <div style="clear:right;"></div>
  </div>
  
  <div id="foot">
    -Developed by Group B6-
  </div>
</body>

</html>
