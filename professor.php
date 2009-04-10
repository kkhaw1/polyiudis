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
      echo "<title>Polytechnic IUDIS - Professor Home: ". $_SESSION['username'] ."</title>";
    ?>
    <link rel="stylesheet" href="css/master.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" charset="utf-8">

      var ApplicationObj = (function() {
        var $content;
        
        var addBlur = function(varName) {
          $content.find('input:text').blur( function() {
            if ( $(this).val() != varName[$(this).attr('id')] ){
              $(this).css({
                backgroundColor: '#FFC',
                border:'1px dashed #F00'
              });
            } else {
              $(this).css({
                backgroundColor: '#FFF',
                border:'1px solid #000'
              });
            }
          });
        };
        
        var showPersonalInfo = function(head) {
          $content.html(head);
          $form = $content.append('<form id="frmPInfo" method="POST" action="cgi-bin/query.cgi" />').find('#frmPInfo');
          $form.append('<table align="center" border="0px" cellpadding="3px" cellspacing="0" width="45%" />').find('table').append('<tbody />').find('tbody')
                .append('<tr><td id="key"> <? echo ucwords($_SESSION[role]); ?> ID: </td><td id="val">' +person.id+ '</td></tr>')
                .append('<tr><td id="key"> First Name: </td><td id="val">' +person.fname+ '</td></tr>')
                .append('<tr><td id="key"> Last Name: </td><td id="val">' +person.lname+ '</td></tr>')
                .append('<tr><td id="key"><label for="email"> Email: </label></td><td id="val"> <input type="text" id="email" value="' +person.email+ '" /> </td></tr>')
                .append('<tr><td id="key"><label for="address"> Address: </label></td><td id="val"> <input type="text" id="address" value="' +person.address+ '" /> </td></tr>')
                .append('<tr><td id="key"><label for="city"> City: </label></td><td id="val"> <input type="text"  id="city" value="' +person.city+ '" /> </td></tr>')
                .append('<tr><td id="key"><label for="state"> State: </label></td><td id="val"> <input type="text" id="state" value="' +person.state+ '" /> </td></tr>')
                .append('<tr><td id="key"><label for="zip"> Zip: </label></td><td id="val"> <input type="text" id="zip" value="' +person.zip+ '" /> </td></tr>')
                .append('<tr><td id="key"><label for="phone"> Phone: </label></td><td id="val"> <input type="text" id="phone" value="' +person.phone+ '" /> </td></tr>')
                .append('<tr><td colspan="2" align="right"> <input type="submit" id="btnUpdate" style="padding:3px;border:1px solid #000;cursor:pointer;" value="Update" /> </td></tr>');
          $('#frmPInfo').submit( function() {
              $('#frmPInfo #btnUpdate').attr('disabled','disabled').val('Please Wait...');
              var qry = "UPDATE users SET email='"+ $('#email').val() +"', address='"+ $('#address').val();
              qry += "', city='"+ $('#city').val() +"', state='"+ $('#state').val() +"', zip='";
              qry += $('#zip').val() +"', phone='"+ $('#phone').val() +"' WHERE id='" + person.id + "'";
              
              $.post('cgi-bin/query.cgi', {query:qry}, function(){
                person.email = $('#email').val();
                person.address = $('#address').val();
                person.city = $('#city').val();
                person.state = $('#state').val();
                person.zip = $('#zip').val();
                person.phone = $('#phone').val();
                showPersonalInfo('<h3>Personal Info</h3>');
                $('#frmPInfo #btnUpdate').removeAttr('disabled').val('Update');
              });
              return false;
            });
        };

        var _init = function() {
          $content = $('#main').append('<div id="content" />').find('#content');
          $('#logout').live( 'click', function() {
            window.location.href = "cookie.php?action=logout";
          });

          $('#p_info').live( 'click', function() {
            var head='<h3>Personal Info</h3>';
            $.post('cgi-bin/pinfo.cgi', {userid:<? echo $_SESSION['userid']; ?>}, function(data) {
              $content.append(data);
              showPersonalInfo(head);
              addBlur(person);
            });
          });
        };

        return {
          init: function() {
            return _init();
          }
        };
      })();

      $(document).ready( function() {
        ApplicationObj.init();
      });

    </script>
  </head>

  <body>
    <div id="body_w">

    <div id="nav">
      <a href="http://www.poly.edu/">Polytechnic Institute of NYU</a> 
      <a href="http://pdc-amd01.poly.edu/~kkhawa01/polyiudis/" id="cat">IUDIS</a>
      <a href="#" class="right" id="logout">Logout</a>
      <div class="right" id="logout">Welcome, <? echo $_SESSION[username];?></div>
      <div style="clear:both"> </div>
    </div>

    <div id="nav" style="cursor:pointer;text-align:center;margin-top:-11px;">
      <a href="#" id="p_info"> View Personal Information </a>
      <div style="clear:both"> </div>
    </div>

    <div id="body_panel">
      <div id="main" style="border:1px solid #AAA;margin:15px;background-color:#EEE;">
      </div>
    </div>

    <div id="foot"><span>--Footer--</span><div style="clear:both"></div></div>

    </div>
  </body>

</html>
