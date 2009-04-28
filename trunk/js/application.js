var ApplicationObj = (function() {
  var $content;
  var session_username, session_userid, session_role
  
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
          border:'1px solid #CCC'
        });
      }
    });
  };
  
  // Edit Users Personal Information
  var showPersonalInfo = function(head) {
    $content.html(head);
    $form = $content.append('<form id="frmPInfo" method="POST" action="cgi-bin/query.cgi" />').find('#frmPInfo');
    $form.append('<table id="tblPinfo"/>').find('table').append('<tbody />').find('tbody')
          .append('<tr><td id="key">'+session_role.toUpperCase()+' ID: </td><td id="val">' +person.id+ '</td></tr>')
          .append('<tr><td id="key"> First Name: </td><td id="val">' +person.fname+ '</td></tr>')
          .append('<tr><td id="key"> Last Name: </td><td id="val">' +person.lname+ '</td></tr>')
          .append('<tr><td id="key"><label for="email"> Email: </label></td><td id="val"> <input type="text" id="email" value="' +person.email+ '" /> </td></tr>')
          .append('<tr><td id="key"><label for="address"> Address: </label></td><td id="val"> <input type="text" id="address" value="' +person.address+ '" /> </td></tr>')
          .append('<tr><td id="key"><label for="city"> City: </label></td><td id="val"> <input type="text"  id="city" value="' +person.city+ '" /> </td></tr>')
          .append('<tr><td id="key"><label for="state"> State: </label></td><td id="val"> <input type="text" id="state" maxlength="2" value="' +person.state+ '" /> </td></tr>')
          .append('<tr><td id="key"><label for="zip"> Zip: </label></td><td id="val"> <input type="text" id="zip" maxlength="5" value="' +person.zip+ '" /> </td></tr>')
          .append('<tr><td id="key"><label for="phone"> Phone:<br /><span style="font-size:75%;">(ex. 5555555555)</span></label></td><td id="val"> <input type="text" id="phone" maxlength="10" value="' +person.phone+ '" /></td></tr>')
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
          MsgHandler.addMsg('Successfuly updated Personal Information');
          MsgHandler.showMsg();
        });
        return false;
      });
  };
  
  // Show and Create Invoices
  var create_invoices = function(head) {
    $content.html(head);
    $table = $('<table class="invoices"/>');
    $table.append('<thead />').find('thead')
          .append('<tr><th>Made On</th><th>Request Type</th><th>Description</th><th>Cost</th><th>Fill By</th><th>Approved</th></tr>')
          .end().append('<tbody />');
    if( qry_result == null ) {
      $table.find('tbody').append('<tr />').find('tr:last')
            .append('<td colspan="6">No Invoices Filed</td>')
    } else {
      for( var i=0; i < qry_result.numTuples; ++i ) {
        var inv = qry_result['tuple'+i];
        console.log(inv);
        $table.find('tbody').append('<tr />').find('tr:last')
              .append('<td>' +inv.madeon+ '</td>')
              .append('<td>' +inv.req_desc+ '</td>')
              .append('<td>' +inv.description+ '</td>')
              .append('<td>' +inv.cost+ '</td>')
              .append('<td>' +inv.fillby+ '</td>')
              .append('<td>' +(inv.approved=='t'?'Yes':'No')+ '</td>');
      }
    }
    
    // New Invoice Form
    $frmInv = $('<form id="frmInv" action="#" method="POST" />');
    $frmInv.append('<table id="tblInv" />').find('table')
            .append('<tr><td><label for="req_type">Request Type:</label></td><td><select id="req_type"><option id="dflt">Select Request Type</option></select></td></tr>');
    var qry="select * from request_type order by req_desc";
    $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
      $content.append(data);
      for( var i=0; i<qry_result.numTuples; ++i ) {
        var type = qry_result['tuple'+i];
        $frmInv.find('#req_type').append('<option id="option'+i+'">' +type.req_desc+ '</option>')
               .find('option:last').data('request_data',type);
      }
      $frmInv.find('table')
             .append('<tr><td><label for="descr">Description:</label></td><td>&nbsp;&nbsp;<input type="text" id="descr" /></td></tr>')
             .append('<tr><td><label for="cost">Cost:</label></td><td>$<input type="text" id="cost" /></td></tr>')
             .append('<tr><td><label for="fillby">Fill By:<br /><span>(ex. YYYY-MM-DD)</span></label></td><td>&nbsp;&nbsp;<input type="text" id="fillby" /></td></tr>')
             .append('<tr><td colspan="2" align="center"><input type="submit" id="btnNewInv" value="File New Invoice" /></td></tr>');
      $content.append('<div id="new_inv" />').find('#new_inv')
              .append( $frmInv );
      $content.find('#frmInv').submit(function(e) {
        e.preventDefault();
        if( $(this).find('#req_type option:selected').attr('id')=='dflt' || $(this).find('#descr').val()==''
          || $(this).find('#cost').val() == '' || isNaN(parseFloat($(this).find('#cost').val())) ||
          $(this).find('#fillby').val() == '' || $(this).find('#fillby').val().substring(4,5)!='-' ||
          $(this).find('#fillby').val().substring(7,8)!='-') {
          alert('Please fill out the form correctly.');
        } else {        
          var qry = "INSERT INTO invoice values (DEFAULT,'"+ session_userid +"','";
          qry += $(this).find('#req_type option:selected').data('request_data').id;
          qry += "','"+ parseFloat($(this).find('#cost').val()) +"','"+ $(this).find('#descr').val() +"',current_date,'"+ $(this).find('#fillby').val() +"')";
          $.post('cgi-bin/query.cgi', {query:qry}, function() {
            MsgHandler.addMsg('New Invoice successfully filed.');
            MsgHandler.showMsg();
            setTimeout(function() {
                $('#create_invoice').trigger('click');
            }, 1000);
          });
        }
        return false;
      });
      // Show Invoice History
      $content.append('<div id="inv_hist" />').find('#inv_hist')
              .append( $table );
    });
  };

  // Show Students Class Information
  var stud_showClassInfo = function() {
    $table = $content.append('<table id="tbl_stud_class_info">').find('table');
    $table.append('<thead />').find('thead').append('<tr><td id="coursenum">Subject</td><td id="coursename">Course</td><td id="term">Term</td><td id="credits">Credits</td><td id="prof">Professor</td><td id="email">Professor Email</td></tr>');
    $table.append('<tbody />');
    for( var i=0; i<classes.numCourses; ++i ) {
      var classObj = classes['class'+i];
      $table.find('tbody').append('<tr />').find('tr:last')
            .append('<td id="coursenum">'+ classObj.coursenum +'</td>')
            .append('<td id="coursename">'+ classObj.name +'</td>')
            .append('<td id="term">'+ classObj.term +'</td>')
            .append('<td id="credits">'+ classObj.credits +'</td>')
            .append('<td id="prof">Prof. '+ (!classObj.lname ? 'TBA' : classObj.lname) +'</td>')
            .append('<td id="email">'+ (!classObj.email ? '-' : classObj.email) +'</td>');
    }
    if( classes.numCourses == 0 ) {
      $table.find('tbody').append('<tr />').find('tr:last').append('<td colspan="6">-Not Scheduled For Any Classes-</td>');
    }
  };

  // Show Students grade info
  var stud_showGradeInfo = function() {
    $table = $content.append('<table id="tbl_stud_grade_info" cellpadding="3px">').find('table');
    $table.append('<thead />').find('thead').append('<tr><td>Subject</td><td>Course</td><td>Professor</td><td>Professor Email</td><td>Grade</td></tr>');
    $table.append('<tbody />');
    for( var i=0; i<grades.numCourses; ++i ) {
      var classObj = grades['class'+i];
      $table.find('tbody').append('<tr />').find('tr:last')
            .append('<td id="coursenum">'+ classObj.coursenum +'</td>')
            .append('<td id="coursename">'+ classObj.name +'</td>')
            .append('<td id="prof">Prof. '+ (!classObj.lname ? 'TBA' : classObj.lname) +'</td>')
            .append('<td id="email">'+ (!classObj.email ? '-' : classObj.email) +'</td>')
            .append('<td id="email">'+ (!classObj.letter ? '-' : classObj.letter) +'</td>');
    }
    if( classes.numCourses == 0 ) {
      $table.find('tbody').append('<tr />').find('tr:last').append('<td colspan="6">-Not Scheduled For Any Classes-</td>');
    }
  };
  
  // Show Prof Roster
  var prof_showRoster = function() {
    $content.find('.view_roster').die('click');
    $content.find('.view_roster').live('click', function() {
      $('#prof_class_list').find( 'table#' + $(this).attr('id')).toggle(100);
      $(this).find('img').toggle();
    });
  };
  
  // Show Prof Class list
  var prof_showClassList = function(head) {
    $content.html(head);
    var $roster=new Array();
    var click = new Array();
    var $list = $content.append('<table id="prof_class_list" />').find('#prof_class_list');
    $list.append("<thead />").find('thead').append('<tr />').find('tr')
          .append('<td></td>')
          .append('<td id="coursenum">Subject</td>')
          .append('<td id="coursename">Course Name</td>')
          .append('<td id="term">Term</td>')
          .append('<td id="numStud">Class Count</td>');
    $list = $list.append('<tbody />').find('tbody');
    for( var i=0; i < classlist.numCourses; ++i) {
      $list.append('<tr />').find('tr:last')
            .append('<td class="view_roster" id="roster'+i+'"><img src="minus.png" /></td>')
            .append('<td id="coursenum">'+ classlist['class' + i].coursenum +'</td>')
            .append('<td id="coursename">'+ classlist['class' + i].name +'</td>')
            .append('<td id="term">'+ classlist['class' + i].term +'</td>')
            .append('<td id="numStud">'+ classlist['class' + i].numStudents +'</td>');
      $roster[i] = $list.append('<tr>').find('tr:last').append('<td colspan="5">').find('td').append('<table class="prof_roster" id="roster'+i+'" cellpadding="2px"/>').find('#roster' + i);
      $roster[i] = $roster[i].append('<tbody />').find('tbody');
      for( var j=0; j < classlist['class'+i].numStudents; ++j) {
        $roster[i].append('<tr />').find('tr:last')
                  .append('<td id="rost_id">'+ classlist['class'+i].roster['student'+j].id +'</td>')
                  .append('<td id="rost_name">'+ classlist['class'+i].roster['student'+j].lname +', '+ classlist['class'+i].roster['student'+j].fname +'</td>')
                  .append('<td align="left">'+ classlist['class'+i].roster['student'+j].email +'</td>');
      }
      if (classlist['class'+i].numStudents == 0){
        $roster[i].append('<tr><td colspan="3">No Students Registered</td></tr>');
      }
      $('#prof_class_list').find('table#roster'+i).hide();
    }
  };
  
  // Show Prof Grading
  var prof_showGrades = function(head) {
    $content.html(head);
    // Add a drop down with classes
    $dropList = $content.append('<select id="classDropList"/>').find('#classDropList').append('<option id="dflt">Select Class</option>');

    for( var i=0; i< classgradelist.numCourses; ++i) {
      $dropList.append('<option id="option'+i+'">'+ classgradelist['class'+i].coursenum + ' - ' + classgradelist['class'+i].coursename +'</option>');
      $content.find('#classDropList #option'+i).data('coursename',classgradelist['class'+i].coursename);
      $content.find('#classDropList #option'+i).data('coursenum',classgradelist['class'+i].coursenum);
      $content.find('#classDropList #option'+i).data('classnum',classgradelist['class'+i].classnum);
    }
    $content.find('#classDropList').change( function(){ prof_grade_selectChange(); } );
    
    $roster = $content.append('<div id="gradingRoster" style="text-align:left;" />').find('#gradingRoster');
  };
  
  // Prof Grading change in select
  var prof_grade_selectChange = function() {
    if($content.find('#classDropList option:selected').attr('id') != 'dflt'){
      var index = $content.find('#classDropList option:selected').prev().length;
      $roster = $content.find('#gradingRoster');
      $roster.html('<span>' + classgradelist['class'+index].coursename + '</span>');
      for( var i=0; classgradelist['class'+index].roster !=null && i< classgradelist['class'+index].numStudents; ++i) {
        var student = classgradelist['class'+index].roster['student'+i];
        $roster.append('<div class="student" id="student'+i+'" />').find('#student'+i)
                .append('<div>Student ID:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+ student.studentid + '<br />Student Name: ' + student.lname +', '+ student.fname + '</div>')
                .append('Grades:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select id="gradeDropList" />');
        $roster.find('#student'+i).find('#gradeDropList').append('<option id="dflt">-</option>');
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
          $roster.find('#student'+i).css({border:'2px dashed #A00'});
        }
        $content.find('#student'+i+' #gradeDropList').change( function(){ prof_updateGrade(this); } )
      }
      if (classgradelist['class'+index].numStudents == 0){
        $roster.append('<div class="student" style="font-weight:bold;">No Students Registered</div>');
      }
    }
  };

  // Prof Updates grade
  var prof_updateGrade = function( elem ) {
    if ($(elem).find('option:selected').attr('id') != 'dflt') {
      var gradeid = $(elem).find('option:selected').data('id');
      var studentid = $(elem).find('option:selected').data('studentid');
      var classid = $(elem).find('option:selected').data('classid');
      var qry = "UPDATE class_roster SET gradeid='" +gradeid+ "' WHERE studentid='" +studentid+ "' and classnum='" +classid+ "'";
      $.post('cgi-bin/query.cgi', {query:qry}, function(){
        MsgHandler.addMsg('Successfully gave grade of ' +grades['grade'+ (gradeid-1)].letter);
        MsgHandler.showMsg();
        $(elem).parent('div').css({border:'1px solid #CCC'});
      });
    }
  };
  
  // Show Advisor Student list
  var adv_showStudentList = function(head) {
    $content.html(head);
    $table = $content.append('<table id="advStudList" cellpadding="3">').find('table');
    $table.append('<thead />').find('thead').append('<tr><td>Major</td><td>Degree</td><td>ID</td><td>Student Name</td><td>Student Email</td></tr>');
    $table.append('<tbody />');
    $('#advStudList tr .student').die('click');
    for( var i=0; i < studentList.numStudents; ++i ) {
      var student = studentList['student'+i];
      $table.find('tbody').append('<tr class="student" id="student'+i+'"/>').find('tr:last')
            .append('<td>'+ student.degreename +'</td>')
            .append('<td>'+ student.degreelevel +'</td>')
            .append('<td>'+ student.id +'</td>')
            .append('<td>'+ student.lname + ', ' + student.fname +'</td>')
            .append('<td>'+ student.email +'</td>')
            .data('stud_id',student.id)
            .data('student','student'+i)
            .css({ cursor:'pointer' });
      $('table tr#student'+i).live( 'click', function() { adv_showStudInfo(this); });
    }
    if( studentList.numStudents == 0 ) {
      $table.find('tbody').append('<tr />').find('tr:last').append('<td colspan="5">-No Students Information Available-</td>');
    }
  };

  // Show Advisor Student Info
  var adv_showStudInfo = function( elem ) {
    var stud_id = $(elem).data('stud_id');
    var student = $(elem).data('student');
    var qry = 'select C.coursenum,C.name,P.lname,P.fname,C.term,G.letter,G.value,C.credits  from users as P right outer join course as C on (P.id=c.instructorid) join class_roster as CR on (C.classnum=CR.classnum) left outer join grades as G on (CR.gradeid=G.id) where CR.studentid='+stud_id+' order by C.term DESC';
    
    $box = $content.append('<div id="adv_stud_box" />').find('#adv_stud_box');
    $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
      var credits = 0, grades = 0, gpa = 0;
      $box.html(data)
          .append('<span id="close" style="float:right;font-weight:bold;cursor:pointer;"> Close[x] </span>')
          .append('<h2>' +studentList[student].lname+ ', ' +studentList[student].fname+ '</h2>')
          .append('<h4>' +studentList[student].degreename+ ' - ' +studentList[student].degreelevel+ '</h4>')
          .append('<span>' +studentList[student].address+ '</span><br />')
          .append('<span>' +studentList[student].city+ ', ' +studentList[student].state+ ' ' +studentList[student].zip+ '</span><br />')
          .append('<span>' +studentList[student].phone+ '</span><br />')
          .append('<table id="" cellspacing="5px" cellpadding="5px" align="center" style="margin:5px auto; border:1px solid #000; width:85%;text-align:center"/>').find('table').append('<tr />')
                  .find('tr').append('<td>Semester</td> <td>Course</td> <td>Course Name</td> <td>Professor</td> <td>Credits</td> <td>Grade</td> <td>Value</td>');
      for( var i = 0; qry_result && i < qry_result.numTuples; ++i) {
        $box.find('table').append('<tr />').find('tr:last')
            .append('<td>' + qry_result['tuple'+i].term + '</td>')
            .append('<td>' + qry_result['tuple'+i].coursenum + '</td>')
            .append('<td>' + qry_result['tuple'+i].name + '</td>')
            .append('<td>Prof. ' + ((qry_result['tuple'+i].lname == '')?'TBA':qry_result['tuple'+i].lname + ', ' + qry_result['tuple'+i].fname) + '</td>')
            .append('<td>' + qry_result['tuple'+i].credits + '</td>')
            .append('<td>' + ((qry_result['tuple'+i].letter == '') ? '-' : qry_result['tuple'+i].letter ) + '</td>')
            .append('<td>' + ((qry_result['tuple'+i].value == '' ) ? '-' : qry_result['tuple'+i].value ) + '</td>');
        credits += parseInt( (qry_result['tuple'+i].value=='')?0:qry_result['tuple'+i].credits );
        grades += parseInt(qry_result['tuple'+i].credits)*parseInt( (qry_result['tuple'+i].value=='')?0:qry_result['tuple'+i].value );
      }
      if( !qry_result ) {
        $box.find('table').append('<tr />').find('tr:last').append('<td colspan="7">[No Classes Taken]</td>');
      }
      $box.append('<span style="">Total Credits: ' + credits + '</span><br />')
          .append('<span style="">Overall GPA: ' + ( isNaN(grades/credits)?0:grades/credits ).toPrecision(3) + '</span>');;
      $box.find('table').append('<tr />').find('tr:first').css({fontWeight:'bold'});
      $box.find('#close').live('click',function(){
        $box.animate({opacity:0}, 800, function(){$box.remove();});
      });
    });
  };
  
  // Show Advisor add new student form
  var adv_addNewStud = function(head) {
    $content.find('input').die('click');
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
      $form.append('<table id="adv_new_stud" />').find('table').append('<tbody />').find('tbody')
            .append('<tr><td>New ID: </td><td>' +stud_id+ '</td></tr>')
            .append('<tr><td>First Name:</td><td><input type="text" id="fname" /></td></tr>')
            .append('<tr><td>Last Name:</td><td><input type="text" id="lname" /></td></tr>')
            .append('<tr><td>Username: </td><td><input type="text" id="uname" /></td></tr>')
            .append('<tr><td>Password: </td><td><input type="password" id="pass" /></td></tr>')
            .append('<tr><td>Email: </td><td><input type="text" id="email" /></td></tr>')
            .append('<tr><td>Address: </td><td><input type="text" id="address" /></td></tr>')
            .append('<tr><td>City: </td><td><input type="text" id="city" /></td></tr>')
            .append('<tr><td>State: </td><td><input type="text" id="state" maxlength="2" /></td></tr>')
            .append('<tr><td>Zip: </td><td><input type="text" id="zip" maxlength="5" /></td></tr>')
            .append('<tr><td>Phone: </td><td><input type="text" id="phone" maxlength="10" /></td></tr>')
            .append('<tr><td>Choose Degree: </td><td id="degopts"></td></tr>')
            .append('<tr><td colspan="2" align="right"><input type="submit" id="btnAdd" style="padding:3px;border:1px solid #000;cursor:pointer;" value="Add Student" /></td></tr>');
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
            MsgHandler.addMsg('Successfully added ' + $content.find('#fname').val()+' '+$content.find('#lname').val() + ' as a new Student.');
            MsgHandler.showMsg();
            setTimeout(function() {
              $('#adv_s_list').trigger('click');
            }, 4000);
          });
        }
        return false;
      });
    });
  };
  
  // Show Advisor Register Student
  var adv_registerStudents = function(head) {
    $content.html(head);
    $content.find('input').die('click');
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
            $content.append('<table cellpadding="5px" cellspacing="5px" style="border:1px solid #000;text-align:center;margin:10px 4px;" />') : "";
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
          MsgHandler.addMsg($content.find('#regStudList option:selected').data('name') + ' has been registered for ' + $content.find('#class_to_reg option:selected').data('name'));
          MsgHandler.showMsg();
          setTimeout(function() {
              $('#adv_s_list').trigger('click');
          }, 4000);
        });
      } else {
        alert("Pick a course and try again.");
      }
    });
  };
  
  // Show Advisor employee list
  var admin_showEmp = function(head){
    $content.html(head);
    for( var i=0; i<qry_result.numTuples; ++i) {
      var emp = qry_result['tuple'+i];
      $content.append('<div class="emp"><b>' +emp.lname+ '</b>, <b>' +emp.fname+ '</b> was hired on <i>' +emp.hiredon+ '</i> as <i>' +emp.role+ '</i> earning <em>' +emp.salary+ '</em>.</div>');
    }
  };
  
  // Show Advisor add new hire
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
      $form.append('<table id="admin_tblNewHire" />').find('table').append('<tbody />').find('tbody')
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
            MsgHandler.addMsg('Successfully added ' + $content.find('#fname').val()+' '+$content.find('#lname').val() + ' as a new Staff Member.');
            MsgHandler.showMsg();
            setTimeout(function() {
              $('#admin_show_emp').trigger('click');
            }, 4000);
          });
        }
        return false;
      });
    });
  };
  
  // Admin Show Assign classes
  var admin_assignClass = function(head) {
    var prof_id;
    $content.html(head);
    $content.append('<div id="reset_container"><span id="reset">&laquo;Reset&raquo;</span></div>')
            .append('<p id="help_info">First choose a professor, then click on the classed you want that professor to teach. Then click on the button to save these changes.  Click Reset if you want to start over.</p>');
    $content.find('#reset').click(function(){
      $('#admin_assign_classes').trigger('click');
    });
    $crs = $content.append('<div id="crs"></div>').find('#crs');
    $prof = $content.append('<div style="font-size:12px;font-weight:bold;">Professors:</div><div id="profs"/>').find('#profs');
    for( var i=0; i<qry_result.numTuples; ++i) {
      $prof.append('<div id="prof'+qry_result['tuple'+i].id+'"> ' + qry_result['tuple'+i].lname + ', ' + qry_result['tuple'+i].fname + '<div id="courses" /> </div>');
      $prof.find('#prof' + qry_result['tuple'+i].id).data('id', qry_result['tuple'+i].id).data('lname', qry_result['tuple'+i].lname);
      $prof.find('div').die('click').live('click', function(){
        var prof = this;
        $(prof).css({color:'#444',border:'1px dotted #000'});
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
            $.post('cgi-bin/query.cgi', {query:qry}, function() {
              MsgHandler.addMsg('Successfully assigned class(es) to Prof. ' + $(prof).data('lname') + '.');
              MsgHandler.showMsg();
              setTimeout(function() {
                $('#admin_assign_classes').trigger('click');
              }, 4000);
            });
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

  // Show Admin manage invoices
  var admin_manageInv = function(head) {
    $content.html(head);
    
    // Add request types
    $AddReqType = $content.append('<div id="ad_req" />').find('#ad_req');
    $AddReqType.append('<span>Add Request Types:</span>');
    
    $frmAddReq = $('<form id="frmadd_req" action="#" method="POST" />')
                    .append('<label for="req_desc">Description:</label> <input type="text" id="req_desc" />')
                    .append('<br /><input type="submit" id="btnAddReq" value="Add Request" />');
    
    $reqBox = $('<div id="req_box" />');
    if ( qry_result==null ) {
      $reqBox.append('<div>There are currently no requests</div>');
    } else{
      for( var i=0; i<qry_result.numTuples; ++i) {
        $reqBox.append('<div>' + qry_result['tuple'+i].req_desc + '</div>');
      }
    }
    $AddReqType.append($frmAddReq)
                .append($reqBox)
                .append('<div style="clear:both"></div>');
    $content.find('#frmadd_req').submit( function(e) {
      e.preventDefault();
      if( $(this).find('input#req_desc').val() != '' ) {
        $(this).find('input#btnAddReq').attr('disabled','disabled').val('Adding...');
        $.post('cgi-bin/query.cgi', {query:'Insert into request_type values (DEFAULT,\'' + $(this).find('input#req_desc').val() + '\')'}, function() {
          MsgHandler.addMsg('Successfully added new msg.');
          MsgHandler.showMsg();
          setTimeout(function() {
            $('#admin_manage_inv').trigger('click');
          }, 1000);
        });
      }
      return false;
    });
    
    // Approve Invoices
    $ApproveInvoice = $content.append('<div id="app_inv" />').find('#app_inv');
    $ApproveInvoice.append('<span>Approve Invoices:</span>');
    var qry = "SELECT i.id,u.lname,u.fname,r.req_desc,i.description,i.cost,i.madeon,i.fillby,i.approved";
    qry+=" FROM users as u, invoice as i, request_type as r where u.id=i.userid and i.requestid=r.id and fillby>=current_date order by i.approved,i.fillby,u.lname,u.fname";
    $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
      $content.append(data);
      if( qry_result == null ) {
        $ApproveInvoice.append('<br /><span>There are currently no invoices.</span>');
      } else {
        $ApproveInvoice.append('<table class="invoices" />').find('.invoices')
                        .append('<thead><tr><th>Name</th><th>Request Type</th><th width="20%" style="overflow:auto;">Description</th><th>Cost</th><th>Made On</th><th>Fill By</th><th width="5%"></th></tr></thead>')
                        .append('<tbody />');
        for( var i=0; i<qry_result.numTuples; ++i ) {
          var inv = qry_result['tuple'+i];
          var row = '<tr><td>' +inv.fname+' '+inv.lname+ '</td><td>' +inv.req_desc+ '</td><td>';
          row += inv.description+ '</td><td>' +inv.cost+ '</td><td>' +inv.madeon+ '</td><td>' +inv.fillby;
          row += '</td><td><input type="checkbox" id="approve" ' +(inv.approved == 't' ? 'checked' : '')+ '/></td></tr>';
          $ApproveInvoice.find('table tbody')
                          .append(row).find('#approve:last')
                          .data('invoice', inv);
        }
        $('#approve').die('click').live('click', function() {
          var inv = $(this).data('invoice');
          var checked = $(this).is(':checked');
          var qry = "UPDATE invoice SET approved='" +checked+ "' where id='" +inv.id+ "'";
          $.post('cgi-bin/query.cgi', {query:qry}, function() {
            MsgHandler.addMsg('Successfully ' +(checked?'approved':'disapproved')+ ' invoice.');
            MsgHandler.showMsg();
          });
        });
      }
    });
  };

  var _init = function(s_userid, s_role, s_username) {
    $('#left_col').find('#content').length == 0 ?
      $content = $('#left_col').append('<div id="content" />').find('#content'):
      $content = $('#left_col').find('#content');
    session_userid = s_userid;
    session_username = s_username;
    session_role = s_role;
    
    $('#links a').live('click', function() {
      $('#links a').removeClass('selected');
      $(this).addClass('selected');
    });

    $('#logout').live( 'click', function() {
      window.location.href = "cookie.php?action=logout";
    });

    $('#p_info').live( 'click', function() {
      var head='<h3>Personal Info</h3>';
      $.post('cgi-bin/pinfo.cgi', {userid:session_userid}, function(data) {
        $content.append(data);
        showPersonalInfo(head);
        addBlur(person);
      });
    });
    
    $('#create_invoice').live('click', function() {
      var head='<h3>Create Invoice</h3>';
      var qry ="select * from invoice as i, request_type as r where i.requestid=r.id and i.userid='" +session_userid+ "' order by madeon";
      $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
        $content.append(data);
        create_invoices(head);
      });
    });
    
    /*-Start Students-*/
    $('#classes').live( 'click', function() {
      $content.html('<h3>Class History</h3>');
      $.post('cgi-bin/cinfo.cgi', {userid:session_userid}, function(data) {
        $content.append(data);
        stud_showClassInfo();
      });
    });
    $('#grades').live( 'click', function() {
      $content.html('<h3>Grades History</h3>');
      $.post('cgi-bin/vgrade.cgi', {userid:session_userid}, function(data) {
        $content.append(data);
        stud_showGradeInfo();
      });
    });
    $('#grad').live( 'click', function() {
      $content.html('').html('<h3>Graduation Requirements</h3>');
      $.post('cgi-bin/', {userid:session_userid}, function(data) {
        $content.append(data);
      });
    });
    /*-End Students-*/
    
    /*-Start Professors-*/
    $('#prof_c_list').live( 'click', function() {
      var head='<h3>Class List</h3>';
      $.post('cgi-bin/clist.cgi', {userid:session_userid}, function(data) {
        $content.append(data);
        prof_showClassList(head);
        prof_showRoster();
      });
    });
    
    $('#prof_grades').live( 'click', function() {
      var head='<h3>Grade Your Students</h3>';
      $.post('cgi-bin/grade.cgi', {userid:session_userid}, function(data) {
        $content.append(data);
        prof_showGrades(head);
      });
    });
    /*-End Professors-*/
    
    /*-Start Advisors-*/
    $('#adv_s_list').live( 'click', function() {
      var head='<h3>Students List</h3>';
      $.post('cgi-bin/vstudlist.cgi', {userid:session_userid}, function(data) {
        $content.append(data);
        adv_showStudentList(head);
      });
    })
    
    $('#adv_n_stud').live( 'click', function() {
      var head='<h3>Add New Student</h3>';
      var qry = 'select last_value, increment_by from users_id_seq';
      $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
        $content.append(data);
        adv_addNewStud(head);
      });
    });
    
    $('#adv_r_class').live( 'click', function() {
      var head='<h3>Register For Classes</h3>';
      var qry = 'select u.id,u.lname,u.fname,m.degreename,m.degreelevel ';
      qry+='from students as s, majors as m, users as u where u.id=s.userid and s.degreeid=m.degreeid order by m.degreename,u.lname,u.fname,u.id';
      $.post('cgi-bin/query2.cgi',{query:qry}, function(data) {
        $content.append(data);
        adv_registerStudents(head);
      });
    });
    /*-End Advisors-*/
    
    /*-Start Admin-*/
    $('#admin_show_emp').live( 'click', function() {
      var head='<h3>Department Employees</h3>';
      var qry = 'select u.id,u.fname,u.lname,u.email,r.role,s.salary,s.hiredon from users as u, staff s, roles as r where u.id=s.userid and u.roleid=r.roleid order by r.role,u.lname,u.fname';
      $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
        $content.append(data);
        admin_showEmp(head);
      });
    });
    
    $('#admin_add_emp').live( 'click', function() {
      var head='<h3>Add New Hire</h3>';
      var qry = 'select last_value, increment_by from users_id_seq';
      $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
        $content.append(data);
        admin_addNewHire(head);
      });
    });
    
    $('#admin_assign_classes').live( 'click', function() {
      var head='<h3>Assign Classes</h3>';
      var qry ="select * from users as u, roles as r where u.roleid=r.roleid and role='professor'";
      $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
        $content.append(data);
        admin_assignClass(head);
      });
    });
    
    $('#admin_manage_inv').live('click', function() {
      var head='<h3>Manage Invoices</h3>';
      var qry ="select * from request_type order by req_desc";
      $.post('cgi-bin/query2.cgi', {query:qry}, function(data) {
        $content.append(data);
        admin_manageInv(head);
      });
    });
    /*-End Admin-*/
  };

  return {
    init: function(session_userid, session_role, session_username) {
      return _init(session_userid, session_role, session_username);
    }
  };
})();
