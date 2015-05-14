<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_admin
 *
 * @author ttroy
 */
class user_admin {
    //put your code here

    public function __construct()
	{
		$this->ci =& get_instance();
        $this->ci->load->model('user_admin_model');
	}

    function countUsers()
    {
        return $this->ci->user_admin_model->countUsers();
    }

    function getUsersList($num, $offset)
    {
        return $this->ci->user_admin_model->getUsersList($num, $offset);
    }

    function addCredits($user_id, $numCredits)
    {
        return $this->ci->user_admin_model->addCredits($user_id, $numCredits);
    }

    function deactivateUser($user_id)
    {
        return $this->ci->user_admin_model->deactivateUser($user_id);
    }


}
?>
