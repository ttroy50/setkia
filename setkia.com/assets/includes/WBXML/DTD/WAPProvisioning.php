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
class XML_WBXML_DTD_WAPProvisioning extends XML_WBXML_DTD {

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

        //print "setting up wapprovisioning DTD\n";
        $this->setTag(0x05, "wap-provisioningdoc"); // 0x00
        $this->setTag(0x06, "characteristic"); // 0x00
        $this->setTag(0x07, "parm"); // 0x00
        $this->setTag(0x00, NULL); // 0x00


        //Characteristic Attribute Start Tokens

        $this->setAttributeStart(0x50, array(
                                 "name" => "type",
                                 "value" => ""));
        $this->setAttributeStart(0x51, array(
                                 "name" => "type",
                                 "value" => "PXLOGICAL"));
        $this->setAttributeStart(0x52, array(
                                 "name" => "type",
                                 "value" => "PXPHYSICAL"));
        $this->setAttributeStart(0x53, array(
                                 "name" => "type",
                                 "value" => "PORT"));
        $this->setAttributeStart(0x54, array(
                                 "name" => "type",
                                 "value" => "VALIDITY"));
        $this->setAttributeStart(0x55, array(
                                 "name" => "type",
                                 "value" => "NAPDEF"));
        $this->setAttributeStart(0x56, array(
                                 "name" => "type",
                                 "value" => "BOOTSTRAP"));
        $this->setAttributeStart(0x57, array(
                                 "name" => "type",
                                 "value" => "VENDORCONFIG"));
        $this->setAttributeStart(0x58, array(
                                 "name" => "type",
                                 "value" => "CLIENTIDENTITY"));
        $this->setAttributeStart(0x59, array(
                                 "name" => "type",
                                 "value" => "PXAUTHINFO"));
        $this->setAttributeStart(0x5a, array(
                                 "name" => "type",
                                 "value" => "NAPAUTHINFO"));
        $this->setAttributeStart(0x5b, array(
                                 "name" => "type",
                                 "value" => "ACCESS"));




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
        $this->setAttributeStart(0x08, array(
                                 "name" => "name",
                                 "value" => "NAP-ADDRESS"));
        $this->setAttributeStart(0x09, array(
                                 "name" => "name",
                                 "value" => "NAP-ADDRTYPE"));
        $this->setAttributeStart(0x0a, array(
                                 "name" => "name",
                                 "value" => "CALLTYPE"));
        $this->setAttributeStart(0x0b, array(
                                 "name" => "name",
                                 "value" => "VALIDUNTIL"));
        $this->setAttributeStart(0x0c, array(
                                 "name" => "name",
                                 "value" => "AUTHTYPE"));
        $this->setAttributeStart(0x0d, array(
                                 "name" => "name",
                                 "value" => "AUTHNAME"));
        $this->setAttributeStart(0x0e, array(
                                 "name" => "name",
                                 "value" => "AUTHSECRET"));
        $this->setAttributeStart(0x0f, array(
                                 "name" => "name",
                                 "value" => "LINGER"));
        $this->setAttributeStart(0x10, array(
                                 "name" => "name",
                                 "value" => "BEARER"));
        $this->setAttributeStart(0x11, array(
                                 "name" => "name",
                                 "value" => "NAPID"));
        $this->setAttributeStart(0x12, array(
                                 "name" => "name",
                                 "value" => "COUNTRY"));
        $this->setAttributeStart(0x13, array(
                                 "name" => "name",
                                 "value" => "NETWORK"));
        $this->setAttributeStart(0x14, array(
                                 "name" => "name",
                                 "value" => "INTERNET"));
        $this->setAttributeStart(0x15, array(
                                 "name" => "name",
                                 "value" => "PROXY-ID"));
        $this->setAttributeStart(0x16, array(
                                 "name" => "name",
                                 "value" => "PROXY-PROVIDER-ID"));
        $this->setAttributeStart(0x17, array(
                                 "name" => "name",
                                 "value" => "DOMAIN"));
        $this->setAttributeStart(0x18, array(
                                 "name" => "name",
                                 "value" => "PROVURL"));
        $this->setAttributeStart(0x19, array(
                                 "name" => "name",
                                 "value" => "PXAUTH-TYPE"));
        $this->setAttributeStart(0x1a, array(
                                 "name" => "name",
                                 "value" => "PXAUTH-ID"));
        $this->setAttributeStart(0x1b, array(
                                 "name" => "name",
                                 "value" => "PXAUTH-PW"));
        $this->setAttributeStart(0x1c, array(
                                 "name" => "name",
                                 "value" => "STARTPAGE"));
        $this->setAttributeStart(0x1d, array(
                                 "name" => "name",
                                 "value" => "BASAUTH-ID"));
        $this->setAttributeStart(0x1e, array(
                                 "name" => "name",
                                 "value" => "BASAUTH-PW"));
        $this->setAttributeStart(0x1f, array(
                                 "name" => "name",
                                 "value" => "PUSHENABLED"));
        $this->setAttributeStart(0x20, array(
                                 "name" => "name",
                                 "value" => "PXADDR"));
        $this->setAttributeStart(0x21, array(
                                 "name" => "name",
                                 "value" => "PXADDRTYPE"));
        $this->setAttributeStart(0x22, array(
                                 "name" => "name",
                                 "value" => "TO-NAPID"));
        $this->setAttributeStart(0x23, array(
                                 "name" => "name",
                                 "value" => "PORTNBR"));
        $this->setAttributeStart(0x24, array(
                                 "name" => "name",
                                 "value" => "SERVICE"));
        $this->setAttributeStart(0x25, array(
                                 "name" => "name",
                                 "value" => "LINKSPEED"));
        $this->setAttributeStart(0x26, array(
                                 "name" => "name",
                                 "value" => "DNLINKSPEED"));
        $this->setAttributeStart(0x27, array(
                                 "name" => "name",
                                 "value" => "LOCAL-ADDR"));
        $this->setAttributeStart(0x28, array(
                                 "name" => "name",
                                 "value" => "LOCAL-ADDRTYPE"));
        $this->setAttributeStart(0x29, array(
                                 "name" => "name",
                                 "value" => "CONTEXT-ALLOW"));
        $this->setAttributeStart(0x2a, array(
                                 "name" => "name",
                                 "value" => "TRUST"));
        $this->setAttributeStart(0x2b, array(
                                 "name" => "name",
                                 "value" => "MASTER"));
        $this->setAttributeStart(0x2c, array(
                                 "name" => "name",
                                 "value" => "SID"));
        $this->setAttributeStart(0x2d, array(
                                 "name" => "name",
                                 "value" => "SOC"));
        $this->setAttributeStart(0x2e, array(
                                 "name" => "name",
                                 "value" => "WSP-VERSION"));
        $this->setAttributeStart(0x2f, array(
                                 "name" => "name",
                                 "value" => "PHYSICAL-PROXY-ID"));
        $this->setAttributeStart(0x30, array(
                                 "name" => "name",
                                 "value" => "CLIENT-ID"));
        $this->setAttributeStart(0x31, array(
                                 "name" => "name",
                                 "value" => "DELIVERY-ERR-SDU"));
        $this->setAttributeStart(0x32, array(
                                 "name" => "name",
                                 "value" => "DELIVERY-ORDER"));
        $this->setAttributeStart(0x33, array(
                                 "name" => "name",
                                 "value" => "TRAFFIC-CLASS"));
        $this->setAttributeStart(0x34, array(
                                 "name" => "name",
                                 "value" => "MAX-SDU-SIZE"));
        $this->setAttributeStart(0x35, array(
                                 "name" => "name",
                                 "value" => "MAX-BITRATE-UPLINK"));
        $this->setAttributeStart(0x36, array(
                                 "name" => "name",
                                 "value" => "MAX-BITRATE-DNLINK"));
        $this->setAttributeStart(0x37, array(
                                 "name" => "name",
                                 "value" => "RESIDUAL-BER"));
        $this->setAttributeStart(0x38, array(
                                 "name" => "name",
                                 "value" => "SDU-ERROR-RATIO"));
        $this->setAttributeStart(0x39, array(
                                 "name" => "name",
                                 "value" => "TRAFFIC-HANDL-PRIO"));
        $this->setAttributeStart(0x3a, array(
                                 "name" => "name",
                                 "value" => "TRANSFER-DELAY"));
        $this->setAttributeStart(0x3b, array(
                                 "name" => "name",
                                 "value" => "GUARANTEED-BITRATE-UPLINK"));
        $this->setAttributeStart(0x3c, array(
                                 "name" => "name",
                                 "value" => "GUARANTEED-BITRATE-DNLINK"));
        $this->setAttributeStart(0x3d, array(
                                 "name" => "name",
                                 "value" => "PXADDR-FQDN"));
        $this->setAttributeStart(0x3e, array(
                                 "name" => "name",
                                 "value" => "PROXY-PW"));
        $this->setAttributeStart(0x3f, array(
                                 "name" => "name",
                                 "value" => "PPGAUTH-TYPE"));
        $this->setAttributeStart(0x47, array(
                                 "name" => "name",
                                 "value" => "PULLENABLED"));
        $this->setAttributeStart(0x48, array(
                                 "name" => "name",
                                 "value" => "DNS-ADDR"));
        $this->setAttributeStart(0x49, array(
                                 "name" => "name",
                                 "value" => "MAX-NUM-RETRY"));
        $this->setAttributeStart(0x4a, array(
                                 "name" => "name",
                                 "value" => "FIRST-RETRY-TIMEOUT"));
        $this->setAttributeStart(0x4b, array(
                                 "name" => "name",
                                 "value" => "REREG-THRESHOLD"));
        $this->setAttributeStart(0x4c, array(
                                 "name" => "name",
                                 "value" => "T-BIT"));
        $this->setAttributeStart(0x4e, array(
                                 "name" => "name",
                                 "value" => "AUTH-ENTITY"));
        $this->setAttributeStart(0x4f, array(
                                 "name" => "name",
                                 "value" => "SPI"));


