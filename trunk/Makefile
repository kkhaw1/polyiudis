#
# Makefile
#

CC = gcc
FLAGS = -std=c99
YACGI = yacgi12/src

all: login query pinfo cinfo

login: $(YACGI)/yacgi.h login.c
	$(CC) $(FLAGS) -o cgi-bin/login.cgi login.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

query: $(YACGI)/yacgi.h query.c
	$(CC) $(FLAGS) -o cgi-bin/query.cgi query.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

pinfo: $(YACGI)/yacgi.h personal_info.c
	$(CC) $(FLAGS) -o cgi-bin/pinfo.cgi personal_info.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

cinfo: $(YACGI)/yacgi.h class_info.c
	$(CC) $(FLAGS) -o cgi-bin/cinfo.cgi class_info.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

clean:
	rm -f *~ cgi-bin/login.cgi cgi-bin/pinfo.cgi cgi-bin/query.cgi cgi-bin/cinfo.cgi

