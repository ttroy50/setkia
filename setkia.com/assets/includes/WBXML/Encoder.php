<?php

include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/ContentHandler.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTDManager.php';

/**
 * From Binary XML Content Format Specification Version 1.3, 25 July 2001
 * found at http://www.wapforum.org
 *
 * $Horde: framework/XML_WBXML/WBXML/Encoder.php,v 1.25.10.18 2009/01/06 15:23:50 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Anthony Mills <amills@pyramid6.com>
 * @package XML_WBXML
 */
class XML_WBXML_Encoder extends XML_WBXML_ContentHandler {

    var $_strings = array();

    var $_stringTable;

    var $_hasWrittenHeader = false;

    var $_dtd;
    var $_prevDTD;

    var $_output = '';

    var $_splitXML;

    var $_uris = array();

    var $_uriNums = array();

    var $_currentURI;

    var $_subParser = null;
    var $_subParserStack = 0;

    var $_doctype = "";
    var $_attCodePage = 0;
    var $_tagCodePage = 0;

    /**
     * The XML parser.
     *
     * @var resource
     */
    var $_parser;

    /**
     * The DTD Manager.
     *
     * @var XML_WBXML_DTDManager
     */
    var $_dtdManager;

    /**
     * Constructor.
     */
    function XML_WBXML_Encoder()
    {
        //print "initializing XML_WBXMK_Encoder\n";
        $this->_dtdManager = &new XML_WBXML_DTDManager();
        $this->_stringTable = &new XML_WBXML_HashTable();
    }

    /**
     * Take the input $xml and turn it into WBXML. This is _not_ the
     * intended way of using this class. It is derived from
     * Contenthandler and one should use it as a ContentHandler and
     * produce the XML-structure with startElement(), endElement(),
     * and characters().
     */
    function encode($xml)
    {
        //print "encode an XML string to WBXML\n";
        //print "xml is ".$xml."\n";

            
        //manual hack to find if it's a wap-provisioning doc type
        if(strpos($xml, "wap-provisioningdoc") !== false)
            $this->_doctype = "-//WAPFORUM//DTD PROV 1.0//EN";

        $xml = preg_replace('/<\?xml version=\"1\.0\"\?>/', '', $xml);
        $xml = preg_replace('/<!DOCTYPE [^>]*>/', '', $xml);
        
        //print "xml after change is \n".$xml."\n";
            // Create the XML parser and set method references.


        $this->_splitXML = explode("\n", $xml);

        //foreach($this->_splitXML as $mytemp)
        //    echo "mytempline is ".$mytemp."\n";


        $this->_parser = xml_parser_create_ns($this->_charset);
        xml_set_object($this->_parser, $this);
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($this->_parser, '_startElement', '_endElement');
        xml_set_character_data_handler($this->_parser, '_characters');
        xml_set_processing_instruction_handler($this->_parser, '');
        xml_set_external_entity_ref_handler($this->_parser, '');

        if (!xml_parse($this->_parser, $xml)) {
            //print "parse error \n";
            return $this->raiseError(sprintf('XML error: %s at line %d',
                                             xml_error_string(xml_get_error_code($this->_parser)),
                                             xml_get_current_line_number($this->_parser)));
        }

        xml_parser_free($this->_parser);

        if($this->hasError())
            return $this->getErrStr();
            
        //print "encode output is ".$this->_output."\n";
        return $this->_output;
        //print "finished encode\n";
    }

