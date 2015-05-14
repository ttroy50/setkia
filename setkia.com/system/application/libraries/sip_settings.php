<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sip_wizard
 *
 * @author ttroy
 */
class sip_settings {

    var $adv = array();

    var $wizard = array();
    
    var $_xml;
    var $_xmlSTUN;
    var $_xmlVoIP;

    var $_xmlVersion = '<?xml version="1.0"?>';
    var $_doctype = '<!DOCTYPE wap-provisioningdoc PUBLIC "-//WAPFORUM//DTD PROV 1.0//EN" "http://www.wapforum.org/DTD/prov.dtd">';
    var $_newline = "";
    var $_opentag = '<wap-provisioningdoc>';
    var $_char_type_start = '<characteristic type="APPLICATION">';
    var $_char_type_end = '</characteristic>';
    var $_appidSIP = 'w9010';
    var $_appidSTUN = 'w902E';
    var $_appidVoIP = 'w9013';
    var $_endtag = '</wap-provisioningdoc>';

    public function __construct()
	{
		$this->ci =& get_instance();
	}

    function wizardToAdvanced($data = array())
    {
        /*
         *
         * 		Mappings
			puid = username@domain
			Appaddr -> Addr = proxy
			resource -> uri = domain
			appauth -> aauthname = username
			appauth -> aauthsecret = password
			appauth -> aauthdata = realm
			resource -> aauthname = username
			resource -> aauthsecret = password
			resource -> aauthdata = realm
			ptype = forced to ietf
         */
        $this->adv['name'] = $data['name'];
        $this->adv['puid'] = $data['username'].'@'.$data['domain'];
        $this->adv['appaddr_addr'] = $data['proxy'];
        $this->adv['appref'] = $data['appref'];
        $this->adv['resource_uri'] = $data['domain'];//.':'.$data['port'].';transport='.$data['protocol'];
        $this->adv['appauth_aauthname'] = $data['username'];
        $this->adv['appauth_aauthsecret'] = $data['password'];
        $this->adv['appauth_aauthdata'] = $data['realm'];
        $this->adv['resource_aauthname'] = $data['username'];
        $this->adv['resource_aauthsecret'] = $data['password'];
        $this->adv['resource_aauthdata'] = $data['realm'];
        $this->adv['ptype'] = 'IETF';
        $this->adv['provider-id'] = $data['domain'];
        if($data['registration'] == 1)
            $this->adv['autoreg'] = true;
        else
            $this->adv['autoreg'] = false;
        $this->adv['appaddr_port_portnbr'] = $data['port'];
        $this->adv['protocol'] = $data['protocol'];
        $this->adv['resource_protocol'] = $data['protocol'];
        $this->adv['resource_port_portnbr'] = $data['port'];

        $this->wiz = $data;

        return $this->adv;

    }

    function formatAdvanced($data = array())
    {
        $this->adv = $data;
        $this->adv['ptype'] = 'IETF';
        
        if($data['registration'] == 1)
            $this->adv['autoreg'] = true;
        else
            $this->adv['autoreg'] = false;

        //$this->adv['appref'] = $data['appref'];
        $this->adv['provider-id'] = $data['resource_uri'];

        return $this->adv;
    }


    function generateSIP_XML($data = null)
    {
        if($data !== false)
            $this->adv = $data;
            
        //write standard start for all settings
        $this->_xml = $this->_xmlVersion.$this->_newline;
        $this->_xml .= $this->_doctype.$this->_newline;
        $this->_xml .= $this->_opentag.$this->_newline;
        $this->_xml .= $this->_generateSIP_AppXML();
        $this->_xml .= $this->_endtag.$this->_newline;

        return $this->_indent($this->_xml);
    }

