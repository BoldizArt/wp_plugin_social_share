<?php
/*
 Plugin Name: Social share plugin
 Plugin URI: https://github.com/BoldizArt
 Description: Supported shortcodes: [social_share_buttons]
 Version: 0.0.2
 Author: Boldizar Santo (boldizar.santo@gmail.com)
 Author URI: https://github.com/BoldizArt
 Licence: MTI
 Text Domain: social_share
 */
if (!defined('ABSPATH')) {
   die;
}

/**
 * @package Social share plugin
 */
require __DIR__ . '/vendor/autoload.php';

use BoldizArt\SocialShare\SocialShare;

// Sef init
$socialShare = new SocialShare();

// Activation
register_activation_hook(__FILE__, [$socialShare, 'activate']);

// Deactivation
register_deactivation_hook(__FILE__, [$socialShare, 'deactivate']);
