/* emailhd.c:  Simple CGI Email Handler
 *
 *             This code demonstrates the main features of the YACGI
 *             C/C++ library for CGI programming.
 */

#include <stdio.h>
#include <stdlib.h>
#include "yacgi.h"

/* Where the email configuration file */
#define EMAIL_CONF_PATH "emailhd.conf"

/* Where the email program */
#define EMAIL_BINARY "/usr/bin/mail"

main(int argc, char *argv[])
{
    CGI  *cgi;
    char *name;
    char *email;
    char *subject;
    char *recipient;
    char *content;
    char recipient_conf[256];
    char returnpage[256];
    int   recipient_found;
    FILE *out;
    FILE *in;
    char buf[256];


    printf("Content-type: text/html%c%c",10,10);
    /*-----------------------------------------------------------
     *              Opening Relation
     *-----------------------------------------------------------*/
    cgi = cgiOpen();
    if(!cgi)
    {
       printf("<H3>%s</H3>%c",cgiStateMsg(),10);
       exit(1);
    }

    /*-----------------------------------------------------------
     *              Evaluating Relation
     *-----------------------------------------------------------*/
    name      = cgiValueFirst(cgi, "name");
    email     = cgiValueFirst(cgi, "email");
    subject   = cgiValueFirst(cgi, "subject");
    recipient = cgiValueFirst(cgi, "recipient");
    content   =  cgiSafeValue(cgi, "content");

    if((name == NULL) || (email == NULL) || 
      (content == NULL) || (recipient == NULL)) 
    {
    	printf("<TITLE>Email Rejected</TITLE>%c",10);
    	printf("<H1>Email Rejected</H1>%c",10);
	printf("<P>Please fill out all fields provided.<BR>%c",10);
	printf("Back up to the previous page to try again.<BR>%c",10);
        cgiClose(cgi);
        exit(1);
    }

    /*-----------------------------------------------------------
     *              Reading Email Configuration File
     *-----------------------------------------------------------*/
    in = fopen(EMAIL_CONF_PATH, "r");
    if(in==NULL)
    {
    	printf("<TITLE>Email Rejected</TITLE>%c",10);
    	printf("<H1>Email Rejected</H1>%c",10);
	printf("<P>Can not open email confiquration file.<BR>%c",10);
        cgiClose(cgi);
        exit(1);
    }
    
    recipient_found = 0;
    while(1) 
    {
        if (!fgets(recipient_conf, 80, in))  break;
        if (!fgets(returnpage, 80, in))      break;
        if (!strncmp(recipient, recipient_conf, strlen(recipient))) 
        {
	  recipient_found = 1;
	  break;
        }
    }
    fclose(in);
    if (!recipient_found) 
    {
    	printf("<TITLE>Email Rejected</TITLE>%c",10);
    	printf("<H1>Email Rejected</H1>%c",10);
	printf("<P>%s is not one of the permitted email recipients.%c",
		recipient,10);
	printf("Back up to the previous page to try again.%c",10);
       cgiClose(cgi);
       exit(1);
    }

    /*-----------------------------------------------------------
     *              Sending A Message
     *-----------------------------------------------------------*/
    sprintf(buf, "%s %s", EMAIL_BINARY, recipient);
    out = popen(buf, "w");
    fprintf(out, "Subject: %s\n", subject);
    fprintf(out, "Reply-To: %s\n\n", email);
    fprintf(out, "Supposedly-From: %s\n", name);
    fprintf(out, "[This message was sent through a www-email gateway.]\n");
    fprintf(out, "--\n");
    fprintf(out, "%s\n", content);
    pclose(out);
    
    printf("<TITLE>Message Accepted</TITLE>%c",10);
    printf("<H1>Message Accepted</H1>%c",10);
    printf("<A HREF=\"%s\">Follow this link to continue.</A>%c", returnpage,10);

    /*-----------------------------------------------------------
     *              Closing Relation
     *-----------------------------------------------------------*/
    cgiClose(cgi);
}