       //ADDRTYPE Value
       $this->setAttributeValue(0x85, "IPV4");
       $this->setAttributeValue(0x86, "IPV6");
       $this->setAttributeValue(0x87, "E164");
       $this->setAttributeValue(0x88, "ALPHA");
       $this->setAttributeValue(0x89, "APN");
       $this->setAttributeValue(0x8a, "SCODE");
       $this->setAttributeValue(0x8b, "TETRA-ITSI");
       $this->setAttributeValue(0x8c, "MAN");

       //CALLTYPE Value
       $this->setAttributeValue(0x90, "ANALOG-MODEM");
       $this->setAttributeValue(0x91, "V.120");
       $this->setAttributeValue(0x92, "V.110");
       $this->setAttributeValue(0x93, "X.31");
       $this->setAttributeValue(0x94, "BIT-TRANSPARENT");
       $this->setAttributeValue(0x95, "DIRECT-ASYNCHRONOURS-DATA-SERVICE");


       //AUTHTYPE/PXAUTH-TYPE  Value
       $this->setAttributeValue(0x9a, "PAP");
       $this->setAttributeValue(0x9b, "CHAP");
       $this->setAttributeValue(0x9c, "HTTP-BASIC");
       $this->setAttributeValue(0x9d, "HTTP-DIGEST");
       $this->setAttributeValue(0x9e, "WTLS-SS");
       $this->setAttributeValue(0x9f, "MD5");


