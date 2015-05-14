<?php

include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTDManager.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/ContentHandler.php';

/**
 * From Binary XML Content Format Specification Version 1.3, 25 July 2001
 * found at http://www.wapforum.org
 *
 * $Horde: framework/XML_WBXML/WBXML/Decoder.php,v 1.22.10.12 2009/01/06 15:23:50 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Anthony Mills <amills@pyramid6.com>
 * @package XML_WBXML
 */
class XML_WBXML_Decoder extends XML_WBXML_ContentHandler {

    /**
     * Document Public Identifier type
     * 1 mb_u_int32 well known type
     * 2 string table
     * from spec but converted into a string.
     *
     * Document Public Identifier
     * Used with dpiType.
     */
    var $_dpi;

    /**
     * String table as defined in 5.7
     */
    var $_stringTable = array();

    /**
     * Content handler.
     * Currently just outputs raw XML.
     */
    var $_ch;

    var $_tagDTD;

    var $_prevAttributeDTD;

    var $_attributeDTD;

    /**
     * State variables.
     */
    var $_tagStack = array();
    var $_isAttribute;
    var $_isData = false;

    var $_error = false;

    /**
     * The DTD Manager.
     *
     * @var XML_WBXML_DTDManager
     */
    var $_dtdManager;

    /**
     * The string position.
     *
     * @var integer
     */
    var $_strpos;

    /**
     * Constructor.
     */
    function XML_WBXML_Decoder()
    {
        $this->_dtdManager = &new XML_WBXML_DTDManager();
    }

    /**
     * Sets the contentHandler that will receive the output of the
     * decoding.
     *
     * @param XML_WBXML_ContentHandler $ch The contentHandler
     */
    function setContentHandler(&$ch)
    {
        $this->_ch = &$ch;
    }
    /**
     * Return one byte from the input stream.
     *
     * @param string $input  The WBXML input string.
     */
    function getByte($input)
    {
        return ord($input{$this->_strpos++});
    }

    /**
     * Takes a WBXML input document and returns decoded XML.
     * However the preferred and more effecient method is to
     * use decode() rather than decodeToString() and have an
     * appropriate contentHandler deal with the decoded data.
     *
     * @param string $wbxml  The WBXML document to decode.
     *
     * @return string  The decoded XML document.
     */
    function decodeToString($wbxml)
    {
        $this->_ch = &new XML_WBXML_ContentHandler();

        $r = $this->decode($wbxml);
        if (is_a($r, 'PEAR_Error')) {
            return $r;
        }
        return $this->_ch->getOutput();
    }

    /**
     * Takes a WBXML input document and decodes it.
     * Decoding result is directly passed to the contentHandler.
     * A contenthandler must be set using setContentHandler
     * prior to invocation of this method
     *
     * @param string $wbxml  The WBXML document to decode.
     *
     * @return mixed  True on success or PEAR_Error.
     */
    function decode($wbxml)
    {
        $this->_error = false; // reset state

        $this->_strpos = 0;

        if (empty($this->_ch)) {
            return $this->raiseError('No Contenthandler defined.');
        }

        // Get Version Number from Section 5.4
        // version = u_int8
        // currently 1, 2 or 3
        $this->_wbxmlVersion = $this->getVersionNumber($wbxml);
	
        // Get Document Public Idetifier from Section 5.5
        // publicid = mb_u_int32 | (zero index)
        // zero = u_int8
        // Containing the value zero (0)
        // The actual DPI is determined after the String Table is read.
        $dpiStruct = $this->getDocumentPublicIdentifier($wbxml);

        // Get Charset from 5.6
        // charset = mb_u_int32
        $this->_charset = $this->getCharset($wbxml);

        // Get String Table from 5.7
        // strb1 = length *byte
        $this->retrieveStringTable($wbxml);

        // Get Document Public Idetifier from Section 5.5.
        $this->_dpi = $this->getDocumentPublicIdentifierImpl($dpiStruct['dpiType'],
                                                             $dpiStruct['dpiNumber'],
                                                             $this->_stringTable);
        
        // Now the real fun begins.
        // From Sections 5.2 and 5.8


        // Default content handler.
        $this->_dtdManager = &new XML_WBXML_DTDManager();

        //print "decoder dpi is ".$this->_dpi."\n";
        // Get the starting DTD.
        $this->_tagDTD = $this->_dtdManager->getInstance($this->_dpi);
        
        if (!$this->_tagDTD) {
            return $this->raiseError('No DTD found for '
                             . $this->_dpi . '/'
                             . $dpiStruct['dpiNumber']);
        }

        $this->_attributeDTD = $this->_tagDTD;

        while (empty($this->_error) && $this->_strpos < strlen($wbxml)) {
            //echo '<br /> '.$this->_strpos.'<br />';
            //print "------------------------------------------------------------------the string position in the decode while loop is ".$this->_strpos."\n";
            $this->_decode($wbxml);
        }
        if (!empty($this->_error)) {
            return $this->_error;
        }
        return true;
    }

