<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of settings
 *
 * @author ttroy
 */
class Settings extends Controller{

    function Settings()
	{
		parent::Controller();
	}

    function index()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provisioning';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('settings/settingshome', $data, true);
        $this->load->view('template', $data);
    }

    function provisionXML()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provision XML';
         

        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;
       

        $this->form_validation->set_rules('xml', 'xml', 'trim|required|callback_doctype_check');
        $this->form_validation->set_rules('pintype', 'pintype', 'required|integer');
        $this->form_validation->set_rules('pin', 'pin', 'trim|integer|callback_pin_check');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('settings/provisionxml', null, true);
            $this->load->view('template', $data);
        }
        else
        {
            if($profile[0]['sms_available'] <= 0)
            {
                $data['error'] = '<p class="error">You have insufficent credit to send this message</p>';
	            $data['headContent'] = $this->load->view('headcontent', $data, true);
                $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
                $data['content'] = $this->load->view('settings/provisionxml', $data, true);
                $this->load->view('template', $data);
                return;
            }

            $settings = $this->redux_auth->settingsProfile();

            //$xml = $_POST['xml'];
            $xml = $this->input->post('xml');
            $this->load->library('WBXMLEncoder', $xml);

            $libwbxml = true;
            $encoded = false;
            if($settings[0]['xmlparser'] == 0)
            {
                log_message('error', 'using libwbxlm');
                $encoded = $this->wbxmlencoder->encode_libwbxml($xml);
            }
            else
            {
                log_message('error', 'using myxml parser');
                $encoded = $this->wbxmlencoder->encode($xml);
            }

            if($encoded)
            {
                $wbxml = $this->wbxmlencoder->getWBXMLForURL();
                $pin = $this->input->post('pin');
                $pinType = $this->input->post('pintype');

                $this->load->helper('provisioning_helper');
                $wspData = WSP_Data($pinType, 'push', 'wap.connectivity-wbxml');

                $udhData = array(
                        'destPort' => '0B84',
                        'srcPort' => '23F0',
                        'multiSMS' => false,
                        'portType' => '1'
                        );

                $this->load->library('UDHGenerator', $udhData);
                $this->load->library('WSPGenerator', $wspData);

                $wsp = $this->wspgenerator->generateWSP($this->wbxmlencoder->getWBXML(), $pin);
                $udh = $this->udhgenerator->generateUDH();

                $this->load->library('ClickatellSMS');
                $this->clickatellsms->startSession();

                $cliMsgId = $this->clickatellsms->generateCliMsgID($profile[0]['phonenumber']);
                $result = $this->clickatellsms->sendUDH($profile[0]['phonenumber'], $wsp.$wbxml, $udh, $cliMsgId, $profile[0]['sms_available'], $profile[0]['charge_per_part']);

                $this->load->model('message_history_model');

                if (stripos(substr($result, 0 , 5), 'Err') === false)
                {
                    $charge = $profile[0]['charge_per_part'] * $this->clickatellsms->getMessageParts();
                    $this->message_history_model->add_XML_message($this->clickatellsms->get_MessageID(), $profile[0]['id'], $udh, $wsp, $profile[0]['phonenumber'], $cliMsgId,
                        $this->clickatellsms->getMessageParts(), $charge );

                    $this->redux_auth_model->smssent($profile[0]['username'], $this->clickatellsms->getMessageParts());
                    $this->session->set_flashdata('message', '<p class="success">Message Sent</p>');
                    $this->session->set_flashdata('xml', $xml);
                    $this->session->set_flashdata('cliMsgId', $cliMsgId);
                    redirect('settings/success');
                    
                }
                else
                {
                    $this->message_history_model->add_XML_message($this->clickatellsms->get_MessageID(), $profile[0]['id'], $udh, $wsp,$profile[0]['phonenumber'], $cliMsgId, 0, 0, true, $result);
                    
                    $data['error'] = '<p class="error">Error Sending message. Error is '.$result.'</p>';
                }
            }
            else
            {
                $error = $this->wbxmlencoder->getError();

                $data['error'] = '<p class="error">Error encoding to WBXML. '.$error.'</p>';
	            
            }

            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('settings/provisionxml', $data, true);
            $this->load->view('template', $data);
            
        }
    }

    function provisionWBXML()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provision WBXML';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $config['upload_path'] = './uploads/wbxml';
		$config['allowed_types'] = 'wbxml';
		$config['max_size']	= '0';
        $config['encrypt_name']	= TRUE;
        $config['remove_spaces'] = TRUE;


		$this->load->library('upload', $config);

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;


        $this->form_validation->set_rules('pintype', 'pintype', 'required|integer');
        $this->form_validation->set_rules('pin', 'pin', 'trim|integer|callback_pin_check');


	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('settings/provisionwbxml', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            if($profile[0]['sms_available'] <= 0)
            {
                $data['error'] = '<p class="error">You have insufficent credit to send this message</p>';
	            $data['headContent'] = $this->load->view('headcontent', $data, true);
                $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
                $data['content'] = $this->load->view('settings/provisionwbxml', $data, true);
                $this->load->view('template', $data);
                return;
            }
            
            if ( !$this->upload->do_upload('wbxml'))
            {
                $error = array('error' => $this->upload->display_errors('<p class="error">', '</p>'));
               // echo strtolower(preg_replace("/^(.+?);.*$/", "\\1", $_FILES['wbxml']['type']));
                $data['headContent'] = $this->load->view('headcontent', $data, true);
                $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
                $data['content'] = $this->load->view('settings/provisionwbxml', $error, true);
                $this->load->view('template', $data);
                return;
            }
            else
            {
                $upload_data = $this->upload->data();
            }

            
            $this->load->helper('file');

            if(strcasecmp($upload_data['file_ext'], '.wbxml') == 0)
            {
                $wbxmlfile = read_file($upload_data['full_path']);

                if($wbxmlfile == false)
                {
                    $data['error'] = '<p class="error">Error Uploading file</p>';
                    $data['headContent'] = $this->load->view('headcontent', $data, true);
                    $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
                    $data['content'] = $this->load->view('settings/provisionwbxml', $data, true);
                    $this->load->view('template', $data);
                    return;
                }
                unlink($upload_data['full_path']);
                $wbxml = $this->_ordstr($wbxmlfile);

                $pin = $this->input->post('pin');
                $pinType = $this->input->post('pintype');

                $this->load->helper('provisioning_helper');
                $wspData = WSP_Data($pinType, 'push', 'wap.connectivity-wbxml');

                //default pin
                if($pin === false)
                {
                    $pin = 1234;
                }

                $udhData = array(
                        'destPort' => '0B84',
                        'srcPort' => '23F0',
                        'multiSMS' => false,
                        'portType' => '1'
                        );

                $this->load->library('UDHGenerator', $udhData);
                $this->load->library('WSPGenerator', $wspData);

                $wsp = $this->wspgenerator->generateWSP($wbxmlfile, $pin);
                $smslength = strlen($wsp.$wbxml);


                $this->load->library('ClickatellSMS');
                $this->clickatellsms->startSession();

                $udh = $this->udhgenerator->generateUDH();

                $cliMsgId = $this->clickatellsms->generateCliMsgID($profile[0]['phonenumber']);
                $result = $this->clickatellsms->sendUDH($profile[0]['phonenumber'], $wsp.$wbxml, $udh, $cliMsgId, $profile[0]['sms_available'], $profile[0]['charge_per_part']);
                $this->load->model('message_history_model');
                
                if (stripos(substr($result, 0 , 5), 'Err') === false)
                {
                    $charge = $profile[0]['charge_per_part'] * $this->clickatellsms->getMessageParts();
                    $this->message_history_model->add_WBXML_message($this->clickatellsms->get_MessageID(), $profile[0]['id'], $udh, $wsp, $profile[0]['phonenumber'], $cliMsgId,
                        $this->clickatellsms->getMessageParts(), $charge );
                    //$messageid, $user_id, $udh, $wsp
                    $this->redux_auth_model->smssent($profile[0]['username'], $this->clickatellsms->getMessageParts());
                    $this->session->set_flashdata('message', '<p class="success">Message Sent</p>');
                    $this->session->set_flashdata('xml', '');
                    $this->session->set_flashdata('cliMsgId', $cliMsgId);
                    redirect('settings/success');

                }
                else
                {
                    $this->message_history_model->add_WBXML_message($this->clickatellsms->get_MessageID(), $profile[0]['id'], $udh, $wsp,$profile[0]['phonenumber'], $cliMsgId, 0, 0, true, $result);
                    $this->session->set_flashdata('message', '<p class="error">Error Sending message. Error is '.$result.'</p>');
                }
            }
            else
            {
                //$this->form_validation->set_message('wbxml', 'error');
                $this->session->set_flashdata('message', '<p class="error">Error uploading file. It appears to be the wrong type</p>');
            }

            redirect('settings/provisionwbxml');

        }
    }

    function success()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provision XML Successful';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;
        
        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('messagesent', $data, true);
        $this->load->view('template', $data);
    }

    function saved()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provision XML Successful';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;
        $data['settings_id'] = $this->session->flashdata('settings_id');
        $data['settings_type'] = $this->session->flashdata('settings_type');
        
        $this->session->keep_flashdata('settings_id');
        $this->session->keep_flashdata('settings_type');

        $this->load->library('sip_settings');
        $settings = $this->sip_settings->get_settings($profile[0]['id'], $data['settings_id'], $data['settings_type']);

        if(!$settings)
        {
            //unable to retrieve settings
            $data['xml'] = '';
        }
        else
        {
            if(strcmp($data['settings_type'], 'stun') == 0)
            {
                $data['xml'] = $this->sip_settings->generateSTUN_XML($settings[0]);
            }
            else if(strcmp($data['settings_type'], 'sip') == 0)
            {
                $data['xml'] = $this->sip_settings->generateSIP_XML($settings[0]);
            }
            else if(strcmp($data['settings_type'], 'voip') == 0)
            {
                $data['xml'] = $this->sip_settings->generateVoIP_XML($settings[0]);
            }
            else
            {
                $data['xml'] = '';
            }
        }


        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('settingssaved', $data, true);
        $this->load->view('template', $data);
    }

    function viewsinglesavedxml()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Provision XML Successful';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;
        $this->load->library('sip_settings');
        $defaultURI = array('sid', 'stype');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);


        log_message('error', 'settings_id is '.$uri_segments['sid']);
        log_message('error', 'settings_type is '.$uri_segments['stype']);
        $this->load->library('sip_settings');
        $settings = $this->sip_settings->get_settings($profile[0]['id'], $uri_segments['sid'], $uri_segments['stype']);

        if(!$settings)
        {
            log_message('error', 'unable to load settings from database');
            //unable to retrieve settings
            $data['xml'] = '';
        }
        else
        {
            if(strcmp($uri_segments['stype'], 'stun') == 0)
            {
                $data['xml'] = $this->sip_settings->generateSTUN_XML($settings[0]);
            }
            else if(strcmp($uri_segments['stype'], 'sip') == 0)
            {
                $data['xml'] = $this->sip_settings->generateSIP_XML($settings[0]);
            }
            else if(strcmp($uri_segments['stype'], 'voip') == 0)
            {
                $data['xml'] = $this->sip_settings->generateVoIP_XML($settings[0]);
            }
            else
            {
                $data['xml'] = '';
            }
        }

       

        $data['content'] = $this->load->view('settings/singlesavedxml', $data);

    }

    function voipsettings()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - VoIP Settings';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $defaultURI = array('sid');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);
        $data['defaultsipprofile'] = $uri_segments['sid'];
        
        $this->load->library('sip_settings');
        $data['sipHistory'] = $this->sip_settings->getSipHistoryListForUser($profile[0]['id'], 50, 0);

        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('providerid', 'providerid', 'trim|required');
        $this->form_validation->set_rules('to-appref', 'to-appref', 'trim|required|integer|callback_to_appref');
        $this->form_validation->set_rules('otherto-appref', 'otherto-appref', 'trim|callback_otherto_appref');
        $this->form_validation->set_rules('smport', 'smport', 'trim|required|integer');
        $this->form_validation->set_rules('emport', 'emport', 'trim|required|integer');
        $this->form_validation->set_rules('mediaqos', 'mediaqos', 'trim|required|integer');
        $this->form_validation->set_rules('dtmfib', 'dtmfib', 'trim|required|integer');
        $this->form_validation->set_rules('dtmfob', 'dtmfob', 'trim|required|integer');
        $this->form_validation->set_rules('voipoverwcdma', 'voipoverwcdma', 'trim|required|integer');
        $this->form_validation->set_rules('rtcp', 'rtcp', 'trim|required|integer');
        $this->form_validation->set_rules('uahtermtype', 'uahtermtype', 'trim|required|integer');
        $this->form_validation->set_rules('uahmac', 'uahmac', 'trim|required|integer');
        $this->form_validation->set_rules('uahfree', 'uahfree', 'trim');
        $this->form_validation->set_rules('securecall', 'securecall', 'trim|required|integer');
        $this->form_validation->set_rules('voipdigits', 'voipdigits', 'trim|required|integer');
        $this->form_validation->set_rules('igndom', 'igndom', 'trim|required|integer');
        $this->form_validation->set_rules('codec1', 'codec1', 'trim|required|integer|callback_codec_check');
        $this->form_validation->set_rules('codec2', 'codec2', 'trim|required|integer');
        $this->form_validation->set_rules('codec3', 'codec3', 'trim|required|integer');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('settings/voipsettings', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            $input['name'] = $this->input->post('name');
            $input['provider-id'] = $this->input->post('providerid');
            $input['to-appref'] = $this->input->post('to-appref');
            $input['otherto-appref'] = $this->input->post('otherto-appref');
            $input['smport'] = $this->input->post('smport');
            $input['emport'] = $this->input->post('emport');
            $input['mediaqos'] = $this->input->post('mediaqos');
            $input['dtmfib'] = $this->input->post('dtmfib');
            $input['dtmfob'] = $this->input->post('dtmfob');
            $input['voipoverwcdma'] = $this->input->post('voipoverwcdma');
            $input['rtcp'] = $this->input->post('rtcp');
            $input['uahtermtype'] = $this->input->post('uahtermtype');
            $input['uahmac'] = $this->input->post('uahmac');
            $input['uahfree'] = $this->input->post('uahfree');
            $input['securecall'] = $this->input->post('securecall');
            $input['voipdigits'] = $this->input->post('voipdigits');
            $input['igndom'] = $this->input->post('igndom');
            $input['codec1'] = $this->input->post('codec1');
            $input['codec2'] = $this->input->post('codec2');
            $input['codec3'] = $this->input->post('codec3');

            $this->load->library('sip_settings');

            $insertId = $this->sip_settings->save_voip($input, $profile[0]['id']);

            if(!$insertId)
                $this->session->set_flashdata('message', '<p class="success">Settings Not Saved</p>');
            else
                $this->session->set_flashdata('message', '<p class="success">Settings Saved</p>');

            $this->session->set_flashdata('settings_type', 'voip');
            $this->session->set_flashdata('settings_id', $insertId);


            redirect('settings/saved');
        }
        

        
    }

    function sendwithvoip()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Send Settings';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $defaultURI = array('sid', 'stype');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);
        
        $this->load->library('sip_settings');
        $data['settings'] = $this->sip_settings->get_voip_for_sip($profile[0]['id'], $uri_segments['sid'], $uri_segments['stype']);

        
        $data['sid'] = $uri_segments['sid'];
        $data['stype'] = $uri_segments['stype'];

        $this->load->view('settings/sendwithvoip', $data);

    }


    function sendsaved()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Send Settings';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;



        $defaultURI = array('sid', 'stype', 'vid', 'votype');
        $uri_segments = $this->uri->uri_to_assoc(3, $defaultURI);

        $this->load->library('sip_settings');
        $settings = $this->sip_settings->get_settings($profile[0]['id'], $uri_segments['sid'], $uri_segments['stype']);

        
        //$second_settings = false;
        if($uri_segments['votype'] !== false)
        {
            $second_settings = $this->sip_settings->get_settings($profile[0]['id'], $uri_segments['vid'], element('votype', $uri_segments));
        }

        if(!$settings)
        {
            log_message('error', 'unable to load settings from database');
            //unable to retrieve settings
            $data['xml'] = '';
        }
        else
        {
            if(strcmp($uri_segments['stype'], 'stun') == 0)
            {
                $data['xml'] = $this->sip_settings->generateSTUN_XML($settings[0]);
            }
            else if(strcmp($uri_segments['stype'], 'sip') == 0)
            {

                if($uri_segments['vid'] === false)
                {
                    $data['xml'] = $this->sip_settings->generateSIP_XML($settings[0]);
                    
                }
                else
                {
                    if(!$second_settings)
                    {
                        $data['xml'] = $this->sip_settings->generateSIP_XML($settings[0]);
                    }
                    else
                    {
                        $data['xml'] = $this->sip_settings->generateSIP_VoIP_XML($settings[0], $second_settings[0], $uri_segments['vid']);
                    }
                }
            }
            else if(strcmp($uri_segments['stype'], 'voip') == 0)
            {
                $data['xml'] = $this->sip_settings->generateVoIP_XML($settings[0]);
            }
            else
            {
                $data['xml'] = '';
            }
        }
        $data['sid'] = $uri_segments['sid'];
        $data['stype'] = $uri_segments['stype'];

        $this->form_validation->set_rules('xml', 'xml', 'trim|required|callback_doctype_check');
        $this->form_validation->set_rules('pintype', 'pintype', 'required|integer');
        $this->form_validation->set_rules('pin', 'pin', 'trim|integer|callback_pin_check');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('sendsaved', $data, true);
            $this->load->view('template', $data);
        }
        else
        {

            if($profile[0]['sms_available'] <= 0)
            {
                $data['error'] =  '<p class="error">You have insufficent credit to send this message</p>';
	            $data['headContent'] = $this->load->view('headcontent', $data, true);
                $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
                $data['content'] = $this->load->view('provisionxml', $data, true);
                $this->load->view('template', $data);
                return;
            }

            $settings = $this->redux_auth->settingsProfile();

            //$xml = $_POST['xml'];
            $xml = $this->input->post('xml');
            $this->load->library('WBXMLEncoder', $xml);

            $libwbxml = true;
            $encoded = false;
            if($settings[0]['xmlparser'] == 0)
            {
                log_message('error', 'using libwbxlm');
                $encoded = $this->wbxmlencoder->encode_libwbxml($xml);
            }
            else
            {
                og_message('error', 'using myxml parser');
                $encoded = $this->wbxmlencoder->encode($xml);
            }

            if($encoded)
            {
                $pinType = $this->input->post('pintype');
                $pin = $this->input->post('pin');
                $wbxml = $this->wbxmlencoder->getWBXMLForURL();

                $this->load->helper('provisioning_helper');
                $wspData = WSP_Data($pinType, 'push', 'wap.connectivity-wbxml');

                $udhData = array(
                        'destPort' => '0B84',
                        'srcPort' => '23F0',
                        'multiSMS' => false,
                        'portType' => '1'
                        );

                $this->load->library('UDHGenerator', $udhData);
                $this->load->library('WSPGenerator', $wspData);

                $wsp = $this->wspgenerator->generateWSP($this->wbxmlencoder->getWBXML(), $pin);
                $udh = $this->udhgenerator->generateUDH();

                $this->load->library('ClickatellSMS');
                $this->clickatellsms->startSession();

                $cliMsgId = $this->clickatellsms->generateCliMsgID($profile[0]['phonenumber']);
                $result = $this->clickatellsms->sendUDH($profile[0]['phonenumber'], $wsp.$wbxml, $udh, $cliMsgId, $profile[0]['sms_available'], $profile[0]['charge_per_part']);

                //$this->load->model('message_history_model');
                $this->load->library('message_history');
                if (stripos(substr($result, 0 , 5), 'Err') === false)
                {
                    $charge = $profile[0]['charge_per_part'] * $this->clickatellsms->getMessageParts();
                    $this->message_history->add_SentSaved_message($this->clickatellsms->get_MessageID(), $profile[0]['id'], $udh, $wsp, $profile[0]['phonenumber'],$uri_segments['stype'], $cliMsgId,
                        $this->clickatellsms->getMessageParts(), $charge );

                    $this->redux_auth_model->smssent($profile[0]['username'], $this->clickatellsms->getMessageParts());
                    $this->session->set_flashdata('message', '<p class="success">Message Sent</p>');
                    $this->session->set_flashdata('xml', $xml);
                    $this->session->set_flashdata('cliMsgId', $cliMsgId);
                    redirect('settings/success');
                    
                }
                else
                {
                    $this->message_history->add_SentSaved_message($this->clickatellsms->get_MessageID(), $profile[0]['id'], $udh, $wsp,$profile[0]['phonenumber'], $uri_segments['stype'],$cliMsgId, 0, 0, true, $result);
                    
                    $data['error'] =  '<p class="error">Error Sending message. Error is '.$result.'</p>';
                }
            }
            else
            {
                $error = $this->wbxmlencoder->getError();

                $data['error'] = '<p class="error">Error encoding to WBXML. '.$error.'</p>';
	            
            }

            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('sendsaved', $data, true);
            $this->load->view('template', $data);
        }
    }

    function sipWizard()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - SIP Settings Wizard';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('appref', 'appref', 'trim|required|callback_appref_check');
        $this->form_validation->set_rules('username', 'username', 'trim|required');
        $this->form_validation->set_rules('password', 'password', 'trim|required');
        $this->form_validation->set_rules('registration', 'registration', 'trim|required|integer');
        $this->form_validation->set_rules('proxy', 'proxy', 'trim|required');
        $this->form_validation->set_rules('domain', 'domain', 'trim|required');
        $this->form_validation->set_rules('realm', 'realm', 'trim|required');
        $this->form_validation->set_rules('protocol', 'protocol', 'trim|required');
        $this->form_validation->set_rules('port', 'port', 'trim|required|integer');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('settings/sipsettingswizard', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            $this->load->library('sip_settings');

            $inputs['name'] = $this->input->post('name');
            $inputs['appref'] = $this->input->post('appref');
            $inputs['username'] = $this->input->post('username');
            $inputs['password'] = $this->input->post('password');
            $inputs['registraton'] = $this->input->post('registration');
            $inputs['proxy'] = $this->input->post('proxy');
            $inputs['domain'] = $this->input->post('domain');
            $inputs['realm'] = $this->input->post('realm');
            $inputs['protocol'] = $this->input->post('protocol');
            $inputs['port'] = $this->input->post('port');

            $advSIP = $this->sip_settings->wizardToAdvanced($inputs);
            //$xml = $this->sip_settings->generateSIP_XML();

            $insertId = $this->sip_settings->save_sip($advSIP, $profile[0]['id']);

            if(!$insertId)
                $this->session->set_flashdata('message', '<p class="success">Settings Not Saved</p>');
            else
                $this->session->set_flashdata('message', '<p class="success">Settings Saved</p>');
            $this->session->set_flashdata('settings_type', 'sip');
            $this->session->set_flashdata('settings_id', $insertId);


            redirect('settings/saved');
        }
        

    }

    function sipAdvanced()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Advanced SIP Settings';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('appref', 'appref', 'trim|required|callback_appref_check');
        $this->form_validation->set_rules('ptype', 'Service Provider', 'trim|required');
        $this->form_validation->set_rules('puid', 'Public Username', 'trim|required');
        $this->form_validation->set_rules('registration', 'registration', 'trim|required|integer');
        $this->form_validation->set_rules('appaddr_addr', 'Proxy Server', 'trim|required');
        $this->form_validation->set_rules('appauth_aauthdata', 'Proxy Realm', 'trim|required');
        $this->form_validation->set_rules('appauth_aauthname', 'Proxy Username', 'trim|required');
        $this->form_validation->set_rules('appauth_aauthsecret', 'Proxy Password', 'trim|required');
        $this->form_validation->set_rules('protocol', 'Proxy Protocol', 'trim|required');
        $this->form_validation->set_rules('appaddr_lr', 'Use Loose Routing', 'trim|required');
        $this->form_validation->set_rules('appaddr_port_portnbr', 'Proxy Port Number', 'trim|required|integer|max_length[5]');
        $this->form_validation->set_rules('resource_uri', 'Registrar Server', 'trim|required');
        $this->form_validation->set_rules('resource_aauthdata', 'Registrar Realm', 'trim|required');
        $this->form_validation->set_rules('resource_aauthname', 'Registrar Username', 'trim|required');
        $this->form_validation->set_rules('resource_aauthsecret', 'Registrar Password', 'trim|required');
        $this->form_validation->set_rules('resource_protocol', 'Registrar Protocol', 'trim|required');
        $this->form_validation->set_rules('resource_port_portnbr', 'Registrar Port Number', 'trim|required|integer|max_length[5]');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('settings/sipsettingsadvanced', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            $this->load->library('sip_settings');

            $input['name'] = $this->input->post('name');
            $input['appref'] = $this->input->post('appref');
            //$input['ptype'] = $this->input->post('ptype');
            $input['puid'] = $this->input->post('puid');
            //$input['registration'] = $this->input->post('registration');
            $input['appaddr_addr'] = $this->input->post('appaddr_addr');
            $input['appauth_aauthdata'] = $this->input->post('appauth_aauthdata');
            $input['appauth_aauthname'] = $this->input->post('appauth_aauthname');
            $input['appauth_aauthsecret'] = $this->input->post('appauth_aauthsecret');
            //$input['appaddr_lr'] = $this->input->post('appaddr_lr');
            $input['appaddr_port_portnbr'] = $this->input->post('appaddr_port_portnbr');
            $input['resource_uri'] = $this->input->post('resource_uri');
            $input['protocol'] = $this->input->post('protocol');
            $input['resource_aauthdata'] = $this->input->post('resource_aauthdata');
            $input['resource_aauthname'] = $this->input->post('resource_aauthname');
            $input['resource_aauthsecret'] = $this->input->post('resource_aauthsecret');
            $input['resource_protocol'] = $this->input->post('resource_protocol');
            $input['resource_port_portnbr'] = $this->input->post('resource_port_portnbr');

            $advSIP = $this->sip_settings->formatAdvanced($input);
            //$xml = $this->sip_settings->generateSIP_XML();

            $insertId = $this->sip_settings->save_sip($advSIP, $profile[0]['id']);

            if(!$insertId)
                $this->session->set_flashdata('message', '<p class="success">Settings Not Saved</p>');
            else
                $this->session->set_flashdata('message', '<p class="success">Settings Saved</p>');
                
            $this->session->set_flashdata('settings_type', 'sip');
            $this->session->set_flashdata('settings_id', $insertId);


            redirect('settings/saved');
        }

    }

    function stun()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - STUN';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('appref', 'appref', 'trim|required');
        $this->form_validation->set_rules('domain', 'domain', 'trim|required');
        $this->form_validation->set_rules('stunsrvaddr', 'stunsrvaddr', 'trim|required');
        $this->form_validation->set_rules('stunsrvport', 'stunservport', 'trim|required|integer');
        $this->form_validation->set_rules('natrefreshtcp', 'natrefreshtcp', 'trim|integer');
        $this->form_validation->set_rules('natrefreshudp', 'natrefreshudp', 'trim|integer');

	    $this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	    if ($this->form_validation->run() == false)
        {
            $data['headContent'] = $this->load->view('headcontent', $data, true);
            $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
            $data['content'] = $this->load->view('stunsettings', $data, true);
            $this->load->view('template', $data);
        }
        else
        {
            $this->load->library('sip_settings');

            $inputs['name'] = $this->input->post('name');
            $inputs['appref'] = $this->input->post('appref');
            $inputs['domain'] = $this->input->post('domain');
            $inputs['stunsrvaddr'] = $this->input->post('stunsrvaddr');
            $inputs['stunsrvport'] = $this->input->post('stunsrvport');
            $inputs['natrefreshtcp'] = $this->input->post('natrefreshtcp');
            $inputs['natrefreshudp'] = $this->input->post('natrefreshudp');


            //$xml = $this->sip_settings->generateSTUN_XML($inputs);
            
            $insertId = $this->sip_settings->save_stun($inputs, $profile[0]['id']);

            if(!$insertId)
                $this->session->set_flashdata('message', '<p class="success">Settings Not Saved</p>');
            else
                $this->session->set_flashdata('message', '<p class="success">Settings Saved</p>');
            $this->session->set_flashdata('settings_type', 'stun');
            $this->session->set_flashdata('settings_id', $insertId);

            
            redirect('settings/saved');
        }

    }

    function accesspoints()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Access Points';


        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = '<p class="notice">Not Yet Implemented</p>';
        $this->load->view('template', $data);

    }

    function stunHistory()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Saved STUN Settings';



        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $this->load->library('sip_settings');

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //load pagination
        $this->load->library('pagination');
        $this->config->item('base_url');
        $config['base_url'] = $this->config->item('base_url').$this->config->item('index_page').'/settings/stunhistory';
        //need to change to stun settings foruser
        $config['total_rows'] = $this->sip_settings->countStunHistoryForUser($profile[0]['id']);
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

        $result = $this->sip_settings->getStunHistoryListForUser($profile[0]['id'], $config['per_page'], $uri_segment);

        $data['stunhistory'] = $result;

        $data['send_icon'] = $this->config->item('send_icon');
        $data['xml_icon'] = $this->config->item('xml_icon');
        $data['new_voip_icon'] = $this->config->item('new_voip_icon');
        $data['send_voip_icon'] = $this->config->item('send_voip_icon');

        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('settings/stunhistory', $data, true);
        $this->load->view('template', $data);

    }

     function voipHistory()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Saved VoIP Settings';



        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $this->load->library('sip_settings');

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //load pagination
        $this->load->library('pagination');
        $this->config->item('base_url');
        $config['base_url'] = $this->config->item('base_url').$this->config->item('index_page').'/settings/voiphistory';
        //need to change to stun settings foruser
        $config['total_rows'] = $this->sip_settings->countVoIPHistoryForUser($profile[0]['id']);
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

        $result = $this->sip_settings->getVoIPHistoryListForUser($profile[0]['id'], $config['per_page'], $uri_segment);

        $data['voiphistory'] = $result;

        $data['send_icon'] = $this->config->item('send_icon');
        $data['xml_icon'] = $this->config->item('xml_icon');
        $data['new_voip_icon'] = $this->config->item('new_voip_icon');
        $data['send_voip_icon'] = $this->config->item('send_voip_icon');

        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('settings/voiphistory', $data, true);
        $this->load->view('template', $data);

    }

    function sipHistory()
    {
        $data['loggedIn'] = $this->redux_auth->logged_in();
        $data['currentPageTitle'] = 'SetKia - Saved SIP Settings';



        if(!$data['loggedIn'])
        {
            redirect('users/login');
        }

        $this->load->library('sip_settings');

        $profile = $this->redux_auth->profile();
        $data['profile'] = $profile;

        //load pagination
        $this->load->library('pagination');
        $this->config->item('base_url');
        $config['base_url'] = $this->config->item('base_url').$this->config->item('index_page').'/settings/siphistory';
        //need to change to stun settings foruser
        $config['total_rows'] = $this->sip_settings->countSipHistoryForUser($profile[0]['id']);
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

        $result = $this->sip_settings->getSipHistoryListForUser($profile[0]['id'], $config['per_page'], $uri_segment);

        $data['siphistory'] = $result;

        $data['send_icon'] = $this->config->item('send_icon');
        $data['xml_icon'] = $this->config->item('xml_icon');
        $data['new_voip_icon'] = $this->config->item('new_voip_icon');
        $data['send_voip_icon'] = $this->config->item('send_voip_icon');


        $data['headContent'] = $this->load->view('headcontent', $data, true);
        $data['sideMenu'] = $this->load->view('sidemenuloggedin', null, true);
        $data['content'] = $this->load->view('settings/siphistory', $data, true);
        $this->load->view('template', $data);

    }


    function doctype_check($xml)
    {
        $xml = htmlspecialchars_decode($xml);
        //manual hack to find if it's a wap-provisioning doc type
        if(strpos($xml, '<?xml version="1.0"?>') === false)
        {
            $this->form_validation->set_message('doctype_check', 'XML Version must be set, XML Version should be '.htmlspecialchars('<?xml version="1.0"?>'));
	        return false;
        }
        else if (strpos($xml, '<!DOCTYPE wap-provisioningdoc PUBLIC "-//WAPFORUM//DTD PROV 1.0//EN" "http://www.wapforum.org/DTD/prov.dtd">') === false)
        {
            $this->form_validation->set_message('doctype_check', 'Doctype must be set, Doctype should be '.htmlspecialchars('<!DOCTYPE wap-provisioningdoc PUBLIC "-//WAPFORUM//DTD PROV 1.0//EN" "http://www.wapforum.org/DTD/prov.dtd">'));
	        return false;
        }
        else
        {
            return true;
        }

            /*
             *
             * <?xml version="1.0"?>
             * <!DOCTYPE wap-provisioningdoc PUBLIC "-//WAPFORUM//DTD PROV 1.0//EN" "http://www.wapforum.org/DTD/prov.dtd">
             * */
             
    }

    function _ordstr($str)
    {
        $ret = '';
        for ($a = 0; $a < strlen($str); $a++)
        {
            $chr = dechex(ord($str[$a]));
            if(strlen($chr) == 1)
                $ret .= '0'.$chr;
            else
                $ret .= $chr;
        }
        return $ret;
    }

    function otherto_appref($input)
    {
        if($this->input->post('to-appref') == 0)
        {
            if($this->input->post('otherto-appref') != false)
            {
                return true;
            }
            else
            {
                $this->form_validation->set_message('otherto_appref', 'If to-appref is set to Other, you must specify an Other SIP APP Reference');

                return false;
            }
        }
        else
        {
            return true;
        }
    }

    function appref_check($input)
    {
        if($input == false)
        {
            return false;
        }
        $this->load->library('sip_settings');
        if($this->sip_settings->appref_check($input))
        {
            $this->form_validation->set_message('appref_check', 'This appref already exists. Please choose another');
            return false;
            
        }
        else
        {
            return true;
        }
    }

    function codec_check($input)
    {
        $codec1 = $this->input->post('codec1');
        $codec2 = $this->input->post('codec2');
        $codec3 = $this->input->post('codec3');

        if($codec1== false | $codec2 == false || $codec3 == false)
        {
            $this->form_validation->set_message('codec_check', 'You must set all codec preferences');
            return false;
        }
        else
        {
            if($codec1 == $codec2 && ($codec1 != 110))
            {
                $this->form_validation->set_message('codec_check', 'First and Second Codec are the same');
                return false;
            }
            else if($codec1 == $codec3 && ($codec1 != 110))
            {
                $this->form_validation->set_message('codec_check', 'First and Third Codec are the same');
                return false;
            }
            else if($codec2 == $codec3 && ($codec2 != 110))
            {
                $this->form_validation->set_message('codec_check', 'Second and Third Codec are the same');
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    function pin_check($input)
    {
        $pinType = $this->input->post('pintype');
        $pin = $this->input->post('pin');

        if($pinType != 1 && $pin === false)
        {
            $this->form_validation->set_message('pin_check', 'If you set Pin Type to userpin or network pin, you must set a pin');
            return false;
        }
        else if($pinType == 2)
        {
            if(strlen($pin) != 4)
            {
                $this->form_validation->set_message('pin_check', 'For User PIN the pin length must be 4 digits');
                return false;
            }
            else
            {
                return true;
            }
        }
        else if($pinType == 3)
        {
            if(strlen($pin) != 14 || strlen($pin) != 15)
            {
                $this->form_validation->set_message('pin_check', 'For Network PIN the pin should be your IMSI. An IMSI is either 14 or 15 digits in length');
                return false;
            }
            else
            {
                return true;
            }
        }
        else if($pinType == 1)
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('pin_check', 'Invalid PIN Type');
            return false;
        }
    }

    function to_appref($input)
    {
        if($this->input->post('to-appref') == 0)
        {
            return true;
        }
        else
        {
            $this->load->library('sip_settings');
            if($this->sip_settings->get_settings($this->redux_auth->get_id(), $input, 'sip') == false)
            {
                $this->form_validation->set_message('to_appref', 'SIP APP Reference is invalid');
                return false;
            }
            else
            {
                return true;
            }

        }
    }
}
?>
