<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of about
 *
 * @author ttroy
 */
class About extends Controller{


    function index()
    {
        $data['currentPageTitle'] = 'SetKia - About';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['content'] = $this->load->view('about/about', $data, true);
        $this->load->view('template', $data);
    }

    function privacypolicy()
    {
        $data['currentPageTitle'] = 'SetKia - Privacy Policy';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }
        $data['content'] = $this->load->view('privacypolicy', null, true);;
        
        $this->load->view('template', $data);
    }

    function faq()
    {
        $data['currentPageTitle'] = 'SetKia - FAQ';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }
        $data['content'] = $this->load->view('faq', null, true);

        $this->load->view('template', $data);
    }

    function terms()
    {
        $data['currentPageTitle'] = 'SetKia - Terms';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }
        $data['content'] = $this->load->view('about/terms', null, true);
        
        $this->load->view('template', $data);
    }

    function contact()
    {
        $data['currentPageTitle'] = 'SetKia - Contact';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }
        $data['content'] = $this->load->view('contactdetails', null, true);
        
        $this->load->view('template', $data);
    }

    function pricing()
    {
        $data['currentPageTitle'] = 'SetKia - Pricing';
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        $data['useJS'] = true;
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        if($data['loggedIn'])
        {
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', $data, true);
        }
        else
        {
            $data['sideMenu'] = $this->load->view('sidemenuhome', $data, true);
        }

        $this->load->library('pricing');
        //load pagination
        $this->load->library('pagination');
        $this->config->item('base_url');
        $config['base_url'] = $this->config->item('base_url').$this->config->item('index_page').'/about/pricing';
        //need to change to stun settings foruser
        $config['total_rows'] = $this->pricing->countAllNetworks();
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

        $result = $this->pricing->getCostList($config['per_page'], $uri_segment);

        $countries = $this->pricing->getCountryList();

        $country['0'] = 'Select';
        foreach($countries as $row)
        {
            $country[$row['id']] = $row['country'];
        }
        $data['countries'] = $country;
        
        $data['costs'] = $result;
            
        $data['content'] = $this->load->view('about/pricing', $data, true);

        $this->load->view('template', $data);
    }

    function singlecountrycost()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['profile'] = $this->redux_auth->profile();
        $data['useJS'] = true;

        $this->load->library('pricing');

        $this->form_validation->set_rules('country', 'country', 'trim|required|integer');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $this->load->view('about/singlecountrycost', $data);
        }
        else
        {

            $result = $this->pricing->getCostForCountry($this->input->post('country'));
            $data['costs'] = $result;

            $this->load->view('about/singlecountrycost', $data);
        }
    }
}
?>