    function getVersionNumber($input)
    {
        $byte = $this->getByte($input);
       // echo 'getVersionNumber byte = '.$byte."\n";
        return $byte;
    }

    function getDocumentPublicIdentifier($input)
    {
        $i = XML_WBXML::MBUInt32ToInt($input, $this->_strpos);
        //echo 'getDocumentPublicIdentifier i = '.$i."\n";
        if ($i == 0) {
            return array('dpiType' => 2,
                         'dpiNumber' => $this->getByte($input));
        } else {
            return array('dpiType' => 1,
                         'dpiNumber' => $i);
        }
    }

    function getDocumentPublicIdentifierImpl($dpiType, $dpiNumber)
    {
        if ($dpiType == 1) {
            //print "getting DPI string from known list\n";
            return XML_WBXML::getDPIString($dpiNumber);
        } else {
            //print "getting DPI string from stringtable\n";
            return $this->getStringTableEntry($dpiNumber);
        }
    }

    /**
     * Returns the character encoding. Only default character
     * encodings from J2SE are supported.  From
     * http://www.iana.org/assignments/character-sets and
     * http://java.sun.com/j2se/1.4.2/docs/api/java/nio/charset/Charset.html
     */
    function getCharset($input)
    {
        $cs = XML_WBXML::MBUInt32ToInt($input, $this->_strpos);
        return XML_WBXML::getCharsetString($cs);
    }

    /**
     * Retrieves the string table.
     * The string table consists of an mb_u_int32 length
     * and then length bytes forming the table.
     * References to the string table refer to the
     * starting position of the (null terminated)
     * string in this table.
     */
    function retrieveStringTable($input)
    {
        $size = XML_WBXML::MBUInt32ToInt($input, $this->_strpos);
        //print "strpos before getting stringtable is ".$this->_strpos."\n";
       // echo "retrieveStringTable size =".$size."\n";
        $this->_stringTable = substr($input, $this->_strpos, $size);
        $this->_strpos += $size;
         //print "stringtable($size):" . $this->_stringTable ."\n";
         //print "strpos after getting stringtable is ".$this->_strpos."\n";
    }

    function getStringTableEntry($index)
    {
        if ($index >= strlen($this->_stringTable)) {
            $this->_error =
                $this->raiseError('Invalid offset ' . $index
                                  . ' value encountered around position '
                                  . $this->_strpos
                                  . '. Broken wbxml?');
            return '';
        }

        // copy of method termstr but without modification of this->_strpos

        $str = '#'; // must start with nonempty string to allow array access

        $i = 0;
        $ch = $this->_stringTable[$index++];
        if (ord($ch) == 0) {
            return ''; // don't return '#'
        }

        while (ord($ch) != 0) {
            $str[$i++] = $ch;
            if ($index >= strlen($this->_stringTable)) {
                break;
            }
            $ch = $this->_stringTable[$index++];
        }
        // //print "string table entry: $str\n";
        return $str;

    }

