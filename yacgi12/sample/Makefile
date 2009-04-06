.SUFFIXES: .c .o
CC=gcc
SHELL = /bin/sh
CFLAGS = -I../include 
CLIBS= ../lib/libyacgi.a
PROG=yacsampl 	

# compile, and build executive
.c:
	$(CC) $(CFLAGS) $*.c $(CLIBS)
	mv a.out $*

all:	$(PROG)
