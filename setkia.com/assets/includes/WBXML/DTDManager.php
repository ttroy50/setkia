<?php

include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTD/SyncML.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTD/SyncMLMetInf.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTD/SyncMLDevInf.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTD/WAPProvisioning.php';
include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTD/WAPProvisioningcp1.php';
/**
 * From Binary XML Content Format Specification Version 1.3, 25 July 2001
 * found at http://www.wapforum.org
 *
 * $Horde: framework/XML_WBXML/WBXML/DTDManager.php,v 1.3.12.15 2009/01/06 15:23:50 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Anthony Mills <amills@pyramid6.com>
 * @package XML_WBXML
 */
class XML_WBXML_DTDManager {

    /**
     * @var array
     */
    var $_strDTD = array();

    /**
     * @var array
     */
    var $_strDTDURI = array();

    var $_codepageDTD = array();

    /**
     */
    function XML_WBXML_DTDManager()
    {
        $this->registerDTD(DPI_DTD_SYNCML_1_0, 'syncml:syncml1.0', new XML_WBXML_DTD_SyncML(0), 0);
        $this->registerDTD(DPI_DTD_SYNCML_1_1, 'syncml:syncml1.1', new XML_WBXML_DTD_SyncML(1), 0);
        $this->registerDTD(DPI_DTD_SYNCML_1_2, 'syncml:syncml1.2', new XML_WBXML_DTD_SyncML(2), 0);

        $this->registerDTD(DPI_DTD_METINF_1_0, 'syncml:metinf1.0', new XML_WBXML_DTD_SyncMLMetInf(0), 1);
        $this->registerDTD(DPI_DTD_METINF_1_1, 'syncml:metinf1.1', new XML_WBXML_DTD_SyncMLMetInf(1), 1);
        $this->registerDTD(DPI_DTD_METINF_1_2, 'syncml:metinf1.2', new XML_WBXML_DTD_SyncMLMetInf(2), 1);

        $this->registerDTD(DPI_DTD_DEVINF_1_0, 'syncml:devinf1.0', new XML_WBXML_DTD_SyncMLDevInf(0), 2);
        $this->registerDTD(DPI_DTD_DEVINF_1_1, 'syncml:devinf1.1', new XML_WBXML_DTD_SyncMLDevInf(1), 2);
        $this->registerDTD(DPI_DTD_DEVINF_1_2, 'syncml:devinf1.2', new XML_WBXML_DTD_SyncMLDevInf(2), 2);

        $this->registerDTD(DPI_DTD_PROV_1_0, 'prov', new XML_WBXML_DTD_WAPProvisioning(0), 0);
        $this->registerDTD(DPI_DTD_PROV_1_0, 'oma', new XML_WBXML_DTD_WAPProvisioningcp1(1), 1);

        //$this->registerDTD(DPI_DTD_PROV_1_0, 'prov', new XML_WBXML_DTD_WAPProvisioningcp1(0));
        //$this->registerDTD(DPI_DTD_PROV_1_0, 'oma', new XML_WBXML_DTD_WAPProvisioningcp1(1));
    }

    /**
     */
    function &getInstance($publicIdentifier, $codepage = 0)
    {
        //print "getInstance in DTDMangaer publicIdentifier is ".$publicIdentifier." codepage is ".$codepage."\n";
        $publicIdentifier = strtolower($publicIdentifier);

      /*  if($codepage == 0)
        {
            //print "getInstance in DTDMangaer codepage 0\n";
            if (isset($this->_strDTD[$publicIdentifier])) {
                $dtd = &$this->_strDTD[$publicIdentifier];
            } else {
                $dtd = null;
            }
        }
        else
        {*/
            //print "getInstance in DTDMangaer codepage is ".$codepage."\n";
            $publicIdentifier_codepage = strtolower($publicIdentifier."_".$codepage);
            //print "publicIdentifier_codepage is ".$publicIdentifier_codepage."\n";
            if(isset($this->_codepageDTD[$publicIdentifier_codepage]))
            {
                //print "getting codepageDTD for ".$publicIdentifier_codepage."\n";
                $dtd = &$this->_codepageDTD[$publicIdentifier_codepage];
            }
            else
            {
                //print "no DTD found\n";
                $dtd = null;
            }
        //}
        //print "DTD Manager getInstance codepage URI is ".$dtd->getURI()."\n";
        return $dtd;
    }

    /**
     */
    function &getInstanceURI($uri)
    {
        //print "getInstanceURI in DTDMangaer URI is ".$uri."\n";
        $uri = strtolower($uri);

        // some manual hacks:
        if ($uri == 'syncml:syncml') {
            $uri = 'syncml:syncml1.0';
        }
        if ($uri == 'syncml:metinf') {
            $uri = 'syncml:metinf1.0';
        }
        if ($uri == 'syncml:devinf') {
            $uri = 'syncml:devinf1.0';
        }

        if (isset($this->_strDTDURI[$uri])) {
            $dtd = &$this->_strDTDURI[$uri];
        } else {
            $dtd = null;
        }
        return $dtd;
    }

    /**
     */
    function registerDTD($publicIdentifier, $uri, &$dtd, $codepage = 0)
    {
        $dtd->setDPI($publicIdentifier);

        $publicIdentifier = strtolower($publicIdentifier);
        
        //hack to set a more than 1 codepage per DTD
        $publicIdentifier_codepage = strtolower($publicIdentifier."_".$codepage);

        $this->_strDTD[$publicIdentifier] = $dtd;
        $this->_strDTDURI[strtolower($uri)] = $dtd;
        //print "setting _codepageDTD as ".$publicIdentifier_codepage."\n";
        $this->_codepageDTD[$publicIdentifier_codepage] = $dtd;

        if(isset($this->_codepageDTD[$publicIdentifier_codepage]))
        {
            //print "_codepageDTD set \n";
            //print "URI is ".$this->_codepageDTD[$publicIdentifier_codepage]->getURI()."\n";
        }
        else
        {
            //print "_codepageDTD not set \n";
        }
    }

}
