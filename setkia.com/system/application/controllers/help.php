<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of help
 *
 * @author ttroy
 */
class Help extends Controller{
    //put your code here

    function Help()
	{
		parent::Controller();
	}

    function advancedsip()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provisioning';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $this->load->view('help/advancedsip', $data);
    }

    function sipwizard()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provisioning';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $this->load->view('help/sipwizard', $data);
    }
}
?>
