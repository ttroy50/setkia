<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" :
 * <thepixeldeveloper@googlemail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Mathew Davies
 * ----------------------------------------------------------------------------
 */
 
/**
* Redux Authentication 2
*/
class redux_auth
{
	/**
	 * CodeIgniter global
	 *
	 * @var string
	 **/
	protected $ci;

	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * __construct
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		$email = $this->ci->config->item('email');
		$this->ci->load->library('email', $email);
	}
	
	/**
	 * Activate user.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($code, $identity)
	{
		return $this->ci->redux_auth_model->activate($code, $identity);
	}
	
	/**
	 * Deactivate user.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($identity)
	{
	    return $this->ci->redux_auth_model->deactivate($code);
	}
	
	/**
	 * Change password.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
        return $this->ci->redux_auth_model->change_password($identity, $old, $new);
	}

	/**
	 * forgotten password feature
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password($email)
	{
		$forgotten_password = $this->ci->redux_auth_model->forgotten_password($email);
		
		if ($forgotten_password)
		{
			// Get user information.
			$profile = $this->ci->redux_auth_model->profile($email);

			$data = array('identity'                => $profile->{$this->ci->config->item('identity')},
    			          'forgotten_password_code' => $this->ci->redux_auth_model->forgotten_password_code);
                
			$message = $this->ci->load->view($this->ci->config->item('email_templates').'forgotten_password', $data, true);
				
			$this->ci->email->clear();
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from('', '');
			$this->ci->email->to($profile->email);
			$this->ci->email->subject('Email Verification (Forgotten Password)');
			$this->ci->email->message($message);
			return $this->ci->email->send();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code)
	{
	    $identity                 = $this->ci->config->item('identity');
	    $profile                  = $this->ci->redux_auth_model->profile($code);
		$forgotten_password_complete = $this->ci->redux_auth_model->forgotten_password_complete($code);

		if ($forgotten_password_complete)
		{
			$data = array('identity'    => $profile->{$identity},
				         'new_password' => $this->ci->redux_auth_model->new_password);
            
			$message = $this->ci->load->view($this->ci->config->item('email_templates').'new_password', $data, true);
				
			$this->ci->email->clear();
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from('', '');
			$this->ci->email->to($profile->email);
			$this->ci->email->subject('New Password');
			$this->ci->email->message($message);
			return $this->ci->email->send();
		}
		else
		{
			return false;
		}
	}


	/**
	 * register
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function register($username, $password, $email, $phoneNo)
	{
	    $email_activation = $this->ci->config->item('email_activation');
        $sms_activation = $this->ci->config->item('sms_activation');
	    $email_folder     = $this->ci->config->item('email_templates');
        $allow_registration = $this->ci->config->item('allow_registration');

        if(!$allow_registration)
            return false;

        $this->ci->load->library('ClickatellSMS');
        $this->ci->clickatellsms->startSession();
        $coverage = $this->clickatellsms->coverageCheck($phonenumber);
        if (stripos($coverage, "Err") !== false)
	    {
	        return false;
        }
        else
        {
            $charge = extractChargeFromStatus($coverage);
        }

		if (!$email_activation && !$sms_activation)
		{
			return $this->ci->redux_auth_model->register($username, $password, $email, $phoneNo, $charge);
		}
		else if($email_activation)
		{
			$register = $this->ci->redux_auth_model->register($username, $password, $email, $phoneNo, $charge);
            
			if (!$register) { return false; }

			$deactivate = $this->ci->redux_auth_model->deactivate($username);

			if (!$deactivate) { return false; }

			$activation_code = $this->ci->redux_auth_model->activation_code;

			$data = array('username' => $username,
        				'password'   => $password,
        				'email'      => $email,
        				'activation' => $activation_code);
            
			$message = $this->ci->load->view($email_folder.'activation', $data, true);
            
			$this->ci->email->clear();
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from('', '');
			$this->ci->email->to($email);
			$this->ci->email->subject('Email Activation (Registration)');
			$this->ci->email->message($message);
			
			return $this->ci->email->send();
		}
        else //use sms activation
		{
			$register = $this->ci->redux_auth_model->register($username, $password, $email, $phoneNo, $charge);

			if (!$register) { return false; }

			$deactivate = $this->ci->redux_auth_model->deactivate($username, true);

			if (!$deactivate) { return false; }

			$activation_code = $this->ci->redux_auth_model->activation_code;

			$message = 'Welcome to SetKia, '.$username.'. Your activation code is '.$activation_code;

			
            $cliMsgId = $this->ci->clickatellsms->generateCliMsgID($phoneNo);
            $result = $this->ci->clickatellsms->send($phoneNo, $message, $cliMsgId);

            $this->ci->load->model('message_history_model');

            $id = $this->ci->redux_auth_model->get_id_for_identity($username);

            //add the validation message to the database to record it.
            if (stripos(substr($result, 0 , 5), 'Err') === false)
            {
                $this->ci->message_history_model->add_validation_message($this->ci->clickatellsms->get_MessageID(), $id, $phoneNo, $cliMsgId);
                //$messageid, $user_id, $udh, $wsp

            }
            else
            {
                $this->ci->message_history_model->add_validation_message($this->ci->clickatellsms->get_MessageID(), $id, $phoneNo, $cliMsgId, true, $result);
            }
            return true;
		}
	}
	
	/**
	 * login
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function login($identity, $password)
	{
		return $this->ci->redux_auth_model->login($identity, $password);
	}
	
	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
	    $identity = $this->ci->config->item('identity');
	    $this->ci->session->unset_userdata($identity);
		$this->ci->session->sess_destroy();
	}
	
	/**
	 * logged_in
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logged_in()
	{
	    $identity = $this->ci->config->item('identity');
		return ($this->ci->session->userdata($identity)) ? true : false;
	}

    public function get_credit_available()
    {
        $identity = $this->ci->config->item('identity');
		return $this->ci->redux_auth_model->get_credits_available($identity);
    }
	/**
	 * Profile
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function profile()
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    return $this->ci->redux_auth_model->profile($identity);
	}

    public function get_id()
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    return $this->ci->redux_auth_model->get_id_for_identity($identity);
	}

    public function settingsProfile()
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    return $this->ci->redux_auth_model->settingsProfile($identity);
	}

    public function updateSettings($mailinglist = null)
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    return $this->ci->redux_auth_model->updateSettings($identity, $mailinglist);
	}

    public function updateProfile($email = null, $first_name = null, $last_name = null)
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    return $this->ci->redux_auth_model->updateProfile($identity, $email, $first_name, $last_name);
	}

    public function updateemail_check($email)
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    return $this->ci->redux_auth_model->updateemail_check($identity, $email);
	}
	
}
