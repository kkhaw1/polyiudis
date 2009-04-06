/*
 * Copyright(c) 1997 Andrew Girow.
 * All rights reserved.
 *
 * All of the documentation and software included in the YACGI 1.2
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
#include "yacgi.h"

/*--------------------------------------------------------------------
 *                        Name-Value Relation
 *-------------------------------------------------------------------*/

static int    cgiStrInd(char *s,char c);
static int    cgiParsePost(CGI *cgi);
static int    cgiParseGet(CGI *cgi);
static int    cgiParse(CGI *cgi);
static char   cgiX2c(char *what);
static void   cgiUnescape(char *url);
static void   cgiPlus2space(char *str);

int cgiFirst(CGI *cgi)
{
   if(!cgi) return 0;
   cgi->pos= cgi->first;
   return 1;
}

int cgiNext(CGI *cgi)
{
   CGI_ENTRY *t;
   if(!cgi || !cgi->pos->next) return 0;
   t = cgi->pos;
   cgi->pos = t->next;
   return 1;
}

char *cgiName(CGI *cgi)
{
   CGI_ENTRY *t;
   if(!cgi) return 0;
   return cgi->pos->name;
}

char *cgiValue(CGI *cgi)
{
   CGI_ENTRY *t;
   if(!cgi) return 0;
   return cgi->pos->value;
}

char *cgiValueFirst(CGI *cgi, char *name)
{
   if(!cgi) return 0;
   cgi->pos = cgi->first;
   return cgiValueNext(cgi,name);
}

char *cgiValueNext(CGI *cgi, char *name)
{
   CGI_ENTRY *t;
   if(!cgi) return 0;
   while( cgi->pos )
   {
      t = cgi->pos;
      cgi->pos = t->next;
      if(!strcmp(t->name,name))
      {
         return t->value;
      }
   }
   return 0;
}

CGI *cgiOpen()
{
    CGI *cgi;
    cgi = (CGI *) malloc( sizeof(CGI));
    if (!cgi)
    {
       cgiStateSet(CGI_MEMORY);
       return 0;
    }

    /* Init CGI structure */
    cgi->cString = 0;
    cgi->cLength = 0;
    cgi->first = 0;   /* init entries' list */
    /* Parse request */
    if (!strcmp(cgiEnvGet("REQUEST_METHOD"), "POST"))
    {
          if (!strcmp(cgiEnvGet("CONTENT_TYPE"),
	                   "application/x-www-form-urlencoded"))
	  {
                if (cgiParsePost(cgi) != CGI_OK)
		{
		    return 0;
		}
	  }
	  else
	  {
                cgiStateSet(CGI_CONTENTTYPE);
	        return 0;
	  }
    }
    else
    {
          if (!strcmp(cgiEnvGet("REQUEST_METHOD"), "GET"))
          {

                if (cgiParseGet(cgi) != CGI_OK)
	        {
		    return 0;
	        }
          }
          else
          {
                cgiStateSet(CGI_REQUESTMETHOD);
                return 0;
          }
    }
    cgiFirst(cgi);
    return cgi;
}

int cgiParsePost(CGI *cgi)
{
    cgi->cLength = atoi(cgiEnvGet("CONTENT_LENGTH"));
    if (!cgi->cLength)
    {
        return cgiStateSet(CGI_OK);
    }
    cgi->cString = (char *) malloc(cgi->cLength+1);
    if (!cgi->cString)
    {
        return cgiStateSet(CGI_MEMORY);
    }
    if (fread(cgi->cString, 1, cgi->cLength, stdin) != cgi->cLength)
    {
        return cgiStateSet(CGI_IO);
    }
    cgi->cString[ cgi->cLength ] = '\0';
    return cgiParse(cgi);
}

