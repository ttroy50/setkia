<?php if (!defined('BASEPATH')) { die('No direct script access allowed'); }
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WSPGenerator
 *
 * @author ttroy
 */
class WSPGenerator{
            /*
        01                                       TID - [WSP] Ch. 8.2.1
06                                       PDU Type (PUSH) - [WSP] Ch. 8.2.1 + App. A
2F1F2D                                   Headers Length - [WSP] Ch. 8.2.4.1
B6                                       Content-Type - application/vnd.wap.connectivity-wbxml
9181                                     SEC - USERPIN
92                                       MAC
4430453033344330383634453545373244464541
3645334234333032324133324232333941463736 MAC value
00                                       End of MAC-value
...                                      WBXML content (see the example)
*/
    var $TID = '01';
    var $secType;
    var $pduType;
    var $pin;
    var $contentType;
    var $wbxml;

    var $_PDUType;
    var $_headerLength;
    var $_contentType;
    var $_secHeader;
    var $_secType;

    var $_MAC = '92';
    var $_MACValue;
    var $_endOfMAC = '00';

    var $_wspHeader;

    //codes taken from http://www.wapforum.org/wina/wsp-content-type.htm
    //Code is code from site + 0x80 e.g. application/vnd.wap.connectivity-wbxml is 0x36 + 0x80 = 0xB6
    //There are alot more content types. I don't need them for now so leaving them out.
    var $_contentTypesArr = array(
                    "application/vnd.wap.connectivity-wbxml" => "B6",
                    "text/x-vCalendar" => "86",
                    "text/x-vCard" => "87",
                    "text/vnd.wap.wml" => "88",
                    "application/vnd.syncml.dm+wbxml" => "C2",
                    "application/vnd.wap.wbxml" => "A9",
                    //short makes for content Types
                    "wap.connectivity-wbxml" => "B6",
                    "vCalendar" => "86",
                    "vCard" => "87",
                    "wap.wml" => "88",
                    "sync.dm+wbxml" => "C2",
                    "wap.wbxml" => "A9"
                    );

      //pdu types from WAP-230-WSP-20010705-a appendix A. Again there are more in the document
      //Only really interested in push
      var $_pduTypesArr = array(
                    "connect" => "01",
                    "connectReply" => "02",
                    "push" => "06",
                    "confirmedPush" => "07"
      );

      var $_secTypesArr = array(
                    "userpin" => "81",
                    "netwpin" => "80",
                    "usernetwpin" => "82",
                    "userpinmac" => "83"
      );

      function WSPGenerator($config = array())
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

      function generateWSP($wbxml = null, $pin = null)
      {
          
          //add tid. Reset's teh _wspHeader if the class had been used before
          if(!isset($this->TID))
            return false;

            
          $this->_wspHeader = $this->TID;

          //add the pdu type
          if(!$this->_generatePDUType())
            return false;

          $this->_wspHeader .= $this->_PDUType;


          //generate the rest of the header then the length before adding to wsp header
          if(!$this->_generateContentType())
            return false;

          $tempHeader = $this->_contentType;
          if($this->_secHeader())
          {
              //sec header in use so we need to generate the mac
              if(!$this->_generateMAC($wbxml, $pin))
              {
                  return false;
              }
              $tempHeader .= $this->_secHeader.$this->_MAC.$this->_MACValue.$this->_endOfMAC;
              $this->_generateLength($tempHeader);

              $tempHeader = '1F'.$this->_headerLength.$tempHeader;
          }

          

          $this->_generateLength($tempHeader);
          
          $this->_wspHeader .= $this->_headerLength.$tempHeader;

          return $this->_wspHeader;


          
          //tid.pdutype.headerlength.contenttype.sec.mac.macvalue.endofmacvlaue
      }

    function getWSP()
    {
        if(isset($this->_WSPHeader))
        {
            return $this->_WSPHeader;
        }
        else
            return '';
    }

      function _generateLength($str)
      {
            $temp = dechex(strlen($str) / 2);
            if(strlen($temp) < 2)
                $temp = '0'.$temp;
            $this->_headerLength = $temp;
            return true;
      }

      function _secHeader()
      {
          if(array_key_exists($this->secType, $this->_secTypesArr))
          {
              $this->_secHeader = '91'.$this->_secTypesArr[$this->secType];
              return true;
          }
          else
            return false;
      }

      //input should be pin e.g. 1234 or IMSI
      function _generateMAC($wbxml = null, $pin = null)
      {
          if(isset($wbxml))
            $this->wbxml = $wbxml;
          //hmac generation now working properly so need to work on it
          if(isset($pin))
          {
              //use the pin passed in
              $hmac = hash_hmac('sha1', $this->wbxml, $pin);
              //need to encode properly for URL howto???
              $this->_MACValue = $this->_str_hex($hmac);

          }
          else
          {
              //use the pin set in the constructor. If it's not set there is an error so return false;
              if(!isset($this->pin))
                return false;
                
              $hmac = hash_hmac('sha1', $this->wbxml, $this->pin);
              $this->_MACValue = $this->_str_hex($hmac);

          }
          

          return true;
      }

      function _str_hex($string){
            $hex='';
            for ($i=0; $i < strlen($string); $i++){
                $hex .= dechex(ord($string[$i]));
            }
            return $hex;
       }

      function _generateContentType()
      {
          
          if(array_key_exists($this->contentType, $this->_contentTypesArr )){
            $this->_contentType = $this->_contentTypesArr[$this->contentType];
            return true;
          }
          else
            return false;
          
      }

      function _generatePDUType()
      {
          if(array_key_exists($this->pduType, $this->_pduTypesArr )){
            $this->_PDUType = $this->_pduTypesArr[$this->pduType];
            return true;
          }
          else {
            return false;
          }
      }
}
?>
