#include <iostream>
#include <string>
#include <map>
#include<libpq-fe.h>
using namespace std;

/**
 **Usage:
 **./iudis login username password
 **./iudus 
**/

class PGDB {
	public:
		PGDB() {
			conn = PQconnectdb( "host=pdc-amd01 user=kkhawa01" );
			if ( PQstatus( conn ) == CONNECTION_BAD ) {
				cerr << "Bad connection." << endl;
			}
		}
		~PGDB() {
			PQfinish( conn );
		}
		bool getStatus() {return PQstatus( conn ) == CONNECTION_BAD;}
		int getRows() {return PQntuples(theResult);}
		int getCols() {return PQnfields(theResult);}
		void sql(string qry){
			theResult= PQexec(conn, qry.c_str());
		}
		string* getEntry(int row) {
			string* tuple = new string[ getCols() ];
			for (int i=0; i < getCols(); ++i)
				tuple[i] = PQgetvalue(theResult, row, i);
			return tuple;
		}
		string getEntry(int row, int col) {
			return PQgetvalue(theResult, row, col);
		}
		string* getFieldNames() {
			string* fieldsTuple = new string[ getCols() ];
			for (int i=0; i < getCols(); ++i)
				fieldsTuple[i] = PQfname(theResult, i);
			return fieldsTuple;
		}
		void reset() {
			PQclear(theResult);
		}
	private:
		PGconn* conn;
		PGresult* theResult;		
};

// Globals:
PGDB* myDB;

// Functions:
int login( string , string );

int main( int argc, char* argv[] ){
	myDB = new PGDB();
	if ( argc > 1 && strcmp( argv[1] , "login" ) == 0 ) {
		cout << login(argv[2], argv[3]) << endl;
	}
/*
	myDB->sql("select * from users");
	int rws = myDB->getRows(), cols = myDB->getCols();
	string* rowK = myDB->getFieldNames(), *rowV;
	cout << "<br />" << endl;
	for (int i = 0; i < rws; ++i) {
		rowV = myDB->getEntry(i);
		for (int j = 0; j < cols; ++j) {
			cout << rowK[j] << ": " << rowV[j] << "<br />";
		}cout << "<br />" << endl;
	}
*/
}

int login( string username, string password ) {
	string qry = "SELECT roleid FROM users WHERE username='"+ username +"' and password='"+ password +"'";
	myDB->sql(qry);
	return ( myDB->getRows() > 0 ? atoi( myDB->getEntry(0, 0).c_str() ) : -1 );
}