int cgiParseGet(CGI *cgi)
{
    cgi->cLength = strlen(cgiEnvGet("QUERY_STRING"));
    if (!cgi->cLength)
    {
        return cgiStateSet(CGI_OK);
    }
    cgi->cString = (char *) malloc(cgi->cLength+1);
    if (!cgi->cString)
    {
        return cgiStateSet(CGI_MEMORY);
    }
    strcpy(cgi->cString,cgiEnvGet("QUERY_STRING"));
    cgi->cString[ cgi->cLength ] = '\0';
    return cgiParse(cgi);
}


int cgiStrInd(char *s, char c) 
{
    register int x;
    for(x=0;s[x];x++)
    {
        if(s[x] == c) return x;
    }    
    return -1;
}

int cgiParse(CGI *cgi)
{
    char *tokname;
    char *tokval;
    CGI_ENTRY *n;
    CGI_ENTRY *l;
    char *string;
    int pos;

    /* Init pointer */
    string= cgi->cString;
    
    /* Take the first pair*/
    pos = cgiStrInd(string,'=');
    if( pos == -1)
    {
       tokname = 0; 
    }
    else 
    {
       tokname = string;
       string[pos]='\0';
       string = &string[pos+1];
       pos =  cgiStrInd(string,'&');
       tokval = string;
       if( pos != -1)
       {
            string[pos]='\0';
            string = &string[pos+1];  
       }    
    }

    l = 0;

    while(tokname)
    {
        /* Add a new pair to the list */
        n = (CGI_ENTRY *) malloc (sizeof(CGI_ENTRY));
        if(!n)
        {
            return cgiStateSet(CGI_MEMORY);
        }
        n->name = tokname;
        n->value = tokval;
        n->next = 0;
        if(!l)
        {
           cgi->first = n;
        }
        else
        {
           l->next= n;
        }
        l = n;
        cgiPlus2space(n->value);
        cgiUnescape(n->value);

        /* Get next pair */
        pos = cgiStrInd(string,'=');
        if( pos == -1)
        {
           tokname = 0; 
        }
        else 
        { 
           tokname = string;
           string[pos]='\0';
           string = &string[pos+1];
           pos =  cgiStrInd(string,'&');
           tokval = string;
           if( pos != -1)
           {
               string[pos]='\0';
               string = &string[pos+1];  
           }    
        }
    }
    return cgiStateSet(CGI_OK);
}

void cgiClose(CGI *cgi)
{
     CGI_ENTRY *t;

     if(!cgi) return;

     /* I think that all names/values are in cgi->cString 
      * So I need free all entries and THE ONLY cString. 
      */ 
     cgi->pos = cgi->first;
     while(cgi->pos)
     {
        t= cgi->pos->next;
     	free(cgi->pos); 
	cgi->pos= t;
     }
   
     if(cgi->cString)
     {
          free(cgi->cString);
     }
     free(cgi);
}

/*--------------------------------------------------------------------
 *                        Advanced Features & Utilities
 *-------------------------------------------------------------------*/
char cgiX2c(char *what)
{
    register char digit;

    digit = (what[0] >= 'A' ? ((what[0] & 0xdf) - 'A')+10 : (what[0] - '0'));
    digit *= 16;
    digit += (what[1] >= 'A' ? ((what[1] & 0xdf) - 'A')+10 : (what[1] - '0'));
    return(digit);
}

void cgiUnescape(char *url)
{
    register int x,y;

    for(x=0,y=0;url[y];++x,++y)
    {
        if((url[x] = url[y]) == '%')
        {
            url[x] = cgiX2c(&url[y+1]);
            y+=2;
        }
    }
    url[x] = '\0';
}

void cgiPlus2space(char *str)
{
    register int x;

    for(x=0;str[x];x++) if(str[x] == '+') str[x] = ' ';
}

static char *cgiSafeStr(char *string);


char *cgiSafeStr(char *string)
{
   char *t;
   t= string;
   while(t)
   {
      t = strpbrk(t,"&;`'\"|*?~<>^()[]{}$\\");
      if(t)
      {
          memmove(t,(char *)(t+1),strlen((char *)(t+1))+1);
      }
   }
   return string;
}