    function _generateSIP_AppXML()
    {
        $sipxml = $this->_char_type_start.$this->_newline;
        $sipxml .= '<parm name="APPID" value="'.$this->_appidSIP.'"/>'.$this->_newline;

        //write settings
        $sipxml .= '<parm name="PROVIDER-ID" value="'.$this->adv['provider-id'].'"/>'.$this->_newline;
        $sipxml .= '<parm name="PTYPE" value="'.$this->adv['ptype'].'"/>'.$this->_newline;
        $sipxml .= '<parm name="PUID" value="'.$this->adv['puid'].'"/>'.$this->_newline;
        $sipxml .= '<parm name="NAME" value="'.$this->adv['name'].'"/>'.$this->_newline;
        $sipxml .= '<parm name="APPREF" value="'.$this->adv['appref'].'"/>'.$this->_newline;

        if(strcmp($this->adv['resource_protocol'], 'UDP') == 0 ||  strcmp($this->adv['resource_protocol'], 'TCP') == 0)
        {
            $sipxml .= '<parm name="APROTOCOL" value="'.$this->adv['protocol'].'"/>'.$this->_newline;
        }


        if($this->adv['autoreg'])
            $sipxml .= '<parm name="AUTOREG"/>'.$this->_newline;

        $sipxml .= '<characteristic type="APPADDR">'.$this->_newline;
            $sipxml .= '<parm name="LR"/>'.$this->_newline;
            $sipxml .= '<parm name="ADDR" value="'.$this->adv['appaddr_addr'].'"/>'.$this->_newline;

            $sipxml .= '<characteristic type="PORT">'.$this->_newline;
                $sipxml .= '<parm name="PORTNBR" value="'.$this->adv['appaddr_port_portnbr'].'"/>'.$this->_newline;
            $sipxml .= $this->_char_type_end.$this->_newline; // end port

        $sipxml .= $this->_char_type_end.$this->_newline;//end appaddr

        $sipxml .= '<characteristic type="APPAUTH">'.$this->_newline;
            $sipxml .= '<parm name="AAUTHNAME" value="'.$this->adv['appauth_aauthname'].'"/>'.$this->_newline;
            $sipxml .= '<parm name="AAUTHSECRET" value="'.$this->adv['appauth_aauthsecret'].'"/>'.$this->_newline;
            $sipxml .= '<parm name="AAUTHDATA" value="'.$this->adv['appauth_aauthdata'].'"/>'.$this->_newline;
        $sipxml .= $this->_char_type_end.$this->_newline; // end appauth

        $sipxml .= '<characteristic type="RESOURCE">'.$this->_newline;

        //prepare the resource uri wiht port and transport if they are valid
        $uri = $this->adv['resource_uri'];
        if($this->adv['resource_port_portnbr'] != false && $this->adv['resource_port_portnbr'] != 5060)
        {
            $uri .= ':'.$this->adv['resource_port_portnbr'];
        }
        if(strcmp($this->adv['resource_protocol'], 'UDP') == 0 ||  strcmp($this->adv['resource_protocol'], 'TCP') == 0)
        {
            $uri .= ';transport='.$this->adv['resource_protocol'];
        }

            $sipxml .= '<parm name="URI" value="'.$uri.'"/>'.$this->_newline;
            $sipxml .= '<parm name="AAUTHNAME" value="'.$this->adv['resource_aauthname'].'"/>'.$this->_newline;
            $sipxml .= '<parm name="AAUTHSECRET" value="'.$this->adv['resource_aauthsecret'].'"/>'.$this->_newline;
            $sipxml .= '<parm name="AAUTHDATA" value="'.$this->adv['resource_aauthdata'].'"/>'.$this->_newline;
        $sipxml .= $this->_char_type_end.$this->_newline; // end resource


        //write standard end for all settings
        $sipxml .= $this->_char_type_end.$this->_newline; //end applicaton

        return $sipxml;
    }


    function generateVoIP_XML($data = null)
    {
        //write standard start for all settings
        $this->_xmlVoIP = $this->_xmlVersion.$this->_newline;
        $this->_xmlVoIP .= $this->_doctype.$this->_newline;
        $this->_xmlVoIP .= $this->_opentag.$this->_newline;

        $this->_xmlVoIP .= $this->_generateVoIP_AppXML($data);

        $this->_xmlVoIP .= $this->_endtag.$this->_newline;

        return $this->_indent($this->_xmlVoIP);
    }

    function generateSIP_VoIP_XML($sip = null, $voip = null, $default = 0)
    {
        if($sip !== false)
            $this->adv = $sip;

        if($default == 0)
        {
            $voip['to-apprefname'] = $sip['appref'];
            $voip['to-appref'] = '1';
            $voip['name'] = $sip['name'];
            $voip['provider-id'] = $sip['provider-id'];
        }

        //write standard start for all settings
        $this->_xmlVoIP = $this->_xmlVersion.$this->_newline;
        $this->_xmlVoIP .= $this->_doctype.$this->_newline;
        $this->_xmlVoIP .= $this->_opentag.$this->_newline;

        $this->_xmlVoIP .= $this->_generateSIP_AppXML();

        $this->_xmlVoIP .= $this->_generateVoIP_AppXML($voip);



        $this->_xmlVoIP .= $this->_endtag.$this->_newline;

        return $this->_indent($this->_xmlVoIP);
    }

