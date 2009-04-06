/* yacsampl.c: This code demonstrates the main features of the YACGI
 *             C/C++ library for CGI programming.
 */

#include <stdio.h>
#include <stdlib.h>
#include "yacgi.h"

main(int argc, char *argv[])
{
    CGI *cgi;
    int more;
    char *string;
    int flag;
    long i;
    double r;
    char c[31];


    printf("Content-type: text/html %c%c",10,10);
    /*-----------------------------------------------------------
     *              Opening Relation
     *-----------------------------------------------------------*/
    cgi = cgiOpen();
    if(!cgi)
    {
       printf("<H3>%s</H3>%c",cgiStateMsg(),10);
       exit(1);
    }

    printf("<CENTER><H1>YACGI Demostrating script</H1></CENTER>");
    /*-----------------------------------------------------------
     *              Scanning Relation
     *-----------------------------------------------------------*/
    printf("<CENTER><H2>Scanning Name-Value Relation</H2></CENTER>");
    printf("You submitted the following name/value pairs:<p>%c",10);
    printf("<ul>%c",10);
    more= cgiFirst(cgi);
    while(more)
    {
        printf("<li> <code>%s = %s</code>%c",cgiName(cgi),
               cgiValue(cgi),10);
        more = cgiNext(cgi);
    }
    printf("</ul>%c",10);
    /*-----------------------------------------------------------
     *              Evaluating Relation
     *-----------------------------------------------------------*/

    printf("<CENTER><H2>Evaluating Name-Value Relation</H2></CENTER>");
    printf("<p><b>%s</b><br>%c","First Name",10);
    string = cgiValueFirst(cgi, "First_Name");
    if(string) printf("<code>%s</code><br>%c",string,10);

    printf("<p><b>%s</b><br>%c","Last Name",10);
    string = cgiValueFirst(cgi, "Last_Name");
    if(string) printf("<code>%s</code><br>%c",string,10);

    printf("<p><b>%s</b><br>%c","Date of Birth",10);
    string = cgiValueFirst(cgi, "D_O_B");
    if(string) printf("<code>%s</code><br>%c",string,10);

    printf("<p><b>%s</b><br>%c","Sex",10);
    string = cgiValueFirst(cgi, "Sex");
    if(string) printf("<code>%s</code><br>%c",string,10);

    printf("<p><b>%s</b><br>%c","Degree",10);
    string = cgiValueFirst(cgi, "Degree");
    if(string) printf("<code>%s</code><br>%c",string,10);

    printf("<p><b>%s</b><br>%c","Current Occupation",10);
    string = cgiValueFirst(cgi, "Occupation");
    if(string) printf("<code>%s</code><br>%c",string,10);

    printf("<p><b>%s</b><br>%c","Duties",10);
    string = cgiValueFirst(cgi, "Duties");
    if(string) printf("<code>%s</code><br>%c",string,10);

    /*----------------------------------------------------------
     * Let us now look at multiple relations. Assume we want the
     * skills of the person.
     * We write:
     *---------------------------------------------------------*/

    printf("<p><b>%s</b><br>%c","Skills",10);
    string = cgiValueFirst(cgi, "Skills");
    while(string)
    {
        printf("<code>%s</code><br>%c",string,10);
        string = cgiValueNext(cgi, "Skills");
    }

    /*-------------------------------------------------------------
     *              Advanced Features: Input String, Integer, Real,
     *              Safe String
     *-----------------------------------------------------------*/
    printf("<CENTER><H2>Advanced Features</H2></CENTER>");

    printf("<p><b>%s</b><br>%c","String",10);
    flag = cgiValueString(cgi, "String", c, 30);
    if(flag == CGI_OK || CGI_VAL_TRUNCATED ) 
        printf("<code>%s</code><br>%c",c,10);
    else
        printf("<code>%s</code><br>%c",cgiStateMsg(),10);
               
    printf("<p><b>%s</b><br>%c","Integer",10);
    flag = cgiValueInteger(cgi, "Integer", &i, 1);
    if(flag == CGI_OK ) 
        printf("<code>%ld</code><br>%c",i,10);
    else
        printf("<code>%s</code><br>%c",cgiStateMsg(),10);

    printf("<p><b>%s</b><br>%c","Real",10);
    flag = cgiValueReal(cgi, "Real", &r, 1);
    if(flag == CGI_OK ) 
        printf("<code>%lf</code><br>%c",r,10);
    else
        printf("<code>%s</code><br>%c",cgiStateMsg(),10);
        

    printf("<p><b>%s</b><br>%c","SafeString",10);
    string = cgiSafeValue(cgi, "SafeString");
    if(string) 
        printf("<code>%s</code><br>%c",string,10);
        
    /*-----------------------------------------------------------
     *              Closing Relation
     *-----------------------------------------------------------*/
    cgiClose(cgi);
    exit(0);
}