       //BEARER VALUE
       $this->setAttributeValue(0xa2, "GSM-USSD");
       $this->setAttributeValue(0xa3, "GSM-SMS");
       $this->setAttributeValue(0xa4, "ANSI-136-GUTS");
       $this->setAttributeValue(0xa5, "IS-95-CDMA-SMS");
       $this->setAttributeValue(0xa6, "IS-95-CDMA-CSD");
       $this->setAttributeValue(0xa7, "IS-95-CDMA-PACKET");
       $this->setAttributeValue(0xa8, "ANSI-136-CSD");
       $this->setAttributeValue(0xa9, "ANSI-136-GPRS");
       $this->setAttributeValue(0xaa, "GSM-CSD");
       $this->setAttributeValue(0xab, "GSM-GPRS");
       $this->setAttributeValue(0xac, "AMPS-CDPD");
       $this->setAttributeValue(0xad, "PDC-CSD");
       $this->setAttributeValue(0xae, "PDC-PACKET");
       $this->setAttributeValue(0xaf, "IDEN-SMS");
       $this->setAttributeValue(0xb0, "IDEN-CSD");
       $this->setAttributeValue(0xb1, "IDEN-PACKET");
       $this->setAttributeValue(0xb2, "FLEX/REFLEX");
       $this->setAttributeValue(0xb3, "PHS-SMS");
       $this->setAttributeValue(0xb4, "PHS-CSD");
       $this->setAttributeValue(0xb5, "TETRA-SDS");
       $this->setAttributeValue(0xb6, "TETRA-PACKET");
       $this->setAttributeValue(0xb7, "ANSI-136-GHOST");
       $this->setAttributeValue(0xb8, "MOBITEX-MPAK");
       $this->setAttributeValue(0xb9, "CDMA2000-1X-SIMPLE-IP");
       $this->setAttributeValue(0xba, "CDMA2000-1X-MOBILE-IP");


