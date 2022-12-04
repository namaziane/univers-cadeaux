<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if(!class_exists( 'opace_essential_seo_toolkit_settings' )) {
  class opace_essential_seo_toolkit_settings {
    protected $plugin_url;
    protected $plugin_textdomain = "opace-essential-seo-toolkit";
    protected $version;
    public $settings_page ='';
    
    function __construct() {
      $this->version = '1.2.5';
      $this->plugin_url = plugin_dir_url( dirname(__FILE__) );
      
      //Add Settings page actions/filters
      
      //Settings Page - Submenu page
      add_action( 'admin_menu', array( $this, 'register_settings_page') );
      
      //Settings Page - Section
      add_action( 'admin_init', array( $this, 'setup_sections' ) );
      
      //Settings Page - Fields
      add_action( 'admin_init', array( $this, 'setup_fields' ) );
      
    }
     
    /*
    * Add settings link to plugins table
    * $links - array - exisiting links
    * @return - array - links
    */
    public function add_settings_link( $links ) {
      //Create our link
      $settings_link = array('<a href="'.admin_url( 'options-general.php?page=essential-seo-toolkit' ).'">' . __( 'Settings', $this->plugin_textdomain ) . '</a>');
      //Return and merge our link in to exisiting links
      return array_merge( $links, $settings_link );
      
    }
    
    /*
    * Get default options
    * $single_setting - string - if set will return just that setting values
    * @return - array - setting => value pairs
    * @return - mixed - single setting value
    *
    */
    public function get_default_options( $single_setting = false ) {
      $default_settings = array(
        'eseot_post_types'  => array(
          'post'          => 1, 
          'page'          => 1, 
          'attachment'    => 0,
        ),
        'eseot_categories' => array(
          0 => 'SEO &amp; Traffic Analysis',
          1 => 'Speed &amp; Performance Analysis',
          2 => 'Website &amp; SEO Auditing',
          3 => 'Backlink Analysis',
          4 => 'Social Signals',
          5 => 'User Experience',
          6 => 'Technical SEO',
        ),
        'eseot_sources' => array(
          array(
            'name' => 'Alexa',
            'url'  => 'https://www.alexa.com/siteinfo/[%host%]',
            'cat'  => 0,
          ),
          array(
            'name' => 'SE Ranking',
            'url'  => 'https://online.seranking.com/login.html',
            'cat'  => 0,
          ),
          array(
            'name' => 'SEMRush',
            'url' => 'https://www.semrush.com/info/[%scheme%][%host%]',
            'cat' => 0,
          ),
          array(
            'name' => 'Pingdom Tools',
            'url' => 'https://tools.pingdom.com/',
            'cat' => 1,
          ),
          array(
            'name' => 'GT Metrix',
            'url' => 'https://gtmetrix.com/?url=[%scheme%][%host%][%path%]',
            'cat' => 1,
          ),
          array(
            'name' => 'Google PageSpeed Insights',
            'url' => 'https://developers.google.com/speed/pagespeed/insights/?url=[%scheme%][%host%][%path%]',
            'cat' => 1,
          ),  
          array(
            'name' => 'WooRank',
            'url' => 'https://www.woorank.com/en/www/[%host%]',
            'cat' => 2,
          ),
          array(
            'name' => 'Nibbler',
            'url' => 'http://nibbler.silktide.com',
            'cat' => 2,
          ),
          array(
            'name' => 'SEOptimer',
            'url' => 'https://www.seoptimer.com/[%host%]',
            'cat' => 2,
          ),
          array(
            'name' => 'SiteChecker',
            'url' => 'https://sitechecker.pro/seo-report/[%scheme%][%host%][%path%]',
            'cat' => 2,
          ),
          array(
            'name' => 'Majestic',
            'url' => 'https://majestic.com/reports/site-explorer?folder=&q=[%scheme%][%host%][%path%]&IndexDataSource=F',
            'cat' => 3,
          ),
          array(
            'name' => 'Ahrefs',
            'url' => 'https://ahrefs.com/site-explorer',
            'cat' => 3
          ),
          array(
            'name' => 'Moz Link Explorer',
            'url' => 'https://analytics.moz.com/pro/link-explorer/overview?site=[%scheme%][%host%]&target=domain',
            'cat' => 3,
          ),
          array(
            'name' => 'Social Share Count Checker',
            'url' => 'https://sharescount.com/',
            'cat' => 4,
          ),
          array(
            'name' => 'Count Checker',
            'url' => 'http://countchecker.com/',
            'cat' => 4
          ), 
          array(
            'name' => 'Google Mobile-Friendly Test',
            'url' => 'https://search.google.com/test/mobile-friendly?url=[%scheme%][%host%][%path%]',
            'cat' => 5
          ), 
          array(
            'name' => 'Think With Google Test',
            'url' => 'https://www.thinkwithgoogle.com/intl/en-gb/feature/testmysite/',
            'cat' => 5
          ), 
          array(
            'name' => 'Responsinator',
            'url' => 'https://www.responsinator.com/?url=[%scheme%][%host%][%path%]',
            'cat' => 5
          ), 
          array(
            'name' => 'XML Sitemap Generator',
            'url' => 'https://www.xml-sitemaps.com/',
            'cat' => 6
          ),
          array(
            'name' => 'Copyscape Duplicate Content Checke',
            'url' => 'https://www.copyscape.com/?q=[%scheme%][%host%][%path%]',
            'cat' => 6
          ),
          array(
            'name' => 'Siteliner Crawler',
            'url' => 'http://www.siteliner.com/',
            'cat' => 6
          ),
          array(
            'name' => 'Google Structured Data Testing Tool',
            'url' => 'https://search.google.com/structured-data/testing-tool#url=[%scheme%][%host%][%path%]',
            'cat' => 6
          ),
        ),
      );
      
      if( !empty( $single_setting ) ) {
        return $default_settings[$single_setting];
      }
      
      return $default_settings;
    }
    
    
    /*
    * Setup our default options
    * @return void
    */
    public function set_default_options() {
      /*
      * Add default option for enabled types
      */
      $default_settings = $this->get_default_options();
      
      if(is_array($default_settings)) {
        foreach($default_settings as $setting => $value) {
          //Check if option exists
          if(false === get_option( $setting ) ) { 
            //Add option so we wont overwrite if exisiting
            add_option( $setting, $value );
          }
        }
      }
    }
    
    /*
    * On Plugin Update plugin options
    * @return void
    */
    public function update_options() {
        //1.2.0 Update add new UE Sources
        if($this->version !== get_option('eseot_version') && $this->version == '1.2.0') {
          //Categories - Add User Experience to end of array
          $categories = get_option('eseot_categories');
          //Add UE to end of array
          $categories[] = 'User Experience';
          //Get last key (id) from categories array
          $ue_category_id = strval(array_pop(array_keys($categories)));
          //Update option
          update_option('eseot_categories', $categories);
                
          //Sources - Add User Experience sources to end of array
          $sources = get_option('eseot_sources');
          //Remove Google Mobile Friendly Test from SEO Audit cat to go in our new UE cat.
          foreach($sources as $k => $v) {
            if($v['name'] == 'Google Mobile-Friendly Test' && $v['cat'] == 2) {
              unset($sources[$k]);
            }
          }
          //Our new sources to add
          $ue_sources = array(
            array(
              'name' => 'Google Mobile-Friendly Test',
              'url' => 'https://search.google.com/test/mobile-friendly?url=[%scheme%][%host%][%path%]',
              'cat' => $ue_category_id
            ), 
            array(
              'name' => 'Think With Google Test',
              'url' => 'https://www.thinkwithgoogle.com/intl/en-gb/feature/testmysite/',
              'cat' => $ue_category_id
            ), 
            array(
              'name' => 'Responsinator',
              'url' => 'https://www.responsinator.com/?url=[%scheme%][%host%][%path%]',
              'cat' => $ue_category_id
            ),
          );
                
          //Merge new UE sources to saved sources  
          $sources = array_merge($sources,$ue_sources);
          //Update option
          update_option('eseot_sources', $sources);
          //Unset used vars
          unset($cats, $ue_category_id, $sources, $ue_sources);
          update_option('eseot_version', $this->version);
        }
        //1.2.5 Update add new TSEO Sources
        if($this->version !== get_option('eseot_version') && $this->version == '1.2.5') {
          //Categories - Add Technical SEO to end of array
          $categories = get_option('eseot_categories');
          //Add TSEO to end of array
          $categories[] = 'Technical SEO';
          //Get last key (id) from categories array
          $tseo_category_id = strval(array_pop(array_keys($categories)));
          //Update option
          update_option('eseot_categories', $categories);
          
          //Sources - Add User Experience sources to end of array
          $sources = get_option('eseot_sources');
          //Remove Google Structured Data Testing Tool from SEO Audit cat to go in our new TSEO cat.
          foreach($sources as $k => $v) {
            if($v['name'] == 'Google Structured Data Testing Tool' && $v['cat'] == 2) {
              unset($sources[$k]);
            }
          }
          
          $tseo_sources = array(
            array(
              'name' => 'XML Sitemap Generator',
              'url' => 'https://www.xml-sitemaps.com/',
              'cat' => 6
            ),
            array(
              'name' => 'Copyscape Duplicate Content Checke',
              'url' => 'https://www.copyscape.com/?q=[%scheme%][%host%][%path%]',
              'cat' => 6
            ),
            array(
              'name' => 'Siteliner Crawler',
              'url' => 'http://www.siteliner.com/',
              'cat' => 6
            ),
            array(
              'name' => 'Google Structured Data Testing Tool',
              'url' => 'https://search.google.com/structured-data/testing-tool#url=[%scheme%][%host%][%path%]',
              'cat' => 6
            ),
          );
          
          //Merge new TSEO sources to saved sources  
          $sources = array_merge($sources,$tseo_sources);
          //Update option
          update_option('eseot_sources', $sources);
          //Unset used vars
          unset($cats, $tseo_category_id, $sources, $tseo_sources);
          update_option('eseot_version', $this->version);
        }
    }
    
    /*
    * Register Settings
    * @return void
    */
    public function register_settings_page() {
      
      
      $plugin_settings = add_options_page( __( 'Opace Essential SEO Toolkit - Settings', $this->plugin_textdomain ), __( 'Opace Essential SEO Toolkit', $this->plugin_textdomain ), 'manage_options', 'essential-seo-toolkit', array( $this, 'settings_page') );
      
      //Add css to our settings page
      add_action('admin_print_scripts-' . $plugin_settings, array( $this, 'settings_scripts' ) );
    }
    
    /*
    * Add css to our settings page
    * @return void
    */
    public function settings_scripts() {
      wp_enqueue_script( 'eseot-settings', $this->plugin_url.'assets/js/eseot-settings.js', array('jquery'), false, false );
      wp_enqueue_style( 'eseot-settings', $this->plugin_url.'assets/css/eseot-settings.css' );
    }
    
    /*
    * Settings Page Display
    * @return void
    */
    public function settings_page() {
      $tab_active = '';
      if( isset( $_GET['tab'] ) ) {
        $tab_active = sanitize_text_field( $_GET['tab'] );
      }
    ?>
      <div class="wrap">
        <h1><?php _e( 'Opace Essential SEO Toolkit', $this->plugin_textdomain );?></h1>
        <div class="wp-filter">
          <ul class="filter-links">
            <li><a class="<?php $this->is_active_tab('',$tab_active); ?>" href="?page=essential-seo-toolkit">Sources</a></li>
            <li><a class="<?php $this->is_active_tab('categories',$tab_active); ?>" href="?page=essential-seo-toolkit&tab=categories">Categories</a></li>
            <li><a class="<?php $this->is_active_tab('settings',$tab_active); ?>" href="?page=essential-seo-toolkit&tab=settings">Settings</a></li>
          </ul>
        </div>
        <?php
          switch($tab_active) {
            default:
        ?>
          <form method="post" action="options.php">
          <?php
            settings_fields( 'essential-seo-toolkit-sources' );
            do_settings_sections( 'essential-seo-toolkit-sources' );
            submit_button();
          ?>
          </form>
        <?php
            break;
            case 'categories':
        ?>
          <form method="post" action="options.php">
          <?php
            settings_fields( 'essential-seo-toolkit-categories' );
            do_settings_sections( 'essential-seo-toolkit-categories' );
            submit_button();
          ?>
          </form>
        <?php
            break;
            case 'settings':
        ?>
          <form method="post" action="options.php">
          <?php
            settings_fields( 'essential-seo-toolkit-settings' );
            do_settings_sections( 'essential-seo-toolkit-settings' );
            submit_button();
          ?>
          </form>
        <?php
            break;
          }
        ?>
      </div>
    <?php
    }
    
    /*
    * Settings page tab checker
    * @return - echoes - current 
    */
    public function is_active_tab($value, $tab) {
      if($value === $tab) {
        echo esc_attr( 'current' );
      }
      return;
    }
    
    /*
    * Register our settings sections
    * @return void
    */
    public function setup_sections() {
      add_settings_section( 'enable-plugin', 'Enable plugin', array( $this, 'sections_callback' ), 'essential-seo-toolkit-settings' );
      
      add_settings_section( 'sources', 'Sources', array( $this, 'sections_callback' ), 'essential-seo-toolkit-sources' );
      
      add_settings_section( 'categories', 'Categories', array( $this, 'sections_callback' ), 'essential-seo-toolkit-categories' );
    }
    
    /*
    * Settings section callback
    * $args - array - section arguments
    * @return void
    */
    public function sections_callback($args) {
      switch( $args['id'] ) {
        case 'enable-plugin':
          _e( 'Select which post types you would like to enable Essential SEO Toolkit on.', $this->plugin_textdomain );
        break;
        case 'sources':
          _e( 'Here you can create new sources add your own additional SEO tools to the plugin. In the makeup of the URL, you can use three tags, which will be replaced by the current pages URL markup:', $this->plugin_textdomain );
          ?>
          <ul>
            <li><strong>[%scheme%]</strong> - <?php _e('The scheme identifies the protocol to be used to access the resource on the Internet. It can be HTTP (without SSL) or HTTPS (with SSL).', $this->plugin_textdomain);?></li>
            <li><strong>[%host%]</strong> - <?php _e('The host name identifies the host that holds the resource. For example, www.yoursite.com.', $this->plugin_textdomain);?></li>
            <li><strong>[%path%]</strong> - <?php _e('The path identifies the specific resource in the host that the web client wants to access. For example, /tags/news/', $this->plugin_textdomain);?></li>
          </ul>
          <?php
        break;
        case 'categories':
          _e( 'Here you can create new categories or remove exisiting ones.', $this->plugin_textdomain );
        break;
      }
    }
    
    /*
    * Setup settings filds for settings page
    * @return void
    */
    public function setup_fields() {
      
      $fields = array(
        array(
          'uid'           => 'eseot_post_types',
          'label'         => __( 'Post Types', $this->plugin_textdomain ),
          'section'       => 'enable-plugin',
          'type'          => 'post-types',
          'options'       => $this->post_type_options($this->get_post_types()),
          'page'          => 'essential-seo-toolkit-settings',
        ),
        array(
          'uid'           => 'eseot_categories',
          'label'         => __( 'Categories', $this->plugin_textdomain ),
          'section'       => 'categories',
          'type'          => 'categories',
          'options'       => $this->get_categories(),
          'page'          => 'essential-seo-toolkit-categories',
        ),
        array(
          'uid'           => 'eseot_sources',
          'label'         => __( 'Sources', $this->plugin_textdomain ),
          'section'       => 'sources',
          'type'          => 'sources',
          'options'       => $this->get_sources(),
          'page'          => 'essential-seo-toolkit-sources',
        ),
      );
      
      foreach($fields as $field) {        
      
        add_settings_field( $field['uid'], $field['label'], array( $this, 'fields_callback' ), $field['page'],  $field['section'], $field );
        
        register_setting( $field['page'], $field['uid'], array( $this, 'fields_validation') );
      }
    }
    /*
    * Validation/strip tags/strip slashes
    * WordPress settings API will handle sanitizing for DB
    */
    public function fields_validation( $input ) {
      //Create output
      $output = array();
      
      foreach( $input as $k => $v ) {
        if( isset( $input[$k] ) && !empty( $input[$k] ) ) {
          if( is_array($input[$k] ) ) {
            foreach( $input[$k] as $k2 => $v2 ) {
              if(isset( $input[$k][$k2] ) &&  $input[$k][$k2] !== '' ) {
                $output[$k][$k2] =  $input[$k][$k2];
              }
            }
          } else {
            $output[$k] =  sanitize_text_field( $input[$k] );
          }
        }
      }
      
      // Return the array processing and filter
      return apply_filters( 'eseot_validate_inputs', $output, $input );
    }
        
    /*
    * Setting fields callback
    * $args - array - field arguments
    * @return void
    */
    public function fields_callback($args) {
      $value = get_option( $args['uid'] );
      
      //Check value exists, else set defaults
      if( ! $value ) { 
        $value = $args['default']; // Set to our default if we use one
      }
      
      switch( $args['type'] ) {
        case 'post-types':
          if( ! empty ( $args['options'] ) && is_array( $args['options'] ) ){
            $markup = '';
            foreach( $args['options'] as $key => $label ){

              $markup .= sprintf( '<label for="enable-%1$s"><input id="enable-%1$s" type="checkbox" name="%2$s[%1$s]" value="1" %3$s> %4$s</label><br/>', 
                esc_attr($key), 
                esc_attr($args['uid']), 
                checked($value[$key], 1, false),
                esc_attr($label)
              );  
            }
            printf( '<div class="eseot-enabled-types-wrapper">%s</div>',
              $markup
            );
          }
        break;
        case 'categories':    
          if( ! empty ( $args['options'] ) && is_array( $args['options'] ) ){
            $markup = '';
            foreach( $args['options'] as $key => $label ){
              $markup .= sprintf( '<tr>
                                    <td class="actions"><span class="actions"><a class="action action-remove" data-action="remove" title="%3$s"><span class="dashicons dashicons-no"></span></a></span></td>
                                    <td class="cat-id">%1$s</td>
                                    <td class="cat-name"><input type="hidden" name="eseot_categories[%1$s]" value="%2$s"/> %2$s </td>
                                  </tr>',
                esc_attr($key), 
                esc_attr($label),
                __('Remove Category', $this->plugin_textdomain )
              );
            }
          } 
          $custom_input = sprintf('<label for="eseot-custom-name">%1$s</label> <input id="eseot-custom-name" class="regular-text" name="eseot_categories[]" type="text" placeholder="%2$s" />',
            __( 'Add New Category', $this->plugin_textdomain),
            __( 'Category name', $this->plugin_textdomain)
          );
          
          printf( '<div class="eseot-categories-wrapper">
                    <table class="eseot-table eseot-category-table wp-list-table widefat fixed striped">
                      <thead>
                        <tr>
                          <th class="actions"></th>
                          <th class="cat-id">ID</th>
                          <th class="cat-name">Category Name</th>
                        </tr>
                      </thead>
                      <tbody>
                        %s
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="3">%s %s</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>', 
            $markup,      
            $custom_input,
            get_submit_button('Add Category', 'primary', 'submit', false )
          );
        break;
        case 'sources':
          $categories = $this->get_categories();
          if( ! empty ( $args['options'] ) && is_array( $args['options'] ) ){
            $next = count($args['options']);
            $markup = '';
            foreach( $args['options'] as $key => $array ){
              $markup .= sprintf( '<tr>
                                    <td class="actions">
                                      <span class="actions"><a class="action action-remove" data-action="remove-source" title="%4$s"><span class="dashicons dashicons-no"></span></a></span>
                                    </td>
                                    <td class="source-name">
                                      <input type="hidden" name="eseot_sources[%1$s][name]" value="%2$s"/>
                                      %2$s
                                    </td>
                                    <td class="source-url">
                                      <input class="input-sourceurl" type="hidden" name="eseot_sources[%1$s][url]" value="%3$s"/>
                                      %3$s
                                    </td>
                                    <td class="source-cat">
                                      <input type="hidden" name="eseot_sources[%1$s][cat]" value="%6$s"/>
                                      %5$s
                                    </td>
                                  </tr>',
                esc_attr( $key ), 
                esc_attr( $array['name'] ),
                esc_attr( $array['url'] ),
                __( 'Remove Source', $this->plugin_textdomain ),
                esc_attr( $categories[$array['cat']] ),
                esc_attr($array['cat'])
              );
            }
          } 
          $custom_input = sprintf('<label for="eseot-source-name">%1$s</label> 
                                    <input id="eseot-source-name" class="regular-text" name="eseot_sources[%5$s][name]" type="text" placeholder="%2$s" />
                                    <input type="text" class="regular-text source-url" name="eseot_sources[%5$s][url]" placeholder="%4$s"/> 
                                    %3$s',
            __( 'Add New Source', $this->plugin_textdomain),
            __( 'Source name', $this->plugin_textdomain),
            $this->category_dropdown( 'eseot_sources['.$next.'][cat]', null, false ),
            __( 'Source URL', $this->plugin_textdomain ),
            $next
          );
          
          printf( '<div class="eseot-sources-wrapper">
                    <table class="eseot-table eseot-source-table wp-list-table widefat fixed striped">
                      <col width="20">
                      <col width="100">
                      <col width="">
                      <col width="100">
                      <thead>
                        <tr>
                          <th class="actions"></th> 
                          <th class="source-name">Source Name</th>
                          <th class="source-url">Access URL</th>
                          <th class="source-cat">Category</th>
                        </tr>
                      </thead>
                      <tbody>
                        %s
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="4">%s %s</td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>', 
            $markup,          
            $custom_input,
            get_submit_button('Add Source', 'primary', 'submit' )
          );
        break;
      }
    }
    
    /*
    * Category Selec dropdown
    * @return - echo or string
    */
    public function category_dropdown( $name = 'category', $selected = null, $render = false ) {
      $categories = $this->get_categories();
      $markup = '';
      if( is_array( $categories ) && !empty( $categories ) ) {
        foreach($categories as $key => $value) {

          if($selected === $key) {
            $sel = 'selected="selected"';
          } else {
            $sel = null;
          }
       
          $markup .= sprintf('<option value="%s" %s >%s</option>', 
            esc_attr($key),
            $sel,
            esc_attr($value)
          );
        }
      } else {
        $markup .= '<option value="">-- No Categories Created --</option>';
      }
      $dropdown = sprintf('<select name="%s">
                            <option value="">-- Select Category --</option>
                            %s
                          </select>',
        esc_attr( $name ),
        $markup
      );
      if(false == $render) {
        return $dropdown;
      }
      echo $dropdown;
    }
    /*
    * Get wordpress registered post types
    * @return - array - all post types
    */
    public static function get_post_types() {
      
      //Get all public facing post types
      $types = get_post_types( array( 'public' => true, 'show_ui' => true ) );
      
      return $types;
    }
    
    /*
    * Get our SEO categories
    * @return - array - id => name
    */
    public function get_categories() {
      //Get options
      $categories = get_option( 'eseot_categories' );
      
      return apply_filters( 'eseot-get-categories', $categories );
    }
    
    /*
    * Get our SEO Sources
    * @return - array - name => url scheme/cat
    */
    public function get_sources() {
      //Get options
      $sources = get_option( 'eseot_sources' );

      return apply_filters( 'eseot-get-sources', $sources );
    }
    
    /*
    * Util function to sort post type in to managable $slug => $label array
    * for use on settings page
    * @return - array - post type slug => post type label name
    */
    private function post_type_options( $types ) {
      $types_sorted = array();
      if( !empty( $types ) && is_array( $types ) ) {
        foreach($types as $key => $type) {
          //Get post type obj
          $type_obj = get_post_type_object( $type );
          //Check we have type object
          if(!empty( $type_obj ) && is_object( $type_obj ) ) {
            //Set out array
            $types_sorted[ $type ] = $type_obj->labels->name; 
          }
          //Unset the type obj to free up memory
          unset( $type_obj );
        }
      }
      //Return our sorted array
      return $types_sorted;
    }
    
  } //End Class
} //End if class