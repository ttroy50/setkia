<?php if (!defined('BASEPATH')) { die('No direct script access allowed'); }
/*
* Codeigniter library for linking into the WBXML Encoder
 */
//include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/Encoder.php';
/**
 * Description of WBXMLEncoder
 *
 * @author ttroy
 */
class WBXMLEncoder{

    /**
    * XML to be encoded
    * @private mixed
    */

    var $_xml;

    /**
    * WBXML encoder
    * @private XML_WBXML_Encoder
    */
    
    var $_encoder;
    /**
    * WBXLM output
    * @public mixed
    */
    var $wbxml;

    var $error;

    function WBXMLEncoder ($xml = null) {
        $this->CI =& get_instance();

        $this->_xml = $xml;
       // $this->_encoder = &new XML_WBXML_Encoder();
    }

     /**
    * Encode the xml to wbxml
    * @param mixed The xml to encode. If null the xml passed throug the constructor is used
    * @access public
    */
    function encode($xml = null)
    {
        if(isset($xml) && isset($this->_encoder))
        {
            $this->wbxml = $this->_encoder->encode($xml);

            if(strpos($this->wbxml, 'XML Error') !== false)
            {
                $this->error = $this->wbxml;
                $this->wbxml = '';
                return false;
            }

            return true;
        }
        else if(isset($this->_xml) && isset($this->_encoder))
        {
            $this->wbxml = $this->_encoder->encode($this->_xml);

            if(strpos($this->wbxml, 'XML Error') !== false)
            {
                $this->error = $this->wbxml;
                $this->wbxml = '';
                return false;
            }

            return true;
        }
        else
        {
            return false;
        }

    }

    function encode_libwbxml($xml = null)
    {
        $this->CI->load->helper('file');
        $now = microtime();
        $salt = 'salt';
        for($i = 0; $i < 8; $i++)
        {
            $salt .= rand();
        }
        $filename = sha1($now.$salt).'.xml';
        $basepath = '/hsphere/local/home/ttroy/setkia.com//uploads/temp/';
        $xmlpath = $basepath.$filename;

        $wbxmlFile = sha1($now.$salt).'.wbxml';
        $wbxmlpath = $basepath.$wbxmlFile;

        if(isset($xml))
        {
            write_file($xmlpath, $xml);
            
            $command = 'xml2wbxml -o '.$wbxmlpath.' '.$xmlpath;
            
            echo exec($command, $output, $ret);
            $worked = false;
            
            if(file_exists($wbxmlpath))
            {
                $worked = true;
            }

            if($worked)
            {
                $wbxmloutput = read_file($wbxmlpath);

                //may want to check if wbxmloutput is valid
                $this->wbxml = $wbxmloutput;
                unlink($wbxmlpath);
                unlink($xmlpath);
                return true;
            }
            else
            {
                
                unlink($xmlpath);
                $this->error = 'XML Error : Unable to encode to wbxml';
                return false;
            }

        }
        else if (isset($this->_xml))
        {
            write_file($xmlpath, $this->_xml);

            $command = 'xml2wbxml -o '.$wbxmlpath.' '.$xmlpath;

            echo exec($command, $output, $ret);
            $worked = false;

            if(file_exists($wbxmlpath))
            {
                $worked = true;
            }

            if($worked)
            {
                $wbxmloutput = read_file($wbxmlpath);

                //may want to check if wbxmloutput is valid
                $this->wbxml = $wbxmloutput;
                unlink($wbxmlpath);
                unlink($xmlpath);
                return true;
            }
            else
            {

                unlink($xmlpath);
                $this->error = 'XML Error : Unable to encode to wbxml';
                return false;
            }
        }
        else
        {
            $this->error = 'XML Error : No XML to encode';
            return false;
        }
    }

    /**
    * Encode a xml file to wbxml
    * @param mixed The xml file to encode
    * @access public
    */
    function encodeFromFile($xmlfile)
    {
        $xml_in = file_get_contents($xmlfile, 'rb');
        if($xml_in === false)
            return false;


        $this->wbxml = $this->_encoder->encode($xml_in);
            return true;

    }


            /**
    * Return the WBXML as encoded (hex)
    * @return wbxml as hex string or -1
    * @access public
    */
    function getWBXML()
    {
        if(isset($this->wbxml))
        {
            return $this->wbxml;
        }
        else
        {
            return -1;
        }
    }


    /**
     * 
    * Return the WBXML after re-encoding to make it URL safe
    * @return wbxml string or -1
    * @access public
    */
    function getWBXMLForURL()
    {
        if(!isset($this->wbxml))
            return -1;

        //echo $this->wbxml;
            
        $ret = '';
        for ($a = 0; $a < strlen($this->wbxml); $a++)
        {
            $chr = dechex(ord($this->wbxml[$a]));
            if(strlen($chr) == 1)
                $ret .= '0'.$chr;
            else
                $ret .= $chr;
        }
        return $ret;
    }

    function getError()
    {
        if(isset($this->error))
            return $this->error;
        else
            return -1;
    }
    /**
    * Write the wbxml to a file
    * @param filename mixed. The path to the output file
    * @access public
    */
    function outputToFile($filename)
    {
        $handle = fopen($filename, 'wb');
        if (!$handle) {
            return false;
        }

        // Write $input to our opened file.
        if (fwrite($handle, $this->wbxml) === FALSE) {
            fclose($handle);
            return false;
        }

        fclose($handle);

        return true;

    }
}