    /**
     * This will write the correct headers.
     */
    function writeHeader($uri)
    {
        //print "writeHeader Start. \n";
        //print "++++++++++++++++++++++++++++++++++++URI is ".$uri."\n";

        if(isset($uri) && $uri !== "")
            $this->_dtd = $this->_dtdManager->getInstanceURI($uri);
        else
            $this->_dtd = $this->_dtdManager->getInstance($this->_doctype);
            
        if (!$this->_dtd) {
            // TODO: proper error handling
            die('Unable to find dtd for ' . $uri);
        }
        $dpiString = $this->_dtd->getDPI();
        //print "dpiString is ".$dpiString."\n";

        // Set Version Number from Section 5.4
        // version = u_int8
        // currently 1, 2 or 3
        $this->writeVersionNumber($this->_wbxmlVersion);

        // Set Document Public Idetifier from Section 5.5
        // publicid = mb_u_int32 | ( zero index )
        // zero = u_int8
        // containing the value zero (0)
        // The actual DPI is determined after the String Table is read.
        $this->writeDocumentPublicIdentifier($dpiString, $this->_strings);

        // Set Charset from 5.6
        // charset = mb_u_int32
        $this->writeCharset($this->_charset);

        // Set String Table from 5.7
        // strb1 = length *byte
        $this->writeStringTable($this->_strings, $this->_charset, $this->_stringTable);

        $this->_currentURI = $uri;

        $this->_hasWrittenHeader = true;
    }

    function writeVersionNumber($version)
    {
        //print "writeVersionNumber version is ".$version."\n";
        $this->_output .= chr($version);
    }

    function writeDocumentPublicIdentifier($dpiString, &$strings)
    {
        //print "writeDocumentPublicIdentifier dpiString is".$dpiString."\n";
        $i = 0;

        // The OMA test suite doesn't like DPI as integer code.
        // So don't try lookup and always send full DPI string.
        $i = XML_WBXML::getDPIInt($dpiString);

        if ($i == 0) {
            $strings[0] = $dpiString;
            $this->_output .= chr(0);
            $this->_output .= chr(0);
        } else {
            XML_WBXML::intToMBUInt32($this->_output, $i);
        }
    }

    function writeCharset($charset)
    {
        //print "wirteCharset charset is ".$charset."\n";
        $cs = XML_WBXML::getCharsetInt($charset);

        if ($cs == 0) {
            return $this->raiseError('XML Error : Unsupported Charset: ' . $charset);
        } else {
            XML_WBXML::intToMBUInt32($this->_output, $cs);
        }
    }

    function writeStringTable($strings, $charset, $stringTable)
    {
        //print "writeStringTable\n";

        $stringBytes = array();
        $count = 0;
        foreach ($strings as $str) {
            $bytes = $this->_getBytes($str, $charset);
            $stringBytes = array_merge($stringBytes, $bytes);
            $nullLength = $this->_addNullByte($bytes);
            $this->_stringTable->set($str, $count);
            $count += count($bytes) + $nullLength;
        }

        XML_WBXML::intToMBUInt32($this->_output, count($stringBytes));
        $this->_output .= implode('', $stringBytes);
    }

    function writeString($str, $cs)
    {
        //print "writeString string is ".$str."\n";
        $bytes = $this->_getBytes($str, $cs);
        $this->_output .= implode('', $bytes);
        $this->writeNull($cs);
    }

    function writeNull($charset)
    {
        //print "writeNull\n";
        $this->_output .= chr(0);
        return 1;
    }

    function _addNullByte(&$bytes)
    {
        //print "_addNullByte \n";
        $bytes[] = chr(0);
        return 1;
    }

    function _getBytes($string, $cs)
    {
        //print "_getBytes\n";
        $nbytes = strlen($string);
        //print "number of bytes is ".$nbytes."\n";
        $bytes = array();
        for ($i = 0; $i < $nbytes; $i++) {
            $bytes[] = $string{$i};
        }

        return $bytes;
    }

    function _splitURI($tag)
    {
        //print "_splitURI tag is ".$tag."\n";
        $parts = explode(':', $tag);
        $name = array_pop($parts);
        $uri = implode(':', $parts);
        return array($uri, $name);
    }