    function _decode($input)
    {
        $token = $this->getByte($input);
        //echo '<br />token = '.$token.'<br />';
        //print "token is ".$token."\n";
        $str = '';

         //print "position: " . $this->_strpos . " token: " . $token . " str10: " . substr($input, $this->_strpos, 10) . "\n"; // @todo: remove debug output

        switch ($token) {
        case XML_WBXML_GLOBAL_TOKEN_STR_I:
            // Section 5.8.4.1
            $str = $this->termstr($input);
            $this->_ch->characters($str);
             //print "XML_WBXML_GLOBAL_TOKEN_STR_I str: $str\n"; // @TODO Remove debug code
            break;

        case XML_WBXML_GLOBAL_TOKEN_STR_T:
            // Section 5.8.4.1
            $x = XML_WBXML::MBUInt32ToInt($input, $this->_strpos);
            $str = $this->getStringTableEntry($x);
            $this->_ch->characters($str);
            //print "XML_WBXML_GLOBAL_TOKEN_STR_T\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_EXT_I_0:
        case XML_WBXML_GLOBAL_TOKEN_EXT_I_1:
        case XML_WBXML_GLOBAL_TOKEN_EXT_I_2:
            // Section 5.8.4.2
            $str = $this->termstr($input);
            $this->_ch->characters($str);
            //print "XML_WBXML_GLOBAL_TOKEN_EXT_I_0\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_EXT_T_0:
        case XML_WBXML_GLOBAL_TOKEN_EXT_T_1:
        case XML_WBXML_GLOBAL_TOKEN_EXT_T_2:
            // Section 5.8.4.2
            $str = $this->getStringTableEnty(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
            $this->_ch->characters($str);
            //print "XML_WBXML_GLOBAL_TOKEN_EXT_T_0\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_EXT_0:
        case XML_WBXML_GLOBAL_TOKEN_EXT_1:
        case XML_WBXML_GLOBAL_TOKEN_EXT_2:
            // Section 5.8.4.2
            $extension = $this->getByte($input);
            $this->_ch->characters($extension);
            //print "XML_WBXML_GLOBAL_TOKEN_EXT_0\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_ENTITY:
            // Section 5.8.4.3
            // UCS-4 chracter encoding?
            $entity = $this->entity(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));

            $this->_ch->characters('&#' . $entity . ';');
            //print "XML_WBXML_GLOBAL_TOKEN_ENTITY\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_PI:
            //print "XML_WBXML_GLOBAL_TOKEN_PI\n\n\nShould die here maybe \n\n\n";
            // Section 5.8.4.4
            // throw new IOException
             //die("WBXML global token processing instruction(PI, " + token + ") is unsupported!\n");
            break;

        case XML_WBXML_GLOBAL_TOKEN_LITERAL:
            // Section 5.8.4.5
            $str = $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
            $this->parseTag($input, $str, false, false);
            //print "XML_WBXML_GLOBAL_TOKEN_LITERAL\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_LITERAL_A:
            // Section 5.8.4.5
            $str = $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
            $this->parseTag($input, $str, true, false);
            //print "XML_WBXML_GLOBAL_TOKEN_LITERAL_A\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_LITERAL_AC:
            // Section 5.8.4.5
            $str = $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
            $this->parseTag($input, $string, true, true);
            //print "XML_WBXML_GLOBAL_TOKEN_LITERAL_AC\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_LITERAL_C:
            // Section 5.8.4.5
            $str = $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
            $this->parseTag($input, $str, false, true);
            //print "XML_WBXML_GLOBAL_TOKEN_LITERAL_C\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_OPAQUE:
            // Section 5.8.4.6
            $size = XML_WBXML::MBUInt32ToInt($input, $this->_strpos);
            if ($size>0) {
                $b = substr($input, $this->_strpos, $size);
                 //print "opaque of size $size: ($b)\n"; // @todo remove debug
                $this->_strpos += $size;
                // opaque data inside a <data> element may or may not be
                // a nested wbxml document (for example devinf data).
                // We find out by checking the first byte of the data: if it's
                // 1, 2 or 3 we expect it to be the version number of a wbxml
                // document and thus start a new wbxml decoder instance on it.

                if ($size > 0 && $this->_isData && ord($b) <= 10) {
                    $decoder = &new XML_WBXML_Decoder(true);
                    $decoder->setContentHandler($this->_ch);
                    $s = $decoder->decode($b);
            //                /* // @todo: FIXME currently we can't decode Nokia
                    // DevInf data. So ignore error for the time beeing.
                    if (is_a($s, 'PEAR_Error')) {
                        $this->_error = $s;
                        return;
                    }
                    // */
                    // $this->_ch->characters($s);
                } else {
                    /* normal opaque behaviour: just copy the raw data: */
                     //print "opaque handled as string=$b\n"; // @todo remove debug
                    $this->_ch->characters($b);
                }
            }
            // old approach to deal with opaque data inside ContentHandler:
            // FIXME Opaque is used by SYNCML.  Opaque data that depends on the context
            // if (contentHandler instanceof OpaqueContentHandler) {
            //     ((OpaqueContentHandler)contentHandler).opaque(b);
            // } else {
            //     String str = new String(b, 0, size, charset);
            //     char[] chars = str.toCharArray();

            //     contentHandler.characters(chars, 0, chars.length);
            // }

            break;

        case XML_WBXML_GLOBAL_TOKEN_END:
            // Section 5.8.4.7.1
            $str = $this->endTag();
            //print "XML_WBXML_GLOBAL_TOKEN_END\n";
            break;

        case XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE:
            // Section 5.8.4.7.2
            $codePage = $this->getByte($input);
             //print "switch to codepage $codePage\n"; // @todo: remove debug code
            $this->switchElementCodePage($codePage);
            //print "XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE\n";
            break;

        default:
            // Section 5.8.2
            // Section 5.8.3
            //print "starting _decode default case\n";
            $hasAttributes = (($token & 0x80) != 0);
            $hasContent = (($token & 0x40) != 0);
            $realToken = $token & 0x3F;
            $str = $this->getTag($realToken);

             //print "tag element: $str\n"; // @TODO Remove debug code
             if($hasAttributes)
                 //print "hasAttributes\n";
            if($hasContent)
                 //print "hasContent\n";

            //print "entering parseTag\n";
            $this->parseTag($input, $str, $hasAttributes, $hasContent);
            //print "finished parseTag\n";
            if ($realToken == 0x0f) {
                // store if we're inside a Data tag. This may contain
                // an additional enclosed wbxml document on which we have
                // to run a seperate encoder
                //print "_isData is true\n";
                $this->_isData = true;
            } else {
                //print "_isData is false\n";
                $this->_isData = false;
            }
            break;
        }
    }