char *cgiSafeValue(CGI *cgi, char *name)
{
    char *string;
    string = cgiValueFirst(cgi, name);
    if (!string)
    {
        return 0;
    }
    return cgiSafeStr(string);
}

int cgiValueString(CGI *cgi, char *name, char *result, int max)
{
    char *string;
    string = cgiValueFirst(cgi, name);
    if (!string)
    {
        strcpy(result, "");
        return cgiStateSet(CGI_VAL_NOTFOUND);
    }
    strncpy(result,string,max);
    if ( strlen(string) > max )
    {
        return cgiStateSet(CGI_VAL_TRUNCATED);
    }
    return cgiStateSet(CGI_OK);
}

int cgiValueInteger(CGI *cgi, char *name, long *result, long defval)
{
    char  *string;
    int   cnt,signOn;
    char  *tmp;

    string = cgiValueFirst(cgi, name);
    if (!string)
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_NOTFOUND);
    }
    if (!strlen(string))
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_EMPTY);
    }

    /* parse out the long string */
    tmp= string;
    while(*tmp && *tmp == ' ') tmp++;  /* skip blanks */
    if(!(*tmp) )                       /* empty string */
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_EMPTY);
    }

    cnt= 0;
    signOn= 0;
    if( *tmp == '-' || *tmp == '+' )   /* signstring */
    {
       cnt++; signOn++; tmp++;
    }

    while( *tmp && isdigit(*tmp))      /* string */
    {
       cnt++;
       tmp++;
    }

    if( !cnt || (cnt==1 && signOn) )
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_INVALID);
    }
    *result = atol(string);
    return cgiStateSet(CGI_OK);
}

int cgiValueReal(CGI *cgi, char *name, double *result, double defval)
{
    char  *string;
    int cnt,signOn,decimal;
    char  *tmp;

    string = cgiValueFirst(cgi, name);
    if (!string)
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_NOTFOUND);
    }
    if (!strlen(string))
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_EMPTY);
    }
    /* parse out the numeric string */
    tmp= string;
    while(*tmp && *tmp == ' ') tmp++;  /* skip blanks */
    if(!(*tmp) )                       /* empty string */
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_EMPTY);
    }

    cnt= 0;
    signOn= 0;
    if( *tmp == '-' || *tmp == '+' )   /* signstring */
    {
       cnt++; signOn++; tmp++;
    }

    while( *tmp && isdigit(*tmp))      /* string */
    {
       cnt++;
       tmp++;
    }

    if( *tmp == '.')                   /* separator */
    {
       cnt++; tmp++;
    }

    decimal =0;
    while( *tmp && isdigit(*tmp))      /* decimalstring */
    {
       decimal++;
       cnt++;
       tmp++;
    }

    if( !cnt || (cnt==1 && signOn) )
    {
        *result = defval;
        return cgiStateSet(CGI_VAL_INVALID);
    }
    *result = atof(string);
    return cgiStateSet(CGI_OK);
}

/*--------------------------------------------------------------------
 *                        State of Relation
 *-------------------------------------------------------------------*/
/* Private data */
static int _cgi_state;
static char *_cgi_msgs[]= 
{
    "The function successfully performed",
    "Out-of-memory error",
    "MIME content type error",
    "Request metod error",
    "I/O error",
    "Value was cut short",
    "Value was not a legal type",
    "Value contained no data",
    "No value was submitted",
};    


int cgiStateSet( int code)
{
    _cgi_state = code;
    return code;
}

int cgiStateGet(void)
{
    return _cgi_state;
}

void cgiStateClear(void)
{
    cgiStateSet(CGI_OK);
}

char *cgiStateMsg(void)
{
    if( 0 <= _cgi_state && _cgi_state < CGI_LASTERROR )
       return _cgi_msgs[_cgi_state];
    return "";      
}

/*--------------------------------------------------------------------
 *                        CGI Environment
 *-------------------------------------------------------------------*/
char *cgiEnvGet(char *var)
{
    char *s;
    s= getenv(var);
    if (!s)
    {
       return("");
    }
    return s;
}

/* EOF */

