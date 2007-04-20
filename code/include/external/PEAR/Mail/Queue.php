<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PEAR :: Mail :: Queue                                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Radek Maciaszek <chief@php.net>                             |
// |          Lorenzo Alberton <l dot alberton at quipo dot it>           |
// +----------------------------------------------------------------------+
//
// $Id: Queue.php,v 1.15 2004/07/27 08:58:03 quipo Exp $

/**
* Class for handle mail queue managment.
* Wrapper for Pear::Mail and Pear::DB.
* Could load, save and send saved mails in background
* and also backup some mails.
*
* Mail queue class put mails in a temporary
* container waiting to be fed to the MTA (Mail Transport Agent)
* and send them later (eg. every few minutes) by crontab or in other way.
*
* -------------------------------------------------------------------------
* A basic usage example:
* -------------------------------------------------------------------------
*
* $container_options = array(
*   'type'        => 'db',
*   'database'    => 'dbname',
*   'phptype'     => 'mysql',
*   'username'    => 'root',
*   'password'    => '',
*   'mail_table'  => 'mail_queue'
* );
*   //optionally, a 'dns' string can be provided instead of db parameters.
*   //look at DB::connect() method or at DB or MDB docs for details.
*   //you could also use mdb container instead db
*
* $mail_options = array(
*   'driver'   => 'smtp',
*   'host'     => 'your_smtp_server.com',
*   'port'     => 25,
*   'auth'     => false,
*   'username' => '',
*   'password' => ''
* );
*
* $mail_queue =& new Mail_Queue($container_options, $mail_options);
* *****************************************************************
* // Here the code differentiates wrt you want to add an email to the queue
* // or you want to send the emails that already are in the queue.
* *****************************************************************
* // TO ADD AN EMAIL TO THE QUEUE
* *****************************************************************
* $from             = 'user@server.com';
* $from_name        = 'admin';
* $recipient        = 'recipient@other_server.com';
* $recipient_name   = 'recipient';
* $message          = 'Test message';
* $from_params      = empty($from_name) ? '"'.$from_name.'" <'.$from.'>' : '<'.$from.'>';
* $recipient_params = empty($recipient_name) ? '"'.$recipient_name.'" <'.$recipient.'>' : '<'.$recipient.'>';
* $hdrs = array( 'From'    => $from_params,
*                'To'      => $recipient_params,
*                'Subject' => "test message body"  );
* $mime =& new Mail_mime();
* $mime->setTXTBody($message);
* $body = $mime->get();
* $hdrs = $mime->headers($hdrs);
*
* // Put message to queue
* $mail_queue->put( $from, $recipient, $hdrs, $body );
* //Also you could put this msg in more advanced mode [look at Mail_Queue docs for details]
* $seconds_to_send = 3600;
* $delete_after_send = false;
* $id_user = 7;
* $mail_queue->put( $from, $recipient, $hdrs, $body, $seconds_to_send, $delete_after_send, $id_user );
*
* *****************************************************************
* // TO SEND EMAILS IN THE QUEUE
* *****************************************************************
* // How many mails could we send each time
* $max_ammount_mails = 50;
* $mail_queue =& new Mail_Queue($container_options, $mail_options);
* $mail_queue->sendMailsInQueue($max_ammount_mails);
* *****************************************************************
*
* // for more examples look to docs directory
*
* // end usage example
* -------------------------------------------------------------------------
*
* @version $Revision: 1.15 $
* $Id: Queue.php,v 1.15 2004/07/27 08:58:03 quipo Exp $
* @author Radek Maciaszek <chief@php.net>
*/

/**
 * This is special constant define start offset for limit sql queries to
 * get mails.
 */
define('MAILQUEUE_START', 0);

/**
 * You can specify how many mails will be loaded to
 * queue else object use this constant for load all mails from db.
 */
define('MAILQUEUE_ALL', -1);

/**
 * When you put new mail to queue you could specify user id who send e-mail.
 * Else you could use system id: MAILQUEUE_SYSTEM or user unknown id: MAILQUEUE_UNKNOWN
 */
