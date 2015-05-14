<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * licensing to appear here
 */

/**
 * Description of provisioning_settings_model
 *
 * @author Thomas
 */
class provisioning_settings_model extends Model
{

    public function __construct()
	{
		parent::__construct();
	}

    /**
	 * Saves the stun settings to the database.
	 *
	 * @return insertID or false
     * @param data Mixed Array of the stun settings
     * @param id int the id of the  user saving the settings
	 * @author Thomas
	 **/
    public function save_STUN($data, $id)
    {
        /*
        $inputs['name'] = $this->input->post('name');
            $inputs['appref'] = $this->input->post('appref');
            $inputs['domain'] = $this->input->post('domain');
            $inputs['stunsrvaddr'] = $this->input->post('stunsrvaddr');
            $inputs['stunsrvport'] = $this->input->post('stunsrvport');
            $inputs['natrefreshtcp'] = $this->input->post('natrefreshtcp');
            $inputs['natrefreshudp'] = $this->input->post('natrefreshudp');
         * *
         */
        $data['user_id'] = $id;
        $this->db->insert('stunsettings', $data);
        $insertId = $this->db->insert_id();

        return $insertId;

    }

    /**
	 * Saves the sip settings in advanced format to the database.
	 *
	 * @return insertID or false
     * @param data Mixed Array of the sip settings
     * @param id int the id of the  user saving the settings
	 * @author Thomas
	 **/
    public function save_SIP($data, $id)
    {
        /*
        $inputs['name'] = $this->input->post('name');
            $inputs['appref'] = $this->input->post('appref');
            $inputs['domain'] = $this->input->post('domain');
            $inputs['stunsrvaddr'] = $this->input->post('stunsrvaddr');
            $inputs['stunsrvport'] = $this->input->post('stunsrvport');
            $inputs['natrefreshtcp'] = $this->input->post('natrefreshtcp');
            $inputs['natrefreshudp'] = $this->input->post('natrefreshudp');
         * *
         */
        $data['user_id'] = $id;
        $this->db->insert('sipsettings', $data);
        $insertId = $this->db->insert_id();

        return $insertId;

    }

    public function save_voip($data, $id)
    {
        $data['user_id'] = $id;
        $this->db->insert('voipsettings', $data);
        $insertId = $this->db->insert_id();

        return $insertId;

    }

    /**
	 * Get the stun settings from the database.
	 *
	 * @return mixed Array of stun settings
     * @param user_id The user id of hte user attempting to get the settings
     * @param stun_id The id of the stun settings
	 * @author Thomas
	 **/
    public function get_STUN($user_id, $stun_id)
    {
        $stun_table      =  'stunsettings';




	    if ($user_id === false || $stun_id === false)
	    {
	        return false;
	    }

		$this->db->select($stun_table.'.id, '.
						  $stun_table.'.user_id, ' .
						  $stun_table.'.name, '.
						  $stun_table.'.appref, '.
                          $stun_table.'.domain,'.
						  $stun_table.'.stunsrvaddr, '.
						  $stun_table.'.stunsrvport , '.
                          $stun_table.'.natrefreshtcp , '.
						  $stun_table.'.natrefreshudp');


		$this->db->from($stun_table);
	    $this->db->where($stun_table.'.id', $stun_id);
        $this->db->where($stun_table.'.user_id', $user_id);

		$this->db->limit(1);
		$i = $this->db->get();

		return ($i->num_rows > 0) ? $i->result_array() : false;
    }

