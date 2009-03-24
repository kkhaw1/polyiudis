#
# Makefile for Poly IUDIS
#

#ORACLEDB = -L /opt/oracle/lib -I /opt/oracle/rdbms/public -locci -lclntsh

all: iudis

iudis: iudis.cpp
	g++ -o iudis iudis.cpp -I`pg_config --includedir` -lpq

clean:
	rm -f *~ *.o iudis

