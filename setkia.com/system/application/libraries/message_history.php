<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of message_history
 *
 * @author ttroy
 */
class message_history {
    //put your code here

    public function __construct()
	{
		$this->ci =& get_instance();

		$this->ci->load->model('message_history_model');
        $this->ci->load->config('setkia_constants');
	}

    function add_XML_message($apiMsgId, $user_id, $udh, $wsp, $to, $cliMsgId = '', $messageParts = 0, $setkiaCharge = 0, $error=false, $errorStr='')
    {

        $types = $this->ci->config->item('messageTypesStr');

        $this->ci->message_history_model->add_UDH_Message($apiMsgId, $user_id, $udh, $wsp, $to, $types['XML'], $cliMsgId, $messageParts, $setkiaCharge, $error, $errorStr);
    }

    function add_WBXML_message($apiMsgId, $user_id, $udh, $wsp, $to, $cliMsgId = '', $messageParts = 0, $setkiaCharge = 0, $error=false, $errorStr='')
    {

        $types = $this->ci->config->item('messageTypesStr');

        $this->ci->message_history_model->add_UDH_Message($apiMsgId, $user_id, $udh, $wsp, $to, $types['WBXML'], $cliMsgId, $messageParts, $setkiaCharge, $error, $errorStr);
    }

    function add_SentSaved_message($apiMsgId, $user_id, $udh, $wsp, $to, $type, $cliMsgId = '', $messageParts = 0, $setkiaCharge = 0,$error=false, $errorStr='')
    {
        $this->ci->load->helper('array');
        log_message('error', 'type is '.$type);
        $types = $this->ci->config->item('messageTypesStr');
        
        $dbType = element($type, $types, $types['Unknown']);
        
            

        $this->ci->message_history_model->add_UDH_Message($apiMsgId, $user_id, $udh, $wsp, $to, $dbType, $cliMsgId, $messageParts, $setkiaCharge, $error, $errorStr);
    }

    function add_validation_message($apiMsgId, $user_id, $to, $cliMsgId = '', $error=false, $errorStr='')
    {
        $this->ci->message_history_model->add_validation_message($apiMsgId, $user_id, $to, $cliMsgId, $error, $errorStr);
    }

    function countStunHistoryForUser($user_id)
    {
        return $this->ci->message_history_model->countMessageHistoryForUser($user_id);

    }

}
?>
