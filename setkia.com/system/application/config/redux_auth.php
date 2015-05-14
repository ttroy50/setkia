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
	 * Tables.
	 **/
	$config['tables']['groups'] = 'groups';
	$config['tables']['users'] = 'users';
	$config['tables']['meta'] = 'meta';
    $config['tables']['user_billing'] = 'user_billing';
    $config['tables']['settings'] = 'user_settings';
	
    /**
	 * Allow or disallow registraiton
	 */
    $config['allow_registration'] = true;
	/**
	 * Default group, use name
	 */
	$config['default_group'] = 'users';
	 
	/**
	 * Meta table column you want to join WITH.
	 * Joins from users.id
	 **/
	$config['join'] = 'user_id';
	
	/**
	 * Columns in your meta table,
	 * id not required.
	 **/
	$config['columns'] = array('first_name', 'last_name');

    /**
	 * Columns in the billing table,
	 * id not required.
	 **/
    $config['billing_columns'] = array('sms_available', 'total_num_sms_bought', 'charge_per_part');

    /**
	 * Columns in the settings table,
	 * id not required.
	 **/
    $config['settings_columns'] = array('xmlparser', 'mailinglist');

    /**
	 * Default values in the settings table,
	 * id not required.
	 **/
    $config['settings_defaults'] = array(
                                'xmlparser' => '0',
                                'mailinglist' => '0'
                                );

    /**
	 * default number of free sms to give a user when they register.
	 **/
    $config['default_free_sms_num'] = 2;

	/**
	 * A database column which is used to
	 * login with.
	 **/
	$config['identity'] = 'username';

	/**
	 * Email Activation for registration
	 **/
	$config['email_activation'] = false;

    /**
	 * SMS Activation for registration
	 **/
    $config['sms_activation'] = true;
	
	/**
	 * Folder where email templates are stored.
     * Default : redux_auth/
	 **/
	$config['email_templates'] = 'redux_auth/';

	/**
	 * Salt Length
	 **/
	$config['salt_length'] = 10;
	
?>