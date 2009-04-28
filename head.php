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
    echo "<title>Polytechnic IUDIS - Department Head Home</title>";
  ?>
  <link charset="utf-8" title="no title" media="screen" type="text/css" href="css/styles.css" rel="stylesheet">
  <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="js/application.js"></script>
  <script type="text/javascript" charset="utf-8" src="js/msg_handler.js"></script>
  <script type="text/javascript" charset="utf-8">
    $(document).ready( function() {
      ApplicationObj.init( <?php echo $_SESSION['userid'] .',\''. $_SESSION['role'] .'\',\''. $_SESSION['username'] .'\''; ?> );
    });
  </script>

    <!--
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
        
        var createUName = function() {
          var fname = $content.find('#fname').val();
          var lname = $content.find('#lname').val();
          var uname = fname.substr(0, fname.length/2);
          uname += lname.substr(0, 2 * lname.length/3);
          alert(uname);
        }
        
        var admin_addNewHire = function(head) {
          $content.html(head);
          var userid = parseInt( qry_result.tuple0.increment_by ) + parseInt( qry_result.tuple0.last_value );

          var qry = "SELECT * FROM roles where role!='student' order by roleid";
          $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
            $content.append(data);
            $roles = $('<select id="roles" />');
            $roles.append('<option id="dflt">Select Role</option>');
            for( var i = 0; i < qry_result.numTuples; ++i ) {
              $roles.append('<option id="role' +i+ '">' + qry_result['tuple'+i].role.toUpperCase() + '</option>');
              $roles.find('#role'+i).data('roleid', qry_result['tuple'+i].roleid);
            }
            
            $form = $content.append('<form id="frmAddHire" method="POST" action="cgi-bin/query.cgi" />').find('#frmAddHire');
            $form.append('<table align="center" border="0px" cellpadding="3px" cellspacing="0" width="45%" />').find('table').append('<tbody />').find('tbody')
                  .append('<tr><td>UserID: </td><td>' + userid + '</td></tr>')
                  .append('<tr><td>First Name:</td><td><input type="text" id="fname" /></td></tr>')
                  .append('<tr><td>Last Name:</td><td><input type="text" id="lname" /></td></tr>')
                  .append('<tr><td>Username: </td><td><input type="text" id="uname" /></td></tr>')
                  .append('<tr><td>Password: </td><td><input type="password" id="pass" /></td></tr>')
                  .append('<tr><td>Email: </td><td><input type="text" id="email" /></td></tr>')
                  .append('<tr><td>Address: </td><td><input type="text" id="address" /></td></tr>')
                  .append('<tr><td>City: </td><td><input type="text" id="city" /></td></tr>')
                  .append('<tr><td>State: </td><td><input type="text" id="state" /></td></tr>')
                  .append('<tr><td>Zip: </td><td><input type="text" id="zip" /></td></tr>')
                  .append('<tr><td>Phone: </td><td><input type="text" id="phone" /></td></tr>')
                  .append('<tr><td>Salary:</td><td>$<input type="text" id="pay" /></td></tr>')
                  .append('<tr><td>Choose Degree: </td><td id="roleopts"></td></tr>')
                  .append('<tr><td colspan="2" align="right"><input type="submit" id="btnAdd" style="padding:3px;border:1px solid #000;cursor:pointer;" value="Add New Hire" /></td></tr>');
            $form.find('#roleopts').html( $roles );
            $('#frmAddHire').submit( function(e) {
              e.preventDefault();
              if( $content.find('#fname').val() == '' || $content.find('#lname').val() == '' || $content.find('#pass').val() == '' || $content.find('#email').val() == ''|| $content.find('#address').val() == '' ||
              $content.find('#city').val() == '' || $content.find('#state').val() == '' || $content.find('#zip').val() == '' || $content.find('#phone').val() == '' || $content.find('#uname').val() == '' ||
              $content.find('#roles option:selected').attr('id') == 'dflt' || $content.find('#pay').val() == '' || isNaN(parseInt($content.find('#pay').val())) ) {
                alert("Please fill out completely, and try again.");
                return false;
              } else {
                var roleid=$content.find('#roles option:selected').data('roleid');
                var qry = "INSERT INTO users VALUES(DEFAULT, '"+$content.find('#uname').val()+"', '"+$content.find('#pass').val()+"', '"+$content.find('#email').val()+"',";
                qry += " '"+$content.find('#fname').val()+"', '"+$content.find('#lname').val()+"', '"+$content.find('#address').val()+"', '"+$content.find('#city').val()+"',";
                qry += " '"+$content.find('#state').val()+"', '"+$content.find('#zip').val()+"', '"+$content.find('#phone').val()+"', " +roleid+ ")";
                $.post('cgi-bin/query.cgi', {query:qry});
                var qry2 = "INSERT INTO staff VALUES('" +userid+ "', '" + $content.find('#pay').val() + "', NOW())";
                $.post('cgi-bin/query.cgi', {query:qry2}, function() {
                  $content.append('<div id="msgBox" />'). find('#msgBox').text('Successfully added ' + $content.find('#fname').val()+' '+$content.find('#lname').val() + ' as a new Staff Member.' );
                });
              }
              return false;
            });
          });
        };
        
        var admin_assignClass = function(head) {
          $content.html(head);
          $content.append('<span id="reset">Reset</span>');
          console.log(          $content.find('span#reset').length);
          $content.find('span#reset').click(function(){
            console.log("ReSET");
            $('#assign_classes').trigger('click');
          });
          $prof = $content.append('<div id="profs" style="text-align:left;"/>').find('#profs');
          $crs = $content.append('<div id="crs" style="float:right;border:1px solid #000;text-align:left;line-height:30px;z-index:1;"/> <div style="clear:both;"> </div>').find('#crs');
          for( var i=0; i<qry_result.numTuples; ++i) {
            $prof.append('<div id="prof'+qry_result['tuple'+i].id+'" style="cursor:pointer"> ' + qry_result['tuple'+i].lname + ', ' + qry_result['tuple'+i].fname + '<div id="courses" /> </div>');
            $prof.find('#prof' + qry_result['tuple'+i].id).data('id', qry_result['tuple'+i].id);
            $prof.find('div').die('click').live('click', function(){
              var prof = this;
              var qry='select * from course where instructorid is null';
              $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
                $crs.html(data);
                for( var j=0; j< qry_result.numTuples; j++ ) {
                  $crs.append('<div id="crs' +qry_result['tuple'+j].classnum+ '" style="cursor:pointer;padding:3px;"> ' + qry_result['tuple'+j].coursenum + ' - ' + qry_result['tuple'+j].name + ' </div>');
                  $crs.find('#crs'+qry_result['tuple'+j].classnum).data('classnum', qry_result['tuple'+j].classnum);
                  $crs.find('#crs'+qry_result['tuple'+j].classnum).data('coursenum', qry_result['tuple'+j].coursenum);
                  $crs.find('#crs'+qry_result['tuple'+j].classnum).data('name', qry_result['tuple'+j].name);
                }
                $content.append('<input id="btnAssignClass" type="button" value="Assign Class(es)" />').find('#btnAssignClass').click( function(){
                  var qry = "Update course set instructorid='" + $(prof).data('id') + "' where classnum='" + $(prof).find('span:first').attr('id') + "'";
                  for( var q=1; q < $(prof).find('span').length; ++q ) { qry+=" or classnum='" + $(prof).find('span:eq(' + q + ')').attr('id') + "'";}
                  console.log(qry);
                });
                $crs.find('div').die('click').live('click', function(){
                  if ( $(prof).find('span#' + $( '#' + $(this).attr('id') ).data('classnum')).length == 0 ) {
                    $(prof).find('#courses').append('<span id="' + $('#' + $(this).attr('id')).data('classnum') + '">' + $('#' + $(this).attr('id')).data('coursenum') + '</span>, ')
                  }
                });
                
              });
            });
          }
          
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
                showPersonalInfo(head);
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
          
          $('#admin_add_emp').live( 'click', function() {
            var head='<h3>Add New Hire</h3>';
            var qry = 'select last_value, increment_by from users_id_seq';
            $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
              $content.append(data);
              addNewHire(head);
            });
          });
          
          $('#admin_assign_classes').live( 'click', function() {
            var head='<h3>Assign Classes</h3>';
            var qry ="select * from users as u, roles as r where u.roleid=r.roleid and role='professor'";
            $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
              $content.append(data);
              assignClass(head);
            });
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
    !-->
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
      <a href="#" id="admin_show_emp">Employee Lists</a>
      <a href="#" id="admin_add_emp">New Hire</a>
      <a href="#" id="admin_assign_classes">Assign Classes</a>
      <a href="#" id="admin_manage_inv">Manage Invoices</a>
      <a href="#" id="create_invoice">Create Invoice</a>
      <a href="#" id="p_info">View Personal Information</a>
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
</body>
<!--
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
      <a href="#" id="add_emp"> Add New Employee </a>
      <a href="#" id="assign_classes"> Assign Classes </a>
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
!-->
</html>