    function parseTag($input, $tag, $hasAttributes, $hasContent)
    {
        
        //print "_______________Got currentURI".$this->getCurrentURI()."\n";
        $attrs = array();
        if ($hasAttributes) {
            //print "about to get attributes in parseTAG \n";
            $attrs = $this->getAttributes($input);
        }

        foreach ($attrs as $att){
            foreach ($att["attribute"] as $attarr){
                //print "attribute is ".$attarr."\n";
            }
            //print "attribute value is ".$att["value"]."\n";
         }

        //print "entering ch->startElement\n";
        $this->_ch->startElement($this->getCurrentURI(), $tag, $attrs, $this->printXMLNS() );
        //print "leaving ch->startElement\n";

        //this could be put into start element to allow you to end an element in one tag e.g. < />
        if ($hasContent) {
            // FIXME I forgot what does this does. Not sure if this is
            // right?
            //print "adding tag to tagStack ".$tag."\n";
            $this->_tagStack[] = $tag;
        } else {
            //print "going into endElement";
            $this->_ch->endElement($this->getCurrentURI(), $tag);
        }
    }

    function endTag()
    {
        if (count($this->_tagStack)) {
            $tag = array_pop($this->_tagStack);
        } else {
            $tag = 'Unknown';
        }

        $this->_ch->endElement($this->getCurrentURI(), $tag);

        return $tag;
    }

