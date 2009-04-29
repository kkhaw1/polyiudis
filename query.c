#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>
#include "yacgi12/src/yacgi.h"

#include "DB.h"

void processQuery();
void runQuery( char* );

int main( int argc, char* argv[] ) {
  processQuery();
}

void processQuery() {
  char buf[512], *query;
  int len = atoi( getenv("CONTENT_LENGTH") );
  fread( buf, len, 1, stdin );
  buf[len]='\0';
  
  query = buf+6;

  int qi=0, bi=0, c1=-1, c2=-1;
  //////////////////////////////
  // url decode the post data //
  //////////////////////////////
  while( qi < len ){
	  if( query[qi] == '%' ){
		  if( (qi+1<len && isxdigit(query[qi+1])) && (qi+2<len && isxdigit(query[qi+2])) ){
			  /* Valid %HH sequence found. */
			  c1 = tolower(query[qi+1]);
			  c2 = tolower(query[qi+2]);
			  if( c1 <= '9' )
				  c1 = c1 - '0';
			  else
				  c1 = c1 - 'a' + 10;
			  if( c2 <= '9' )
				  c2 = c2 - '0';
			  else
				  c2 = c2 - 'a' + 10;
			  buf[bi++] = (char)( 16 * c1 + c2 );
			  qi+=3;
		  } else {
			  /* Invalid or incomplete sequence. */
			  buf[bi++]= '%';
			  qi++;
		  }
	  } else if( query[qi] == '+' ){
		  buf[bi++] = ' ';
		  qi++;
	  } else {
		  buf[bi++] = query[qi];
		  qi++;
	  }
  }
  buf[bi]='\0';
  query = buf;
  //////////////////////////////
  runQuery(query);
}

void runQuery( char *query ) {
  /*-----------------------------------------------------------
  *              Opening PG DB
  *-----------------------------------------------------------*/
  PGconn* conn = PQconnectdb( CONNSTRING );
  if ( PQstatus( conn ) == CONNECTION_BAD ) {
    printf( "Bad connection.%c", 10);
  }
  
  printf("Content-type:text/html%c%c", 10, 10);
  printf("query: %s", query);
  
  PGresult* theResult= PQexec(conn, query);
  /*------------------------------------------------------
  *                 Closing DB connection and CGI Relation.
  *------------------------------------------------------*/
  PQclear(theResult);
  PQfinish( conn );
}

