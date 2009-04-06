#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>
#include "yacgi12/src/yacgi.h"

void test();

int main( int argc, char* argv[] ) {
  test();
}

void test() {
  CGI *cgi;
  int more;
  char *username, *pass;

  printf("Content-type: text/html%c%c",10,10);
  /*-----------------------------------------------------------
  *              Opening Relation
  *-----------------------------------------------------------*/
  cgi = cgiOpen();
  if(!cgi) {
    printf("<H3>%s</H3>%c",cgiStateMsg(),10);
    exit(1);
  }

  /*-----------------------------------------------------------
  *              Scanning Relation
  *-----------------------------------------------------------*/
  more= cgiFirst(cgi);
  while(more) {
    if ( strcmp(cgiName(cgi), "username") == 0 ) {
      username = cgiValue(cgi);
    } else if ( strcmp(cgiName(cgi), "pwd") == 0 ) {
      pass = cgiValue(cgi);
    }
    more = cgiNext(cgi);
  }

  /*-----------------------------------------------------------
  *              Opening PG DB
  *-----------------------------------------------------------*/
  PGconn* conn = PQconnectdb( "host=pdc-amd01 user=kkhawa01" );
  if ( PQstatus( conn ) == CONNECTION_BAD ) {
    printf( "Bad connection.%c", 10);
  }
  char qry[512];
  sprintf(qry, "Select users.id, users.roleid, roles.role from users, roles where username='%s' and password='%s' and users.roleid=roles.roleid", username, pass);

  PGresult* theResult= PQexec(conn, qry);
  int result = ( PQntuples(theResult) > 0 ? atoi( PQgetvalue(theResult, 0, 1) ) : -1 );

  if(result < 0 ) {
    printf("<meta http-equiv=\"refresh\" content=\"0;URL=http://pdc-amd01.poly.edu/~kkhawa01/polyiudis/login.html?error=true\">");
  } else {
    printf("<html>");
    printf("<head>");
    printf("<script type='text/javascript' src='http://pdc-amd01.poly.edu/~kkhawa01/Test/incl/js/cookie_check.js'></script>");
    printf("</head>");
    printf("<body onload='window.location.href=\"../cookie.php?action=set&username=%s&roleid=%s&userid=%s&role=%s\"'>", username, PQgetvalue(theResult, 0, 1), PQgetvalue(theResult, 0, 0), PQgetvalue(theResult, 0, 2));
    printf("</body>");
    printf("</html>");
  }

  /*------------------------------------------------------
  *                 Closing DB connection and CGI Relation.
  *------------------------------------------------------*/
  PQclear(theResult);
  PQfinish( conn );

  cgiClose(cgi);

}

