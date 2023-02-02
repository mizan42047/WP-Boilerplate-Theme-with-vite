<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;  

// dist subfolder - defined in vite.config.json
define('DIST_DEF', 'dist');

// defining some base urls and paths
define('DIST_URI', get_template_directory_uri() . '/' . DIST_DEF);
define('DIST_PATH', get_template_directory() . '/' . DIST_DEF);

// js enqueue settings
define('JS_DEPENDENCY', array()); // array('jquery') as example
define('JS_LOAD_IN_FOOTER', true); // load scripts in footer?

// deafult server address, port and entry point can be customized in vite.config.json
define('VITE_SERVER', 'http://localhost:3000');
define('VITE_ENTRY_POINT', '/main.js');

// enqueue hook
add_action( 'wp_enqueue_scripts', function() {
    
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {

        // insert hmr into head for live reload
        function vite_head_module_hook() {
            echo '<script type="module" crossorigin src="' . VITE_SERVER . VITE_ENTRY_POINT . '"></script>';
        }
        add_action('wp_head', 'vite_head_module_hook');        

    } else {

        // production version, 'npm run build' must be executed in order to generate assets
        // ----------

        // read manifest.json to figure out what to enqueue
        $manifest = json_decode( file_get_contents( DIST_PATH . '/manifest.json'), true );
        // is ok
        if (is_array($manifest)) {
            
            // get first key, by default is 'main.js' but it can change
            $manifest_key = array_keys($manifest);
            $manifest_values = array_values($manifest);
            if(!empty($manifest_values)){
                foreach($manifest_values as $key => $value){
                   if($manifest_key[$key] === 'main.css'){
                        wp_enqueue_style('theme-style', get_theme_file_uri('dist/'. $value['file']));
                   }elseif($manifest_key[$key] === 'main.js'){
                        wp_enqueue_script('theme-script', get_theme_file_uri('dist/'. $value['file']),[], false, true);
                   }
                }
            }
        }

    }


});