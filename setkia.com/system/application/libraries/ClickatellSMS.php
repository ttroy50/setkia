<?php if (!defined('BASEPATH')) { die('No direct script access allowed'); }
/*
* Clickatell SMS api for code igniter
 */

/**
 * Description of ClickatellSMS
 *
 * @author ttroy
 */
class ClickatellSMS {
   /**
    * Clickatell API-ID
    * @link http://sourceforge.net/forum/forum.php?thread_id=1005106&forum_id=344522 How to get CLICKATELL API ID?
    * @public integer
    */
    public $api_id = "";

    /**
    * Clickatell username
    * @public mixed
    */
    public $user = "";

    /**
    * Clickatell password
    * @public mixed
    */
    public $password = "";

    /**
    * Use SSL (HTTPS) protocol
    * @public bool
    */
    public $use_ssl = false;

    /**
    * Define SMS balance limit below class will not work
    * @public integer
    */
    public $balace_limit = 0;

    /**
    * Gateway command sending method (curl,fopen)
    * @public mixed
    */
    public $sending_method = "fopen";

    /**
    * Does to use facility for delivering Unicode messages
    * @public bool
    */
    public $unicode = false;

    /**
    * Optional CURL Proxy
    * @public bool
    */
    public $curl_use_proxy = false;

    /**
    * Proxy URL and PORT
    * @public mixed
    */
    public $curl_proxy = "http://127.0.0.1:8080";

    /**
    * Proxy username and password
    * @public mixed
    */
    public $curl_proxyuserpwd = "login:secretpass";

    /**
    * Callback
    * 0 - Off
    * 1 - Returns only intermediate statuses
    * 2 - Returns only final statuses
    * 3 - Returns both intermediate and final statuses
    * @public integer
    */
    public $callback = 0;

    /**
    * Session publiciable
    * @public mixed
    */
    public $session = null;

    /**
    * from username to send from
    * @public mixed
    */
    public $from = "";

    var $_messageID = '';

    var $_messageParts = 0;

	public $apiMethod = array(
        'startSession' => 'startSession',
		'send' => 'send',
		'getbalance' => 'getbalance',
        'coverageCheck' => 'coverageCheck',
        'queryMessage' => 'queryMessage',
        'getValidationStatusAsText' => 'getValidationStatusAsText',
        'getMessageStatusAsText' => 'getMessageStatusAsText',
        'initialize' => 'initialize'
	);

        /**
    * Class constructor
    * Create SMS object and authenticate SMS gateway
    * @return object New SMS object.
    * @access public
    */
    function ClickatellSMS ($config = array()) {
        $this->CI =& get_instance();

        if (count($config) > 0)
        {
            $this->initialize($config);
        }
        else
        {
            if ($this->use_ssl)
            {
                $this->base   = "http://api.clickatell.com/http";
                $this->base_s = "http://api.clickatell.com/http";
            }
            else
            {
                $this->base   = "http://api.clickatell.com/http";
                $this->base_s = $this->base;
            }
        }
    }

    /**
    * initialize the settings for the app. If you are using a config file doesn't need to be called
     * If not you should pass the config array here ??
     * @param config mixed array  The configuration array
    * @access public
    */
    function initialize($config = array())
    {

        foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

        if ($this->use_ssl)
        {
            $this->base   = "http://api.clickatell.com/http";
            $this->base_s = "http://api.clickatell.com/http";
        }
        else
        {
            $this->base   = "http://api.clickatell.com/http";
            $this->base_s = $this->base;
        }
    }

    /**
    * Start an authentication session for sending requests. Must be called before sending
    * @access public
    */
    function startSession()
    {
        return $this->_auth();
    }



        /**
    * Query SMS credis balance
    * @return integer  number of SMS credits
    * @access public
    */
    function getbalance() {
    	$comm = sprintf ("%s/getbalance?session_id=%s", $this->base, $this->session);
        return $this->_parse_getbalance ($this->_execgw($comm));
    }