define('MAILQUEUE_SYSTEM',  -1);
define('MAILQUEUE_UNKNOWN', -2);

/**
 * This constant tells Mail_Queue how many times should try
 * to send mails again if was any errors before.
 */
define('MAILQUEUE_TRY', 25);

/**
 * MAILQUEUE_ERROR constants
 */
define('MAILQUEUE_ERROR',                   -1);
define('MAILQUEUE_ERROR_NO_DRIVER',         -2);
define('MAILQUEUE_ERROR_NO_CONTAINER',      -3);
define('MAILQUEUE_ERROR_CANNOT_INITIALIZE', -4);
define('MAILQUEUE_ERROR_NO_OPTIONS',        -5);
define('MAILQUEUE_ERROR_CANNOT_CONNECT',    -6);
define('MAILQUEUE_ERROR_QUERY_FAILED',      -7);
define('MAILQUEUE_ERROR_UNEXPECTED',        -8);
define('MAILQUEUE_ERROR_CANNOT_SEND_MAIL',  -9);

# Modified for Appleseed (added path)
require_once 'code/include/external/PEAR/PEAR.php';
require_once 'code/include/external/PEAR/Mail.php';
require_once 'code/include/external/PEAR/Mail/mime.php';


/**
 * Mail_Queue - base class for mail queue managment.
 *
 * @author   Radek Maciaszek <wodzu@tonet.pl>
 * @version  $Id: Queue.php,v 1.15 2004/07/27 08:58:03 quipo Exp $
 * @package  Mail_Queue
 * @access   public
 */
class Mail_Queue extends PEAR
{
    // {{{ Class vars

    /**
     * Mail options: smtp, mail etc. see Mail::factory
     *
     * @var array
     */
    var $mail_options;

    /**
     * Mail_Queue_Container
     *
     * @var object
     */
    var $container;

    /**
     * Reference to Pear_Mail object
     *
     * @var object
     */
    var $send_mail;

    /**
     * Pear error mode (when raiseError is called)
     * (see PEAR doc)
     *
     * @var int $_pearErrorMode
     * @access private
     */
    var $pearErrorMode = PEAR_ERROR_RETURN;

    // }}}
    // {{{ Mail_Queue

