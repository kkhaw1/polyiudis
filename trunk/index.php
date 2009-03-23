<html>

<head>
  <title>Polytechnic Institute of NYU: IUDIS</title>
  <link rel="stylesheet" href="incl/css/master.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="incl/css/login.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <script type="text/javascript" charset="utf-8" src="incl/js/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" charset="utf-8">

    var validateFields = function(){
      if(document.getElementsByName("username")[0].value.length==0){
        alert("An Email Address has not been entered");
	document.forms[0].preventDefault();
      } else if(document.getElementsByName("pwd")[0].value.length==0){
	alert("Password is Needed");
      } else {
	document.forms[0].submit();
      }
    }

    var ErrorHandler = ( function(){
      var addError = function(msg) {
	$('#errors span').text(msg);
      };
      var showError = function(){
	if ( $('#errors span').length > 0 ) {
	  $('#errors').stop().animate({opacity:1},300).animate({opacity:0},5000);
        }
      };

      return {
	addError: function(msg) {
	  addError(msg);
	},
        showError: function() {
	  showError();
	}
      };
    })();

    $(document).ready( function(){
      $('#frmLogin').submit( function(e){
	if ( $('#username').val() == "" ) {
	  ErrorHandler.addError("Please enter a username");
	  ErrorHandler.showError();
	  return false;
	} else if ( $('#pwd').val() == "" ) {
	  ErrorHandler.addError("Please enter a password");
	  ErrorHandler.showError();
	  return false;
	}
	return true;
      });
    });

  </script>
</head>

<body><center>
  <div id="body_w">

    <div id="banner">Banner Box</div>

    <div id="errors"><span></span></div>

    <div id="body_panel">
      <div id="loginPanel">      
        <form method="POST" action="incl/cgi-bin/login.cgi" name="frmLogin" id="frmLogin">
	  <label for="username">Username: </label>
	  <input type="text" name="username" id="username" />
	  <br />
	  <label for="pwd">Password: </label>
	  <input type="password" name="pwd" id="pwd" />
	  <br />
	  <button id="login">Login</button>
        </form>
      </div>
    </div>

    <div id="footer">--Footer--</div>

  </div>
</center></body>

</html>