    /**
    * Send SMS message
    * @param to mixed  The destination address.
    * @param from mixed  The source/sender address
    * @param text mixed  The text content of the message
    * @return mixed  "OK" or Error
    * @access public
    */
    function send($to=null, $text=null, $cliMsgId = null) {

    	/* Check SMS credits balance */
    	if ($this->getbalance() < $this->balace_limit) {
    	    return "Err: You have reach the SMS credit limit!";
    	};

    	/* Check SMS $text length */
        if ($this->unicode == true) {
            $this->_chk_mbstring();
            if (mb_strlen ($text) > 210) {
        	    return "Err: Your unicode message is too long! (Current lenght=".mb_strlen ($text).")";
        	}
        	/* Does message need to be concatenate */
            if (mb_strlen ($text) > 70) {
                $concat = "&concat=3";
        	} else {
                $concat = "";
            }
        } else {
            if (strlen ($text) > 459) {
    	        return "Err: Your message is too long! (Current lenght=".strlen ($text).")";
    	    }
        	/* Does message need to be concatenate */
            if (strlen ($text) > 160) {
                $concat = "&concat=3";
        	} else {
                $concat = "";
            }
        }

    	/* Check $to and $from is not empty */
        if (empty ($to)) {
    	    return "Err: You not specify destination address (TO)!";
    	}
        if (empty ($this->from)) {
    	    return "Err: You not specify source address (FROM)!";
    	}

        $to = $this->_cleanup_Number($to);
        
        $urlCliMsgId = '';
        if(isset($cliMsgId))
        {
            $urlCliMsgId = '$cliMsgId='.$cliMsgId;
        }

        $callback = '';
        if($this->callback != 0)
            $callback = '&callback='.$this->callback;

    	/* Send SMS now */
    	$comm = sprintf ("%s/sendmsg?session_id=%s&to=%s&from=%s&text=%s&unicode=%s%s%s%s",
            $this->base,
            $this->session,
            rawurlencode($to),
            rawurlencode($this->from),
            $this->encode_message($text),
            $this->unicode,
            $cliMsgId,
            $callback,
            $concat
        );
        return $this->_parse_send ($this->_execgw($comm));
    }



