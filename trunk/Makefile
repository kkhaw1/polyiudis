#
# Makefile
#

DATABASE = -L /opt/oracle/lib -I /opt/oracle/rdbms/public
APPSERVER = logger.o
# db.o

all: ${APPSERVER} test

logger.o: Logger.h Logger.cpp
	g++ -o logger.o -c Logger.cpp

test:
	g++ -o test test.cpp ${APPSERVER}

clean:
	rm -f test *.o *~