    function _generateVoIP_AppXML($data)
    {
        $xmlVoIP = $this->_char_type_start.$this->_newline;
        $xmlVoIP .= '<parm name="APPID" value="'.$this->_appidVoIP.'"/>'.$this->_newline;

        //write settings
        $xmlVoIP .= '<parm name="PROVIDER-ID" value="'.$data['provider-id'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="NAME" value="'.$data['name'].'"/>'.$this->_newline;
        if($data['to-appref'] != 0)
            $xmlVoIP .= '<parm name="TO-APPREF" value="'.$data['to-apprefname'].'"/>'.$this->_newline;
        else
            $xmlVoIP .= '<parm name="TO-APPREF" value="'.$data['otherto-appref'].'"/>'.$this->_newline;

        $xmlVoIP .= '<parm name="SMPORT" value="'.$data['smport'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="EMPORT" value="'.$data['emport'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="MEDIAQOS" value="'.$data['mediaqos'].'"/>'.$this->_newline;

        if($data['dtmfib'] == 0)
            $xmlVoIP .= '<parm name="NODTMFIB"/>'.$this->_newline;
        if($data['dtmfob'] == 0)
            $xmlVoIP .= '<parm name="NODTMFOOB"/>'.$this->_newline;

        $xmlVoIP .= '<parm name="SECURECALLPREF" value="'.$data['securecall'].'"/>'.$this->_newline;

        if($data['voipoverwcdma'] == 1)
            $xmlVoIP .= '<parm name="ALLOWVOIPOVERWCDMA"/>'.$this->_newline;

        $xmlVoIP .= '<parm name="RTCP" value="'.$data['rtcp'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="UAHTERMINALTYPE" value="'.$data['uahtermtype'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="UAHWLANMAC" value="'.$data['uahmac'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="UAHSTRING" value="'.$data['uahfree'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="VOIPDIGITS" value="'.$data['voipdigits'].'"/>'.$this->_newline;
        $xmlVoIP .= '<parm name="IGNDOMPART" value="'.$data['igndom'].'"/>'.$this->_newline;

        $xmlVoIP .= $this->_generateCodec_XML($data['codec1'], 1);
        $xmlVoIP .= $this->_generateCodec_XML($data['codec2'], 2);
        $xmlVoIP .= $this->_generateCodec_XML($data['codec3'], 3);
        //write standard end for all settings
        $xmlVoIP .= $this->_char_type_end.$this->_newline; //end applicaton

        return $xmlVoIP;
    }

    function _generateCodec_XML($type, $priority)
    {
        $xml = '';
        if($type == 0 || $type == 1|| $type == 3|| $type == 4|| $type == 10)
        {
            $xml = '<characteristic type="CODEC">'.$this->_newline;
            $xml .= '<parm name="MEDIASUBTYPE" value="'.$type.'"/>'.$this->_newline;
            $xml .= '<parm name="PRIORITYINDEX" value="'.$priority.'"/>'.$this->_newline;
            $xml .= '</characteristic>'.$this->_newline;
        }
        return $xml;
    }


    function generateSTUN_XML($data)
    {
        //write standard start for all settings
        $this->_xmlSTUN = $this->_xmlVersion.$this->_newline;
        $this->_xmlSTUN .= $this->_doctype.$this->_newline;
        $this->_xmlSTUN .= $this->_opentag.$this->_newline;

        $this->_xmlSTUN .= $this->_generateSTUN_AppXML($data);
        
        $this->_xmlSTUN .= $this->_endtag.$this->_newline;

        return $this->_indent($this->_xmlSTUN);
    }

    function _generateSTUN_AppXML($data)
    {
        $xmlSTUN = $this->_char_type_start.$this->_newline;
        $xmlSTUN .= '<parm name="APPID" value="'.$this->_appidSTUN.'"/>'.$this->_newline;

        //write standard end for all settings
        $xmlSTUN .= '<parm name="NAME" value="'.$data['name'].'"/>'.$this->_newline;
        $xmlSTUN .= '<parm name="APPREF" value="'.$data['appref'].'"/>'.$this->_newline;

        $xmlSTUN .= '<characteristic type="NW">'.$this->_newline;
            $xmlSTUN .= '<parm name="DOMAIN" value="'.$data['domain'].'"/>'.$this->_newline;
            $xmlSTUN .= '<parm name="STUNSRVADDR" value="'.$data['stunsrvaddr'].'"/>'.$this->_newline;
            $xmlSTUN .= '<parm name="STUNSRVPORT" value="'.$data['stunsrvport'].'"/>'.$this->_newline;
            if($data['natrefreshtcp'] != FALSE)
                $xmlSTUN .= '<parm name="NATREFRESHTCP" value="'.$data['natrefreshtcp'].'"/>'.$this->_newline;
            if($data['natrefreshudp'] != FALSE)
                $xmlSTUN .= '<parm name="NATREFRESHUDP" value="'.$data['natrefreshudp'].'"/>'.$this->_newline;

        $xmlSTUN .= $this->_char_type_end.$this->_newline; // end appauth

        $xmlSTUN .= $this->_char_type_end.$this->_newline; //end applicaton

        return $xmlSTUN;
    }
    function _indent($text)
    {
        // Create new lines where necessary
        $find = array('>', '</', "\n\n");
        $replace = array(">\n", "\n</", "\n");
        $text = str_replace($find, $replace, $text);
        $text = trim($text); // for the \n that was added after the final tag

        $text_array = explode("\n", $text);
        $open_tags = 0;
        
        foreach ($text_array AS $key => $line)
        {
            $tabs = '';
            if (($key == 0) || ($key == 1) || ($key == 2)) // The first line shouldn't affect the indentation
                    $tabs = '';
            else
            {
                    for ($i = 1; $i <= $open_tags; $i++)
                            $tabs .= "   ";
            }

            if ($key != 0 && $key != 1 && $key != 2)
            {
                    if ((strpos($line, '</') === false) && (strpos($line, '>') !== false))
                    {
                        if((strpos($line, '/>') === false))
                            $open_tags++;
                    }
                    else if ($open_tags > 0)
                            $open_tags--;
            }

            $new_array[] = $tabs . $line;

            unset($tabs);
        }
        $indented_text = implode("\n", $new_array);

        return $indented_text;
    }

    function save_stun($data, $id)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->save_STUN($data, $id);
    }

    function save_sip($data, $id)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->save_SIP($data, $id);
    }

    function save_voip($data, $id)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->save_voip($data, $id);
    }

    function get_settings($user_id = null, $insertId = null, $settings_type = null)
    {
        if($user_id === false || $insertId === false || $settings_type === false)
        {
            return false;
        }

        $this->ci->load->model('provisioning_settings_model');
        if(strcmp($settings_type, 'stun') == 0)
        {
            return $this->ci->provisioning_settings_model->get_STUN($user_id, $insertId);
        }
        else if(strcmp($settings_type, 'sip') == 0)
        {
            return $this->ci->provisioning_settings_model->get_SIP($user_id, $insertId);
        }
        else if(strcmp($settings_type, 'voip') == 0)
        {
            return $this->ci->provisioning_settings_model->get_VoIP($user_id, $insertId);
        }
        else if(strcmp($settings_type, 'addvoip') == 0)
        {
            if($insertId == 0)
            {
                return $this->ci->provisioning_settings_model->get_Default_VoIP();
            }
            else
            {
                return $this->ci->provisioning_settings_model->get_VoIP($user_id, $insertId);
            }
        }
        else
        {
            return false;
        }

    }

    function getStunHistoryListForUser($user_id, $num, $offset)
    {
        $this->ci->load->model('provisioning_settings_model');

        return $this->ci->provisioning_settings_model->getStunHistoryListForUser($user_id, $num, $offset);
    }

    function getVoIPHistoryListForUser($user_id, $num, $offset)
    {
        $this->ci->load->model('provisioning_settings_model');

        return $this->ci->provisioning_settings_model->getVoIPHistoryListForUser($user_id, $num, $offset);
    }

    function get_voip_for_sip($user_id, $sid, $stype)
    {
        $this->ci->load->model('provisioning_settings_model');

        return $this->ci->provisioning_settings_model->get_voip_for_sip($user_id, $sid, $stype);
    }

    function countStunHistoryForUser($user_id)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->countStunHistoryForUser($user_id);

    }

    function countVoIPHistoryForUser($user_id)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->countVoIPHistoryForUser($user_id);

    }

    function getSipHistoryListForUser($user_id, $num, $offset)
    {
        $this->ci->load->model('provisioning_settings_model');

        return $this->ci->provisioning_settings_model->getSipHistoryListForUser($user_id, $num, $offset);
    }

    function countSipHistoryForUser($user_id)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->countSipHistoryForUser($user_id);

    }

    function appref_check($appref)
    {
        $this->ci->load->model('provisioning_settings_model');
        return $this->ci->provisioning_settings_model->appref_check($appref, $this->ci->redux_auth->get_id());

    }

}
?>
