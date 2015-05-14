<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clickatellcallback
 *
 * @author ttroy
 */
class Clickatellcallback extends Controller{
    //put your code here
    function Clickatellcallback()
	{
		parent::Controller();
	}

    function index()
    {
        $this->load->model('message_history_model');

        //$this->form_validation->set_rules('username', 'username', 'trim|required');
        //$this->form_validation->set_rules('password', 'password', 'trim|required');
        $this->form_validation->set_rules('api_id', 'api_id', 'trim|required');
        $this->form_validation->set_rules('apiMsgId', 'apiMsgId', 'trim|required');
        $this->form_validation->set_rules('timestamp', 'timestamp', 'trim|required');
        $this->form_validation->set_rules('to', 'to', 'trim|required');
        $this->form_validation->set_rules('from', 'from', 'trim|required');
        $this->form_validation->set_rules('charge', 'charge', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'trim|required');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            //is not from clickatell
                log_message('error', 'Attempt to use callback page with incorrect post data');

                //show_404('clickatellcallback');
        }
        else
        {
            $username = $this->input->server('PHP_AUTH_USER');
            $password = $this->input->server('PHP_AUTH_PW');
            $api_id = $this->input->post('api_id');
            $cliMsgId = $this->input->post('cliMsgId');
            $apiMsgId = $this->input->post('apiMsgId');
            $timestamp = $this->input->post('timestamp');
            $to = $this->input->post('to');
            $from = $this->input->post('from');
            $charge = $this->input->post('charge');
            $status = $this->input->post('status');
            $ip_address = $this->input->ip_address();


            $this->load->config('clickatellsms');
            if(strcmp($api_id, $this->config->item('api_id')) == 0 && strcmp($password, $this->config->item('callbackPassword')) == 0
                && strcmp($username, $this->config->item('callbackUsername')) == 0 && strcmp($ip_address, $this->config->item('clickatellIP')) == 0)
            {
                log_message('info', 'callback authenticated');
                if(!$cliMsgId)
                {
                    //if the cliMsgId doesn't exist use the apiMsgId
                    if(!$this->message_history_model->apiMsgIdExists($apiMsgId))
                    {
                        log_message('error', 'apiMsgId Does not exist');
                        log_message('error', 'apiMsgId is '.htmlspecialchars($apiMsgId));

                        //show_404('clickatellcallback');
                    }
                    else
                    {
                        $this->message_history_model->updateAfterCallback($cliMsgId, $apiMsgId, $timestamp, $charge, $status, true);
                        //show_404('clickatellcallback');
                    }
                }
                else
                {
                    // use the cliMsgId
                    if(!$this->message_history_model->cliMsgIdExists($cliMsgId))
                    {
                        log_message('error', 'cliMsgId Does not exist');
                        log_message('error', 'cliMsgId is '.htmlspecialchars($cliMsgId));

                        //show_404('clickatellcallback');
                    }
                    else
                    {
                        $this->message_history_model->updateAfterCallback($cliMsgId, $apiMsgId, $timestamp, $charge, $status, false);
                        //show_404('clickatellcallback');
                    }
                    
                }

                //is from clickatell
                //show_404('clickatellcallback');
            }
            else
            {
                //is not from clickatell
                log_message('error', 'Attempt to use callback page with invalid username, password or IP or unknown Id');
                
                
                
                log_message('error', 'apiMsgId is '.htmlspecialchars($apiMsgId));
                if(strcmp($api_id, $this->config->item('api_id')) != 0)
                {
                    log_message('error', 'api_id is '.$api_id);
                }
                if(strcmp($username, $this->config->item('callbackUsername')) != 0)
                {
                    log_message('error', 'username is '.htmlspecialchars($username));
                }
                if(strcmp($password, $this->config->item('callbackPassword')) != 0)
                {
                    log_message('error', 'password is '.htmlspecialchars($password));
                }
                if(strcmp($ip_address, $this->config->item('clickatellIP')) != 0)
                {
                    log_message('error', 'IP is '.htmlspecialchars($ip_address));
                }

                //show_404('clickatellcallback');
            }
        }
    }

}
?>
