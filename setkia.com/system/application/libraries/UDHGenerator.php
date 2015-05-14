<?php if (!defined('BASEPATH')) { die('No direct script access allowed'); }
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UDHGenerator
 *
 * @author ttroy
 */
class UDHGenerator {
            /*
         *UDH can also be referred to the WDP
        0B   Length of WDP header (11 bytes)
0504 Header info
0B84 Destination port (2948 = WAP Push)
0B84 Source port (2948)
00   Header info
03   Multi-SMS Header Length (SAR)
27   Datagram Reference (must be identical for all the Multi-SMS)
02   Total number of SMS (2)
01   This SMS reference (first SMS)
...
*/

    var $_lengthHeader;
    var $_multiLengthHeader;
    var $_headerInfo;
    var $_destinationPort;
    var $_sourcePort;
    var $_datagramReference;
    var $_smsReference;
    var $_UDHHeader;

    var $srcPort;
    var $destPort;
    var $multiSMS = false;
    var $numOfSMSs;

    //1 = Hex, 2 = Int, 3 = short code
    var $portType = 1;

    var $portTypesArr = array (
                    'OTA' => '0B84'

    );


    function UDHGenerator($config = array())
    {
        $this->CI =& get_instance();
        if (count($config) > 0)
        {
          $this->initialize($config);
        }
    }

    function initialize($config = array())
    {

        foreach ($config as $key => $val)
        {
            $this->$key = $val;
        }
    }


    function generateUDH($multiSMS = false, $numberSMSs = null, $smsNumber = null)
    {
        $tempUDH = $this->_generateHeaderInfo();

        if($this->_encodePorts())
        {
            $tempUDH .=$this->_destinationPort.$this->_sourcePort;
        }
        else
            return false;

        //generate multiSMS header information
        if($multiSMS || $this->multiSMS)
        {
            $tempUDH .= '00';
            $tempMulti = '27'; //Datagram Reference (must be identical for all the Multi-SMS)
            $tempMulti .= $this->_numToHex($numberSMSs);
            $tempMulti .= $this->_numToHex($smsNumber);
            $this->_multiLengthHeader = $this->_generateLength($tempMulti);
            $tempUDH .= $this->_multiLengthHeader.$tempMulti;


        }

        $this->_lengthHeader = $this->_generateLength($tempUDH);

        $this->_UDHHeader = $this->_lengthHeader.$tempUDH;
        return $this->_UDHHeader;
    }

    function getUDH()
    {
        if(isset($this->_UDHHeader))
        {
            return $this->_UDHHeader;
        }
        else
            return '';
    }


    function _encodePorts()
    {
        switch ($this->portType) {
            case 1:
                //ports given in already encoded to hex
                if(isset($this->srcPort) && isset($this->destPort))
                {
                    $this->_destinationPort = $this->destPort;
                    $this->_sourcePort = $this->srcPort;
                }
                else
                {
                    return false;
                }
                break;
            case 2:
                //ports given in as integer
                if(is_int($this->srcPort) && is_int($this->destPort))
                {
                    $this->_destinationPort = dechex($this->destPort);
                    $this->_sourcePort = dechex($this->srcPort);
                }
                else
                {
                    return false;
                }
                break;
            case 3:
                if(array_key_exists($this->srcPort, $this->portTypesArr) && array_key_exists($this->destPort, $this->portTypesArr))
                {
                    $this->_destinationPort = $this->portTypesArr[$this->destPort];
                    $this->_sourcePort = $this->portTypesArr[$this->srcPort];
                }
                else
                {
                    return false;
                }
                break;
            default:
                return false;
        }
        return true;
    }

    function _generateHeaderInfo()
    {
        //hardcoded for now until I know what other valid values can be here
        return '0504';
    }

    function _generateLength($str)
    {
          $temp = dechex(strlen($str) / 2);
          if(strlen($temp) < 2)
              $temp = '0'.$temp;
          return $temp;
    }

    function _numToHex($num)
    {
          $temp = dechex($num);
          if(strlen($temp) < 2)
              $temp = '0'.$temp;
          return $temp;
    }
}
?>