/*
  Tasks:
    Grades have id => Get these and make a drop down with these values
           id | value | letter 
          ----+-------+--------
            1 |   4.0 | A
            2 |   3.7 | A-
            3 |   3.3 | B+
            4 |   3.0 | B
            5 |   2.7 | B-
            6 |   2.3 | C+
            7 |   2.0 | C
            8 |   1.7 | C-
            9 |   1.3 | D+
           10 |   1.0 | D
           11 |   0.5 | F
    Have drop down with each class
qry: select * from course where instructorid='%s';
   : All classes this prof is teaching
   
qry: select cr.classnum, u.id, u.fname, u.lname, u.email from class_roster as cr, users as u where cr.studentid=u.id and cr.classnum='%s';
   : All classes this prof is teaching
 */


#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>

void printClassInfo();

int main( int argc, char* argv[] ) {
  printClassInfo();
}

void printClassInfo() {
  char buf[512], *userid;
  int len = atoi( getenv("CONTENT_LENGTH") );
  fread( buf, len, 1, stdin );
  buf[len] = '\0';
  userid = buf+7;

  // Get all of the class information stored for user with given userid:
  PGconn* conn = PQconnectdb( "host=pdc-amd01 user=kkhawa01" );
  if ( PQstatus( conn ) == CONNECTION_BAD ) {
    printf( "Bad connection.%c", 10);
  }
  char qry1[512], qry2[512], qry3[512];
  sprintf(qry1, "select classnum, coursenum, name, term from course where instructorid='%s' order by classnum asc;", userid);
  sprintf(qry2, "select id,value,letter from grades;");

  PGresult *coursesQry= PQexec(conn, qry1);
  PGresult *gradesListQry=PQexec(conn, qry2), *rosterQry;
  int numCourses = PQntuples(coursesQry), numStudents = 0;
  
  printf("Content-type: text/html%c%c", 10, 10);
  printf("<script type='text/javascript'>");
  printf("var grades = (function(){return {");
  for( int i = 0; i < PQntuples(gradesListQry); ++i) {
    printf("grade%d:{", i);
    printf("id:\"%s\",", PQgetvalue(gradesListQry, i, 0));
    printf("value:\"%s\",", PQgetvalue(gradesListQry, i, 1));
    printf("letter:\"%s\"", PQgetvalue(gradesListQry, i, 2));
    printf("},");
  }
  printf("numGrades:%d};})();", PQntuples(gradesListQry));


  printf("var classgradelist = (function(){return {");
  for( int i=0; i < numCourses; ++i ) {
    printf("class%i:{", i);
    printf("classnum:\"%s\",", PQgetvalue(coursesQry, i, 0));
    printf("coursenum:\"%s\",", PQgetvalue(coursesQry, i, 1));
    printf("coursename:\"%s\",", PQgetvalue(coursesQry, i, 2));
    printf("term:\"%s\",", PQgetvalue(coursesQry, i, 3));
    printf("roster:{");
    
    sprintf(qry2, "select cr.classnum, u.id, u.fname, u.lname, u.email, cr.gradeid from class_roster as cr, users as u where cr.studentid=u.id and cr.classnum='%s' order by u.lname, u.fname, u.id;", PQgetvalue(coursesQry, i, 0));
    rosterQry = PQexec(conn, qry2);
    numStudents = PQntuples(rosterQry);

    for( int k=0; k < numStudents; ++k) {
      printf("student%d:{", k);
      printf("classnum:\"%s\",", PQgetvalue(rosterQry, k, 0));
      printf("studentid:\"%s\",", PQgetvalue(rosterQry, k, 1));
      printf("fname:\"%s\",", PQgetvalue(rosterQry, k, 2));
      printf("lname:\"%s\",", PQgetvalue(rosterQry, k, 3));
      printf("email:\"%s\",", PQgetvalue(rosterQry, k, 4));
      printf("gradeid:\"%s\"", PQgetvalue(rosterQry, k, 5));
      printf("},");
    }
    printf("},numStudents:%d,},", numStudents);
    PQclear(rosterQry);
  }
  printf("numCourses:\"%d\"", numCourses);
  printf("};})();");
  printf("</script>");
  
  PQclear(gradesListQry);
  PQclear(coursesQry);
  PQfinish( conn );
}

