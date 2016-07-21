<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/** Load composer packages **/

require __DIR__ . '/vendor/autoload.php';

/** Enable Tracy **/
\Tracy\Debugger::enable();

function dd($val, $exit = NULL) {
    \Tracy\Debugger::dump($val);

    if ($exit !== NULL) {
        exit;
    }
}

function cdd($val, $exit = NULL) {
    \Tracy\Debugger::barDump($val);

    if ($exit !== NULL) {
        exit;
    }
}

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
