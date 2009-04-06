CC = cc
CFLAGS = -I../include 
CLIBS  = ../lib/libyacgi.a
OBJS   = emailhd.o 

# compile, and build executive
.c:
	$(CC) $(CFLAGS) -c $*.c

all:    emailhd #clean

emailhd:  $(OBJS)
	$(CC) -o emailhd $(OBJS) $(CLIBS)

clean:	
		rm -f *.o