    function startElement($uri, $name, $attributes = array())
    {
        //print "encoder StartElement\n";

       // print "***************************************************** output in StartElement is ".$this->ordstr($this->_output)."\n";
        if ($this->_subParser == null) {
            //print "subParser is null\n";
            if (!$this->_hasWrittenHeader) {
                $this->writeHeader($uri);
            }
            if ($this->_currentURI != $uri) {
                print "currentURI is not = uri currentURI is ".$this->currentURI." uri is ".$uri."\n";
                $this->changecodepageURI($uri);
            }
            if ($this->_subParser == null) {
                $linenum = xml_get_current_line_number($this->_parser);
                //print "!!!!!!!!!!!!!!! parser line is ".$linenum."\n";
                //print "!!!!!!!!!!!!!!! xml line is    ".$this->_splitXML[$linenum-1]."\n";
                if(strpos($this->_splitXML[$linenum-1], '/>') === false)
                {
                    //print "paramater has content \n";
                    $hasContent = true;
                }
                else
                {
                    //print "Doesn't have content \n";

                    $hasContent = false;
                }

                $this->writeTag($name, $attributes, $hasContent, $this->_charset);
            } else {
                $this->_subParser->startElement($uri, $name, $attributes);
            }
        } else {
            $this->_subParserStack++;
            $this->_subParser->startElement($uri, $name, $attributes);
        }
    }

        function ordstr($str)
    {
        $ret = '';
        for ($a = 0; $a < strlen($str); $a++)
        {
            $chr = dechex(ord($str[$a]));
            if(strlen($chr) == 1)
                $ret .= '0'.$chr;
            else
                $ret .= $chr;
        }
        return $ret;
    }

    function _startElement($parser, $tag, $attributes)
    {
        //print "encoder _StartElement\n";
        //print "_doctype is ".$this->_doctype."\n";
        //print "***************************************************** output in _startElement is ".$this->ordstr($this->_output)."\n";
        //if(isset($this->_doctype) == false)
            list($uri, $name) = $this->_splitURI($tag);

        //print "+++++++++++++++++++++++++uri is ".$uri. "name is ".$name."\n";
        //if(isset($this->_dtd))
            //print "DTD URI is ".$this->_dtd->getURI()."\n";
        $this->startElement($uri, $name, $attributes);
    }

