<?php

include_once '/hsphere/local/home/ttroy/setkia.com/includes/WBXML/DTD.php';

/**
 * From Binary XML Content Format Specification Version 1.3, 25 July 2001
 * found at http://www.wapforum.org
 *
 * $Horde: framework/XML_WBXML/WBXML/DTD/SyncML.php,v 1.6.12.9 2009/01/06 15:23:50 jan Exp $
 *
 * Copyright 2003-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Anthony Mills <amills@pyramid6.com>
 * @package XML_WBXML
 */
class XML_WBXML_DTD_WAPProvisioningcp1 extends XML_WBXML_DTD {

    function init()
    {
        /* this code table has been extracted from libwbxml
         * (see http://libwbxml.aymerick.com/) by using
         *
         * grep '\"[^\"]*\", *0x.., 0x.. },' wbxml_tables.c
         * | sed -e 's#^.*\"\([^\"]*\)\", *\(0x..\), \(0x..\) },.*$#        \$this->setTag\(\3, \"\1\"\); // \2#g'

const WBXMLTagEntry sv_prov10_tag_table[] = {
    { "wap-provisioningdoc",        0x00, 0x05 },
    { "characteristic",             0x00, 0x06 },
    { "parm",                       0x00, 0x07 },

    { "characteristic",             0x01, 0x06 }, /* OMA
    { "parm",                       0x01, 0x07 }, /* OMA
    { NULL,                         0x00, 0x00 }
};
*/
        $this->printXMLNS = false;


        //print "setting up wapprovisioning code page 1 DTD\n";
        //$this->setTag(0x05, "wap-provisioningdoc"); // 0x00
        $this->setTag(0x06, "characteristic"); // 0x01
        $this->setTag(0x07, "parm"); // 0x01

        $this->setAttributeStart(0x50, array(
                                 "name" => "type",
                                 "value" => ""));
        $this->setAttributeStart(0x53, array(
                                 "name" => "type",
                                 "value" => "PORT"));
        $this->setAttributeStart(0x55, array(
                                 "name" => "type",
                                 "value" => "APPLICATION"));
        $this->setAttributeStart(0x56, array(
                                 "name" => "type",
                                 "value" => "APPADDR"));
        $this->setAttributeStart(0x57, array(
                                 "name" => "type",
                                 "value" => "APPAUTH"));
        $this->setAttributeStart(0x58, array(
                                 "name" => "type",
                                 "value" => "CLIENTIDENTITY"));
        $this->setAttributeStart(0x59, array(
                                 "name" => "type",
                                 "value" => "RESOURCE"));


               //Parm Attribute Start Tokens
       $this->setAttributeStart(0x05, array(
                                 "name" => "name",
                                 "value" => ""));
       $this->setAttributeStart(0x06, array(
                                 "name" => "value",
                                 "value" => ""));
       $this->setAttributeStart(0x07, array(
                                 "name" => "name",
                                 "value" => "NAME"));
       $this->setAttributeStart(0x14, array(
                                 "name" => "name",
                                 "value" => "INTERNET"));
       $this->setAttributeStart(0x1c, array(
                                 "name" => "value",
                                 "value" => "STARTPAGE"));
        $this->setAttributeStart(0x22, array(
                                 "name" => "name",
                                 "value" => "TO-NAPID"));
       $this->setAttributeStart(0x23, array(
                                 "name" => "value",
                                 "value" => "PORTNBR"));
        $this->setAttributeStart(0x24, array(
                                 "name" => "name",
                                 "value" => "SERVICE"));
       $this->setAttributeStart(0x2e, array(
                                 "name" => "value",
                                 "value" => "AACCEPT"));
        $this->setAttributeStart(0x2f, array(
                                 "name" => "name",
                                 "value" => "AAUTHDATA"));
       $this->setAttributeStart(0x30, array(
                                 "name" => "value",
                                 "value" => "AAUTHLEVEL"));
        $this->setAttributeStart(0x31, array(
                                 "name" => "name",
                                 "value" => "AAUTHNAME"));
       $this->setAttributeStart(0x32, array(
                                 "name" => "value",
                                 "value" => "AAUTHSECRET"));
        $this->setAttributeStart(0x33, array(
                                 "name" => "name",
                                 "value" => "AAUTHTYPE"));
       $this->setAttributeStart(0x34, array(
                                 "name" => "value",
                                 "value" => "ADDR"));
        $this->setAttributeStart(0x35, array(
                                 "name" => "name",
                                 "value" => "ADDRTYPE"));
       $this->setAttributeStart(0x36, array(
                                 "name" => "value",
                                 "value" => "APPID"));
        $this->setAttributeStart(0x37, array(
                                 "name" => "name",
                                 "value" => "APROTOCOL"));
       $this->setAttributeStart(0x38, array(
                                 "name" => "name",
                                 "value" => "PROVIDER-ID"));
        $this->setAttributeStart(0x39, array(
                                 "name" => "name",
                                 "value" => "TO-PROXY"));
       $this->setAttributeStart(0x3a, array(
                                 "name" => "value",
                                 "value" => "URI"));
        $this->setAttributeStart(0x3b, array(
                                 "name" => "name",
                                 "value" => "RULE"));


                //ADDRTYPE Value
       $this->setAttributeValue(0x86, "IPV6");
       $this->setAttributeValue(0x87, "E164");
       $this->setAttributeValue(0x88, "ALPHA");
       $this->setAttributeValue(0x8d, "APPSRV");
       $this->setAttributeValue(0x8e, "OBEX");


       //AUTHTYPE VALUE
       $this->setAttributeValue(0x90, ",");
       $this->setAttributeValue(0x91, "HTTP-");
       $this->setAttributeValue(0x92, "BASIC");
       $this->setAttributeValue(0x93, "DIGEST");







                /* this code table has been extracted from libwbxml
         * (see http://libwbxml.aymerick.com/) by using
         *
         * grep '\"[^\"]*\", *0x.., 0x.. },' wbxml_tables.c
         * | sed -e 's#^.*\"\([^\"]*\)\", *\(0x..\), \(0x..\) },.*$#        \$this->setTag\(\3, \"\1\"\); // \2#g'
         */
/*
const WBXMLAttrEntry sv_prov10_attr_table[] = {
    /* Wap-provisioningdoc *
    { "version",    NULL,               0x00, 0x45 },

    { "name",        NULL,                  0x01, 0x05 }, /* OMA *
    { "value",       NULL,                  0x01, 0x06 }, /* OMA *
    { "name",        "NAME",                0x01, 0x07 }, /* OMA *
    { "name",        "INTERNET",            0x01, 0x14 }, /* OMA *
    { "name",        "STARTPAGE",           0x01, 0x1c }, /* OMA *
    { "name",        "TO-NAPID",            0x01, 0x22 }, /* OMA *
    { "name",        "PORTNBR",             0x01, 0x23 }, /* OMA *
    { "name",        "SERVICE",             0x01, 0x24 }, /* OMA *
    { "name",        "AACCEPT",             0x01, 0x2e }, /* OMA *
    { "name",        "AAUTHDATA",           0x01, 0x2f }, /* OMA *
    { "name",        "AAUTHLEVEL",          0x01, 0x30 }, /* OMA *
    { "name",        "AAUTHNAME",           0x01, 0x31 }, /* OMA *
    { "name",        "AAUTHSECRET",         0x01, 0x32 }, /* OMA /
    { "name",        "AAUTHTYPE",           0x01, 0x33 }, /* OMA/
    { "name",        "ADDR",                0x01, 0x34 }, /* OMA /
    { "name",        "ADDRTYPE",            0x01, 0x35 }, /* OMA /
    { "name",        "APPID",               0x01, 0x36 }, /* OMA /
    { "name",        "APROTOCOL",           0x01, 0x37 }, /* OMA /
    { "name",        "PROVIDER-ID",         0x01, 0x38 }, /* OMA /
    { "name",        "TO-PROXY",            0x01, 0x39 }, /* OMA *
    { "name",        "URI",                 0x01, 0x3a }, /* OMA *
    { "name",        "RULE",                0x01, 0x3b }, /* OMA *
};
*/
        /*
        if ($this->version == 1) {
            $this->setCodePage(0, DPI_DTD_SYNCML_1_1, 'syncml:syncml1.1');
            $this->setCodePage(1, DPI_DTD_METINF_1_1, 'syncml:metinf1.1');
            $this->setURI('syncml:syncml1.1');
        } elseif ($this->version == 2) {
            $this->setCodePage(0, DPI_DTD_SYNCML_1_2, 'syncml:syncml1.2');
            $this->setCodePage(1, DPI_DTD_METINF_1_2, 'syncml:metinf1.2');
            $this->setURI('syncml:syncml1.2');
        } else {*/
        //wap-provisioning specifies 2 code pages ??what to call them??
            $this->setCodePage(0, DPI_DTD_PROV_1_0, 'prov');
            $this->setCodePage(1, DPI_DTD_PROV_1_0, 'oma');
            //$this->setCodePage(1, DPI_DTD_METINF_1_0, 'syncml:metinf1.0');
            $this->setURI('oma');
        //}*/
    }

}
