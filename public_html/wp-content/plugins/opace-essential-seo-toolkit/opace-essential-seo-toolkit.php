<?php
/*
* Plugin Name:      Opace Essential SEO Toolkit
* Plugin URI:       http://www.opace.co.uk
* Description:      The Opace Essential SEO Toolkit is a collection of useful SEO tools, which when clicked opens the tool using the current URL.
* Version:          1.2.5
* Text Domain:      opace-essential-seo-toolkit
* Author:           Opace Web Design 
* Author URI:       http://www.opace.co.uk
* License:          GPL-3.0
* License URI:      http://www.gnu.org/licenses/gpl-3.0.txt
* Domain Path:      /languages
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// If this file was called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
* Main Plugin Class
*/
if(!class_exists( 'opace_essential_seo_toolkit' )) {
  class opace_essential_seo_toolkit {
    
    protected $plugin_textdomain = "opace-essential-seo-toolkit";
    protected $plugin_dir;
    protected $plugin_url;
    protected $version; 
    
    //Instance
    static $instance = false;
    
    protected $settings;
    
    /*
    * Activation Function
    * @return void
    */
    public static function activation() {
      //Call the class because it's not loaded on activation
      $essential_seo_toolkit = new opace_essential_seo_toolkit;
      $essential_seo_toolkit->settings->set_default_options();
      
      update_option('eseot_version', $essential_seo_toolkit->version);

      //Register Uninstall Function
      register_uninstall_hook( __FILE__, array( 'opace_essential_seo_toolkit', 'uninstall' ) );
      //Free up memory
      unset($essential_seo_toolkit);
    }
    
    /*
    * Uninstall Function
    * @return void
    */
    function uninstall() {
      //Clean up our database options.
      delete_option('eseot_post_types');
      delete_option('eseot_categories');
      delete_option('eseot_sources');
    }
    /*
    * Constructor
    * @return void
    */
    function __construct() {
      //Set up our protected vars
      $this->plugin_dir = plugin_dir_path( __FILE__ );
      $this->plugin_url = plugin_dir_url( __FILE__ );
      
      //Define and get our Plugin version
      $this->version = '1.2.5';
      
      $this->load_dependencies();
      
      //Register Activation
      register_activation_hook( __FILE__, array( 'opace_essential_seo_toolkit', 'activation' ));
      
      //Register Update
      add_action('plugins_loaded', array( $this->settings, 'update_options' ) );
      
      //Load Textdomain
      add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
      
      //Admin Scripts
      add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts') );
      
      // Add settings link to plugins list page -- Called from class-settings-page.php
      add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this->settings, 'add_settings_link' ) );
      
      //Add Meta Box
      add_action( 'do_meta_boxes', array( $this, 'create_metaboxes'	),	10,	2	);
    }
    
    /*
    * Checks only 1 instance is running, if not create it and returns it.
    * @return essential_seo_toolkit
    */
    public static function getInstance() {
      if ( !self::$instance ) {
        self::$instance = new self;
        return self::$instance;
      }
    }
    
    /*
    * Load Textdomain
    * @return void
    */
    public function textdomain() {
      load_plugin_textdomain($this->plugin_textdomain, false, $this->plugin_dir . '/languages/');
    }
    
    /*
    * Load dependencies
    * @access private
    * @return void
    */
    private function load_dependencies() {
      /*
      * Class for settings page
      */
      //Check file exists
      if(file_exists($this->plugin_dir.'includes/class-settings-page.php')) {
        //Require file
        require_once $this->plugin_dir.'includes/class-settings-page.php';
        //Check class exists
        if(class_exists('opace_essential_seo_toolkit_settings')) {
          //Load up class to $settings var
          $this->settings = new opace_essential_seo_toolkit_settings();
        }
      }
    }
    /*
    * Get plugin version
    * @return - string - current plugin version number
    */
    public function get_version() {
      return $this->version;
    }
    
    /*
    * Admin Scripts
    * @return void
    */
    public function admin_scripts($hook) {
      /*
      * If current screen is supported type
      * Register/Enqueue our meta js/css
      */
      if ( $this->is_screen_supported() ) {
        /*
        * Javascript
        */
        //Register
        wp_register_script( 'eseot-meta', $this->plugin_url.'assets/js/eseot-meta.js', array('jquery'), $this->version, false);
        //Enqueue
        wp_enqueue_script( 'eseot-meta' );
        
        /*
        * CSS
        */
        //Register
        wp_register_style( 'eseot-meta', $this->plugin_url.'assets/css/eseot-meta.css');
        //Enqueue
        wp_enqueue_style( 'eseot-meta' );
      } else if('settings_page_essential-seo-toolkit' == $hook) {
        //Admin Settings Page
        
      }
      return;
    }
    
    /*
    * Add meta box
    * @return void
    */
    public function create_metaboxes( $page, $contex ) {
      //Check if current page is supported
      if( $this->is_screen_supported( $page ) ) {
        //Add metabox
        add_meta_box( 'essential-seo-toolkit', __( 'Opace Essential SEO Toolkit', $this->plugin_textdomain ), array( $this, 'display_metabox' ), $page, 'side', 'low' );
      }
    }
    
    /*
    * Display metabox
    * @return void
    */
    public function display_metabox( $post ) {      
      //Get post permalink
      $permalink = get_permalink();
      //Get sources & categories
      $sources = $this->settings->get_sources();
      $categories = $this->settings->get_categories();
      
      printf('<p>%3$s <a href="%1$s" title="%2$s" target="_blank">%2$s</a> %4$s:</p>',
        esc_url($permalink), 
        esc_attr($post->post_title),
        __('Open', $this->plugin_textdomain),
        __('with', $this->plugin_textdomain) 
      );
        
      $markup = '';
      foreach( $categories as $id => $label ) {
        $inner = '';
        foreach( $sources as $key => $values ) {
          if( $values['cat'] == $id ) {
            $link = $this->generate_link( $permalink, $values['url'] );
            $inner .= sprintf('<li><a href="%1$s" title="%2$s" target="_blank">%2$s</a></li>', 
              esc_url($link), 
              esc_attr( $values['name'] ) 
            );
          }
        }
        $inner = sprintf( '<ul class="inner">%1$s</ul>', $inner);
        $markup .= sprintf('<li><a class="eseot-toggle" href="#">%1$s</a>%2$s</li>', esc_attr( $label ), $inner);
      }
      printf('<ul class="eseot-accordion">%1$s</ul>', $markup);
      printf('<p><small>* %1$s</small></p>', __('Some tools may require account creation in order to access the data. Others may require you to copy the URL into their tool.', $this->plugin_textdomain));
    }
    
    /*
    * Convert our source links to real links
    * Change shortcodes to link parts
    * var $link - string - link to be appended to source url
    * var $source_link - string - the source url to shortcode links to
    * return - string - completed source link | false
    */
    public function generate_link($link = null, $source_link = null) {
      if( empty($link) || empty($source_link) ){
        return $source_link;
      }
      //Check if we need to do any replacements in link
      if( !preg_match('/\[%([a-z]+)%\]/', $source_link)) {
        return $source_link;
      }
      //Generate Permalink Parts
      $link_parts = $this->generate_link_parts( $link );
      
      //Replace [%scheme%]
      $source_link = str_replace('[%scheme%]', $link_parts['scheme'].'://', $source_link);
      //Replace [%host%]
      $source_link = str_replace('[%host%]', $link_parts['host'], $source_link);
      //Replace [%path%]
      $source_link = str_replace('[%path%]', $link_parts['path'], $source_link);
      
      return $source_link;
    }
    
    /*
    * Generate a safe array with our permalink parts
    * @vars - $link - default current $post permalink
    * @return - safe array of URL components | false
    */
    public function generate_link_parts( $link = null ) {
      if( empty( $link ) ) {
        $link = get_permalink();
      }
      
      $url_parts = false;
      
      if( false != $link ) {
        $url_parts = parse_url($link);
        //Check if URL Parts is ok
        if(false != $url_parts) {
          //Make sure query is set before trying to merge with paths
          if(isset($url_parts['query'])) {
            $url_parts['path'] .= $url_parts['query'];
          }
        }
      }
      return $url_parts;
    }
    
    /*
    * Get supported types
    * @return - array - filtered
    */
    public function get_supported_post_types() {
      //Get all registered types
      $types = $this->settings::get_post_types();
      //Get user saved enabled types option
      $enabled_types = get_option( 'eseot_post_types' );
      
      /*
      * We want to filter registered types through our enabled setting
      * in case a new type was registered since the enabled
      * settings were last saved.
      */
      
      //Make sure we have options
      if( false !== $enabled_types && is_array( $enabled_types ) ) {
        //Loop through all registered types
        foreach($types as $type) {
          //Check if type is not in/enabled in our settings
          if( !isset($enabled_types[$type]) || $enabled_types[$type] != 1 ) {
            //Remove type from array
            unset($types[$type]);
          }
        }
      }
      //Return supported types array
      return $types;
    }
    
    /*
    * Check to see if current screen is supported
    * @var - screen - string/wp_screen obj - default: current screen
    * @return - boolean - true/false
    */
    public function is_screen_supported( $screen = null ) {
      
      //Get all supported post types
      $types = $this->get_supported_post_types();
      //If screen not passed through Get current screen
      if ( empty( $screen ) ) {
        $screen	= get_current_screen();
      } else if ( is_string( $screen ) ) {
        $screen = convert_to_screen( $screen );
      }
      
      if ( in_array( $screen->post_type , $types ) ) {
        return true;
      }
      return false;
    }
    
  } // End Class
} // End If Class Exists


/*
* Load up Plugin instance
*/
if(class_exists( 'opace_essential_seo_toolkit' )) {
  //Load Instance
  $essential_seo_toolkit = opace_essential_seo_toolkit::getInstance();
}
?>