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
      echo "<title>Polytechnic IUDIS - Advisor Home: ". $_SESSION['username'] ."</title>";
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
        
        var showStudInfo = function( elem ) {
          var stud_id = $(elem).data('stud_id');
          var student = $(elem).data('student');
          var qry = 'select C.coursenum,C.name,P.lname,P.fname,C.term,G.letter,G.value,C.credits  from users as P right outer join course as C on (P.id=c.instructorid) join class_roster as CR on (C.classnum=CR.classnum) left outer join grades as G on (CR.gradeid=G.id) where CR.studentid='+stud_id+' order by C.term DESC';
          
          $box = $content.append('<div id="box" />').find('#box');
          $box.css({
            position:'absolute',
            left:0,
            right:0,
            backgroundColor:'#9CC',
            border:'1px solid #000',
            width:'800px',
            top:'100px',
            textAlign:'center',
            margin:'auto',
            display:'block'
          });
          $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
            var credits = 0, grades = 0, gpa = 0;
            $box.html(data)
                .append('<span id="close" style="float:right; font-weight:bold;cursor:pointer;"> Close[x] </span>')
                .append('<h2>' +studentList[student].lname+ ', ' +studentList[student].fname+ '</h2>')
                .append('<h4>' +studentList[student].degreename+ ' - ' +studentList[student].degreelevel+ '</h4>')
                .append('<span>' +studentList[student].address+ '</span><br />')
                .append('<span>' +studentList[student].city+ ', ' +studentList[student].state+ ' ' +studentList[student].zip+ '</span><br />')
                .append('<span>' +studentList[student].phone+ '</span><br />')
                .append('<table cellspacing="5px" cellpadding="5px" align="center" style="margin:5px auto; border:1px solid #000; width:85%;text-align:center"/>').find('table').append('<tr />')
                        .find('tr').append('<td>Semester</td> <td>Course</td> <td>Course Name</td> <td>Professor</td> <td>Credits</td> <td>Grade</td> <td>Value</td>');
            for( var i = 0; qry_result && i < qry_result.numTuples; ++i) {
              $box.find('table').append('<tr />').find('tr:last')
                  .append('<td>' + qry_result['tuple'+i].term + '</td>')
                  .append('<td>' + qry_result['tuple'+i].coursenum + '</td>')
                  .append('<td>' + qry_result['tuple'+i].name + '</td>')
                  .append('<td>Prof. ' + qry_result['tuple'+i].fname.substring(0,1) + '. ' + qry_result['tuple'+i].lname + '</td>')
                  .append('<td>' + qry_result['tuple'+i].credits + '</td>')
                  .append('<td>' + ((qry_result['tuple'+i].letter == '') ? '-' : qry_result['tuple'+i].letter ) + '</td>')
                  .append('<td>' + ((qry_result['tuple'+i].value == '' ) ? '-' : qry_result['tuple'+i].value ) + '</td>');
              credits += parseInt( (qry_result['tuple'+i].value=='')?0:qry_result['tuple'+i].credits );
              grades += parseInt(qry_result['tuple'+i].credits)*parseInt( (qry_result['tuple'+i].value=='')?0:qry_result['tuple'+i].value );
            }
            if( !qry_result ) {
              $box.find('table').append('<tr />').find('tr:last').append('<td colspan="7">No Classes Taken</td>');
            }
            $box.append('<span style="">Total Credits: ' + credits + '</span><br />')
                .append('<span style="">Overall GPA: ' + ( isNaN(grades/credits)?0:grades/credits ).toPrecision(3) + '</span>');;
            $box.find('table').append('<tr />').find('tr:first').css({fontWeight:'bold'});
            $box.find('#close').live('click',function(){
              $box.animate({opacity:0}, 800, function(){$box.remove();});
            });
          });
        };
        
        var showStudentList = function(head) {
          $content.html(head);
          $table = $content.append('<table cellspacing="0" cellpadding="3"/ align="center" style="text-align:center;width:750px;font-size:12px;">').find('table');
          $table.append('<thead />').find('thead').append('<tr><td>Major</td><td>Degree</td><td>ID</td><td>Student Name</td><td>Student Email</td></tr>');
          $table.append('<tbody />');
          for( var i=0; i < studentList.numStudents; ++i ) {
            var student = studentList['student'+i];
            $table.find('tbody').append('<tr id="student'+i+'"/>').find('tr:last')
                  .append('<td id="">'+ student.degreename +'</td>')
                  .append('<td id="">'+ student.degreelevel +'</td>')
                  .append('<td id="">'+ student.id +'</td>')
                  .append('<td id="">'+ student.lname + ', ' + student.fname +'</td>')
                  .append('<td id="">'+ student.email +'</td>')
                  .data('stud_id',student.id)
                  .data('student','student'+i)
                  .css({ cursor:'pointer' });
            $('table tr#student'+i).live( 'click', function() { showStudInfo(this); });
          }
          if( studentList.numStudents == 0 ) {
            $table.find('tbody').append('<tr />').find('tr:last').append('<td colspan="5">-No Students Information Available-</td>');
          }
        };
        
        var addNewStud = function(head) {
          var qry = "select degreeid, degreename, degreelevel from majors";
          var stud_id = parseInt(qry_result.tuple0.last_value) + parseInt(qry_result.tuple0.increment_by);
          $.post('cgi-bin/query2.cgi',{query:qry}, function(data) {
            $content.append(data);
            $content.html(head);
            $majors = $('<select id="majors"/>');
            for( var i=0; i < qry_result.numTuples; ++i) {
              $majors.append('<option id="major' +i+ '">' + qry_result['tuple'+i].degreename + '-' + qry_result['tuple'+i].degreelevel + '</option>');
              $majors.find('#major'+i).data('degreeid', qry_result['tuple'+i].degreeid);
            }

            $form = $content.append('<form id="frmAddStud" method="POST" action="cgi-bin/query.cgi" />').find('#frmAddStud');
            $form.append('<table align="center" border="0px" cellpadding="3px" cellspacing="0" width="45%" />').find('table').append('<tbody />').find('tbody')
                  .append('<tr><td>StudentID: </td><td>' +stud_id+ '</td></tr>')
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
                  .append('<tr><td>Choose Degree: </td><td id="degopts"></td></tr>')
                  .append('<tr><td colspan="2" align="right"><input type="submit" id="btnAdd" style="padding:3px;border:1px solid #000;cursor:pointer;" value="Add New Student" /></td></tr>');
            $form.find('#degopts').append($majors);
            $('#frmAddStud').submit( function(e) {
              e.preventDefault();
              if( $content.find('#fname').val() == '' || $content.find('#lname').val() == '' || $content.find('#pass').val() == '' || $content.find('#email').val() == ''|| $content.find('#address').val() == '' ||
              $content.find('#city').val() == '' || $content.find('#state').val() == '' || $content.find('#zip').val() == '' || $content.find('#phone').val() == '' || $content.find('#uname').val() == '' ) {
                alert("Please fill out completely, and try again.");
                return false;
              } else {
                var degreeid=$content.find('#majors option:selected').data('degreeid');
                var qry = "INSERT INTO users VALUES(DEFAULT, '"+$content.find('#uname').val()+"', '"+$content.find('#pass').val()+"', '"+$content.find('#email').val()+"',";
                qry += " '"+$content.find('#fname').val()+"', '"+$content.find('#lname').val()+"', '"+$content.find('#address').val()+"', '"+$content.find('#city').val()+"',";
                qry += " '"+$content.find('#state').val()+"', '"+$content.find('#zip').val()+"', '"+$content.find('#phone').val()+"', '6')";
                $.post('cgi-bin/query.cgi', {query:qry});
                var qry2 = "INSERT INTO students VALUES('" +stud_id+ "', '" +degreeid+ "')";
                $.post('cgi-bin/query.cgi', {query:qry2}, function() {
                  $content.append('<div id="msgBox" />'). find('#msgBox').text('Successfully added ' + $content.find('#fname').val()+' '+$content.find('#lname').val() + ' as a new Student.' );
                });
              }
              return false;
            });
          });
        };
        
        var registerStudents = function(head) {
          $content.html(head);
          $studDropList = $content.append('<select id="regStudList" />').find('#regStudList').append('<option id="dflt">Select Student</option>');
          for( var i=0; i< qry_result.numTuples; ++i) {
            var regstudent = qry_result['tuple'+i];
            $content.find('#regStudList').append('<option id="student' + i + '">' + regstudent.lname + ', ' + regstudent.fname + ' - ' + regstudent.degreename + ' ' + regstudent.degreelevel + '</option>');
            $content.find('#regStudList option#student'+i).data('id', regstudent.id);
            $content.find('#regStudList option#student'+i).data('name', regstudent.fname + " " + regstudent.lname);
          }
          $content.find('#regStudList').change( function(){
            var crsTaken = new Array();
            $content.find('select#class_to_reg').remove();
            $content.find('#btnRegisterClass').remove();
            if( $content.find('#regStudList option:selected').attr('id') != 'dflt' ) {
              var stud_id = $content.find('#regStudList option:selected').data('id');
              var qry = 'select C.classnum,C.coursenum,C.name,C.term,G.letter,C.credits from course as C';
              qry +=' join class_roster as CR on (C.classnum=CR.classnum) left outer join grades';
              qry +=' as G on (CR.gradeid=G.id) where CR.studentid='+stud_id+' order by C.term DESC';

              ($content.find('table').text() == "") ? 
                  $content.append('<table cellpadding="5px" cellspacing="5px" style="border:1px solid #000;text-align:center;margin:10px auto;" />') : "";
              $content.find('table').html('<tr> <td>Semester</td> <td>Course</td> <td>Course Name</td> <td>Credits</td> <td>Grade</td> </tr>').find('tr:first')
                      .css({fontWeight:'bold'});
              $.post('cgi-bin/query2.cgi',{query:qry}, function(data) {
                $content.append(data);
                for( var i = 0; qry_result && i < qry_result.numTuples; ++i) {
                  $content.find('table').append('<tr />').find('tr:last')
                          .append('<td>' + qry_result['tuple'+i].term + '</td>')
                          .append('<td>' + qry_result['tuple'+i].coursenum + '</td>')
                          .append('<td>' + qry_result['tuple'+i].name + '</td>')
                          .append('<td>' + qry_result['tuple'+i].credits + '</td>')
                          .append('<td>' + ((qry_result['tuple'+i].letter == '') ? '-' : qry_result['tuple'+i].letter ) + '</td>');
                  crsTaken[i] = qry_result['tuple'+i].classnum;
                }
                if( !qry_result ) {
                  $content.find('table').append('<tr />').find('tr:last').append('<td colspan="7">No Classes Taken</td>');
                }
                
                qry='select * from course ' + (crsTaken.length ? 'where ':'');
                for(var i=0;i<crsTaken.length; ++i) {
                  qry += "classnum!='" + crsTaken[i] + "' " + ( (i+1 < crsTaken.length) ? 'and ': '' );
                }
                $.post('cgi-bin/query2.cgi',{query:qry}, function(data) {
                  $content.append(data);
                  // Create a select box with these vals.
                  $content.append('<select id="class_to_reg" style="float:left;" />').find("#class_to_reg").append('<option id="dflt">Select Course</option>');
                  for( var i=0; i< qry_result.numTuples; ++i) {
                    var regclass = qry_result['tuple'+i];
                    $content.find('#class_to_reg').append('<option id="class' + i + '">' + regclass.coursenum + ' - ' + regclass.name + '</option>');
                    $content.find('#class_to_reg option#class'+i).data('classnum', regclass.classnum);
                    $content.find('#class_to_reg option#class'+i).data('name', regclass.name);
                    $content.find('#class_to_reg option#class'+i).data('term', regclass.term);
                  }
                  // button to create class
                  $content.append('<input type="button" id="btnRegisterClass" value="Register" style="float:right;" /><div style="clear:both;"> </div>');
                });
              });
            }
          });
          $content.find('#btnRegisterClass').live('click', function() {
            var studentid = $content.find('#regStudList option:selected').data('id');
            var classnum  = $content.find('#class_to_reg option:selected').data('classnum');
            var term = "(Select term from curr_term)";
            if (classnum) {
              var qry = "Insert into class_roster values ('"+ classnum +"',"+ term +",'"+ studentid +"',null)";
              $.post('cgi-bin/query.cgi', {query:qry}, function() {
                $content.append('<h4>' + $content.find('#regStudList option:selected').data('name') + ' has been registered for ' + $content.find('#class_to_reg option:selected').data('name') + '</h4>');
                //$content.find().trigger();
              });
            } else {
              alert("Pick a course and try again.");
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
          $content = $('#main').append('<div id="content" style="padding:10px;" />').find('#content');
          $('#logout').live( 'click', function() {
            window.location.href = "cookie.php?action=logout";
          });

          $('#s_list').live( 'click', function() {
            var head='<h3>Students List</h3>';
            $.post('cgi-bin/vstudlist.cgi', {userid:<? echo $_SESSION['userid']; ?>}, function(data) {
              $content.append(data);
              showStudentList(head);
            });
          }).trigger('click');
          
          $('#n_stud').live( 'click', function() {
            var head='<h3>Add New Student</h3>';
            var qry = 'select last_value, increment_by from users_id_seq';
            $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
              $content.append(data);
              addNewStud(head);
            });
          });
          
          $('#r_class').live( 'click', function() {
            var head='<h3>Register For Classes</h3>';
            var qry = 'select u.id,u.lname,u.fname,m.degreename,m.degreelevel ';
            qry+='from students as s, majors as m, users as u where u.id=s.userid and s.degreeid=m.degreeid order by m.degreename,u.lname,u.fname,u.id';
            $.post('cgi-bin/query2.cgi',{query:qry}, function(data) {
              $content.append(data);
              registerStudents(head);
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
      <a href="#" id="s_list"> Students List </a>
      <a href="#" id="n_stud"> Add New Student </a>
      <a href="#" id="r_class"> Register Student </a>
      <a href="#" id="p_info"> View Personal Information </a>
      <div style="clear:both"></div>
    </div>

    <div id="body_panel">
      <div id="main" style="border:1px solid #AAA;margin:15px;background-color:#EEE;">
      </div>
    </div>

    <div id="foot"><span>--Footer--</span><div style="clear:both"></div></div>

    </div>
  </body>

</html>
