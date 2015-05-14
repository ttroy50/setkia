<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pricing_model
 *
 * @author ttroy
 */
class pricing_model extends Model{
    //put your code here

    public function __construct()
	{
		parent::__construct();
		
	}

    function getCountryList()
    {
        $country_table  =   'countries';
        $cost_table      =  'networkcost';

		$query = $this->db->select($country_table.'.id, '.
						  $country_table.'.country, ')
                      ->order_by('country')
                      ->get($country_table);

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

    function getCostList($num, $offset)
    {
        $country_table  =   'countries';
        $cost_table      =  'networkcost';

		$query = $this->db->select($country_table.'.id, '.
						  $country_table.'.country, ' .
						  $country_table.'.ccode, '.
						  $cost_table.'.id, '.
                          $cost_table.'.network, '.
                          $cost_table.'.countryid,'.
						  $cost_table.'.cost ')
                      ->join($cost_table, $country_table.'.id = '.$cost_table.'.countryid', 'left')
                      ->order_by('country')
                      ->get($country_table, $num, $offset);

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

    function getCostForCountry($cid)
    {
        $country_table  =   'countries';
        $cost_table      =  'networkcost';

		$query = $this->db->select($country_table.'.id, '.
						  $country_table.'.country, ' .
						  $country_table.'.ccode, '.
						  $cost_table.'.id, '.
                          $cost_table.'.network, '.
                          $cost_table.'.countryid,'.
						  $cost_table.'.cost ')
                      ->join($cost_table, $country_table.'.id = '.$cost_table.'.countryid', 'left')
                      ->where($country_table.'.id', $cid)
                      ->order_by('country')
                      ->get($country_table);

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

    function countAllNetworks()
    {
        $country_table  =   'countries';
        $cost_table      =  'networkcost';


        $this->db->from($cost_table);
        return $this->db->count_all_results();
    }
}
?>
