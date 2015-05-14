<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_admin_model
 *
 * @author ttroy
 */
class user_admin_model extends Model
{
    //put your code here

    public function __construct()
	{
		parent::__construct();
		$this->load->config('redux_auth');
		$this->tables  = $this->config->item('tables');
		$this->columns = $this->config->item('columns');
        $this->billing_columns = $this->config->item('billing_columns');
        $this->settings_columns = $this->config->item('settings_columns');
	}

    function countUsers()
    {
        $users_table     = $this->tables['users'];
        return $this->db->count_all($users_table);
    }

    function getUsersList($num, $offset)
    {
        $users_table     = $this->tables['users'];

        $query = $this->db->select($users_table.'.id, '.
						  $users_table.'.username, ' .
						  $users_table.'.email, '.
                          $users_table.'.phonenumber,'.
						  $users_table.'.activation_code ')
                      ->order_by('username')
                      ->get($users_table, $num, $offset);

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

    function addCredits($user_id, $numCredits)
    {
        $users_table     = $this->tables['users'];
        $billing_table   = $this->tables['user_billing'];

        if($user_id == false || $numCredits == false)
        {
            return false;
        }

        $billing_query = $this->db->select(
                                        $billing_table.'.user_id, '.
                                        $billing_table.'.sms_available, '.
                                        $billing_table.'.total_num_sms_bought'
                                          )
                                   ->where('user_id', $user_id)
                                   ->limit(1)
                                   ->get($billing_table);

        if($billing_query->num_rows() != 1)
        {
            return false;
        }

        $billing_result = $billing_query->row();
        $sms_available = $billing_result->sms_available;
        $total_num_sms_bought = $billing_result->total_num_sms_bought;

        $new_sms = $numCredits + $sms_available;
        $new_total = $total_num_sms_bought + $numCredits;

        $data = array('sms_available' => $new_sms,
                      'total_num_sms_bought' => $new_total);

		$this->db->update($billing_table, $data, array('user_id' => $user_id));

		return ($this->db->affected_rows() == 1) ? true : false;
    }

    function deactivateUser($user_id)
    {
        $users_table     = $this->tables['users'];

        if($user_id == false)
        {
            return false;
        }

        $user_query = $this->db->select(
                                        $users_table.'.id, '
                                          )
                                   ->where('id', $user_id)
                                   ->limit(1)
                                   ->get($users_table);

        if($user_query->num_rows() != 1)
        {
            return false;
        }


        $activation_code = sha1(md5(microtime()));

        $data = array('activation_code' => $activation_code);

		$this->db->update($users_table, $data, array('id' => $user_id));

		return ($this->db->affected_rows() == 1) ? true : false;
    }
}
?>
