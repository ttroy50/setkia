<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $data['profile'] = $this->redux_auth->profile();
        $data['currentPageTitle'] = 'SetKia - Provision Settings for Nokia Phones';




        $data['headContent'] = $this->load->view('headcontent', $data, true);
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }
		$data['content'] = $this->load->view('home', null, true);
        $this->load->view('template', $data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */