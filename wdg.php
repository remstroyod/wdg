<?php
/**
 * Plugin Name: WDG
 * Plugin URI: https://github.com/remstroyod/wdg
 * Description: Widget for Articles.
 * Version: 1.0
 * Author: Alex Cherniy
 * Author URI: https://www.example.com
 * License: GPL2
 * Network: true
 */

if (!defined('ABSPATH')) exit;

const WDG_PLUGIN_DIR = __DIR__;

define('WDG_PLUGIN_URL', plugin_dir_url(__FILE__));

spl_autoload_register('wdg_autoload');
function wdg_autoload($class): void
{
    if (strpos($class, 'Wdg\\') === 0)
    {
        require_once WDG_PLUGIN_DIR . '/controller/' . substr($class, 4) . '.php';
    }
}

new \Wdg\Wdg();
new \Wdg\WdgRating();
