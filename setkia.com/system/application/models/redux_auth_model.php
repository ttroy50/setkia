<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" :
 * <thepixeldeveloper@googlemail.com> wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Mathew Davies
 * ----------------------------------------------------------------------------
 */
 
/**
* redux_auth_model
*/
class redux_auth_model extends Model
{
	/**
	 * Holds an array of tables used in
	 * redux.
	 *
	 * @var string
	 **/
	public $tables = array();
	
	/**
	 * activation code
	 *
	 * @var string
	 **/
	public $activation_code;
	
	/**
	 * forgotten password key
	 *
	 * @var string
	 **/
	public $forgotten_password_code;
	
	/**
	 * new password
	 *
	 * @var string
	 **/
	public $new_password;
	
	/**
	 * Identity
	 *
	 * @var string
	 **/
	public $identity;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->config('redux_auth');
		$this->tables  = $this->config->item('tables');
		$this->columns = $this->config->item('columns');
        $this->billing_columns = $this->config->item('billing_columns');
        $this->settings_columns = $this->config->item('settings_columns');
	}
	
	/**
	 * Misc functions
	 * 
	 * Hash password : Hashes the password to be stored in the database.
     * Hash password db : This function takes a password and validates it
     * against an entry in the users table.
     * Salt : Generates a random salt value.
	 *
	 * @author Mathew
	 */
	 
	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password($password = false)
	{
	    $salt_length = $this->config->item('salt_length');
	    
	    if ($password === false)
	    {
	        return false;
	    }
	    
		$salt = $this->salt();
		
		$password = $salt . substr(sha1($salt . $password), 0, -$salt_length);
		
		return $password;		
	}
	
	/**
	 * This function takes a password and validates it
     * against an entry in the users table.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password_db($identity = false, $password = false)
	{
	    $identity_column   = $this->config->item('identity');
	    $users_table       = $this->tables['users'];
	    $salt_length       = $this->config->item('salt_length');
	    
	    if ($identity === false || $password === false)
	    {
	        return false;
	    }
	    
	    $query  = $this->db->select('password')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);
            
        $result = $query->row();
        
		if ($query->num_rows() !== 1)
		{
		    return false;
	    }
	    
		$salt = substr($result->password, 0, $salt_length);

		$password = $salt . substr(sha1($salt . $password), 0, -$salt_length);
        
		return $password;
	}
	
	/**
	 * Generates a random salt value.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function salt()
	{
		return substr(md5(uniqid(rand(), true)), 0, $this->config->item('salt_length'));
	}
    
	/**
	 * Activation functions
	 * 
     * Activate : Validates and removes activation code.
     * Deactivae : Updates a users row with an activation code.
	 *
	 * @author Mathew
	 */
	
	/**
	 * activate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($code = false, $identity = false)
	{
	    $identity_column = $this->config->item('identity');
	    $users_table     = $this->tables['users'];
	    
	    if ($code === false || $identity == false)
	    {
	        return false;
	    }
	  
	    $query = $this->db->select($identity_column)
                	      ->where('activation_code', $code)
                          ->where($identity_column, $identity)
                	      ->limit(1)
                	      ->get($users_table);
                	      
		$result = $query->row();
        
		if ($query->num_rows() !== 1)
		{
		    return false;
		}
	    
		$returnedIdentity = $result->{$identity_column};
		
		$data = array('activation_code' => '');
        
		$this->db->update($users_table, $data, array($identity_column => $returnedIdentity));
		
		return ($this->db->affected_rows() == 1) ? true : false;
	}
	
	/**
	 * Deactivate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($username = false, $sms_activation = false)
	{
	    $users_table = $this->tables['users'];
	    
	    if ($username === false)
	    {
	        return false;
	    }
	    if($sms_activation)
        {
            //salted with username because we only take the first 6 characters
            $hash = sha1(md5(microtime().$username).$username);
            $activation_code = '';
            for($i = 0; $i < 6; $i++)
            {
                $activation_code .= $hash[rand(0, strlen($hash))];
            }

            $this->activation_code = $activation_code;
        }
        else
        {
            $activation_code = sha1(md5(microtime()));
            $this->activation_code = $activation_code;
        }
		
		$data = array('activation_code' => $activation_code);
        
		$this->db->update($users_table, $data, array('username' => $username));
		
		return ($this->db->affected_rows() == 1) ? true : false;
	}

	/**
	 * change password
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function change_password($identity = false, $old = false, $new = false)
	{
	    $identity_column   = $this->config->item('identity');
	    $users_table       = $this->tables['users'];
	    
	    if ($identity === false || $old === false || $new === false)
	    {
	        return false;
	    }
	    
	    $query  = $this->db->select('password')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);
                    	   
	    $result = $query->row();

	    $db_password = $result->password; 
	    $old         = $this->hash_password_db($identity, $old);
	    $new         = $this->hash_password($new);

	    if ($db_password === $old)
	    {
	        $data = array('password' => $new);
	        
	        $this->db->update($users_table, $data, array($identity_column => $identity));
	        
	        return ($this->db->affected_rows() == 1) ? true : false;
	    }
	    
	    return false;
	}
	
	/**
	 * Checks username.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function username_check($username = false)
	{
	    $users_table = $this->tables['users'];
	    
	    if ($username === false)
	    {
	        return false;
	    }
	    
	    $query = $this->db->select('id')
                           ->where('username', $username)
                           ->limit(1)
                           ->get($users_table);
		
		if ($query->num_rows() == 1)
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks email.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function email_check($email = false)
	{
	    $users_table = $this->tables['users'];
	    
	    if ($email === false)
	    {
	        return false;
	    }
	    
	    $query = $this->db->select('id')
                           ->where('email', $email)
                           ->limit(1)
                           ->get($users_table);
		
		if ($query->num_rows() == 1)
		{
			return true;
		}
		
		return false;
	}

    /**
	 * Checks email.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function updateemail_check($identity = false, $email = false)
	{
        $identity_column = $this->config->item('identity');
	    $users_table = $this->tables['users'];

	    if ($email === false && $identity == false)
	    {
	        return false;
	    }

	    $query = $this->db->select('id')
                           ->where('email', $email)
                           ->where($identity_column.' !=', $identity)
                           ->limit(1)
                           ->get($users_table);

		if ($query->num_rows() == 1)
		{
			return true;
		}

		return false;
	}

    	/**
	 * Checks email.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function phonenumber_check($phoneNo = false)
	{
	    $users_table = $this->tables['users'];

	    if ($phoneNo === false)
	    {
	        return false;
	    }
        
        $cleanup_chr = array ("+", " ", "(", ")", "\r", "\n", "\r\n");
        $phoneNo = str_replace($cleanup_chr, "", $phoneNo);

        $query = $this->db->select('id')
                           ->like('phonenumber', $phoneNo)
                           ->limit(1)
                           ->get($users_table);

		if ($query->num_rows() == 1)
		{
			return true;
		}

		return false;
	}
	
	/**
	 * Identity check
	 *
	 * @return void
	 * @author Mathew
	 **/
	protected function identity_check($identity = false)
	{
	    $identity_column = $this->config->item('identity');
	    $users_table     = $this->tables['users'];
	    
	    if ($identity === false)
	    {
	        return false;
	    }
	    
	    $query = $this->db->select('id')
                           ->where($identity_column, $identity)
                           ->limit(1)
                           ->get($users_table);
		
		if ($query->num_rows() == 1)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Insert a forgotten password key.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password($email = false)
	{
	    $users_table = $this->tables['users'];
	    
	    if ($email === false)
	    {
	        return false;
	    }
	    
	    $query = $this->db->select('forgotten_password_code')
                    	   ->where('email', $email)
                    	   ->limit(1)
                    	   ->get($users_table);
            
        $result = $query->row();
		
		$code = $result->forgotten_password_code;

		if (empty($code))
		{
			$key = $this->hash_password(microtime().$email);
			
			$this->forgotten_password_code = $key;
		
			$data = array('forgotten_password_code' => $key);
			
			$this->db->update($users_table, $data, array('email' => $email));
			
			return ($this->db->affected_rows() == 1) ? true : false;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code = false)
	{
	    $users_table = $this->tables['users'];
	    $identity_column = $this->config->item('identity'); 
	    
	    if ($code === false)
	    {
	        return false;
	    }
	    
	    $query = $this->db->select('id')
                    	   ->where('forgotten_password_code', $code)
                           ->limit(1)
                    	   ->get($users_table);
        
        $result = $query->row();
        
        if ($query->num_rows() > 0)
        {
            $salt       = $this->salt();
		    $password   = $this->hash_password($salt);
		    
		    $this->new_password = $salt;
		    
            $data = array('password'                => $password,
                          'forgotten_password_code' => '0');
            
            $this->db->update($users_table, $data, array('forgotten_password_code' => $code));

            return true;
        }
        
        return false;
	}


	/**
	 * settings profile
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function settingsProfile($identity = false)
	{
        $users_table     = $this->tables['users'];
        $settings_table  = $this->tables['settings'];
        $meta_join       = $this->config->item('join');
        $identity_column = $this->config->item('identity');




	    if ($identity === false)
	    {
	        return false;
	    }

		$this->db->select($users_table.'.id, '.
						  $users_table.'.username, ' .
						  $users_table.'.password, '.
						  $users_table.'.email, '.
                          $users_table.'.phonenumber,'.
						  $users_table.'.activation_code, '.
						  $users_table.'.forgotten_password_code , '.
						  $users_table.'.ip_address');

        if (!empty($this->settings_columns))
		{
		    foreach ($this->settings_columns as $values)
    		{
    			$this->db->select($settings_table.'.'.$values);
    		}
		}

		$this->db->from($users_table);
        $this->db->join($settings_table, $users_table.'.id = '.$settings_table.'.'.$meta_join, 'left');
	    $this->db->where($users_table.'.'.$identity_column, $identity);

		$this->db->limit(1);
		$i = $this->db->get();

		return ($i->num_rows > 0) ? $i->result_array() : false;
	}

	/**
	 * profile
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function profile($identity = false)
	{
	    $users_table     = $this->tables['users'];
	    $groups_table    = $this->tables['groups'];
	    $meta_table      = $this->tables['meta'];
        $billing_table   = $this->tables['user_billing'];
	    $meta_join       = $this->config->item('join');
	    $identity_column = $this->config->item('identity');    
	    
	    if ($identity === false)
	    {
	        return false;
	    }
	    
		$this->db->select($users_table.'.id, '.
						  $users_table.'.username, ' .
						  $users_table.'.password, '.
						  $users_table.'.email, '.
                          $users_table.'.phonenumber,'.
						  $users_table.'.activation_code, '.
						  $users_table.'.forgotten_password_code , '.
						  $users_table.'.ip_address, '.
						  $groups_table.'.name AS `group`');
		
		if (!empty($this->columns))
		{
		    foreach ($this->columns as $value)
    		{
    			$this->db->select($meta_table.'.'.$value);
    		}
		}

        if (!empty($this->billing_columns))
		{
		    foreach ($this->billing_columns as $values)
    		{
    			$this->db->select($billing_table.'.'.$values);
    		}
		}
		
		$this->db->from($users_table);
		$this->db->join($meta_table, $users_table.'.id = '.$meta_table.'.'.$meta_join, 'left');
		$this->db->join($groups_table, $users_table.'.group_id = '.$groups_table.'.id', 'left');
        $this->db->join($billing_table, $users_table.'.id = '.$billing_table.'.'.$meta_join, 'left');
		
		if (strlen($identity) === 40)
	    {
	        $this->db->where($users_table.'.forgotten_password_code', $identity);
	    }
	    else
	    {
	        $this->db->where($users_table.'.'.$identity_column, $identity);
	    }
	    
		$this->db->limit(1);
		$i = $this->db->get();
		
		return ($i->num_rows > 0) ? $i->result_array() : false;
	}

	/**
	 * Basic functionality
	 * 
	 * Register
	 * Login
	 *
	 * @author Mathew
	 */
	
	/**
	 * register
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function register($username = false, $password = false, $email = false, $phoneNo = false, $charge = 1)
	{
	    $users_table        = $this->tables['users'];
	    $meta_table         = $this->tables['meta'];
	    $groups_table       = $this->tables['groups'];
        $billing_table      = $this->tables['user_billing'];
        $settings_table      = $this->tables['settings'];
	    $meta_join          = $this->config->item('join');
	    $additional_columns = $this->config->item('columns');
        $billing_columns    = $this->config->item('billing_columns');
        $free_sms           = $this->config->item('default_free_sms_num');
        $settings_columns   = $this->config->item('settings_columns');
        $settings_defaults  = $this->config->item('settings_defaults');
        $allow_registration = $this->config->item('allow_registration');
	    
	    if ($username === false || $password === false || $email === false || $phoneNo === false || $allow_registration === false)
	    {
	        return false;
	    }
	    
        // Group ID
	    $query    = $this->db->select('id')->where('name', $this->config->item('default_group'))->get($groups_table);
	    $result   = $query->row();
	    $group_id = $result->id;

        $time = time();
        // IP Address
        $ip_address = $this->input->ip_address();
	    
		$password = $this->hash_password($password);
		
        // Users table.
		$data = array('username' => $username, 
					  'password' => $password, 
					  'email'    => $email,
                      'phonenumber' => $phoneNo,
					  'group_id' => $group_id,
					  'ip_address' => $ip_address,
                      'date_registered' => $time
        );
		  
		$this->db->insert($users_table, $data);
        
		// Meta table.
		$id = $this->db->insert_id();
		
		$data = array($meta_join => $id);
		
		if (!empty($additional_columns))
	    {
	        foreach ($additional_columns as $input)
	        {
	            $data[$input] = $this->input->post($input);
	        }
	    }
        
		$this->db->insert($meta_table, $data);

        $billing_data = array($meta_join => $id);

        
        if(!empty($billing_columns))
        {
            foreach($billing_columns as $billing_input)
            {
                if(strpos($billing_input, 'charge_per_part') == 0)
                {
                    $billing_data[$billing_input] = $charge;
                }
                else
                {
                    $billing_data[$billing_input] = $free_sms;
                }
            }
        }

        $this->db->insert($billing_table, $billing_data);

        $settings_data = array($meta_join => $id);

        if(!empty($settings_columns))
        {
            foreach($settings_columns as $settings_input)
            {
                $settings_data[$settings_input] = $settings_defaults[$settings_input];
            }
        }

        $this->db->insert($settings_table, $settings_data);
        

		return ($this->db->affected_rows() > 0) ? true : false;
	}
	
	/**
	 * login
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function login($identity = false, $password = false)
	{
	    $identity_column = $this->config->item('identity');
	    $users_table     = $this->tables['users'];
	    
	    if ($identity === false || $password === false || $this->identity_check($identity) == false)
	    {
	        return 0;
	    }
	    
	    $query = $this->db->select($identity_column.', password, activation_code')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);
	    
        $result = $query->row();
        
        if ($query->num_rows() == 1)
        {
            $password = $this->hash_password_db($identity, $password);
            
            if (!empty($result->activation_code)) { return 2; }
            
    		if ($result->password === $password)
    		{
    		    $this->session->set_userdata($identity_column,  $result->{$identity_column});
    		    return 1;
    		}
        }
        
		return 0;
	}

    public function get_id_for_identity($identity)
    {
        $identity_column = $this->config->item('identity');
	    $users_table     = $this->tables['users'];

        if($identity === false )
        {
            return 0;
        }

        $query = $this->db->select($identity_column.', id')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);

        $result = $query->row();

        if ($query->num_rows() == 1)
        {
            if($result->id > 0)
                return $result->id;
        }

        return 0;

    }

    public function get_credits_available($identity)
    {
        $identity_column = $this->config->item('identity');
	    $users_table     = $this->tables['users'];
        $billing_table   = $this->tables['user_billing'];

        if($identity === false )
        {
            return 0;
        }

        $query = $this->db->select($identity_column.', id')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);

        $result = $query->row();
        $id = 0;

        if ($query->num_rows() == 1)
        {
            if($result->id > 0)
                $id = $result->id;
        }
        else
            return 0;


        $credit_query = $this->db->select('user_id, sms_available')
                                    -> where('uses_id', $id)
                                    ->limit(1)
                                    ->get($billing_table);

        $credit_result = $credit_query->row();

        if ($query->num_rows() == 1)
        {
            if(isset($result->sms_available))
                $id = $result->sms_available;
        }

        return 0;

    }

    public function updateSettings($identity = false, $mailinglist = false)
    {
        $users_table     = $this->tables['users'];
        $settings_table  = $this->tables['settings'];
        $meta_join       = $this->config->item('join');
        $identity_column = $this->config->item('identity');

        if ($identity === false || $mailinglist === false)
	    {
	        return false;
	    }

        $query = $this->db->select($identity_column.', id')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);

        $result = $query->row();
        $id = 0;

        if ($query->num_rows() == 1)
        {
            if($result->id > 0)
                $id = $result->id;
        }
        else
            return false;


        $data = array('mailinglist' => $mailinglist);

		$this->db->update($settings_table, $data, array($meta_join => $id));

		return ($this->db->affected_rows() == 1) ? true : false;
    }

    public function updateProfile($identity = false, $email = null, $first_name = null, $last_name = null)
    {
        $users_table     = $this->tables['users'];
        $meta_table      = $this->tables['meta'];
        $meta_join       = $this->config->item('join');
        $identity_column = $this->config->item('identity');

        if ($identity === false || $email === false && $first_name === false && $last_name === false)
	    {
	        return false;
	    }

        $query = $this->db->select($identity_column.', id')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);

        $result = $query->row();
        $id = 0;

        if ($query->num_rows() == 1)
        {
            if($result->id > 0)
                $id = $result->id;
        }
        else
            return false;

        $dataUsers = array('email' => $email);
        $this->db->update($users_table, $dataUsers, array('id' => $id));

        $emailupdated = false;
        if($this->db->affected_rows() == 1)
        {
            $emailupdated = true;
        }

        $data = array('first_name' => $first_name,
                    'last_name' => $last_name
                    );

		$this->db->update($meta_table, $data, array($meta_join => $id));

		return ($this->db->affected_rows() == 1 && $emailupdated) ? true : false;
    }

    function smssent($identity, $number = 1)
    {
        $identity_column = $this->config->item('identity');
	    $users_table     = $this->tables['users'];
        $billing_table   = $this->tables['user_billing'];
        $meta_join       = $this->config->item('join');

        if($identity === false )
        {
            return false;
        }

        $query = $this->db->select($identity_column.', id')
                    	   ->where($identity_column, $identity)
                    	   ->limit(1)
                    	   ->get($users_table);

        $result = $query->row();
        $id = 0;

        if ($query->num_rows() == 1)
        {
            if($result->id > 0)
                $id = $result->id;
        }
        else
            return false;

        $SMSquery = $this->db->select($meta_join.', id, sms_available, charge_per_part')
                    	   ->where($meta_join, $id)
                    	   ->limit(1)
                    	   ->get($billing_table);
        
        $SMSresult = $SMSquery->row();
        //echo var_dump($SMSresult);
        $sms_available = 0;
        if ($SMSquery->num_rows() == 1)
        {
            $sms_available = $SMSresult->sms_available;
            $charge_per_part = $SMSresult->charge_per_part;
        }
        else
            return false;

        $newSMS_available = $sms_available - ($number * $charge_per_part);

        $data = array('sms_available' => $newSMS_available);

		$this->db->update($billing_table, $data, array($meta_join => $id));

		return ($this->db->affected_rows() == 1) ? true : false;
    }
}