    function opaque($o)
    {
        //print "encoder opaque\n";
        $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_OPAQUE);
        XML_WBXML::intToMBUInt32($this->_output, strlen($o));
        $this->_output .= $o;
    }

    function characters($chars)
    {
        //print "encoder characters\n";
        $chars = trim($chars);

        if (strlen($chars)) {
            /* We definitely don't want any whitespace. */
            if ($this->_subParser == null) {
                $i = $this->_stringTable->get($chars);
                if ($i != null) {
                    $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_STR_T);
                    XML_WBXML::intToMBUInt32($this->_output, $i);
                } else {
                    $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_STR_I);
                    $this->writeString($chars, $this->_charset);
                }
            } else {
                $this->_subParser->characters($chars);
            }
        }
    }

    function _characters($parser, $chars)
    {
        $this->characters($chars);
    }

    function writeTag($name, $attrs, $hasContent, $cs)
    {
        //print "encoder writeTag\n";
        if ($attrs != null && !count($attrs)) {
            $attrs = null;
            //print "setting attrs to null\n";
        }

        $t = $this->_dtd->toTagInt($name);
        //hack to switchcodepage if no URI found

        $codePageSwitch = false;
        $prevCodePage;
        if ($t == -1)
        {
            //print "tag not found. tag is ".$name."\n";
            if($this->_tagCodePage == 0)
            {
                //print "switch to codepage 1\n";
                $this->_prevDTD = $this->_dtd;
                $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, 1);
                $this->_tagCodePage = 1;
                $this->_attCodePage = 1;
                $codePageSwitch = true;
                $prevCodePage = 0;
            }
            else if($this->_tagCodePage == 1)
            {
                //print "switch to codepage 0\n";
                $this->_prevDTD = $this->_dtd;
                $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, 0);
                $this->_tagCodePage = 0;
                $this->_attCodePage = 0;
                $codePageSwitch = true;
                $prevCodePage = 1;
            }

            $t = $this->_dtd->toTagInt($name);
            if($t == -1)
            {

                //print "tag not found. tag is ".$name."\n";
                if($this->_tagCodePage == 0)
                {
                    //print "switch to codepage 1\n";
                    $this->_prevDTD = $this->_dtd;
                    $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, 1);
                    $this->_tagCodePage = 1;
                    $this->_attCodePage = 1;
                    $codePageSwitch = false;
                   // $prevCodePage = 0;
                }
                else if($this->_tagCodePage == 1)
                {
                    //print "switch to codepage 0\n";
                    $this->_prevDTD = $this->_dtd;
                    $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, 0);
                    $this->_tagCodePage = 0;
                    $this->_attCodePage = 0;
                    $codePageSwitch = false;
                   // $prevCodePage = 1;
                }
            }
        }

        //print "tagInt for ".$name." is ".$t."\n";
        if ($t == -1) {
            $i = $this->_stringTable->get($name);
            if ($i == null) {
                return $this->raiseError('XML Error : '.$name . ' is not found in String Table or DTD');
            } else {
                if ($attrs == null && !$hasContent) {
                    $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_LITERAL);
                } elseif ($attrs == null && $hasContent) {
                    $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_LITERAL_A);
                } elseif ($attrs != null && $hasContent) {
                    $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_LITERAL_C);
                } elseif ($attrs != null && !$hasContent) {
                    $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_LITERAL_AC);
                }

                XML_WBXML::intToMBUInt32($this->_output, $i);
            }
        } else {
            if($codePageSwitch)
            {
                //print "=== adding codepage switch output is ".$this->ordstr(chr(XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE)).$this->ordstr(chr($this->_tagCodePage))."\n";
                $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE);
                $this->_output .= chr($this->_tagCodePage);
            }
            
            if ($attrs == null && !$hasContent) {

                $this->_output .= chr($t);
                //print "tag has no attributes and no content\n";
            } elseif ($attrs == null && $hasContent) {
                $this->_temp = strlen($this->_output); //aaa
                $this->_output .= chr($t | 64);
                //print "tag has no attributes but has content\n";
            } elseif ($attrs != null && $hasContent) {
                $this->_temp = strlen($this->_output); //aaa
                $this->_output .= chr($t | 192);
                //print "=== output is ".$this->ordstr(chr($t | 192))."\n";
                $test = $t | 192;
                //print "t is ".$t."\n";
                //print "test is ".$test."\n";
                //print "tag has attributes and content\n";
            } elseif ($attrs != null && !$hasContent) {

                $this->_output .= chr($t | 128);
                //print "tag has attributes and no content\n";
            }
        }

        if ($attrs != null && is_array($attrs) && count($attrs) > 0) {
            $this->writeAttributes($attrs, $cs, $hasContent);
        }
    }

    function writeAttributes($attrs, $cs, $hasContent)
    {
        //print "encoder writeAttributes\n";
        foreach ($attrs as $name => $value) {
            $ret = $this->writeAttribute($name, $value, $cs);
        }

        //ret may not be needed however has content should be here (I think).
        //If hasContent is set then you should write the end of the attribute otherwise you shouldn't as it is ended with the tag
        // i.e. we shouldn't have 01 01 which stands for end attribute end tag right beside each other
        if($ret || $hasContent)
        {
            //print "=== writing attribute end ".$this->ordstr(chr(XML_WBXML_GLOBAL_TOKEN_END));
            $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_END);
        }
    }

    function writeAttribute($name, $value, $cs)
    {
        //returns if we should write global token end after  write attributes
        //this is only false if the last thing written is a LITERAL. This may not be needed, however leavign it as I don't think it will cause any issues
        $ret = true;
        ////print "encoder writeAttribute\n";
        ////print "name is ".$name." : value is ".$value."\n";
        $codePageSwitch = false;
        $prevCodePage;
        $hasAttStart = false;
        $hasAttValue = false;

        if($this->_dtd->hasAttributeStart($name, $value))
        {
            ////print "has Attribute Start \n";
            $hasAttStart = true;
        }
        else
        {
            //print "doesn't have attribute start checking in other codepage ".$name."\n";
            if($this->_attCodePage == 0)
            {
                //print "!!!!!!!!!!!!!!!!switch to codepage 1\n";
                $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, 1);
                $this->_tagCodePage = 1;
                $this->_attCodePage = 1;
                $codePageSwitch = true;
                $prevCodePage = 0;
            }
            else if($this->_attCodePage == 1)
            {
                //print "!!!!!!!!!!!!!!!!!switch to codepage 0\n";
                $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, 0);
                $this->_tagCodePage = 0;
                $this->_attCodePage = 0;
                $codePageSwitch = true;
                $prevCodePage = 1;
            }

            $hasAttStart = $this->_dtd->hasAttributeStart($name, $value);
            if(!$hasAttStart)
            {
                //print "attributeStart not found in other codepage. attribute is name = ".$name." : value =".$value."\n";
                if($this->_tagCodePage == 0)
                {
                    //print "%%%%%%%%%%%%%%%%%%%%%%%switch back to codepage ".$prevCodePage."\n";
                    $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, $prevCodePage);
                    $this->_tagCodePage = 1;
                    $this->_attCodePage = 1;
                    $codePageSwitch = false;
                   // $prevCodePage = 0;
                }
                else if($this->_tagCodePage == 1)
                {
                    //print "+%%%%%%%%%%%%%%%%%%%%%%%555switch back to codepage ".$prevCodePage."\n";
                    $this->_dtd = $this->_dtdManager->getInstance($this->_doctype, $prevCodePage);
                    $this->_tagCodePage = 0;
                    $this->_attCodePage = 0;
                    $codePageSwitch = false;
                   // $prevCodePage = 1;
                }
            }
            else
            {
                //print "^^^^^^^^^^^^Didn't switch back codepage so element must be found\n";
            }
                

        }
        //print "DTD URI is ".$this->_dtd->getURI()."\n";
        if($this->_dtd->hasAttributeValue($name))
        {
            //print "has Attribute Value\n";
            $hasAttValue= true;
        }

        if($codePageSwitch)
        {
            //print "codepage is ".$this->_tagCodePage."\n";
            //print "=== adding codepage switch output is ".$this->ordstr(chr(XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE)).$this->ordstr(chr($this->_tagCodePage))."\n";
            $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE);
            $this->_output .= chr($this->_tagCodePage);
        }

        $as;
        if($hasAttStart)
        {
            $as = $this->_dtd->toAttributeStartInt($name."=".$value);
            //print "attribute start is ".$as."\n";
            if(!$as)
            {
                //print "Attribute is bigger than just name value attribute start ".$name."=".$value."\n";
                $as = $this->_dtd->toAttributeStartInt($name."=");
                if(!$as)
                {
                    //print "attribute not found when only looking for name= Looked for.".$name."=\n";

                }
                else
                {
                    //print "name without value is attribute start\n";
                    //$this->_output .= $as;
                    //print "=== writing attribute wihtout value".$this->ordstr($as)."\n";
                    XML_WBXML::intToMBUInt32($this->_output, $as);
                    //print "attributeStart encoded is ".$as."\n";
                    //print "current output is ".$this->ordstr($this->_output)."\n";
                    //don't return because we still need to write string
                }

            }
            else
            {
                //print "name value is attribute start\n";
                //$this->_output .= $as;
                //print "=== writing attribute ".$this->ordstr($as)."\n";
                XML_WBXML::intToMBUInt32($this->_output, $as);
                //print "attributeStart encoded is ".$as."\n";
                //print "current output is ".$this->ordstr($this->_output)."\n";
                return $ret;
            }
        }

        if(!$hasAttStart)
        {
            $asnv = $this->_dtd->toAttributeStartInt($name."=");
            if($asnv == -1)
            {

            }
            else
            {
                //print "attribut wihtout value is a valid attribute\n";
                XML_WBXML::intToMBUInt32($this->_output, $asnv);
                //print "=== writing attribute ".$this->ordstr($asnv)."\n";
                //print "current output is ".$this->ordstr($this->_output)."\n";
            }
        }

        //should probably be changed to toAttributeStartStr or may need to get both in a toAttribute function
        //$a = -1;//$this->_dtd->toAttribute($name);
       /* if (!$hasAttValue) {
            $i = $this->_stringTable->get($value);
            if ($i == null) {
                return $this->raiseError($value . ' is not found in String Table or DTD');
            } else {
                $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_LITERAL);
                XML_WBXML::intToMBUInt32($this->_output, $i);
            }
        }*/
        if(!$hasAttValue)
        {
            $i = $this->_stringTable->get($value);
            if ($i != null) {
                $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_STR_T);
                XML_WBXML::intToMBUInt32($this->_output, $i);
                //print "current output is ".$this->ordstr($this->output)."\n";
            } else {
                $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_STR_I);
                $this->writeString($value, $cs);
                //print "current output is ".$this->ordstr($this->_output)."\n";
                $ret = false;
            }
        }

        if($hasAttValue)
        {
            //print "outputting attribute value";
            $av = $this->_dtd->toAttributeValueInt($value);
            XML_WBXML::intToMBUInt32($this->_output, $av);
            //print "current output is ".$this->ordstr($this->_output)."\n";
        }
        return $ret;

    }

    function endElement($uri, $name)
    {
        //print "\nendElement\n";
        if ($this->_subParser == null) {
            //print "subparser is null\n";
            //print "XML_WXBML_GLOBAL_TOKEN_END is ".$this->ordstr(chr(XML_WBXML_GLOBAL_TOKEN_END))."\n";
            $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_END);
        } else {
            $this->_subParser->endElement($uri, $name);
            $this->_subParserStack--;

            if ($this->_subParserStack == 0) {
                $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_OPAQUE);

                XML_WBXML::intToMBUInt32($this->_output,
                                         strlen($this->_subParser->getOutput()));
                $this->_output .= $this->_subParser->getOutput();

                $this->_subParser = null;
            }
        }
        //print "output at end of endElement is ".$this->ordstr($this->_output)."\n";
    }

    function _endElement($parser, $tag)
    {
        //print "output at start of _endElement is ".$this->ordstr($this->_output)."\n";
        //print "_endelement \n";
        list($uri, $name) = $this->_splitURI($tag);
        $this->endElement($uri, $name);
    }

    function changecodepageURI($uri)
    {
        //print "++++++++++++++++++++++++++changecodepageURI uri is ".$uri."\n";
        // @todo: this is a hack!
        if ($this->_dtd->getVersion() == 2 && !preg_match('/1\.2$/', $uri)) {
            $uri .= '1.2';
        }
        if ($this->_dtd->getVersion() == 1 && !preg_match('/1\.1$/', $uri)) {
            $uri .= '1.1';
        }
        if ($this->_dtd->getVersion() == 0 && !preg_match('/1\.0$/', $uri)) {
            $uri .= '1.0';
        }

        $cp = $this->_dtd->toCodePageURI($uri);
        if (strlen($cp)) {
            $this->_dtd = $this->_dtdManager->getInstanceURI($uri);

            $this->_output .= chr(XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE);
            $this->_output .= chr($cp);
            $this->_currentURI = $uri;

        } else {
            $this->_subParser = &new XML_WBXML_Encoder(true);
            $this->_subParserStack = 1;
        }
    }

    function changeCodePage($codepage)
    {
       // &$this->_dtdManager->getInstance($this->_doctype, 1);
    }

    /**
     * Getter for property output.
     */
    function getOutput()
    {
        return $this->_output;
    }

    function getOutputSize()
    {
        return strlen($this->_output);
    }

}
