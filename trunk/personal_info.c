#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>
#include "yacgi12/src/yacgi.h"

typedef struct {
  int id, zip;
  char *username, *password, *email, *fname, *lname, *address, *city, *state, *role, *phone;
} Person;

void printPersonalInfo();

int main( int argc, char* argv[] ) {
  printPersonalInfo();
}

void printPersonalInfo() {
  Person p;
  char buf[512], *userid;
  int len = atoi( getenv("CONTENT_LENGTH") );
  fread( buf, len, 1, stdin );
  buf[len] = '\0';
  userid = buf+7;

  // Get all of the information stored for user with given userid:
  PGconn* conn = PQconnectdb( "host=pdc-amd01 user=kkhawa01" );
  if ( PQstatus( conn ) == CONNECTION_BAD ) {
    printf( "Bad connection.%c", 10);
  }
  char qry[512];
  sprintf(qry, "select * from users , roles where users.id='%s' and users.roleid=roles.roleid", userid);

  PGresult* theResult= PQexec(conn, qry);
  p.id = atoi( PQgetvalue( theResult, 0, 0) );
  p.username = PQgetvalue( theResult, 0, 1);
  p.password = PQgetvalue( theResult, 0, 2);
  p.email = PQgetvalue( theResult, 0, 3);
  p.fname = PQgetvalue( theResult, 0, 4);
  p.lname = PQgetvalue( theResult, 0, 5);
  p.address = PQgetvalue( theResult, 0, 6);
  p.city = PQgetvalue( theResult, 0, 7);
  p.state = PQgetvalue( theResult, 0, 8);
  p.zip = atoi( PQgetvalue( theResult, 0, 9) );
  p.phone = PQgetvalue( theResult, 0, 11);
  p.role = PQgetvalue( theResult, 0, 13);
  
  printf("Content-type: text/html%c%c", 10, 10);
  printf("<html>");
  printf("<body>");
  printf("<script type=\"text/javascript\" charset=\"utf-8\" src=\"js/jquery-1.3.2.min.js\"></script>");
  printf("<script type='text/javascript'>");
  printf("var person = (function(){return {");
  printf("id:'%d',", p.id);
  printf("username:'%s',", p.username);
  printf("email:'%s',", p.email);
  printf("fname:'%s',", p.fname);
  printf("lname:'%s',", p.lname);
  printf("address:'%s',", p.address);
  printf("city:'%s',", p.city);
  printf("state:'%s',", p.state);
  printf("zip:'%d',", p.zip);
  printf("phone:'%s',", p.phone);
  printf("role:'%s'", p.role);
  printf("};})();");
  printf("</script>");
  printf("</body>");
  printf("</html>");
}

