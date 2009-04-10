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
  char qry[512];
  sprintf(qry, "SELECT C.coursenum,C.name,C.term,C.credits,P.lname,P.fname,P.email FROM class_roster as CR, course as C LEFT OUTER JOIN users as P ON (C.instructorid=P.id) where CR.classnum=C.classnum and CR.studentid='%s' order by C.term DESC", userid);

  PGresult* theResult= PQexec(conn, qry);
  int numCourses = PQntuples(theResult);
  
  printf("Content-type: text/html%c%c", 10, 10);
  printf("<script type='text/javascript'>");
  printf("var classes = (function(){return {");
  for( int i = 0; i< numCourses; ++i ) {
    printf("class%d:{ ", i);
    for( int j = 0; j < PQnfields(theResult); ++j ) {
      printf("%s:\"%s\", ", PQfname(theResult, j), PQgetvalue( theResult, i, j ) );
    }
    printf("}, ");
  }
  printf("numCourses:'%d'", numCourses);
  printf("};})();");
  printf("</script>");
  
  PQclear(theResult);
  PQfinish( conn );
}

