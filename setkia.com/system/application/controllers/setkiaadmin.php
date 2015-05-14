<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of setkiaadmin
 *
 * @author ttroy
 */
class setkiaadmin extends Controller{
    //put your code here

    function index()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Admin Section';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //echo var_dump($profile);
        if(strcmp($profile[0]['group'], 'administrators') != 0)
        {
            redirect('users/logout');
        }

        //user is logged in as an admin so we can continue
        
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('admin/adminhome', $data, true);
        $this->load->view('template', $data);
    }

    function viewusers()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Admin Section';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //echo var_dump($profile);
        if(strcmp($profile[0]['group'], 'administrators') != 0)
        {
            redirect('users/logout');
        }

        //user is logged in as an admin so we can continue
        $this->load->library('user_admin');
        //load pagination
        $this->load->library('pagination');
        $this->config->item('base_url');
        $config['base_url'] = $this->config->item('base_url').$this->config->item('index_page').'/admin/viewusers';
        //need to change to stun settings foruser
        $config['total_rows'] = $this->user_admin->countUsers();
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

        $uri_segment = $this->uri->segment(3);
        if($uri_segment > $config['total_rows'])
            $uri_segment = $config['total_rows'] - $config['per_page'];
        else if($uri_segment < 0)
            $uri_segment = FALSE;

        $result = $this->user_admin->getUsersList($config['per_page'], $uri_segment);

        $data['userlist'] = $result;
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('admin/viewusers', $data, true);
        $this->load->view('template', $data);
    }

    function addcredit()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Admin Section';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //echo var_dump($profile);
        if(strcmp($profile[0]['group'], 'administrators') != 0)
        {
            redirect('users/logout');
        }

        //user is logged in as an admin so we can continue

        $defaultURI = array('uid');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);

        $this->load->library('user_admin');
        $this->user_admin->addCredits($uri_segments['uid'], 5);

        redirect('setkiaadmin/viewusers');
    }

    function deactivate()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Admin Section';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //echo var_dump($profile);
        if(strcmp($profile[0]['group'], 'administrators') != 0)
        {
            redirect('users/logout');
        }


        //user is logged in as an admin so we can continue

        $defaultURI = array('uid');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);


        $this->load->library('user_admin');
        $this->user_admin->deactivateUser($uri_segments['uid']);

        redirect('setkiaadmin/viewusers');
    }
}
?>