    /**
	 * Get the sip settings from the database.
	 *
	 * @return mixed Array of stun settings
     * @param user_id The user id of hte user attempting to get the settings
     * @param stun_id The id of the sip settings
	 * @author Thomas
	 **/
    public function get_SIP($user_id, $sip_id)
    {
        $sip_table      =  'sipsettings';




	    if ($user_id === false || $sip_id === false)
	    {
	        return false;
	    }

		$this->db->select($sip_table.'.id, '.
						  $sip_table.'.user_id, ' .
                          $sip_table.'.name, ' .
                          $sip_table.'.appref, ' .
						  $sip_table.'.provider-id, '.
						  $sip_table.'.ptype, '.
                          $sip_table.'.puid,'.
						  $sip_table.'.protocol, '.
						  $sip_table.'.autoreg , '.
                          $sip_table.'.appaddr_addr , '.
                          $sip_table.'.appaddr_port_portnbr , '.
                          $sip_table.'.appauth_aauthname , '.
                          $sip_table.'.appauth_aauthsecret , '.
                          $sip_table.'.appauth_aauthdata , '.
                          $sip_table.'.resource_uri , '.
                          $sip_table.'.resource_aauthname , '.
                          $sip_table.'.resource_aauthsecret , '.
                          $sip_table.'.resource_aauthdata , '.
                          $sip_table.'.resource_port_portnbr , '.
                          $sip_table.'.resource_protocol '
                      );


		$this->db->from($sip_table);
	    $this->db->where($sip_table.'.id', $sip_id);
        $this->db->where($sip_table.'.user_id', $user_id);

		$this->db->limit(1);
		$i = $this->db->get();

		return ($i->num_rows > 0) ? $i->result_array() : false;
    }

    public function get_VoIP($user_id, $voip_id)
    {
        $sip_table      =  'sipsettings';
        $voip_table     =  'voipsettings';



	    if ($user_id === false || $voip_id === false)
	    {
	        return false;
	    }

        $query = $this->db->select($voip_table.'.id, '.
						  $voip_table.'.user_id, ' .
						  $voip_table.'.name, '.
						  $voip_table.'.provider-id, '.
                          $voip_table.'.otherto-appref, '.
                          $voip_table.'.to-appref, '.
                          $voip_table.'.smport, '.
                          $voip_table.'.emport, '.
                          $voip_table.'.mediaqos, '.
                          $voip_table.'.dtmfib, '.
                          $voip_table.'.dtmfob, '.
                          $voip_table.'.voipoverwcdma, '.
                          $voip_table.'.rtcp, '.
                          $voip_table.'.uahtermtype, '.
                          $voip_table.'.uahmac, '.
                          $voip_table.'.uahfree, '.
                          $voip_table.'.securecall, '.
                          $voip_table.'.voipdigits, '.
                          $voip_table.'.igndom, '.
                          $voip_table.'.to-appref, '.
                          $voip_table.'.codec1, '.
                          $voip_table.'.codec2, '.
                          $voip_table.'.codec3, '.
                          $sip_table.'.id AS `sipid`, '.
                          $sip_table.'.appref AS `to-apprefname`'
                          )
                      ->where($voip_table.'.id', $voip_id)
                      ->where($voip_table.'.user_id', $user_id)
                      ->join($sip_table, $sip_table.'.id = '.$voip_table.'.to-appref', 'left')
                      ->limit(1)
                      ->get($voip_table);

		return ($query->num_rows > 0) ? $query->result_array() : false;
    }

    public function get_Default_VoIP()
    {
        $sip_table      =  'sipsettings';
        $voip_table     =  'voipsettings';

        $query = $this->db->select($voip_table.'.id, '.
						  $voip_table.'.user_id, ' .
						  $voip_table.'.name, '.
						  $voip_table.'.provider-id, '.
                          $voip_table.'.otherto-appref, '.
                          $voip_table.'.to-appref, '.
                          $voip_table.'.smport, '.
                          $voip_table.'.emport, '.
                          $voip_table.'.mediaqos, '.
                          $voip_table.'.dtmfib, '.
                          $voip_table.'.dtmfob, '.
                          $voip_table.'.voipoverwcdma, '.
                          $voip_table.'.rtcp, '.
                          $voip_table.'.uahtermtype, '.
                          $voip_table.'.uahmac, '.
                          $voip_table.'.uahfree, '.
                          $voip_table.'.securecall, '.
                          $voip_table.'.voipdigits, '.
                          $voip_table.'.igndom, '.
                          $voip_table.'.to-appref, '.
                          $voip_table.'.codec1, '.
                          $voip_table.'.codec2, '.
                          $voip_table.'.codec3 '
                          )
                      ->where($voip_table.'.name', '-skdefault-')
                      ->where($voip_table.'.user_id', 0)
                      ->limit(1)
                      ->get($voip_table);

		return ($query->num_rows > 0) ? $query->result_array() : false;
    }

