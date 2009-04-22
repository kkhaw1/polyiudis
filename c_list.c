#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>
#include "yacgi12/src/yacgi.h"

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
  char qry[512], qry2[512];
  sprintf(qry, "SELECT coursenum, name, term, classnum FROM course WHERE instructorid='%s';", userid);

  PGresult* theResult= PQexec(conn, qry);
  PGresult* rosterQry;
  int numCourses = PQntuples(theResult), numStudents = 0;
  
  printf("Content-type: text/html%c%c", 10, 10);
  printf("<script type='text/javascript'>");
  printf("var classlist = (function(){return {");
  for( int i = 0; i< numCourses; ++i ) {
    printf("class%d:{ ", i);
    for( int j = 0; j < PQnfields(theResult); ++j ) {
      printf("%s:\"%s\", ", PQfname(theResult, j), PQgetvalue( theResult, i, j ) );
    }
    printf("roster:{");
    sprintf(qry2, "SELECT CR.classnum, U.id, U.lname, U.fname, C.coursenum, U.email FROM class_roster AS CR, course AS C, users AS U WHERE C.classnum=CR.classnum AND U.id=CR.studentid AND C.instructorid='%s' AND C.classnum='%s' ORDER BY U.lname, U.fname ASC;", userid, PQgetvalue( theResult, i, 3 ));
    rosterQry = PQexec(conn, qry2);
    numStudents = PQntuples(rosterQry);
    for( int j = 0; j < numStudents; ++j ) {
      printf("student%d:{", j);
      for( int k = 0; k < PQnfields(rosterQry); ++k ) {
        printf("%s:\"%s\", ", PQfname(rosterQry, k), PQgetvalue( rosterQry, j, k ) );
      }
      printf("},");
    }
    printf("},");
    printf("numStudents:'%d'", numStudents);
    PQclear(rosterQry);
    printf("}, ");
  }
  printf("numCourses:'%d'", numCourses);
  printf("};})();");
  printf("</script>");
  
  PQclear(theResult);
  PQfinish( conn );
}