       //LINKSPEED VALUE
       $this->setAttributeValue(0xc5, "AUTOBAUDING");

       //SERVICE VALUE
       $this->setAttributeValue(0xca, "CL-WSP");
       $this->setAttributeValue(0xcb, "CO-WSP");
       $this->setAttributeValue(0xcc, "CL-SEC-WSP");
       $this->setAttributeValue(0xcd, "CO-SEC-WSP");
       $this->setAttributeValue(0xce, "CL-SEC-WTA");
       $this->setAttributeValue(0xcf, "CO-SEC-WTA");
       $this->setAttributeValue(0xd0, "OTA-HTTP-TO");
       $this->setAttributeValue(0xd1, "OTA-HTTP-TLS-TO");
       $this->setAttributeValue(0xd2, "OTA-HTTP-PO");
       $this->setAttributeValue(0xd3, "OTA-HTTP-TLS-PO");

         //AUTH-ENTITY VALUE
       $this->setAttributeValue(0xe0, "AAA");
       $this->setAttributeValue(0xe1, "HA");







       // $this->setAttribute(0x55, 'NAPDEF');
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
    { "version",    "1.0",              0x00, 0x46 },

    /* Characteristic *
    { "type",        NULL,                  0x00, 0x50 },
    { "type",        "PXLOGICAL",           0x00, 0x51 },
    { "type",        "PXPHYSICAL",          0x00, 0x52 },
    { "type",        "PORT",                0x00, 0x53 },
    { "type",        "VALIDITY",            0x00, 0x54 },
    { "type",        "NAPDEF",              0x00, 0x55 },
    { "type",        "BOOTSTRAP",           0x00, 0x56 },
    { "type",        "VENDORCONFIG",        0x00, 0x57 },
    { "type",        "CLIENTIDENTITY",      0x00, 0x58 },
    { "type",        "PXAUTHINFO",          0x00, 0x59 },
    { "type",        "NAPAUTHINFO",         0x00, 0x5a },
    { "type",        "ACCESS",              0x00, 0x5b }, /* OMA *

    { "type",        NULL,                  0x01, 0x50 }, /* OMA *
    { "type",        "PORT",                0x01, 0x53 }, /* OMA *
    { "type",        "CLIENTIDENTITY",      0x01, 0x58 }, /* OMA *
    { "type",        "APPLICATION",         0x01, 0x55 }, /* OMA *
    { "type",        "APPADDR",             0x01, 0x56 }, /* OMA *
    { "type",        "APPAUTH",             0x01, 0x57 }, /* OMA *
    { "type",        "RESOURCE",            0x01, 0x59 }, /* OMA *

    /* Parm *
    { "name",        NULL,                  0x00, 0x05 },
    { "value",       NULL,                  0x00, 0x06 },
    { "name",        "NAME",                0x00, 0x07 },
    { "name",        "NAP-ADDRESS",         0x00, 0x08 },
    { "name",        "NAP-ADDRTYPE",        0x00, 0x09 },
    { "name",        "CALLTYPE",            0x00, 0x0a },
    { "name",        "VALIDUNTIL",          0x00, 0x0b },
    { "name",        "AUTHTYPE",            0x00, 0x0c },
    { "name",        "AUTHNAME",            0x00, 0x0d },
    { "name",        "AUTHSECRET",          0x00, 0x0e },
    { "name",        "LINGER",              0x00, 0x0f },
    { "name",        "BEARER",              0x00, 0x10 },
    { "name",        "NAPID",               0x00, 0x11 },
    { "name",        "COUNTRY",             0x00, 0x12 },
    { "name",        "NETWORK",             0x00, 0x13 },
    { "name",        "INTERNET",            0x00, 0x14 },
    { "name",        "PROXY-ID",            0x00, 0x15 },
    { "name",        "PROXY-PROVIDER-ID",   0x00, 0x16 },
    { "name",        "DOMAIN",              0x00, 0x17 },
    { "name",        "PROVURL",             0x00, 0x18 },
    { "name",        "PXAUTH-TYPE",         0x00, 0x19 },
    { "name",        "PXAUTH-ID",           0x00, 0x1a },
    { "name",        "PXAUTH-PW",           0x00, 0x1b },
    { "name",        "STARTPAGE",           0x00, 0x1c },
    { "name",        "BASAUTH-ID",          0x00, 0x1d },
    { "name",        "BASAUTH-PW",          0x00, 0x1e },
    { "name",        "PUSHENABLED",         0x00, 0x1f },
    { "name",        "PXADDR",              0x00, 0x20 },
    { "name",        "PXADDRTYPE",          0x00, 0x21 },
    { "name",        "TO-NAPID",            0x00, 0x22 },
    { "name",        "PORTNBR",             0x00, 0x23 },
    { "name",        "SERVICE",             0x00, 0x24 },
    { "name",        "LINKSPEED",           0x00, 0x25 },
    { "name",        "DNLINKSPEED",         0x00, 0x26 },
    { "name",        "LOCAL-ADDR",          0x00, 0x27 },
    { "name",        "LOCAL-ADDRTYPE",      0x00, 0x28 },
    { "name",        "CONTEXT-ALLOW",       0x00, 0x29 },
    { "name",        "TRUST",               0x00, 0x2a },
    { "name",        "MASTER",              0x00, 0x2b },
    { "name",        "SID",                 0x00, 0x2c },
    { "name",        "SOC",                 0x00, 0x2d },
    { "name",        "WSP-VERSION",         0x00, 0x2e },
    { "name",        "PHYSICAL-PROXY-ID",   0x00, 0x2f },
    { "name",        "CLIENT-ID",           0x00, 0x30 },
    { "name",        "DELIVERY-ERR-SDU",    0x00, 0x31 },
    { "name",        "DELIVERY-ORDER",      0x00, 0x32 },
    { "name",        "TRAFFIC-CLASS",       0x00, 0x33 },
    { "name",        "MAX-SDU-SIZE",        0x00, 0x34 },
    { "name",        "MAX-BITRATE-UPLINK",  0x00, 0x35 },
    { "name",        "MAX-BITRATE-DNLINK",  0x00, 0x36 },
    { "name",        "RESIDUAL-BER",        0x00, 0x37 },
    { "name",        "SDU-ERROR-RATIO",     0x00, 0x38 },
    { "name",        "TRAFFIC-HANDL-PRIO",  0x00, 0x39 },
    { "name",        "TRANSFER-DELAY",      0x00, 0x3a },
    { "name",        "GUARANTEED-BITRATE-UPLINK",   0x00, 0x3b },
    { "name",        "GUARANTEED-BITRATE-DNLINK",   0x00, 0x3c },
    { "name",        "PXADDR-FQDN",         0x00, 0x3d }, /* OMA *
    { "name",        "PROXY-PW",            0x00, 0x3e }, /* OMA *
    { "name",        "PPGAUTH-TYPE",        0x00, 0x3f }, /* OMA *
    { "name",        "PULLENABLED",         0x00, 0x47 }, /* OMA *
    { "name",        "DNS-ADDR",            0x00, 0x48 }, /* OMA *
    { "name",        "MAX-NUM-RETRY",       0x00, 0x49 }, /* OMA *
    { "name",        "FIRST-RETRY-TIMEOUT", 0x00, 0x4a }, /* OMA *
    { "name",        "REREG-THRESHOLD",     0x00, 0x4b }, /* OMA *
    { "name",        "T-BIT",               0x00, 0x4c }, /* OMA *
    { "name",        "AUTH-ENTITY",         0x00, 0x4e }, /* OMA *
    { "name",        "SPI",                 0x00, 0x4f }, /* OMA *

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

    { NULL,          NULL,                  0x00, 0x00 }
};
*/
        
        //wap-provisioning specifies 2 code pages ??what to call them??
            $this->setCodePage(0, DPI_DTD_PROV_1_0, 'prov');
            $this->setCodePage(1, DPI_DTD_PROV_1_0, 'oma');
            //$this->setCodePage(1, DPI_DTD_METINF_1_0, 'syncml:metinf1.0');
            $this->setURI('prov');
        //}*/
    }

}
