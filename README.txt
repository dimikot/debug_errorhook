Debug_ErrorHook: Intercept PHP errors (including fatals) and process them (e.g. send to E-mail).
(C) Dmitry Koterov, http://dklab.ru/lib/Debug_ErrorHook/

This library allows to intercept PHP errors (including fatal!) with their 
context (file name, line number, often - stack trace, environment etc.) 
and do some work with this information. You may add any number of
hooks which will be called in case of errors.

The most common usage is to send PHP notices, warnings and even fatal 
errors to developer's E-mail. Of course this sending is wise: if the
same error already happened within N seconds, it will not be resent, 
so the mailbox is never overloaded, and sendmail program cannot crash
the whole system even if your site is very popular.

Commony errors and notices are sent to web server's logs and die there.
Nobody monitors server logs oftenly. If these errors are sent do developers'
emails, developers could react immediately and fix them. It is quite handy, 
especially just after production deployment, and it works great especially 
for fatal errors.

Usage sample
------------

Execute this in your script's bootstrap code:

  $errorsToMail = new Debug_ErrorHook_Listener();
  $errorsToMail->addNotifier(
      new Debug_ErrorHook_RemoveDupsWrapper(
          new Debug_ErrorHook_MailNotifier(
              "developer.email@example.com",
              Debug_ErrorHook_TextNotifier::LOG_ALL
          ),
          "/tmp/errors", // lock directory 
          300            // do not resend the same error within 300 seconds
      )
  );
  // Attention! When $errorsToMail is destroyed (e.g. went out of
  // scope), error hooks are removed. Hooks are active till $errorsToMail's
  // destructor execution. So you should commonly save $errorsToMail somewhere 
  // in a safe place (e.g. in $GLOBALS or in a class static property) and
  // be sure it is not destroyed until your script is finished.

Be sure to set error_reporting(E_ALL) to catch all notices. To test notice
emailing, execute something like:

  echo $non_existed_var;
  call_non_existed_function();

You will receve 2 mails at developer.email@example.com mailbox. First:

  From: developer.email@example.com
  To: developer.email@example.com
  Subject: [ERROR] E_NOTICE: Undefined variable: a at script.php on line 10
  
  //example.com/script.php?args
  E_NOTICE: Undefined variable: a 
  at script.php on line 10
  
  TRACE:
    <stack trace>
  
  SERVER:  
    <content of $_SERVER>
  
  COOKIES:
    <content of $_COOKIE>

  GET:
    <content of $_GET>

  POST:
    <content of $_POST>

  SESSION:
    <content of $_SESSION>

Second mail will contain information about your fatal error. (Unfortunately
PHP does not allow to get stack trace for fatal errors, but other information
will be mailed correctly, including file name, line number and superglobals
state.)
