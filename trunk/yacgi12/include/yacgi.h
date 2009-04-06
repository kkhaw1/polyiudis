/*
 * Copyright(c) 1997 Andrew Girow.
 * All rights reserved.
 *
 * All of the documentation and software included in the YACGI 1.01
 * is copyrighted by Andrew Girow.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 * 3. All advertising materials mentioning features or use of this
 *    software must display the following acknowledgement:
 *    This product includes software developed by Andrew Girow.
 * 4. The name of Andrew Girow may not be used in advertising or
 *    publicity pertaining to distribution of the software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY ANDREW GIROW "AS IS".
 * ANDREW GIROW DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE,
 * INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS, IN
 * NO EVENT WILL ANDREW GIROW BE LIABLE FOR ANY SPECIAL, INDIRECT OR
 * CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS
 * OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE
 * OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE
 * USE OR PERFORMANCE OF THIS SOFTWARE.
 *
 */

#ifndef _YACGI_H_          /* prevent multiple includes */
#define _YACGI_H_

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <ctype.h>

#if defined(__cplusplus)
#define	_BEGIN_DECL_INTERFACE_	extern "C" {
#define	_END_DECL_INTERFACE_	};
#else
#define	_BEGIN_DECL_INTERFACE_
#define	_END_DECL_INTERFACE_
#endif

/*--------------------------------------------------------------------
 *                        Name-Value Relation
 *-------------------------------------------------------------------*/
typedef struct cgiEntryStruct
{
    char *name;
    char *value;
    struct cgiEntryStruct  *next;
} CGI_ENTRY;

typedef struct
{
    char * cString;                /* Content string */
    int    cLength;                /* Content string lenght */
    CGI_ENTRY *first;              /* list of <name,value> pairs */
    CGI_ENTRY *pos;                /* */
} CGI;

_BEGIN_DECL_INTERFACE_ 
CGI  *cgiOpen();                   /* Open/Close functions */
void  cgiClose(CGI *cgi);

char *cgiValueFirst(CGI *cgi, char *name); /* Evaluates the value */
char *cgiValueNext(CGI *cgi, char *name);  /* when given the name */

int   cgiFirst(CGI *cgi);          /* Iterate functions */
int   cgiNext(CGI *cgi);

char *cgiName(CGI *cgi);           /* Returns current name and value */
char *cgiValue(CGI *cgi);
_END_DECL_INTERFACE_ 

/*--------------------------------------------------------------------
 *                        Advanced Features
 *-------------------------------------------------------------------*/
_BEGIN_DECL_INTERFACE_ 
char *cgiSafeValue(CGI *cgi, char *name);
                    /* "Escapes" the shell metacharacters */
int   cgiValueString(CGI *cgi, char *name, char *result, int max);
                    /* Retrieves the string value */
int   cgiValueInteger(CGI *cgi, char *name, long *result, long defval);
                    /* Retrieves the integer value */
int   cgiValueReal(CGI *cgi, char *name, double *result, double defval);
                    /* Retrieves the floating-point value */
_END_DECL_INTERFACE_ 

/*--------------------------------------------------------------------
 *                        State of Relation
 *-------------------------------------------------------------------*/
enum
{
    CGI_OK                =  0,    /* The function successfully performed */
    CGI_MEMORY            =  1,    /* Out-of-memory error */
    CGI_CONTENTTYPE       =  2,    /* MIME content type error */
    CGI_REQUESTMETHOD     =  3,    /* Request metod error */
    CGI_IO                =  4,    /* I/O error */
    CGI_VAL_TRUNCATED     =  5,    /* Value was cut short */
    CGI_VAL_INVALID       =  6,    /* Value was not a legal type */
    CGI_VAL_EMPTY         =  7,    /* Value contained no data */
    CGI_VAL_NOTFOUND      =  8,    /* No value was submitted */
    CGI_LASTERROR         =  9,    /**/
};

_BEGIN_DECL_INTERFACE_ 
void  cgiStateClear();             /* Resets CGI state */
int   cgiStateGet();               /* Returns CGI state */
int   cgiStateSet(int val);        /* Sets CGI State to the given value */
char *cgiStateMsg();               /* Returns CGI state string */
_END_DECL_INTERFACE_ 

/*--------------------------------------------------------------------
 *                        CGI Environment
 *-------------------------------------------------------------------*/
_BEGIN_DECL_INTERFACE_ 
char *cgiEnvGet(char *var);     /* Obtains the current value of the */
                                /* CGI environment, var */
_END_DECL_INTERFACE_ 

#endif   /* _YACGI_H */
