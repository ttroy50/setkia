<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of users
 *
 * @author ttroy
 */
class Users extends Controller{

    function Users()
	{
		parent::Controller();
	}

    function index()
    {

    }

    function register()
    {
        $this->load->library('captcha');
        $this->load->model('captcha_model');

        $data['currentPageTitle'] = 'SetKia - Register';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        if($data['loggedIn'])
        {
            redirect('users/status');
        }
        $data['myCredit'] = 0;
        
	    $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_username_check');
	    $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email|xss_clean|callback_email_check');
        $this->form_validation->set_rules('phonenumber', 'Phone Number', 'trim|required|xss_clean|callback_phonenumber_check|callback_coverage_check');
	    $this->form_validation->set_rules('password', 'Password', 'required|xss_clean|callback_password_check');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
        $this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|xss_clean|callback_captcha_check');
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|xss_clean');


	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
	    {
            $expiration = time()-300;
            $this->captcha_model->deleteByExp($expiration);

            $vals = array(
                        //'word'         => 'Random word',
                        'img_path'     => './captcha/',
                        'img_url'     => base_url().'captcha/',
                        'font_path'     => './system/fonts/arial.ttf',
                        'font_path1'     => './system/fonts/courbi.ttf',
                        'font_path2'     => './system/fonts/timesbi.ttf',
                        'img_width'     => '150',
                        'img_height' => '32',
                        'expiration' => '300'
                    );

            $cap = $this->captcha->create_captcha($vals);

            $data['captchaImage'] = $cap['image'];
            
            $capdata = array(
                        'captcha_id'    => '',
                        'captcha_time'    => $cap['time'],
                        'ip_address'    => $this->input->ip_address(),
                        'word'            => $cap['word']
                        );
                        
            $this->captcha_model->insert_captcha($capdata );

            $data['headContent'] = $this->load->view('headcontent', $data, true);
	        $data['content'] = $this->load->view('register', $data, true);

            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);

	        $this->load->view('template', $data);
	    }
	    else
	    {
	        $username = $this->input->post('username');
	        $email    = $this->input->post('email');
            $phoneNo  = $this->input->post('phonenumber');
	        $password = $this->input->post('password');

	        $register = $this->redux_auth->register($username, $password, $email, $phoneNo);

	        if ($register)
	        {
	            $this->session->set_flashdata('message', '<p class="success">You have now registered. Please login.</p>');
	            redirect('users/register');
	        }
	        else
	        {
                $this->config->load('redux_auth');
                if(!$this->config->item('allow_registration'))
                {
                    $this->session->set_flashdata('message', '<p class="error">New registrations are currently disabled</p>');
                }
                else
                {
                    $this->session->set_flashdata('message', '<p class="error">Something went wrong, please try again or contact the helpdesk.</p>');
                }
                redirect('users/register');
	        }
	    }
    }

    function login()
    {

        $data['currentPageTitle'] = 'SetKia - Login';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        if($data['loggedIn'])
        {
            redirect('users/status');
        }

            
        $data['myCredit'] = 0;
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
	    $this->form_validation->set_rules('password', 'Password', 'required|xss_clean');
	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
	    {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
	        $data['content'] = $this->load->view('login', null, true);
	        $this->load->view('template', $data);
	    }
	    else
	    {
	        $username    = $this->input->post('username');
	        $password = $this->input->post('password');

	        $login = $this->redux_auth->login($username, $password);
            switch ($login) {
                case 1 :
                    //logged in
                    redirect('welcome');
                    break;
                case 2 :
                    //not activated
                    redirect('users/activate');
                    break;
                default :
                    $this->session->set_flashdata('message', '<p class="error">Username or Password incorrect.</p>');
                    $data['headContent'] = $this->load->view('headcontent', $data, true);
                    $data['content'] = $this->load->view('login', null, true);
                    $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
                    $this->load->view('template', $data);
                    break;
            }
	        
	    }
    }

    function status()
    {
        $data['currentPageTitle'] = 'SetKia - User Status';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        

        if($data['loggedIn'])
        {
            $profile = $this->redux_auth->profile();
            $data['settings'] = $this->redux_auth->settingsProfile();
            

            $data['profile'] = $profile;

            $data['headContent'] = $this->load->view('headcontent', $data, true);

            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);

            $data['content'] = $this->load->view('AccountStatus', $data, true);
	        $this->load->view('template', $data);
        }
        else
        {
            $data['myCredit'] = 0;
            redirect('users/login');
        }
    }


    function logout()
    {
        $this->redux_auth->logout();
		redirect('users/login');
    }