    function getStunHistoryListForUser($user_id, $num, $offset)
    {
        $stun_table      =  'stunsettings';

		$query = $this->db->select($stun_table.'.id, '.
						  $stun_table.'.user_id, ' .
						  $stun_table.'.name, '.
						  $stun_table.'.appref, '.
                          $stun_table.'.domain,'.
						  $stun_table.'.stunsrvaddr ')
                      ->where('user_id', $user_id)
                      ->order_by('id', 'desc')
                      ->get($stun_table, $num, $offset);

        //return ($i->num_rows > 0) ? $i->result_array() : false;
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }

    function countStunHistoryForUser($user_id)
    {
        $stun_table      =  'stunsettings';

        $this->db->where('user_id', $user_id);
        $this->db->from($stun_table);
        return $this->db->count_all_results();
    }

    function getSipHistoryListForUser($user_id, $num, $offset)
    {
        $sip_table      =  'sipsettings';

		$query = $this->db->select($sip_table.'.id, '.
						  $sip_table.'.user_id, ' .
						  $sip_table.'.name, '.
                          $sip_table.'.appref, '.
						  $sip_table.'.provider-id, '.
                          $sip_table.'.puid')
                      ->where('user_id', $user_id)
                      ->order_by('id', 'desc')
                      ->get($sip_table, $num, $offset);

        //return ($i->num_rows > 0) ? $i->result_array() : false;
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }

    function getVoIPHistoryListForUser($user_id, $num, $offset)
    {
        $sip_table      =  'sipsettings';
        $voip_table     = 'voipsettings';

		$query = $this->db->select($voip_table.'.id, '.
						  $voip_table.'.user_id, ' .
						  $voip_table.'.name, '.
						  $voip_table.'.provider-id, '.
                          $voip_table.'.otherto-appref, '.
                          $voip_table.'.to-appref, '.
                          $sip_table.'.id AS `sipid`, '.
                          $sip_table.'.name AS `sipname`'
                          )
                      ->where($voip_table.'.user_id', $user_id)
                      ->join($sip_table, $sip_table.'.id = '.$voip_table.'.to-appref', 'left')
                      ->order_by($voip_table.'.id', 'desc')
                      ->get($voip_table, $num, $offset);

        //return ($i->num_rows > 0) ? $i->result_array() : false;
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }

    function get_voip_for_sip($user_id, $sid, $stype)
    {
        $sip_table      =  'sipsettings';
        $voip_table     = 'voipsettings';

		$query = $this->db->select($voip_table.'.id, '.
						  $voip_table.'.user_id, ' .
						  $voip_table.'.name, '.
						  $voip_table.'.provider-id, '.
                          $voip_table.'.otherto-appref, '.
                          $voip_table.'.to-appref, '.
                          $sip_table.'.id AS `sipid`, '.
                          $sip_table.'.name AS `sipname`'
                          )
                      ->where($voip_table.'.user_id', $user_id)
                      ->where($voip_table.'.to-appref', $sid)
                      ->join($sip_table, $sip_table.'.id = '.$voip_table.'.to-appref', 'left')
                      ->order_by($voip_table.'.id', 'desc')
                      ->get($voip_table);

        //return ($i->num_rows > 0) ? $i->result_array() : false;
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return FALSE;
        }
    }

    function countSipHistoryForUser($user_id)
    {
        $sip_table      =  'sipsettings';

        $this->db->where('user_id', $user_id);
        $this->db->from($sip_table);
        return $this->db->count_all_results();
    }

    function countVoIPHistoryForUser($user_id)
    {
        $voip_table      =  'voipsettings';

        $this->db->where('user_id', $user_id);
        $this->db->from($voip_table);
        return $this->db->count_all_results();
    }

    function appref_check($appref, $user_id)
    {
        $sip_table      =  'sipsettings';
        
	    if ($appref === false || $user_id === false)
	    {
	        return false;
	    }

	    $query = $this->db->select('id, appref, user_id')
                           ->where('user_id', $user_id)
                           ->where('appref', $appref)
                           ->limit(1)
                           ->get($sip_table);

		if ($query->num_rows() == 1)
		{
			return true;
		}

		return false;

    }
    //put your code here
}
?>
