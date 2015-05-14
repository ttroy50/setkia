<?php
/**
 * From Binary XML Content Format Specification Version 1.3, 25 July 2001
 * found at http://www.wapforum.org
 *
 * $Horde: framework/XML_WBXML/WBXML/DTD.php,v 1.6.12.9 2009/01/06 15:23:50 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Anthony Mills <amills@pyramid6.com>
 * @package XML_WBXML
 */
class XML_WBXML_DTD {

    var $version;
    var $intTags;
    var $intAttributes;
    var $strTags;
    var $strAttributes;
    var $intCodePages;
    var $strCodePages;
    var $strCodePagesURI;
    var $URI;
    var $XMLNS;
    var $DPI;

    //attribute Starts and Values
    var $intAttributeStarts;
    var $strAttributeStarts;
    var $intAttributeValues;
    var $strAttributeValues;

    var $codePageNumber;

    var $tags;
    //do we want to print the XMLNS in the output xml.
    var $printXMLNS = true;

    function XML_WBXML_DTD($v)
    {
        $this->version = $v;
        $this->init();
    }

    function init()
    {
    }

    function getPrintXMLNS()
    {
        return $this->printXMLNS;
    }
    function setAttribute($intAttribute, $strAttribute)
    {
        $this->strAttributes[$strAttribute] = $intAttribute;
        $this->intAttributes[$intAttribute] = $strAttribute;
    }

    function setAttributeStart($intAttributeStart, $arrAttributeStart = array())
    {
        //need to change arrAttributeStart name => vale to str name.=.value
        $strAttributeStart = $arrAttributeStart['name']."=".$arrAttributeStart['value'];
        $this->strAttributeStarts[$strAttributeStart] = $intAttributeStart;
        $this->intAttributeStarts[$intAttributeStart] = $arrAttributeStart;
    }

    function setAttributeValue($intAttributeValue, $strAttributeValue)
    {
        $this->strAttributeValues[$strAttributeValue] = $intAttributeValue;
        $this->intAttributeValues[$intAttributeValue] = $strAttributeValue;
    }


    function setTag($intTag, $strTag)
    {
        $this->strTags[$strTag] = $intTag;
        $this->intTags[$intTag] = $strTag;
    }

    function setCodePage($intCodePage, $strCodePage, $strCodePageURI)
    {
        $this->strCodePagesURI[$strCodePageURI] = $intCodePage;
        $this->strCodePages[$strCodePage] = $intCodePage;
        $this->intCodePages[$intCodePage] = $strCodePage;
    }

    function toTagStr($tag)
    {
        return isset($this->intTags[$tag]) ? $this->intTags[$tag] : false;
    }

    function toAttributeStr($attribute)
    {
        //this returns from the intTags array, but I think it shoud lreturn from the intAttributes array
        print "toAttributeStr ".$attribute."\n";
        //foreach($this->intTags as $mytag){
        //    //print "mytag is ".$mytag."\n";
        //}
        //return isset($this->intTags[$attribute]) ? $this->intTags[$attribute] : false;
        //mychange to return from intAttribute array
        return isset($this->intAttributes[$attribute]) ? $this->intAttributes[$attribute] : false;
    }

    function toCodePageStr($codePage)
    {
        return isset($this->intCodePages[$codePage]) ? $this->intCodePages[$codePage] : false;
    }

    function toTagInt($tag)
    {
        return isset($this->strTags[$tag]) ? $this->strTags[$tag] : false;
    }

    function toAttributeInt($attribute)
    {
        
        return isset($this->strAttributes[$attribute]) ? $this->strAttributes[$attribute] : false;
    }

    function toCodePageInt($codePage)
    {
        return isset($this->strCodePages[$codePage]) ? $this->strCodePages[$codePage] : false;
    }

    function toCodePageURI($uri)
    {
        $uri = strtolower($uri);
        if (!isset($this->strCodePagesURI[$uri])) {
            die("unable to find codepage for $uri!\n");
        }

        $ret = isset($this->strCodePagesURI[$uri]) ? $this->strCodePagesURI[$uri] : false;

        return $ret;
    }

