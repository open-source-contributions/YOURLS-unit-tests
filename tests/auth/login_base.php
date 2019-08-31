<?php

/**
 * This abstract class isn't supposed to be run as tests
 * See login_*.php files
 */
abstract class Login_Base extends PHPUnit_Framework_TestCase
{
    protected $backup_request;
    
    protected function setUp()
    {
        $this->backup_request = $_REQUEST;
    }

    protected function tearDown()
    {
        $_REQUEST = $this->backup_request;
    }

    /**
     * Check that user, as submitted by REQUEST (see phpunit XML config file), is valid
     *
     * @since 0.1
     */
    public function test_login()
    {
        $pre_login    = yourls_did_action('pre_login');
        $login        = yourls_did_action('login');
        $login_failed = yourls_did_action('login_failed');
        
        $this->assertTrue(yourls_is_valid_user());
        
        $this->assertEquals($pre_login + 1, yourls_did_action('pre_login'));
        $this->assertEquals($login + 1, yourls_did_action('login'));
        $this->assertEquals($login_failed, yourls_did_action('login_failed'));
    }
    
    /**
     * Check that auth is shuntable
     *
     * @since 0.1
     */
    public function test_login_shunt()
    {
        yourls_add_filter('shunt_is_valid_user', 'yourls_return_empty_array');
        $this->assertSame(array(), yourls_is_valid_user());
        yourls_remove_filter('shunt_is_valid_user', 'yourls_return_empty_array');
    }
    
    /**
     * Check that auth returns false with no credential
     *
     * @since 0.1
     */
    public function test_login_with_no_credential()
    {
        $_REQUEST = array();
        $login        = yourls_did_action('login');
        $login_failed = yourls_did_action('login_failed');

        $this->assertNotTrue(yourls_is_valid_user());
        
        $this->assertEquals($login, yourls_did_action('login'));
        $this->assertEquals($login_failed + 1, yourls_did_action('login_failed'));
    }
    
    /**
     * Check that auth returns false with empty credential
     *
     * @since 0.1
     */
    public function test_login_with_empty_credential()
    {
        $_REQUEST = array( 'username' => '', 'password' => '' );
        $login        = yourls_did_action('login');
        $login_failed = yourls_did_action('login_failed');

        $this->assertNotTrue(yourls_is_valid_user());

        $this->assertEquals($login, yourls_did_action('login'));
        $this->assertEquals($login_failed + 1, yourls_did_action('login_failed'));
    }
    
    /**
     * Check that auth returns false with incorrect credentials
     *
     * @since 0.1
     */
    public function test_login_with_random_credentials()
    {
        $_REQUEST = array( 'username' => rand_str(), 'password' => rand_str() );
        $login        = yourls_did_action('login');
        $login_failed = yourls_did_action('login_failed');

        $this->assertNotTrue(yourls_is_valid_user());

        $this->assertEquals($login, yourls_did_action('login'));
        $this->assertEquals($login_failed + 1, yourls_did_action('login_failed'));
    }
}