    /**
     * Mail_Queue constructor
     *
     * @param  array $container_options  Mail_Queue container options
     * @param  array $mail_options  How send mails.
     *
     * @return mixed  True on success else PEAR error class.
     *
     * @access public
     */
    function Mail_Queue($container_options, $mail_options)
    {
        $this->PEAR();
        if (isset($mail_options['pearErrorMode'])) {
            $this->pearErrorMode = $mail_options['pearErrorMode'];
            // ugly hack to propagate 'pearErrorMode'
            $container_options['pearErrorMode'] = $mail_options['pearErrorMode'];
        }

        if (!is_array($mail_options) || !isset($mail_options['driver'])) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_NO_DRIVER,
                        $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__);
        }
        $this->mail_options = $mail_options;

        if (!is_array($container_options) || !isset($container_options['type'])) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_NO_CONTAINER,
                        $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__);
        }
        $container_type = strtolower($container_options['type']);
        $container_class = 'Mail_Queue_Container_' . $container_type;
        $container_classfile = $container_type . '.php';

        include_once 'Mail/Queue/Container/' . $container_classfile;
        $this->container = new $container_class($container_options);
        if(PEAR::isError($this->container)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_CANNOT_INITIALIZE,
                        $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__);
        }
        return true;
    }

    // }}}
    // {{{ _Mail_Queue()

    /**
     * Mail_Queue desctructor
     *
     * @return void
     * @access public
     */
    function _Mail_Queue()
    {
        unset($this);
    }

    // }}}
    // {{{ factorySendMail()

    /**
     * Provides an interface for generating Mail:: objects of various
     * types see Mail::factory()
     *
     * @return void
     *
     * @access public
     */
    function factorySendMail()
    {
        $options = $this->mail_options;
        unset($options['driver']);
        $this->send_mail =& Mail::factory($this->mail_options['driver'], $options);
    }

    // }}}
    // {{{ setBufferSize()

    /**
     * Keep memory usage under control. You can set the max number
     * of mails that can be in the preload buffer at any given time.
     * It won't limit the number of mails you can send, just the
     * internal buffer size.
     *
     * @param integer $size  Optional - internal preload buffer size
     **/
    function setBufferSize($size = 10)
    {
        $this->container->buffer_size = $size;
    }


    // }}}
    // {{{ sendMailsInQueue()

   /**
     * Send mails fom queue.
     *
     * Mail_Queue::sendMailsInQueue()
     *
     * @param integer $limit     Optional - max limit mails send.
     *                           This is the max number of emails send by
     *                           this function.
     * @param integer $offset    Optional - you could load mails from $offset (by id)
     * @param integer $try       Optional - hoh many times mailqueu should try send
     *                           each mail. If mail was sent succesful it will be delete
     *                           from Mail_Queue.
     * @return mixed  True on success else MAILQUEUE_ERROR object.
     **/
    function sendMailsInQueue($limit = MAILQUEUE_ALL, $offset = MAILQUEUE_START,
                              $try = MAILQUEUE_TRY)
    {
        $this->container->setOption($limit, $offset, $try);
        while ($mail = $this->get()) {
            $this->container->countSend($mail);

            $result = $this->sendMail($mail);

            if (!PEAR::isError($result)) {
                $this->container->setAsSent($mail);
                if($mail->isDeleteAfterSend()) {
                    $this->deleteMail($mail->getId());
                }
            } else {
                PEAR::raiseError(
                    'Error in sending mail: '.$result->getMessage(),
                    MAILQUEUE_ERROR_CANNOT_SEND_MAIL, PEAR_ERROR_TRIGGER,
                    E_USER_NOTICE);
            }
        }
        return true;
    }

    // }}}
    // {{{ sendMailById()

    /**
     * Send Mail by $id identifier. (bypass Mail_Queue)
     *
     * @param integer $id  Mail identifier
     * @param  bool   $set_as_sent
     * @return bool   true on success else false
     *
     * @access public
     */
    function sendMailById($id, $set_as_sent=true)
    {
        $mail =& $this->container->getMailById($id);
        $sent = $this->sendMail($mail);
        if ($sent and $set_as_sent) {
            $this->container->setAsSent($mail);
        }
        return $sent;
    }

    // }}}
    // {{{ sendMail()

    /**
     * Send mail from MailBody object
     *
     * @param object  MailBody object
     * @return mixed  True on success else pear error class
     *
     * @access public
     */
    function sendMail($mail)
    {
        $recipient = $mail->getRecipient();
        $hdrs = $mail->getHeaders();
        $body = $mail->getBody();
        if (empty($this->send_mail)) {
            $this->factorySendMail();
        }
        return $this->send_mail->send($recipient, $hdrs, $body);
    }

    // }}}
    // {{{ get()

    /**
     * Get next mail from queue. The emails are preloaded
     * in a buffer for better performances.
     *
     * @return    object Mail_Queue_Container or error object
     * @throw     MAILQUEUE_ERROR
     * @access    public
     */
    function get()
    {
        return $this->container->get();
    }

    // }}}
    // {{{ put()

    /**
     * Put new mail in queue.
     *
     * @see Mail_Queue_Container::put()
     *
     * @param string  $time_to_send  When mail have to be send
     * @param integer $id_user  Sender id
     * @param string  $ip    Sender ip
     * @param string  $from  Sender e-mail
     * @param string  $to    Reciepient e-mail
     * @param string  $hdrs  Mail headers (in RFC)
     * @param string  $body  Mail body (in RFC)
     * @return mixed  ID of the record where this mail has been put
     *                or Mail_Queue_Error on error
     *
     * @access public
     **/
    function put($from, $to, $hdrs, $body, $sec_to_send=0, $delete_after_send=true, $id_user=MAILQUEUE_SYSTEM)
    {
        $ip = getenv('REMOTE_ADDR');
        $time_to_send = date("Y-m-d G:i:s", time() + $sec_to_send);
        return $this->container->put( $time_to_send, $id_user,
                            $ip, $from, $to, serialize($hdrs),
                            serialize($body), $delete_after_send );
    }

    // }}}
    // {{{ deleteMail()

    /**
     * Delete mail from queue database
     *
     * @param integer $id  Maila identifier
     * @return boolean
     *
     * @access private
     */
    function deleteMail($id)
    {
        return $this->container->deleteMail($id);
    }

    // }}}
    // {{{ isError()

    /**
     * Tell whether a result code from a Mail_Queue method is an error
     *
     * @param   int       $value  result code
     * @return  boolean   whether $value is an MAILQUEUE_ERROR
     * @access public
     */
    function isError($value)
    {
        return (is_object($value) && is_a($value, 'pear_error'));
    }

    // }}}
    // {{{ errorMessage()

    /**
     * Return a textual error message for a MDB error code
     *
     * @param   int     $value error code
     * @return  string  error message, or false if the error code was
     *                  not recognized
     * @access public
     */
    function errorMessage($value)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                MAILQUEUE_ERROR                    => 'unknown error',
                MAILQUEUE_ERROR_NO_DRIVER          => 'No mail driver specified',
                MAILQUEUE_ERROR_NO_CONTAINER       => 'No container specified',
                MAILQUEUE_ERROR_CANNOT_INITIALIZE  => 'Cannot initialize container',
                MAILQUEUE_ERROR_NO_OPTIONS         => 'No container options specified',
                MAILQUEUE_ERROR_CANNOT_CONNECT     => 'Cannot connect to database',
                MAILQUEUE_ERROR_QUERY_FAILED       => 'db query failed',
                MAILQUEUE_ERROR_UNEXPECTED         => 'Unexpected class',
                MAILQUEUE_ERROR_CANNOT_SEND_MAIL   => 'Cannot send email',
            );
        }

        if (Mail_Queue::isError($value)) {
            $value = $value->getCode();
        }

        return(isset($errorMessages[$value]) ?
           $errorMessages[$value] : $errorMessages[MAILQUEUE_ERROR]);
    }

    // }}}
