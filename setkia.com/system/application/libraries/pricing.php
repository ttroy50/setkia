<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pricing
 *
 * @author ttroy
 */
class pricing {
    //put your code here

    public function __construct()
	{
		$this->ci =& get_instance();
        $this->ci->load->model('pricing_model');
		
	}

    function getCostList($num, $offset)
    {
        return $this->ci->pricing_model->getCostList($num, $offset);
    }

    function getCountryList()
    {
        return $this->ci->pricing_model->getCountryList();
    }


    function countAllNetworks()
    {
        return $this->ci->pricing_model->countAllNetworks();
    }

    function getCostForCountry($cid)
    {
        if($cid === false)
            return false;
        return $this->ci->pricing_model->getCostForCountry($cid);
    }
}
?>
