<?php
/**
 * @file
 * use BoldizArt\SocialShare\SocialShare;
 */
namespace BoldizArt\SocialShare;

/*
Copyright 2023 Boldizar Santo

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
class SocialShare
{
    /** @param string $version; */
    public $version;

    /**
     * Constructor
     */
    function __construct()
    {
        // Set the plugin url
        $this->version = '0.0.1';

        // Add styles
        add_action('init', [$this, 'addStyles']);

        // Add scripts
        add_action('init', [$this, 'addScripts']);

        // Test function
        add_action('init', [$this, 'testFunction']);
    }

    /**
     * Test function
     * @todo Remove this function
     */
    public function testFunction()
    {
        wp_enqueue_style('social_share_style');
        wp_enqueue_script('social_share_script');
    }

    /**
     * Add admin stylesheets to the website
     */
    function addStyles()
    {
        wp_register_style('social_share_style', plugins_url('./../assets/css/minified/social-share.css', __FILE__), [], $this->version, 'all');
    }

    /**
     * Add admin scripts to the website
     */
    function addScripts()
    {
        wp_register_script('social_share_script', plugins_url('./../assets/js/minified/social-share.js', __FILE__), [], $this->version, 'all');
    }

    /** 
     * Activation
     */
    function activate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Deactivation
     */
    function deactivate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Uninstall
     */
    function uninstall()
    {
        // Security checks
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            exit;
        }
    }
}