 /**
    * SendUDH SMS message
    * @param to mixed  The destination address.
    * @param from mixed  The source/sender address
    * @param text mixed  The text content of the message
    * @return mixed  "OK" or Error
    * @access public
    */
    function sendUDH($to=null, $data=null, $UDH = null, $cliMsgId = null, $userCredits = 0, $charge_per_part = 1) {

    	/* Check SMS credits balance */
    	if ($this->getbalance() < $this->balace_limit) {
    	    return "Err: Please contact an administrator. Error 301";
    	};

    	/* Check SMS $text length */
        if ($this->unicode == true) {
            $this->_chk_mbstring();
            if (mb_strlen ($data) > 210) {
        	    return "Err: Your unicode message is too long! (Current lenght=".mb_strlen ($text).")";
        	}
        	/* Does message need to be concatenate */
            if (mb_strlen ($data) > 70) {
                $concat = "&concat=3";
        	} else {
                $concat = "";
            }
        } else {
            //if (strlen ($data) > 459) {
    	      //  return "Err: Your message is too long! (Current lenght=)";
    	    //}
        	/* Does message need to be concatenate */
            if ((strlen ($data) + strlen($UDH)) > 280) {
                /*if ((strlen ($data) + strlen($UDH)) > 560)
                    $concat = "&concat=3";
                else
                    $concat = "&concat=2";*/
                $concat = "&concat=".ceil(strlen ($data) / 273);
                if(ceil(strlen ($data) / 273) > 6)
                {
                    return "Err: Your message is too long. The message is in ".ceil(strlen ($data) / 273)." parts";
                }
        	} else {
                $concat = "";
            }
        }

        $this->_messageParts = ceil(strlen ($data) / 273);
        if(($this->_messageParts * $charge_per_part) > $userCredits)
        {
            return "Err: You have insufficent credit to send this message";
        }

    	/* Check $to and $from is not empty */
        if (empty ($to)) {
    	    return "Err: You not specify destination address (TO)!";
    	}
        if (empty ($this->from)) {
    	    return "Err: You not specify source address (FROM)!";
    	}

        $to = $this->_cleanup_Number($to);

        $urlCliMsgId = '';
        if(isset($cliMsgId)){
            $urlCliMsgId = '&cliMsgId='.$cliMsgId;
        }

        $callback = '';
        if($this->callback != 0)
            $callback = '&callback='.$this->callback;


//Sending same ringtone with the udh parameter:
//http://api.clickatell.com/http/sendmsg?session_id=e74dee1bbed22ee3a39f9aeab606ccf9&to=12345678
//90&udh=06050415810000&data=024A3A5585E195B198040042D9049741A69761781B617615617428
//8B525D85E0A26C24C49A617628930BB125E055856049865885D200
//&ota_type=1
    	/* Send SMS now */
    	$comm = sprintf ("%s/sendmsg?session_id=%s&to=%s&from=%s&udh=%s&data=%s&req_feat=16390%s%s%s",
        //$comm = sprintf ("%s/sendota?session_id=%s&to=%s&ota_type=1&from=%s&udh=%s&data=%s&req_feat=16390%s",
            $this->base,
            $this->session,
            rawurlencode($to),
            rawurlencode($this->from),
            rawurlencode($UDH),
            $data,//rawurlencode($data),
            $urlCliMsgId,
            $callback,
            $concat
        );
       // echo "commm = ".$comm;
        return $this->_parse_send ($this->_execgw($comm));
    }

function hex2bin($h)
  {
  if (!is_string($h)) return null;
  $r='';
  for ($a=0; $a<strlen($h)-1; $a+=2) {
      $r.=chr(hexdec($h{$a}.$h{($a+1)}));

  }
  return $r;
  }
/**
    * Send Wap Push message
    * @param to mixed  The destination address.
    * @param from mixed  The source/sender address
    * @param text mixed  The text content of the message
    * @return mixed  "OK" or Error
    * @access public
    */
    function sendWapPushSI($to=null, $si_id = null, $si_url = null, $si_text = null, $si_created = null, $si_expires = null, $si_action = null) {

    	/* Check SMS credits balance */
    	if ($this->getbalance() < $this->balace_limit) {
    	    return "Err: You have reach the SMS credit limit!";
    	};

    	/* Check required parameters are not empty. For cetain params we will assign a defautl value if the are */
        if (empty ($to)) {
    	    return "Err: You not specify destination address (TO)!";
    	}
        if (empty ($si_url)) {
    	    return "Err: You not specify an SI URL!";
    	}
        if (empty ($si_text)) {
    	    return "Err: You not specify any SI text";
    	}
        if (empty ($si_id)) {
            $si_id = time().rand(10, 5000);
    	}
        if (empty ($si_created)) {
            $si_created = gmdate("Y-m-d\TH:i:s\Z");
    	}
        else
        {
            $si_created = gmdate("Y-m-d\TH:i:s\Z", $si_created);
        }
        if (!empty ($si_expires)) {
            $si_expires = "&si_expires=".gmdate("Y-m-d\TH:i:s\Z", $si_expires);
    	}

        if (!empty ($si_action)) {
            //should check si_action ia an allowed value of
            /*
             *  signal-none
                signal-low
                signal-medium
                signal-high
                delete

             */
            $si_action = "&si_action=".$si_action;
        }



        $to = $this->_cleanup_Number($to);

        if($use_ssl)
            $urltosend = "http://api.clickatell.com/mms/si_push?";
        else
            $urltosend = "http://api.clickatell.com/mms/si_push?";

    	/* Send SMS now */
    	$comm = sprintf ("%ssession_id=%s&to=%s&si_id=%s&si_text=%s&si_created=%s&si_expires=%s%s",
            $urltosend,
            $this->session,
            rawurlencode($to),
            rawurlencode($si_id),
            rawurlencode($si_text),
            $si_created,
            $si_expires,
            $si_action
        );
        return $this->_parse_send ($this->_execgw($comm))."+si_id=".$si_id;
    }

