#
# Makefile
#

CC = gcc
FLAGS = -std=c99
YACGI = yacgi12/src

all: login query query2 pinfo cinfo clist grade vgrades vstudlist

login: $(YACGI)/yacgi.h login.c
	$(CC) $(FLAGS) -o cgi-bin/login.cgi login.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

query: $(YACGI)/yacgi.h query.c
	$(CC) $(FLAGS) -o cgi-bin/query.cgi query.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

query2: $(YACGI)/yacgi.h query2.c
	$(CC) $(FLAGS) -o cgi-bin/query2.cgi query2.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

pinfo: $(YACGI)/yacgi.h personal_info.c
	$(CC) $(FLAGS) -o cgi-bin/pinfo.cgi personal_info.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

cinfo: $(YACGI)/yacgi.h class_info.c
	$(CC) $(FLAGS) -o cgi-bin/cinfo.cgi class_info.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

clist: $(YACGI)/yacgi.h c_list.c
	$(CC) $(FLAGS) -o cgi-bin/clist.cgi c_list.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

grade: $(YACGI)/yacgi.h c_list.c
	$(CC) $(FLAGS) -o cgi-bin/grade.cgi grades.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

vgrades: $(YACGI)/yacgi.h c_list.c
	$(CC) $(FLAGS) -o cgi-bin/vgrade.cgi viewgrades.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

vstudlist: $(YACGI)/yacgi.h c_list.c
	$(CC) $(FLAGS) -o cgi-bin/vstudlist.cgi view_students.c $(YACGI)/yacgi.c -I`pg_config --includedir` -lpq

clean:
	rm -f *~ cgi-bin/login.cgi cgi-bin/pinfo.cgi cgi-bin/query.cgi cgi-bin/query2.cgi cgi-bin/cinfo.cgi cgi-bin/clist.cgi cgi-bin/grade.cgi cgi-bin/vgrade.cgi cgi-bin/vstudlist.cgi

