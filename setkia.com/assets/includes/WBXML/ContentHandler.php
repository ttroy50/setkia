<?php
/**
 * From Binary XML Content Format Specification Version 1.3, 25 July 2001
 * found at http://www.wapforum.org
 *
 * $Horde: framework/XML_WBXML/WBXML/ContentHandler.php,v 1.9.10.12 2009/01/06 15:23:50 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Anthony Mills <amills@pyramid6.com>
 * @package XML_WBXML
 */
class XML_WBXML_ContentHandler {

    var $_currentUri;
    var $_output = '';

    var $_opaqueHandler;

    /**
     * Charset.
     */
    var $_charset = 'UTF-8';

    /**
     * WBXML Version.
     * 1, 2, or 3 supported
     */
    var $_wbxmlVersion = 3;

    var $_error = '';
    var $_errorInParsing = false;

    function XML_WBXML_ContentHandler()
    {
        $this->_currentUri = new XML_WBXML_LifoQueue();
    }

    /**
     */
    function raiseError($error)
    {
        if (!class_exists('PEAR')) {
            //require 'PEAR.php';
        }
        $this->_error .= '...'.$error;
        $this->_errorInParsing = true;
        //echo $error;
       // return PEAR::raiseError($error);
    }

    function getErrorStr()
    {
        return $this->_error;
    }
    function hasError()
    {
        return $this->_errorInParsing;
    }

    function getCharsetStr()
    {
        return $this->_charset;
    }

    function setCharset($cs)
    {
        $this->_charset = $cs;
    }

    function getVersion()
    {
        return $this->_wbxmlVersion;
    }

    function setVersion($v)
    {
        $this->_wbxmlVersion = 2;
    }

    function getOutput()
    {
        return $this->_output;
    }

    function getOutputSize()
    {
        return strlen($this->_output);
    }

    function startElement($uri, $element, $attrs = array(), $printXMLNS = true)
    {
        $this->_output .= '<' . $element;
        //print "moving to top of _currentURI\n";
        $currentUri = $this->_currentUri->top();
        //print "at top of _currentURI\n";

        if($printXMLNS)
        {
            if (((!$currentUri) || ($currentUri != $uri)) && $uri) {
                $this->_output .= ' xmlns="' . $uri . '"';
                //print "adding xmlns to the output\n";
            }
        }

        $this->_currentUri->push($uri);
        /*
        foreach ($attrs as $att){
            foreach ($att["attribute"] as $attarr){
                //print "attribute is ".$attarr."\n";
            }
            //print "attribute value is ".$att["value"]."\n";
         }
         *
         *///attribute
        foreach ($attrs as $attr) {
            $attrarr= $attr['attribute'];
            $this->_output .= ' ' . $attrarr['name'] . '="' . $attrarr['value'] . $attr['value'].'"';
            //print "ch-startElement attrs as attr".$this->_output."\n";
        }

        $this->_output .= '>';
        //print "ch-startElement output".$this->_output."\n";
    }

    function endElement($uri, $element)
    {
        //print "contentHandler endElement\n";
        $this->_output .= '</' . $element . '>';

        $this->_currentUri->pop();
    }

    function characters($str)
    {
        $this->_output .= $str;
    }

    function opaque($o)
    {
        $this->_output .= $o;
    }

    function setOpaqueHandler($opaqueHandler)
    {
        $this->_opaqueHandler = $opaqueHandler;
    }

    function removeOpaqueHandler()
    {
        unset($this->_opaqueHandler);
    }

    function createSubHandler()
    {
        $name = get_class($this); // clone current class
        $sh = new $name();
        $sh->setCharset($this->getCharsetStr());
        $sh->setVersion($this->getVersion());
        return $sh;
    }

}

class XML_WBXML_LifoQueue {

    var $_queue = array();

    function XML_WBXML_LifoQueue()
    {
    }

    function push($obj)
    {
        $this->_queue[] = $obj;
    }

    function pop()
    {
        if (count($this->_queue)) {
            return array_pop($this->_queue);
        } else {
            return null;
        }
    }

    function top()
    {
        if ($count = count($this->_queue)) {
            return $this->_queue[$count - 1];
        } else {
            return null;
        }
    }

}