        /**
    * Encode message text according to required standard
    * @param text mixed  Input text of message.
    * @return mixed  Return encoded text of message.
    * @access public
    */
    function encode_message ($text) {
        if ($this->unicode != true) {
            //standard encoding
            return rawurlencode($text);
        } else {
            //unicode encoding
            $uni_text_len = mb_strlen ($text, "UTF-8");
            $out_text = "";

            //encode each character in text
            for ($i=0; $i<$uni_text_len; $i++) {
                $out_text .= $this->uniord(mb_substr ($text, $i, 1, "UTF-8"));
            }

            return $out_text;
        }
    }

      /**
    * Unicode function replacement for ord()
    * @param c mixed  Unicode character.
    * @return mixed  Return HEX value (with leading zero) of unicode character.
    * @access public
    */
    function uniord($c) {
        $ud = 0;
        if (ord($c{0})>=0 && ord($c{0})<=127)
            $ud = ord($c{0});
        if (ord($c{0})>=192 && ord($c{0})<=223)
            $ud = (ord($c{0})-192)*64 + (ord($c{1})-128);
        if (ord($c{0})>=224 && ord($c{0})<=239)
            $ud = (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
        if (ord($c{0})>=240 && ord($c{0})<=247)
            $ud = (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
        if (ord($c{0})>=248 && ord($c{0})<=251)
            $ud = (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
        if (ord($c{0})>=252 && ord($c{0})<=253)
            $ud = (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
        if (ord($c{0})>=254 && ord($c{0})<=255) //error
            $ud = false;
        return sprintf("%04x", $ud);
    }

        /**
    * Spend voucher with sms credits
    * @param token mixed  The 16 character voucher number.
    * @return mixed  Status code
    * @access public
    */
    function token_pay ($token) {
        $comm = sprintf ("%s/http/token_pay?session_id=%s&token=%s",
        $this->base,
        $this->session,
        $token);

        return $this->_execgw($comm);
    }

        /**
    * Check if a number is in the coverage area
    * @param msisdn mixed The number to check.
    * @return mixed  Status code
    * @access public
    */
    function coverageCheck($msisdn)
    {
        /* Check $to and $from is not empty */
        if (empty ($msisdn)) {
    	    return "Err: You have not specified a MSISDN!";
    	}

        $msisdn = $this->_cleanup_Number($msisdn);

        $urlstring = null;
        if($this->use_ssl)
            $urlstring = "http://api.clickatell.com/utils/routeCoverage.php";
        else
            $urlstring = "http://api.clickatell.com/utils/routeCoverage.php";
        $comm = sprintf("%s?session_id=%s&msisdn=%s",
        $urlstring,
        $this->session,
        rawurlencode($msisdn));

        return $this->_execgw($comm);
    }


        /**
    * Query a messages status using the ID
    * @param msgID mixed The message ID that was returend when sending the message.
    * @return mixed  Status code
    * @access public
    */
    function queryMessage($msgID)
    {
        if (empty ($msgID)) {
    	    return "Err: You have not specified a message ID";
    	}

        $comm = sprintf("%s/querymsg?session_id=%s&apimsgid=%s",
            $this->base,
            $this->session,
            rawurlencode($msgID)
        );
        //http://api.clickatell.com/http/querymsg?session_id=xxx&apimsgid=XXXXX
        return $this->_execgw($comm);
    }


        /**
    * Delete a message that is queued on the router
    * @param msgID mixed The message ID that was returend when sending the message.
    * @return mixed  Status code
    * @access public
    */
    function deleteMessage($msgID)
    {
        if (empty ($msgID)) {
    	    return "Err: You have not specified a message ID";
    	}

        $comm = sprintf("%s/delmsg?session_id=%s&apimsgid=%s",
            $this->base,
            $this->session,
            rawurlencode($msgID)
        );
        //http://api.clickatell.com/http/delmsg?session_id=xxx&apimsgid=XXXXX
        return $this->_execgw($comm);
    }

        /**
    * Returns the status of a result as text description for displaying to a user
    * @param status mixed The status code to check
    * @return mixed  Text description of status code
    * @access public
    */
    function getValidationStatusAsText($status)
    {
        if (empty ($status)) {
    	    return "Err: You have not specified a status";
    	}

        return $validationStatusCodes[$status];
    }


        /**
    * Returns the message status as text description for displaying to a user
    * @param status mixed The status code to check
    * @return mixed  Text description of status code
    * @access public
    */
    function getMessageStatusAsText($status)
    {
        if (empty ($status)) {
    	    return "Err: You have not specified a status";
    	}

        return $messageStatusCodes[$status];
    }

    function getMessageParts()
    {
        return $this->_messageParts;
    }

    function extractChargeFromStatus($status)
    {
        if (stripos($coverage, "Err") !== false)
	    {
            //default charge is 1
	        return 1;
        }
        else
        {
            $parts = explode(":", $status);
            $charge = array_pop($parts);
            if(isset($charge))
            {
                return trim($charge);
            }
            else
            {
                return 1;
            }

        }
    }
/*****************************************
 *  Private Functions
 *
 */
        /**
    * Authenticate SMS gateway
    * @return mixed  "OK" or script die
    * @access private
    */
    function _auth() {
    	$comm = sprintf ("%s/auth?api_id=%s&user=%s&password=%s", $this->base_s, $this->api_id, $this->user, $this->password);
        $result = $this->_parse_auth ($this->_execgw($comm));
        if(substr($result, 0, 3) === "Err")
        {
            $this->session = null;
            return $result;
        }
        else
        {
            $this->session = $result;
            return "OK";
        }

    }

        /**
    * Execute gateway commands
    * @access private
    */
    function _execgw($command) {
        if ($this->sending_method == "curl")
            return $this->_curl($command);
        if ($this->sending_method == "fopen")
            return $this->_fopen($command);
        return ("Err: Unsupported sending method!");
    }


    /**
    * CURL sending method
    * @access private
    */
    function _curl($command) {
        $this->_chk_curl();
        $ch = curl_init ($command);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER,0);
        if ($this->curl_use_proxy) {
            curl_setopt ($ch, CURLOPT_PROXY, $this->curl_proxy);
            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $this->curl_proxyuserpwd);
        }
        $result=curl_exec ($ch);
        curl_close ($ch);
        return $result;
    }

        /**
    * fopen sending method
    * @access private
    */
    function _fopen($command) {
        $result = '';
        $handler = @fopen ($command, 'r');
        if ($handler) {
            while ($line = @fgets($handler,1024)) {
                $result .= $line;
            }
            fclose ($handler);
            return $result;
        } else {
            return ("Err: 999, Error opening connection to server");
        }
    }

    /**
    * Parse authentication command response text
    * @access private
    */
    function _parse_auth ($result) {
    	$session = substr($result, 4);
        $code = substr($result, 0, 2);

        if ($code!="OK") {
            return ($result);
        }
        return $session;
    }

    /**
    * Parse send command response text
    * @access private
    */
    function _parse_send ($result) {
        $this->_messageID = substr($result, 4);
    	$code = substr($result, 0, 2);
    	if ($code!="ID") {
            $this->_messageID = 'setkia_err_'.md5(substr($result, 4).time());
    	    return $result;
    	} else {
    	    $code = "OK";
    	}
        return $code;
    }

    function get_MessageID()
    {
        return $this->_messageID;
    }

    function generateCliMsgID($salt)
    {
        $cliMsgId = 'sk'.sha1(microtime().$salt);
        if(strlen($cliMsgId) > 32)
            return substr($cliMsgId, 0, 32);
        else
            return $cliMsgId;
    }
    /**
    * Parse getbalance command response text
    * @access private
    */
    function _parse_getbalance ($result) {
    	$result = substr($result, 8);
        return (int)$result;
    }

    /**
    * Check for CURL PHP module
    * @access private
    */
    function _chk_curl() {
        if (!extension_loaded('curl')) {
            return ("Er: CURL not supported");
        }
    }

    /**
    * Check for Multibyte String Functions PHP module - mbstring
    * @access private
    */
    function _chk_mbstring() {
        if (!extension_loaded('mbstring')) {
            return ("Err: mbstring not found");
        }
    }

        /**
    * Cleanup the number you want to send messages to
    * @access private
    */
    function _cleanup_Number($msisdn)
    {
        /* Reformat $to number */
        $cleanup_chr = array ("+", " ", "(", ")", "\r", "\n", "\r\n");
        return str_replace($cleanup_chr, "", $msisdn);
    }


    function validationStatusCodeToString($code)
    {
        if(in_array($code, $this->validationStatusCodes))
            return $this->validationStatusCodes[$code];
         else
            return 'Unknown';
    }

    function messageStatusCodeToString($code)
    {
        if(in_array($code, $this->messageStatusCodes))
            return $this->messageStatusCodes[$code];
         else
            return 'Unknown';
    }
    /**
    * error messages generated by the Clickatell gateway during a validation phase
    * before they accept the message. The array lists the number => text description
    * @private
    */

    private $validationStatusCodes = array (
        '001' => 'Authentication failed',
        '002' => 'Unknown username or password',
        '003' => 'Session ID expired',
        '004' => 'Account frozen',
        '005' => 'Missing session ID',
        '007' => 'IP Lockdown violation',
        '101' => 'Invalid or missing parameters',
        '102' => 'Invalid user data header',
        '103' => 'Unknown API message ID',
        '104' => 'Unknown Client Message ID',
        '105' => 'Invalid destination Address',
        '106' => 'Invalid source address',
        '107' => 'Empty message',
        '108' => 'Invalid or missing API ID',
        '109' => 'Missing message ID',
        '110' => 'Error with email message',
        '111' => 'Invalid protocol',
        '112' => 'Invalid message type',
        '113' => 'Maximum message parts exceeded',
        '114' => 'Cannot route message',
        '115' => 'Message expired',
        '116' => 'Invalid unicode data',
        '120' => 'Invalid delivery time',
        '121' => 'Destination mobile number blocked',
        '122' => 'Destination mobile opted out',
        '123' => 'Invalid Sender ID',
        '128' => 'Number delisted',
        '201' => 'Invalid batch ID',
        '202' => 'No batch template',
        '301' => 'No Credit left',
        '302' => 'Max allowed credit',
        '999' => 'Error opening connection to server'
    );

    /**
    * status messages generated by the Clickatell gateway. The array lists the number => text description
    * @private
    */

    private $messageStatusCodes = array (
        '001' => 'Message unknown',
        '002' => 'Message queued',
        '003' => 'Delivered to gateway',
        '004' => 'Received by recipient',
        '005' => 'Error with message',
        '006' => 'User cancelled message delivery',
        '007' => 'Error delivering message',
        '008' => 'OK',
        '009' => 'Routing Error',
        '010' => 'Message expired',
        '011' => 'Message Queued for later delivery',
        '012' => 'SetKia Error. Please contact an admin'
    );
}
?>
