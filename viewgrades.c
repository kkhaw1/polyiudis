// select C.coursenum,C.name,P.lname,P.fname,P.email,G.letter  from users as P right outer join course as C on (P.id=c.instructorid) join class_roster as CR on (C.classnum=CR.classnum) left outer join grades as G on (CR.gradeid=G.id) where CR.studentid=

#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>

void printGradeInfo();

int main( int argc, char* argv[] ) {
  printGradeInfo();
}

void printGradeInfo() {
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
  sprintf(qry, "select C.coursenum,C.name,P.lname,P.fname,P.email,G.letter  from users as P right outer join course as C on (P.id=c.instructorid) join class_roster as CR on (C.classnum=CR.classnum) left outer join grades as G on (CR.gradeid=G.id) where CR.studentid='%s' order by C.term DESC", userid);

  PGresult* theResult= PQexec(conn, qry);
  int numCourses = PQntuples(theResult);
  
  printf("Content-type: text/html%c%c", 10, 10);
  printf("<script type='text/javascript'>");
  printf("var grades = (function(){return {");
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

