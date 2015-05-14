<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of message_history_model
 *
 * @author ttroy
 */
class message_history_model extends Model {

    public function __construct()
	{
		parent::__construct();
        $this->load->config('setkia_constants');
	}

    function add_XML_message($apiMsgId, $user_id, $udh, $wsp, $to, $cliMsgId = '', $messageParts = 0, $setkiaCharge = 0, $error=false, $errorStr='')
    {
        
        $types = $this->config->item('messageTypesStr');

        $this->add_UDH_Message($apiMsgId, $user_id, $udh, $wsp, $to, $types['XML'], $cliMsgId, $messageParts, $setkiaCharge, $error, $errorStr);
    }

    function add_WBXML_message($apiMsgId, $user_id, $udh, $wsp, $to, $cliMsgId = '', $messageParts = 0, $setkiaCharge = 0,$error=false, $errorStr='')
    {
        
        $types = $this->config->item('messageTypesStr');

        $this->add_UDH_Message($apiMsgId, $user_id, $udh, $wsp, $to, $types['WBXML'], $cliMsgId, $messageParts, $setkiaCharge, $error, $errorStr);
    }

    function add_UDH_message($apiMsgId, $user_id, $udh, $wsp, $to, $msgType, $cliMsgId = '', $messageParts = 0, $setkiaCharge = 0,$error=false, $errorStr='')
    {
        $now = time();
        $data = array(
               'user_id' => $user_id ,
               'apiMsgId' => $apiMsgId ,
               'udh' => $udh,
               'wsp' => $wsp,
               'timeSent' => $now,
               'error' => $error,
               'errorStr' => $errorStr,
               'to' => $to,
               'cliMsgId' => $cliMsgId,
               'msgType' => $msgType,
               'messageParts' => $messageParts,
               'setkiacharge' => $setkiaCharge
            );

        $this->db->insert('messagehistory', $data);

    }

    function add_validation_message($apiMsgId, $user_id, $to, $cliMsgId = '', $error=false, $errorStr='')
    {
        
        $types = $this->config->item('messageTypesStr');

        $now = time();
        $data = array(
               'user_id' => $user_id ,
               'apiMsgId' => $apiMsgId ,
               'udh' => '',
               'wsp' => '',
               'timeSent' => $now,
               'error' => $error,
               'errorStr' => $errorStr,
               'to' => $to,
               'cliMsgId' => $cliMsgId,
               'msgType' => $types['Validation']
            );

        $this->db->insert('messagehistory', $data);

    }
    
    function apiMsgIdExists($apiMsgId)
    {
        $query = $this->db->select('apiMsgId, id')
                    	   ->where('apiMsgId', $apiMsgId)
                           ->limit(1)
                    	   ->get('messagehistory');

        $result = $query->row();

        if ($query->num_rows() == 1)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function getMessageHistoryListForUser($user_id, $num, $offset)
    {
        $query = $this->db->select('apiMsgId, cliMsgId, id, user_id, status, error, errorStr, to, timeSent, callbacktimestamp, msgType, messageParts, setkiacharge')
                    	   ->where('user_id', $user_id)
                           ->order_by('timeSent', 'desc')
                    	   ->get('messagehistory', $num, $offset);

        //return ($i->num_rows > 0) ? $i->result_array() : false;
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }

    function countMessageHistoryForUser($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->from('messagehistory');
        return $this->db->count_all_results();
    }

    function getSingleMessageHistoryForUser($user_id, $cliMsgId)
    {
        $query = $this->db->select('apiMsgId, cliMsgId, id, user_id, status, error, errorStr, to, timeSent, callbacktimestamp, msgType, udh, wsp, messageParts, setkiacharge')
                    	   ->where('user_id', $user_id)
                           ->where('cliMsgId', $cliMsgId)
                           ->limit(1)
                    	   ->get('messagehistory');

        //return ($i->num_rows > 0) ? $i->result_array() : false;
        if ($query->num_rows() == 1)
        {
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }
    
    function cliMsgIdExists($cliMsgId)
    {
        $query = $this->db->select('cliMsgId, id')
                    	   ->where('cliMsgId', $cliMsgId)
                           ->limit(1)
                    	   ->get('messagehistory');

        $result = $query->row();

        if ($query->num_rows() == 1)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function updateAfterCallback($cliMsgId, $apiMsgId, $timestamp, $charge, $status, $useApiMsgIdasKey = true)
    {
        $data = array(
               'charge' => $charge,
               'status' => $status,
               'callbacktimestamp' => $timestamp
            );

        if($useApiMsgIdasKey)
            $this->db->where('apiMsgId', $apiMsgId);
        else
            $this->db->where('cliMsgId', $cliMsgId);
            
        $this->db->update('messagehistory', $data);
    }
    //put your code here
}
?>