/**
	 * activate
	 * doesn't currently work
	 *
	 * @return void
	 * @author Mathew
	 **/
	function activate()
	{
        $data['currentPageTitle'] = 'SetKia - User Activation';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        if($data['loggedIn'])
        {
            redirect('users/status');
        }

	    $this->form_validation->set_rules('code', 'Verification Code', 'trim|required|xss_clean');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
	    {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
	        $data['content'] = $this->load->view('activate', null, true);
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
	        $this->load->view('template', $data);
	    }
	    else
	    {
            $username = $this->input->post('username');
	        $code = $this->input->post('code');
			$activate = $this->redux_auth->activate($code, $username);

			if ($activate)
			{
				$this->session->set_flashdata('message', '<p class="success">Your Account is now activated, please login.</p>');
	            redirect('users/login');
			}
			else
			{
				$this->session->set_flashdata('message', '<p class="error">Error activating account. Please confirm your activation code and username are correct</p>');
	            redirect('users/activate');
			}
	    }
	}

    function messageHistory()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Message History';

        $this->load->model('message_history_model');

        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //load pagination
        $this->load->library('pagination');
        $this->config->item('base_url');
        $config['base_url'] = $this->config->item('base_url').$this->config->item('index_page').'/users/messagehistory';
        $config['total_rows'] = $this->message_history_model->countMessageHistoryForUser($profile[0]['id']);
        $config['per_page'] = '20';
        $config['uri_segment'] = 3;
        $config['num_links'] = 2;
        $config['num_links'] = 2;
        $config['full_tag_open'] = '<p class="pageination">';
        $config['full_tag_close'] = '</p>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<span>';
        $config['first_tag_close'] = '</span>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<span>';
        $config['last_tag_close'] = '</span>';
        $config['next_link'] = '&gt;';
        $config['prev_link'] = '&lt;';
        $config['cur_tag_open'] = '<b>';
        $config['cur_tag_close'] = '</b>';

        $this->pagination->initialize($config);
        
        
        $data['msgTypes'] = $this->config->item('messageTypesInt');
        $data['messageStatusCodes'] = $this->config->item('messageStatusCodes');

        $uri_segment = $this->uri->segment(3);
        if($uri_segment > $config['total_rows'])
            $uri_segment = $config['total_rows'] - $config['per_page'];
        else if($uri_segment < 0)
            $uri_segment = FALSE;
            
        $result = $this->message_history_model->getMessageHistoryListForUser($profile[0]['id'], $config['per_page'], $uri_segment);
       
        $data['messagehistory'] = $result;

        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('messagehistory', $data, true);
        $this->load->view('template', $data);

    }

    function singleMessage()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Single Message View';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $this->load->model('message_history_model');
        
        $this->load->config('setkia_constants');
        $data['msgTypes'] = $this->config->item('messageTypesInt');
        $data['messageStatusCodes'] = $this->config->item('messageStatusCodes');

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $defaultURI = array('msgid');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);

        if(!$uri_segments['msgid'])
        {
            $data['messagehistory'] = FALSE;
        }
        else
        {
            $result = $this->message_history_model->getSingleMessageHistoryForUser($profile[0]['id'], $uri_segments['msgid']);
           
            $data['messagehistory'] = $result;
        }
        
        
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('singlemessagehistory', $data, true);
        $this->load->view('template', $data);

    }

    function updateProfile()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Update Profile';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
         $profile = $profile;
         $data['profile'] = $profile;

	    $this->form_validation->set_rules('email', 'email', 'trim|required|valid_email|callback_updateemail_check|xss_clean');
        $this->form_validation->set_rules('first_name', 'first_name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('last_name', 'last_name', 'trim|required|xss_clean');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
	    {
            if(!$this->input->post('Update'))
            {
                //first run populate the fields from profile
                $data['email'] = $profile[0]['email'];
                $data['first_name'] = $profile[0]['first_name'];
                $data['last_name'] = $profile[0]['last_name'];
                
            }
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('updateprofile', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            $email = $this->input->post('email');
            $first_name =$this->input->post('first_name');
            $last_name = $this->input->post('last_name');

            $this->redux_auth->updateProfile($email, $first_name, $last_name);
            redirect('users/status');
        }
    }
    function settings()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Account Settings';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;
        $settings = $this->redux_auth->settingsProfile();

	    $this->form_validation->set_rules('mailinglist', 'mailinglist', 'trim|required|integer|xss_clean');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
	    {
            if(!$this->input->post('Update'))
            {
                //first run populate the fields from profile
                $data['mailinglist'] = $settings[0]['mailinglist'];
            }
            
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('updatesettings', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            $mailinglist = $this->input->post('mailinglist');

            $this->redux_auth->updateSettings($mailinglist);
            redirect('users/status');
        }

    }

    function buycredits()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Buy Credits';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = '<p class="notice">It is currently not possible to buy credits. To have credits added to your account please email admin@setkia.com</p>';
        $this->load->view('template', $data);

    }
    	/**
	 * Username check
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function username_check($username)
	{
        if( $this->input->post('userhid') != '' ) {
            return false;
        }

	    $check = $this->redux_auth_model->username_check($username);

	    if ($check)
	    {
	        $this->form_validation->set_message('username_check', 'The username "'.$username.'" already exists.');
	        return false;
	    }
	    else
	    {
	        return true;
	    }
	}

	/**
	 * Email check
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function email_check($email)
	{
        log_message('error', 'Checking the email');
	    $check = $this->redux_auth_model->email_check($email);

	    if ($check)
	    {
	        $this->form_validation->set_message('email_check', 'The email "'.$email.'" already exists.');
	        return false;
	    }
	    else
	    {
	        return true;
	    }
	}

    /**
	 * Email check
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function updateemail_check($email)
	{
        log_message('error', 'Checking the email before updating');
	    $check = $this->redux_auth->updateemail_check($email);

	    if ($check)
	    {
	        $this->form_validation->set_message('updateemail_check', 'The email "'.$email.'" already exists for a different user.');
	        return false;
	    }
	    else
	    {
	        return true;
	    }
	}

    	/**
	 * Email check
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function phonenumber_check($phonenumber)
	{
	    $check = $this->redux_auth_model->phonenumber_check($phonenumber);

	    if ($check)
	    {
	        $this->form_validation->set_message('phonenumber_check', 'The Phone number "'.$phonenumber.'" already exists.');
	        return false;
	    }
	    else
	    {
	        return true;
	    }
	}

    	/**
	 * Email check
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function coverage_check($phonenumber)
	{
        $this->load->library('ClickatellSMS');
        $this->clickatellsms->startSession();
        $coverage = $this->clickatellsms->coverageCheck($phonenumber);

	    if (stripos($coverage, "Err") !== false)
	    {
	        $this->form_validation->set_message('coverage_check', ' We are unable to route messages to '.$phonenumber.'. Please make sure the number is in the format, + country code number e.g. +353861234567');
	        return false;
        }
        else
        {
            return true;
        }

	    //}
	    //else
	    //{
	        //return true;
	    //}
	}

    function captcha_check()
    {
        // Then see if a captcha exists:
        $exp=time()-300;
        $check = $this->captcha_model->validate_captcha($this->input->post('captcha'), $this->input->ip_address(), $exp);
       

        if (!$check)
        {
            $this->form_validation->set_message('captcha_check', 'The input for the captcha is incorrect');
            return FALSE;
        }else{
            return TRUE;
        }

    }

    function password_check()
    {
        if (strcmp($this->input->post('password'), $this->input->post('passconf')) != 0)
        {
            $this->form_validation->set_message('password_check', 'The supplied passwords do not match');
            return FALSE;
        }else{
            return TRUE;
        }

    }
}
?>