    function getAttributes($input)
    {
        //print "====================getAttributes\n";
        $this->startGetAttributes();
        $hasMoreAttributes = true;

        $attrs = array();
        $attr = null;
        $value = null;
        $token = null;

        while ($hasMoreAttributes) {
            
            //print "getAttributes looping through while === value =".$value." -- attr = ".$attr."\n";
            if($attr != null){
            foreach($attr as $atttoprint){
                //print "attr is ".$atttoprint."\n";
            }
            }
            if(isset($attrs))
            {
                //print "attrs is set\n";
                foreach($attrs as $at){
                    //print "attrs values are ".$at."\n";
                    foreach($at as $at1){
                        //print "at1 values are ".$at1."\n";

                        //reset value if it's already added to attrs array.
                        //may need to put in a check on the attribute tag
                        if($at1 == $value)
                            $value = null;
                    
                    }
                }
            }
            

            $token = $this->getByte($input);
            //print "getAttributes token is ".$token."\n";
            switch ($token) {
            // Attribute specified.
            case XML_WBXML_GLOBAL_TOKEN_LITERAL:
                // Section 5.8.4.5
                if (isset($attr)) {
                    $attrs[] = array('attribute' => $attr,
                                     'value' => $value);
                }

                $attr = $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_LITERAL attr = ".$attr."\n";
                break;

            // Value specified.
            case XML_WBXML_GLOBAL_TOKEN_EXT_I_0:
            case XML_WBXML_GLOBAL_TOKEN_EXT_I_1:
            case XML_WBXML_GLOBAL_TOKEN_EXT_I_2:
                // Section 5.8.4.2
                $value .= $this->termstr($input);
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_EXT_I_0 value = ".$value."\n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_EXT_T_0:
            case XML_WBXML_GLOBAL_TOKEN_EXT_T_1:
            case XML_WBXML_GLOBAL_TOKEN_EXT_T_2:
                // Section 5.8.4.2

                $value .= $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_EXT_I_0 value = ".$value."\n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_EXT_0:
            case XML_WBXML_GLOBAL_TOKEN_EXT_1:
            case XML_WBXML_GLOBAL_TOKEN_EXT_2:
                // Section 5.8.4.2
                $value .= $input[$this->_strpos++];

                break;

            case XML_WBXML_GLOBAL_TOKEN_ENTITY:
                // Section 5.8.4.3
                $value .= $this->entity(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_ENTITY value = ".$value."\n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_STR_I:
                // Section 5.8.4.1
                $value .= $this->termstr($input);
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_STR_I: value = ".$value."\n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_STR_T:
                // Section 5.8.4.1
                $value .= $this->getStringTableEntry(XML_WBXML::MBUInt32ToInt($input, $this->_strpos));
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_STR_T value = ".$value."\n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_OPAQUE:
                // Section 5.8.4.6
                $size = XML_WBXML::MBUInt32ToInt($input, $this->_strpos);
                $b = substr($input, $this->_strpos, $this->_strpos + $size);
                $this->_strpos += $size;

                $value .= $b;
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_OPAQUE value = ".$value."\n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_END:
                // Section 5.8.4.7.1
                $hasMoreAttributes = false;
                if (isset($attr)) {
                    $attrs[] = array('attribute' => $attr,
                                     'value' => $value);
                }
                //print "getAttributes XML_WBXML_GLOBAL_TOKEN_END \n";
                break;

            case XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE:
                // Section 5.8.4.7.2
                //print "getAttributes : XML_WBXML_GLOBAL_TOKEN_SWITCH_PAGE\n";
                $codePage = $this->getByte($input);
                echo "getAttribues : codepage ".$codepage."\n";
                if (!$this->_prevAttributeDTD) {
                    $this->_prevAttributeDTD = $this->_attributeDTD;
                }

                $this->switchAttributeCodePage($codePage);
                break;

            default:
                //print "getAttribtes : XML_default\n";
                if ($token < 128) {
                    //print "getAttribtes : token<128\n";
                    if (isset($attr)) {
                        //print "getAttribtes : attr set ".$attr."\n";
                        //print "getAttribtes : attr set value ".$value."\n";
                        $attrs[] = array('attribute' => $attr,
                                         'value' => $value);
                    }
                    $attr = $this->_attributeDTD->toAttributeStartStr($token);
                } else {
                    // Value.
                    //print "____--getAttribtes : token is greater than 128\n";
                    //print "attributeDTD DPI = ".$this->_attributeDTD->getDPI()."\n";
                    //print "attributeDTD URI = ".$this->_attributeDTD->getURI()."\n";
                    $value .= $this->_attributeDTD->toAttributeValueStr($token);
                    if($value)
                    {
                        //added by Thom for testing
                       // $attrs = $value; //= array('attribute' => $attr,
                         //                'value' => $value);
                        //print "getAttributes : value ".$value."\n";
                    }
                    else
                        //print "getAttributes : value is false \n";
                }
                break;
            }
        }



        //I don't know why this bit of code is here. so commenting it out and replacing
        // it with what I would expect here
        /*if (!$this->_prevAttributeDTD) {
            //print "_prevAttributeDTD is false";
            $this->_attributeDTD = $this->_prevAttributeDTD;
            $this->_prevAttributeDTD = false;
        }*/
         if (!$this->_attributeDTD) {
            //print "_prevAttributeDTD is false";
            $this->_prevAttributeDTD = $this->_attributeDTD;
            $this->_attributeDTD = false;
        }

        $this->stopGetAttributes();
        //print "==adding a returning of attrs\n";
        return $attrs;
    }

    function startGetAttributes()
    {
        $this->_isAttribute = true;
        //print "startGetAttribute setting isAttribute to true\n";
    }

    function stopGetAttributes()
    {
        //print "in stopGetAttributes\n";
        $this->_isAttribute = false;
    }

    function getCurrentURI()
    {
        //print "in getCurrentURI\n";
        if ($this->_isAttribute) {
            //print "getting _tagDTD->getURI\n";
            return $this->_tagDTD->getURI();
        } else {
           //print "getting _attributeDTD->getURI\n";
            return $this->_attributeDTD->getURI();
        }
    }

    function printXMLNS()
    {
        //print "in printXMLNS\n";
        if ($this->_isAttribute) {
            //print "getting _tagDTD->getPrintXMLNS\n";
            return $this->_tagDTD->getPrintXMLNS();
        } else {
           //print "getting _attributeDTD->getPrintXMLNS\n";
            return $this->_attributeDTD->getPrintXMLNS();
        }
    }

    function writeString($str)
    {
        $this->_ch->characters($str);
    }

    function getTag($tag)
    {
        // Should know which state it is in.
        //print "getting tag\n";
        return $this->_tagDTD->toTagStr($tag);
    }

    function getAttribute($attribute)
    {
        // Should know which state it is in.
        $this->_attributeDTD->toAttributeInt($attribute);
    }

    function switchElementCodePage($codePage)
    {
        //print "switchElementCodePage\n";
        $this->_tagDTD = &$this->_dtdManager->getInstance($this->_tagDTD->toCodePageStr($codePage), $codePage);
        $this->switchAttributeCodePage($codePage);
    }

    function switchAttributeCodePage($codePage)
    {
        //print "swithcAttributeCodePage ".$codePage."\n";
        $this->_attributeDTD = &$this->_dtdManager->getInstance($this->_attributeDTD->toCodePageStr($codePage), $codePage);
    }

    /**
     * Return the hex version of the base 10 $entity.
     */
    function entity($entity)
    {
        return dechex($entity);
    }

    /**
     * Reads a null terminated string.
     */
    function termstr($input)
    {
        $str = '#'; // must start with nonempty string to allow array access
        $i = 0;
        $ch = $input[$this->_strpos++];
        if (ord($ch) == 0) {
            return ''; // don't return '#'
        }
        while (ord($ch) != 0) {
            $str[$i++] = $ch;
            $ch = $input[$this->_strpos++];
        }

        return $str;
    }

}