    /**
     * Getter for property version.
     * @return Value of property version.
     */
    function getVersion()
    {
        return $this->version;
    }

    /**
     * Setter for property version.
     * @param integer $v  New value of property version.
     */
    function setVersion($v)
    {
        $this->version = $v;
    }

    /**
     * Getter for property URI.
     * @return Value of property URI.
     */
    function getURI()
    {
        //print "getting the URI of the DTD ".$this->URI."\n";
        return $this->URI;
    }

    /**
     * Setter for property URI.
     * @param string $u  New value of property URI.
     */
    function setURI($u)
    {
        //print "setting the URI of the DTD ".$u."\n";
        $this->URI = $u;
    }

    /**
     * Getter for property DPI.
     * @return Value of property DPI.
     */
    function getDPI()
    {
        return $this->DPI;
    }

    /**
     * Setter for property DPI.
     * @param DPI New value of property DPI.
     */
    function setDPI($d)
    {
        $this->DPI = $d;
    }

    function toAttributeStartStr($attributeStart)
    {
        //this returns from the intTags array, but I think it shoud lreturn from the intAttributes array
        print "toAttributeStartStr ".$attributeStart."\n";
        //foreach($this->intTags as $mytag){
        //    //print "mytag is ".$mytag."\n";
        //}
        //return isset($this->intTags[$attribute]) ? $this->intTags[$attribute] : false;
        //mychange to return from intAttribute array
        return isset($this->intAttributeStarts[$attributeStart]) ? $this->intAttributeStarts[$attributeStart] : false;
    }

    function toAttributeStartInt($attributeStart)
    {
        //print "toAttributeStartInt attributeStart is ".$attributeStart."\n";
        return isset($this->strAttributeStarts[$attributeStart]) ? $this->strAttributeStarts[$attributeStart] : false;
    }

        function toAttributeValueStr($attributeValue)
    {
        //this returns from the intTags array, but I think it shoud lreturn from the intAttributes array
        //print "toAttributeValueStr ".$attributeValue."\n";
        //foreach($this->intTags as $mytag){
        //    print "mytag is ".$mytag."\n";
        //}
        //return isset($this->intTags[$attribute]) ? $this->intTags[$attribute] : false;
        //mychange to return from intAttribute array
        return isset($this->intAttributeValues[$attributeValue]) ? $this->intAttributeValues[$attributeValue] : false;
    }

    function toAttributeValueInt($attributeValue)
    {

        return isset($this->strAttributeValues[$attributeValue]) ? $this->strAttributeValues[$attributeValue] : false;
    }
    
    function hasAttributeStart($name, $value)
    {
        $strAttStart = $name."=".$value;
        
        foreach($this->intAttributeStarts as $attStartarr)
        {
            $attStart = $attStartarr['name']."=".$attStartarr['value'];
            //print "hasAttributeStart strAttStart ".$strAttStart." attStart ".$attStart."\n";
            $pattern = "/^".$value."/";
            if((strpos($attStartarr['name'], $name) !== false) && (preg_match($pattern, $attStartarr['value']) != 0))
                return true;

        }
        return false;
    }

    
    function hasAttributeValue($value)
    {
        
        foreach($this->intAttributeValues as $attValue)
        {
            if(strpos($value, $attValue) !== false)
                return true;
        }
        return false;
    }
}

class Tag
{
    function Tag($cp, $tagI, $tagS)
    {
        $this->codepage = $cp;
        $this->tagInt = $tagI;
        $this->tagStr = $tagStr;
    }
    var $codepage;
    var $tagInt;
    var $tagStr;
}

class Attribute
{
    var $codepage;
    var $attributeInt;
    var $attributeName;
    var $attributeStartValue;
}

class AttributeValue
{
    var $codepage;
    var $valueInt;
    var $valueStr;
}
