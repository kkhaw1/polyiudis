<html>
  <head>
<?php
  echo "<title>Polytechnic IUDIS - Student Home: ". $_COOKIE[username] ."</title>";
?>
    <link rel="stylesheet" href="css/master.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <script type="text/javascript" charset="utf-8" src="js/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" charset="utf-8">
      var personalInfo = function( data ) {
        //$('#main_t span, #main_t label, #main_t input').remove();
        $('#main_t').append('<span id="id">Personal Information For: ' + person.lname + ', ' + person.fname + '</span><br />');
        $('#main_t').append('<span id="id">ID: ' + person.id + '</span><br />');
        $('#main_t').append('<span id="username">Username: ' + person.username + '</span><br />');
        $('#main_t').append('<span id="fname">First Name: ' + person.fname + '</span><br />');
        $('#main_t').append('<span id="lname">Last Name:' + person.lname + '</span><br />');
        $('#main_t').append('<span id="role">Role: ' + person.role + '</span><br />');
        
        $('#main_t').append('<label for="email">Email: </label><input type="text" id="email" value="' + person.email + '" /><br />');
        $('#main_t').append('<label for="address">Address: </label><input type="text" id="address" value="' + person.address + '" /><br />');
        $('#main_t').append('<label for="city">City: </label><input type="text" id="city" value="' + person.city + '" /><br />');
        $('#main_t').append('<label for="state">State: </label><input type="text" id="state" value="' + person.state + '" /><br />');
        $('#main_t').append('<label for="zip">Zip Code: </label><input type="text" id="zip" value="' + person.zip + '" /><br />');
        $('#main_t').append('<label for="phone">Phone (ex:7185552501): </label><input type="text" id="phone" value="' + (person.phone) + '" maxlength=10 /><br />');
        $('#main_t').append('<input type="button" id="btnUpdate" value="Update Personal Info" />');
        
        $('#main_t input').attr('size', 25).css({border:'1px solid #000'});
        $('#main_t span, #main_t label').css({fontWeight:'bold'});
        
        $('#main_t input:text').blur( function(){
          if ( $(this).val() != person[$(this).attr('id')] ){
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
    
      $(document).ready( function(){      
        $('#p_info').click( function() {console.log("Click");
          <?php
            echo "$.post('cgi-bin/pinfo.cgi', {userid:".$_COOKIE[userid]."}, function(data) { $('#main_t').html(data);personalInfo(data); });";
          ?>
          
          $('#btnUpdate').live( 'click', function(){
            var qry = "UPDATE users SET email='"+ $('#email').val() +"', address='"+ $('#address').val();
            qry += "', city='"+ $('#city').val() +"', state='"+ $('#state').val() +"', zip='";
            qry += $('#zip').val() +"', phone='"+ $('#phone').val() +"' WHERE id='" + person.id + "'";
            //console.log(qry);
            
            $.post('cgi-bin/query.cgi', {query:qry}, function(){console.log("callback");
              person.email = $('#email').val();
              person.address = $('#address').val();
              person.city = $('#city').val();
              person.state = $('#state').val();
              person.zip = $('#zip').val();
              person.phone = $('#phone').val();
              personalInfo();
            });
          });
        });
        
        $('#classes').live('click', function() {
        });
        
        $('#grades').live('click', function() {
        });
        
        $('#grad').live('click', function() {
        });
      });
    </script>
  </head>

  <body>
    <div id="body_w">

    <div id="nav">
      <a href="http://www.poly.edu/">Polytechnic Institute of NYU</a> 
      <a href="http://pdc-amd01.poly.edu/~kkhawa01/polyiudis/" id="cat">IUDIS</a> 
      <div style="clear:both"> </div>
    </div>

    <div id="body_panel">
      Welcome, <? echo $_COOKIE[username];?><br />
      <div id="links" style="cursor:pointer;">
        <span id="p_info"> [View/Update Personal Information] </span>|
        <span id="classes"> [View Current Classes] </span>|
        <span id="grades"> [View Grades] </span>|
        <span id="grad"> [View Graduation Requirements] </span>
      </div>
      <div id="main">
        <div id="main_t" style="border:1px solid #ccc;margin:55px;padding:25px;text-align:left;line-height:35px;">
        </div>
      </div>
    </div>

    <div id="foot"><span>--Footer--</span><div style="clear:both"></div></div>

    </div>
  </body>

</html>
