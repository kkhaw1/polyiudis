.SUFFIXES: .c .o
CC = gcc
SHELL = /bin/sh
CFLAGS = 

# compile, then strip unnecessary symbols
.c.o:
	$(CC) -c  $(CFLAGS) $*.c
	ld -r -o $*.xxx $*.o
	mv $*.xxx $*.o

YACGI =	yacgi.o 

all:    libyacgi.a install clean

libyacgi.a: $(YACGI) 
	ar r libyacgi.a $?
	ranlib libyacgi.a
        

install:  libyacgi.a
	mv libyacgi.a ../lib

clean:
	rm -f libyacgi.a *.o


