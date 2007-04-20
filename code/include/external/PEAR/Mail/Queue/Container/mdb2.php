<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PEAR :: Mail :: Queue :: MDB2 Container                              |
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
// | Author: Lorenzo Alberton <l.alberton at quipo.it>                    |
// +----------------------------------------------------------------------+
//
// $Id: mdb2.php,v 1.3 2004/08/17 14:30:53 quipo Exp $

/**
 * Storage driver for fetching mail queue data from a PEAR::MDB2 database
 *
 * This storage driver can use all databases which are supported
 * by the PEAR MDB2 abstraction layer.
 *
 * @author   Lorenzo Alberton <l.alberton at quipo.it>
 * @version  $Id: mdb2.php,v 1.3 2004/08/17 14:30:53 quipo Exp $
 * @package  Mail_Queue
 */
require_once 'MDB2.php';
require_once 'Mail/Queue/Container.php';

/**
 * Mail_Queue_Container_mdb2
 */
class Mail_Queue_Container_mdb2 extends Mail_Queue_Container
{
    // {{{ class vars

    /**
     * Reference to the current database connection.
     * @var object PEAR::MDB2 instance
     */
    var $db;

    /**
     * Table for sql database
     * @var  string
     */
    var $mail_table = 'mail_queue';

    /**
     * @var string  the name of the sequence for this table
     */
    var $sequence = null;

    // }}}
    // {{{ Mail_Queue_Container_mdb2()