/*
    function raiseError($msg, $code = null, $file = null, $line = null, $mode = null)
    {
        if ($file !== null) {
            $err = PEAR::raiseError(sprintf("%s [%s on line %d].", $msg, $file, $line), $code, $mode);
        } else {
            $err = PEAR::raiseError(sprintf("%s", $msg), $code, $mode);
        }
        return $err;
    }
*/
}




/**
 * Mail_Queue_Error implements a class for reporting error
 * messages.
 *
 * @package Mail_Queue
 * @category Mail
 */
class Mail_Queue_Error extends PEAR_Error
{
    // {{{ constructor

    /**
     * Mail_Queue_Error constructor.
     *
     * @param mixed   $code      Mail_Queue error code, or string with error message.
     * @param integer $mode      what 'error mode' to operate in
     * @param integer $level     what error level to use for
     *                           $mode & PEAR_ERROR_TRIGGER
     * @param string  $debuginfo additional debug info
     */
    function Mail_Queue_Error($code = MAILQUEUE_ERROR, $mode = PEAR_ERROR_RETURN,
              $level = E_USER_NOTICE,  $file=__FILE__, $line=__LINE__, $debuginfo='')
    {

        $debuginfo .= (empty($debuginfo) ? '' : ' - '). 'FILE: '.$file.', LINE: '.$line;
        if (is_int($code)) {
            $this->PEAR_Error('Mail Queue Error: ' . Mail_Queue::errorMessage($code),
                              $code, $mode, $level, $debuginfo);
        } else {
            $this->PEAR_Error('Mail Queue Error: ' . $code, MAILQUEUE_ERROR, $mode,
                              $level, $debuginfo);
        }
    }

    // }}}
}
?>
