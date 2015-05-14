<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if ( ! function_exists('WSP_Data'))
{
    function WSP_Data($pinType, $pduType, $contentType)
    {
        if($pinType == 1)
        {
             $wspData = array('pduType' => $pduType,
                 'contentType' => $contentType
                );
        }
        else
        {
            if($pinType == 2)
            {
                $wspData = array('pduType' => $pduType,
                'contentType' => $contentType,
                'secType' => 'userpin'
                );
            }
            else
            {
                $wspData = array('pduType' => $pduType,
                'contentType' => $contentType,
                'secType' => 'netwpin'
                );
            }
         }
         return $wspData;
    }
}
?>
