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
        var showRoster = function() {
          $content.find('.view_roster').die('click');
          $content.find('.view_roster').live('click', function() {
            $('#class_list').find( 'table#' + $(this).attr('id')).toggle('slow');
            if ( $(this).find('input').val() == 'View Class Roster' ) {
              $(this).find('input').val('Hide Class Roster');
            } else {
              $(this).find('input').val('View Class Roster');
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
        
        var showClassList = function(head) {
          $content.html(head);
          var $roster=new Array();
          var click = new Array();
          var $list = $content.append('<table id="class_list" border="0" align="center" cellspacing="0" cellpadding="5" width="100%" />').find('#class_list');
          $list.append("<thead />").find('thead').append('<tr />').find('tr')
                .append('<td align="center">Course Number</td>')
                .append('<td align="center">Course Name</td>')
                .append('<td align="center">Term</td>')
                .append('<td align="center">Class Count</td>').append('<td></td>');
          $list = $list.append('<tbody />').find('tbody');
          for( var i=0; i < classlist.numCourses; ++i) {
            $list.append('<tr />').find('tr:last')
                  .append('<td align="center">'+ classlist['class' + i].coursenum +'</td>')
                  .append('<td align="center">'+ classlist['class' + i].name +'</td>')
                  .append('<td align="center">'+ classlist['class' + i].term +'</td>')
                  .append('<td align="center">'+ classlist['class' + i].numStudents +'</td>')
                  .append('<td class="view_roster" id="roster'+i+'" align="center" style="cursor:pointer;"><input id="btnV" type="button" value="View Class Roster" /></td>');
            $roster[i] = $list.append('<tr>').find('tr:last').append('<td colspan="5" align="right"/>').find('td').append('<table id="roster'+i+'" border="0" align="right" cellspacing="0" cellpadding="5"/>').find('#roster' + i);
            $roster[i] = $roster[i].append('<tbody />').find('tbody');
            for( var j=0; j < classlist['class'+i].numStudents; ++j) {
              $roster[i].append('<tr />').find('tr:last')
                        .append('<td>'+ classlist['class'+i].roster['student'+j].id +'</td>')
                        .append('<td>'+ classlist['class'+i].roster['student'+j].lname +'</td>')
                        .append('<td>'+ classlist['class'+i].roster['student'+j].fname +'</td>')
                        .append('<td>'+ classlist['class'+i].roster['student'+j].email +'</td>');
            }
            if (classlist['class'+i].numStudents == 0){
              $roster[i].append('<tr><td colspan="4">No Students Registered</td></tr>');
            }
            $('#class_list').find('table#roster'+i).hide();
          }
        };
        
        var updateGrade = function( elem ) {
          var gradeid = $(elem).find('option:selected').data('id');
          var studentid = $(elem).find('option:selected').data('studentid');
          var classid = $(elem).find('option:selected').data('classid');
          var qry = "UPDATE class_roster SET gradeid='" +gradeid+ "' WHERE studentid='" +studentid+ "' and classnum='" +classid+ "'";
          $.post('cgi-bin/query.cgi', {query:qry}, function(){
            $content.find('#msgBox').text('Successfully gave grade of ' +grades['grade'+ (gradeid-1)].letter);
            $(elem).parent('div').css({fontWeight:'normal'});
          });
        };
        
        var selectChange = function() {
          var index = $content.find('#classDropList option:selected').data('classnum') - 1;
          
          $roster = $content.find('#gradingRoster');
          $roster.html('<span style="text-decoration:underline;font-weight:bold;padding:15px;">' + classgradelist['class'+index].coursename + '</span>');
          for( var i=0; i< classgradelist['class'+index].numStudents; ++i) {
            var student = classgradelist['class'+index].roster['student'+i];
            $roster.append('<div id="student'+i+'" style="line-height:25px;margin:15px;padding:5px 20px;" />').find('#student'+i)
                    .append('<div style="width:300px;background-color:#CCC;">'+ student.studentid + ' ' + student.lname +', '+ student.fname + '</div>')
                    .append('<div style="margin-left:15px;">' + student.email +'</div>')
                    .append('Grades: <select id="gradeDropList" />')
                    .append('<hr />');
            for( var j=0; j< grades.numGrades; ++j) {
              $roster.find('#student'+i).find('#gradeDropList').append('<option id="grade'+j+'">'+ grades['grade'+j].letter +'</option>');
              $content.find('#student'+i+' #gradeDropList #grade'+j).data('id',grades['grade'+j].id);
              $content.find('#student'+i+' #gradeDropList #grade'+j).data('studentid',student.studentid);
              $content.find('#student'+i+' #gradeDropList #grade'+j).data('classid', $content.find('#classDropList option:selected').data('classnum'));
              $content.find('#student'+i+' #gradeDropList #grade'+j).data('letter',grades['grade'+j].value);
              $content.find('#student'+i+' #gradeDropList #grade'+j).data('value',grades['grade'+j].letter);
            }            
            if( student.gradeid != '' ) {
              $roster.find('#student'+ i +' #gradeDropList option[selected]').removeAttr('selected');
              $roster.find('#student'+ i +' #gradeDropList option#grade' + (student.gradeid-1) ).attr('selected','selected');
            } else {
              $roster.find('#student'+i).css({fontWeight:'bold'});
            }
            $content.find('#student'+i+' #gradeDropList').change( function(){ updateGrade(this); } )
          }
          if (classgradelist['class'+index].numStudents == 0){
            $roster.append('<div style="font-weight:bold;">No Students Registered</div>');
          }
        };
        
        var showGrades = function(head) {
          $content.html(head);
          $content.append('<div id="msgBox" />');
          // Add a drop down with classes
          $dropList = $content.append('<select id="classDropList" style="margin-left:400px;" />').find('#classDropList');
          for( var i=0; i< classgradelist.numCourses; ++i) {
            $dropList.append('<option id="option'+i+'">'+ classgradelist['class'+i].coursenum + ' - ' + classgradelist['class'+i].coursename +'</option>');
            $content.find('#classDropList #option'+i).data('coursename',classgradelist['class'+i].coursename);
            $content.find('#classDropList #option'+i).data('coursenum',classgradelist['class'+i].coursenum);
            $content.find('#classDropList #option'+i).data('classnum',classgradelist['class'+i].classnum);
          }
          $content.find('#classDropList').change( function(){ selectChange(); } ).change();
          
          $roster = $content.append('<div id="gradingRoster" style="text-align:left;" />').find('#gradingRoster');
        };

        var _init = function() {
          $content = $('#main').append('<div id="content" />').find('#content');
          $('#logout').live( 'click', function() {
            window.location.href = "cookie.php?action=logout";
          });
          
          $('#c_list').live( 'click', function() {
            var head='<h3>Class List</h3>';
            $.post('cgi-bin/clist.cgi', {userid:<? echo $_SESSION['userid']; ?>}, function(data) {
              $content.append(data);
              showClassList(head);
              showRoster();
            });
          });
          
          $('#grades').live( 'click', function() {
            var head='<h3>Grade Your Students</h3>';
            $.post('cgi-bin/grade.cgi', {userid:<? echo $_SESSION['userid']; ?>}, function(data) {
              $content.append(data);
              showGrades(head);
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
          $('#c_list').trigger('click');
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
      <a href="#" id="c_list"> View Classes </a>
      <a href="#" id="grades"> Grade Students </a>
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
