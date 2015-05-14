<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" :
 * <thepixeldeveloper@googlemail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Mathew Davies
 * ----------------------------------------------------------------------------
 */

/**
* redux_auth_model
*/
class captcha_model extends Model
{
    function validate_captcha($word, $ip_address, $exp)
    {
        $query = $this->db->select('word, ip_address, captcha_time')
                    	   ->where('word', $word)
                           ->where('ip_address', $ip_address)
                           ->where('captcha_time >', $exp)
                    	   ->get('captcha');

        $result = $query->row();

        if ($query->num_rows() > 0)
        {
            $found = TRUE;
            $this->deleteByIPandWord($word, $ip_address);
        }
        else
        {
            $found = FALSE;
            $this->deleteByIP($ip_address);
        }

        return $found;
    }

    function deleteByExp($exp)
    {
        $this->db->query("DELETE FROM captcha WHERE captcha_time < ".$exp);
    }

    function deleteByIP($ip_address)
    {
        $data = array ('ip_address' => $ip_address);
        $this->db->delete('captcha', $data);
    }

    function deleteByIPandWord($word, $ip_address)
    {
        $data = array ('word' => $word, 'ip_address' => $ip_address);
        $this->db->delete('captcha', $data);
    }

    function insert_captcha($capdata)
    {
        $query = $this->db->insert_string('captcha', $capdata);
        $this->db->query($query);
    }
}
?>