    /**
     * Contructor
     *
     * Mail_Queue_Container_mMDB2:: Mail_Queue_Container_mdb2()
     *
     * @param mixed $options    An associative array of connection option.
     *
     * @access public
     */
    function Mail_Queue_Container_mdb2($options)
    {
        if (!is_array($options) || !isset($options['dsn'])) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_NO_OPTIONS,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'No dns specified!');
        }
        if (isset($options['mail_table'])) {
            $this->mail_table = $options['mail_table'];
        }
        $this->sequence = (isset($options['sequence']) ? $options['sequence'] : $this->mail_table);

        if (!empty($options['pearErrorMode'])) {
            $this->pearErrorMode = $options['pearErrorMode'];
        }
        $this->db =& MDB2::connect($options['dsn'], true);
        if (MDB2::isError($this->db)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_CANNOT_CONNECT,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2::connect failed: '. MDB2::errorMessage($this->db));
        } else {
            $this->db->setFetchMode(MDB2_FETCHMODE_ASSOC);
        }
        $this->setOption();
    }

    // }}}
    // {{{ _preload()

    /**
     * Preload mail to queue.
     *
     * @return mixed  True on success else Mail_Queue_Error object.
     * @access private
     */
    function _preload()
    {
        $query = 'SELECT * FROM ' . $this->mail_table
                .' WHERE sent_time IS NULL AND try_sent < '. $this->try
                .' AND time_to_send < '.$this->db->quote(date("Y-m-d H:i:s"), 'timestamp')
                .' ORDER BY time_to_send';
        $this->db->setLimit($this->limit, $this->offset);
        $res = $this->db->query($query);
        if (MDB2::isError($res)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2::query failed - "'.$query.'" - '.MDB2::errorMessage($res));
        }

        $this->_last_item = 0;
        $this->queue_data = array(); //reset buffer
        while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
            //var_dump($row['headers']);
            if (!is_array($row)) {
                return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                    $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                    'MDB2::query failed - "'.$query.'" - '.MDB2::errorMessage($res));
            }
            $this->queue_data[$this->_last_item] = new Mail_Queue_Body(
                $row['id'],
                $row['create_time'],
                $row['time_to_send'],
                $row['sent_time'],
                $row['id_user'],
                $row['ip'],
                $row['sender'],
                $row['recipient'],
                unserialize($row['headers']),
                unserialize($row['body']),
                $row['delete_after_send'],
                $row['try_sent']
            );
            $this->_last_item++;
        }

        return true;
    }

    // }}}
    // {{{ put()

    /**
     * Put new mail in queue and save in database.
     *
     * Mail_Queue_Container::put()
     *
     * @param string $time_to_send  When mail have to be send
     * @param integer $id_user  Sender id
     * @param string $ip  Sender ip
     * @param string $from  Sender e-mail
     * @param string $to  Reciepient e-mail
     * @param string $hdrs  Mail headers (in RFC)
     * @param string $body  Mail body (in RFC)
     * @param bool $delete_after_send  Delete or not mail from db after send
     *
     * @return mixed  ID of the record where this mail has been put
     *                or Mail_Queue_Error on error
     * @access public
     **/
    function put($time_to_send, $id_user, $ip, $sender,
                $recipient, $headers, $body, $delete_after_send=true)
    {
        $id = $this->db->nextId($this->sequence);
        if (empty($id) || MDB2::isError($id)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'Cannot create id in: '.$this->sequence);
        }
        $query = 'INSERT INTO '. $this->mail_table
                .' (id, create_time, time_to_send, id_user, ip'
                .', sender, recipient, headers, body, delete_after_send) VALUES ('
                .       $this->db->quote($id, 'integer')
                .', ' . $this->db->quote(date("Y-m-d H:i:s"), 'timestamp')
                .', ' . $this->db->quote($time_to_send, 'timestamp')
                .', ' . $this->db->quote($id_user, 'integer')
                .', ' . $this->db->quote($ip, 'text')
                .', ' . $this->db->quote($sender, 'text')
                .', ' . $this->db->quote($recipient, 'text')
                .', ' . $this->db->quote($headers, 'text')   //clob
                .', ' . $this->db->quote($body, 'text')      //clob
                .', ' . ($delete_after_send ? 1 : 0)
                .')';
        $res = $this->db->query($query);
        if (MDB2::isError($res)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2::query failed - "'.$query.'" - '.MDB2::errorMessage($res));
        }
        return $id;
    }

    // }}}
    // {{{ countSend()

    /**
     * Check how many times mail was sent.
     *
     * @param object   Mail_Queue_Body
     * @return mixed  Integer or Mail_Queue_Error class if error.
     * @access public
     */
    function countSend($mail)
    {
        if (!is_object($mail) || !is_a($mail, 'mail_queue_body')) {
            return new Mail_Queue_Error('Expected: Mail_Queue_Body class',
                __FILE__, __LINE__);
        }
        $count = $mail->_try();
        $query = 'UPDATE ' . $this->mail_table
                .' SET try_sent = ' . $this->db->quote($count, 'integer')
                .' WHERE id = '     . $this->db->quote($mail->getId(), 'integer');
        $res = $this->db->query($query);
        if (MDB2::isError($res)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2::query failed - "'.$query.'" - '.MDB2::errorMessage($res));
        }
        return $count;
    }

    // }}}
    // {{{ setAsSent()

    /**
     * Set mail as already sent.
     *
     * @param object Mail_Queue_Body object
     * @return bool
     * @access public
     */
    function setAsSent($mail)
    {
        if (!is_object($mail) || !is_a($mail, 'mail_queue_body')) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_UNEXPECTED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
               'Expected: Mail_Queue_Body class');
        }
        $query = 'UPDATE ' . $this->mail_table
                .' SET sent_time = '.$this->db->quote(date("Y-m-d H:i:s"), 'timestamp')
                .' WHERE id = '. $this->db->quote($mail->getId(), 'integer');

        $res = $this->db->query($query);
        if (MDB2::isError($res)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2::query failed - "'.$query.'" - '.MDB2::errorMessage($res));
        }
        return true;
    }

    // }}}
    // {{{ getMailById()

    /**
     * Return mail by id $id (bypass mail_queue)
     *
     * @param integer $id  Mail ID
     * @return mixed  Mail object or false on error.
     * @access public
     */
    function getMailById($id)
    {
        $query = 'SELECT * FROM ' . $this->mail_table
                .' WHERE id = '   . $this->db->quote($id, 'text');
        $row = $this->db->queryRow($query, null, MDB2_FETCHMODE_ASSOC);
        if (MDB2::isError($row) || !is_array($row)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2: query failed - "'.$query.'" - '.$row->getMessage());
        }
        return new Mail_Queue_Body(
            $row['id'],
            $row['create_time'],
            $row['time_to_send'],
            $row['sent_time'],
            $row['id_user'],
            $row['ip'],
            $row['sender'],
            $row['recipient'],
            unserialize($row['headers']),
            unserialize($row['body']),
            $row['delete_after_send'],
            $row['try_sent']
        );
    }

    // }}}
    // {{{ deleteMail()

    /**
     * Remove from queue mail with $id identifier.
     *
     * @param integer $id  Mail ID
     * @return bool  True on success else Mail_Queue_Error class
     *
     * @access public
     */
    function deleteMail($id)
    {
        $query = 'DELETE FROM ' . $this->mail_table
                .' WHERE id = ' . $this->db->quote($id, 'text');
        $res = $this->db->query($query);

        if (MDB2::isError($res)) {
            return new Mail_Queue_Error(MAILQUEUE_ERROR_QUERY_FAILED,
                $this->pearErrorMode, E_USER_ERROR, __FILE__, __LINE__,
                'MDB2::query failed - "'.$query.'" - '.MDB2::errorMessage($res));
        }
        return true;
    }

    // }}}
}
?>