//select u.id,u.lname,u.fname,u.username,u.email,u.address,u.city,u.state,u.zip,u.phone,m.degreename,m.degreelevel from students as s, majors as m, users as u where u.id=s.userid and s.degreeid=m.degreeid order by u.lname,u.fname,u.id;

#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>
#include "yacgi12/src/yacgi.h"

void printStudentList();

int main( int argc, char* argv[] ) {
  printStudentList();
}

void printStudentList() {
  char buf[512];

  // Get all of the class information stored for user with given userid:
  PGconn* conn = PQconnectdb( "host=pdc-amd01 user=kkhawa01" );
  if ( PQstatus( conn ) == CONNECTION_BAD ) {
    printf( "Bad connection.%c", 10);
  }
  char qry[512];
  sprintf(qry, "select u.id,u.lname,u.fname,u.username,u.email,u.address,u.city,u.state,u.zip,u.phone,m.degreename,m.degreelevel from students as s, majors as m, users as u where u.id=s.userid and s.degreeid=m.degreeid order by m.degreeid,u.lname,u.fname,u.id");

  PGresult* theResult= PQexec(conn, qry);
  int numStudents = PQntuples(theResult);
  
  printf("Content-type: text/html%c%c", 10, 10);
  printf("<script type='text/javascript'>");
  printf("var studentList = (function(){return {");
  for( int i = 0; i< numStudents; ++i ) {
    printf("student%d:{ ", i);
    for( int j = 0; j < PQnfields(theResult); ++j ) {
      printf("%s:\"%s\", ", PQfname(theResult, j), PQgetvalue( theResult, i, j ) );
    }
    printf("}, ");
  }
  printf("numStudents:'%d'", numStudents);
  printf("};})();");
  printf("</script>");
  
  PQclear(theResult);
  PQfinish( conn );
}

