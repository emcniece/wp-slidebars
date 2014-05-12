<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('Redux_Framework_wp_slidebars_config')) {

    class Redux_Framework_wp_slidebars_config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            //$this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            //$this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css, $changed_values) {
            //echo '<h1>The compiler hook has run!</h1>';
            //echo "<pre>";
            //print_r($changed_values); // Values that have changed since the last save
            //echo "</pre>";
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )
            //echo '<pre>CSS OUTPUT: '.print_r($css, true).'</pre>';

            add_action( 'admin_notices', array(&$this, 'compiled_notice' ) );

            
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/css/dynamic.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
                WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             
        }

        function compiled_notice() {
            ?>
            <div class="updated">
                <p><?php _e( 'Dynamic CSS file has been re-compiled.', 'wp-slidebars' ); ?></p>
            </div>
            <?php
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'redux-wp-slidebars'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-wp-slidebars'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {

                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            $sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns        = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'redux-wp-slidebars'), $this->theme->display('Name'));
            
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                <?php endif; ?>

                <h4><?php echo $this->theme->display('Name'); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(__('By %s', 'redux-wp-slidebars'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(__('Version %s', 'redux-wp-slidebars'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . __('Tags', 'redux-wp-slidebars') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'redux-wp-slidebars'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                /** @global WP_Filesystem_Direct $wp_filesystem  */
                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once(ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }
                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'title'     => __('Home Settings', 'redux-wp-slidebars'),
                'desc'      => __('', 'redux-wp-slidebars'),
                'icon'      => 'el-icon-home',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields'    => array(

                    array(
                        'id'        => 'wp-sb-logo',
                        'type'      => 'media',
                        'url'       => true,
                        'title'     => __('Logo', 'redux-wp-slidebars'),
                        'desc'      => __('Adds a logo to the navbar', 'redux-wp-slidebars'),
                        'subtitle'  => __('Upload any media using the WordPress uploader', 'redux-wp-slidebars'),
                        'default'   => array('url' => get_template_directory_uri().'/img/slidebars-logo@2x.png'),
                    ),
                    array(
                        'id'       => 'opt-navbar-color',
                        'type'     => 'background',
                        'title'    => __('Navbar Background Color', 'redux-framework-demo'), 
                        'subtitle' => __('Pick a background color for the navbar.', 'redux-framework-demo'),
                        'default'  => '#ff3971',
                        'compiler' => array('.navbar-default'),
                    ),
                    array(
                        'id'       => 'opt-navbar-glass-effect',
                        'type'     => 'switch', 
                        'title'    => __('Navbar Glass Effect', 'redux-framework-demo'),
                        'subtitle' => __('Adds a light gradient to the navbar', 'redux-framework-demo'),
                        'default'  => true,
                        'on'       => 'Enabled',
                        'off'      => 'Disabled'
                    ),
                    array(
                        'id'        => 'opt-background',
                        'type'      => 'background',
                        'output'    => array('wp-slidebars'),
                        'title'     => __('Body Background', 'redux-wp-slidebars'),
                        'subtitle'  => __('Body background with image, color, etc.', 'redux-wp-slidebars'),
                        'default'   => '#FFFFFF',
                    ),
                    array(
                        'id'        => 'opt-editor',
                        'type'      => 'editor',
                        'title'     => __('Footer Text', 'redux-wp-slidebars'),
                        'subtitle'  => __('You can use the following shortcodes in your footer text: [wp-url] [site-url] [theme-url] [login-url] [logout-url] [site-title] [site-tagline] [current-year]', 'redux-wp-slidebars'),
                        'default'   => 'Copyright &copy; '.date('Y').' WP Slidebars.',
                    ),
                ),
                
            );

            $this->sections[] = array(
                'type' => 'divide',
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-brush',
                'title'     => __('Typography', 'redux-wp-slidebars'),
                'fields'    => array(
                    array(
                        'id'            => 'opt-body-font',
                        'type'          => 'typography',
                        'title'         => __('Body Font', 'redux-wp-slidebars'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => true,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        //'subsets'       => false, // Only appears if google is true and subsets not set to false
                        //'font-size'     => false,
                        //'line-height'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        //'output'        => array('wp-slidebars'), // An array of CSS selectors to apply this font style to dynamically
                        'compiler'      => array('body'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => __('Typography option with each property can be called individually.', 'redux-wp-slidebars'),
                        'default'       => array(
                            'color'         => '#333',
                            'font-style'    => '400',
                            'font-family'   => 'Open Sans',
                            'google'        => true,
                            'font-size'     => '14px',
                            'line-height'   => '20px'),
                        'ext-font-css'  => get_template_directory_uri().'/css/dynamic.css',
                    ),
                    array(
                        'id'        => 'opt-link-color',
                        'type'      => 'link_color',
                        'title'     => __('Links Color Option', 'redux-wp-slidebars'),
                        'subtitle'  => __('Only color validation can be done on this field type', 'redux-wp-slidebars'),
                        'desc'      => __('This is the description field, again good for additional info.', 'redux-wp-slidebars'),
                        //'regular'   => false, // Disable Regular Color
                        //'hover'     => false, // Disable Hover Color
                        //'active'    => false, // Disable Active Color
                        //'visited'   => true,  // Enable Visited Color
                        'compiler' => array('a'),
                        //'output' => array('a'),
                        'default'   => array(
                            'regular'   => '#ff3971',
                            'hover'     => '#ff064c',
                            'active'    => '#ff064c',
                        )
                    ),
                    array(
                        'id'            => 'opt-navbar-font',
                        'type'          => 'typography',
                        'title'         => __('Navbar Font', 'redux-wp-slidebars'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => true,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        //'subsets'       => false, // Only appears if google is true and subsets not set to false
                        //'font-size'     => false,
                        //'line-height'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        //'output'        => array('wp-slidebars'), // An array of CSS selectors to apply this font style to dynamically
                        'compiler'      => array('.navbar .navbar-nav>li>a', '.navbar a', '.navbar'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => __('Set typography for navbar items.', 'redux-wp-slidebars'),
                        'default'       => array(
                            'color'         => '#fff',
                            'font-style'    => '400',
                            'font-family'   => 'Open Sans',
                            'google'        => true,
                            'font-size'     => '14px',
                            'line-height'   => '20px'),
                        'ext-font-css'  => get_template_directory_uri().'/css/dynamic.css',
                    ),

                )
            );


            $this->sections[] = array(
                'icon'      => 'el-icon-cogs',
                'title'     => __('Layout Settings', 'redux-wp-slidebars'),
                'fields'    => array(
                    array(
                        'id'        => 'opt-layout',
                        'type'      => 'image_select',
                        'compiler'  => false,
                        'title'     => __('Main Layout', 'redux-wp-slidebars'),
                        'subtitle'  => __('Select main content and sidebar alignment. Choose between 1, 2 or 3 column layout.', 'redux-wp-slidebars'),
                        'options'   => array(
                            '1' => array('alt' => '1 Column',       'img' => ReduxFramework::$_url . 'assets/img/1col.png'),
                            '2' => array('alt' => '2 Column Left',  'img' => ReduxFramework::$_url . 'assets/img/2cl.png'),
                            '3' => array('alt' => '2 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/2cr.png'),
                            '4' => array('alt' => '3 Column Middle','img' => ReduxFramework::$_url . 'assets/img/3cm.png'),
                            //'5' => array('alt' => '3 Column Left',  'img' => ReduxFramework::$_url . 'assets/img/3cl.png'),
                            //'6' => array('alt' => '3 Column Right', 'img' => ReduxFramework::$_url . 'assets/img/3cr.png')
                        ),
                        'default'   => '2'
                    ),
                    array(
                        'id' => 'section-left-sidebar-start',
                        'type' => 'section',
                        'title' => __('Left Sidebar', 'redux-framework-demo'),
                        'subtitle' => __('Adjust settings for the left sidebar region.', 'redux-framework-demo'),
                        'indent' => true,
                        'required' => array(
                            array('opt-layout', 'not', 1),
                            array('opt-layout', 'not', 3),
                        ),                        
                    ),
                    array( 
                        'id'       => 'opt-left-sidebar-bgcolor',
                        'type'     => 'color_rgba',
                        'title'    => __('Sidebar Background', 'redux-framework-demo'),
                        'subtitle' => __('Gives you the RGBA color.', 'redux-framework-demo'),
                        //'desc'     => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
                        'default'  => array(
                            'color' => '#222222', 
                            'alpha' => '1.0'
                        ),
                        //'output'   => array('left-sidebar'),
                        'compiler' => array('.sb-left'),
                        'mode'     => 'background',
                    ),
                    array(
                        'id' => 'opt-left-sidebar-width',
                        'type' => 'dimensions',
                        'title' => __('Sidebar Width', 'redux-framework-demo'),
                        'subtitle' => __('Sets width for left sidebar.', 'redux-framework-demo'),
                        //'desc' => __('Slider description. Min: 0, max: 300, step: 5, default value: 75', 'redux-framework-demo'),
                        'units' => array('%', 'px', 'em'),
                        'height' => 'false',
                        'default' => array('width'=>'200'),
                        'compiler' => array('.sb-slidebar.sb-right'),
                    ),
                    array(
                        'id'       => 'opt-left-sidebar-widgetarea',
                        'type'     => 'switch', 
                        'title'    => __('Enable Widgets', 'redux-framework-demo'),
                        'subtitle' => __('Adds widget area to left sidebar', 'redux-framework-demo'),
                        'default'  => true,
                        'on'       => 'Enabled',
                        'off'      => 'Disabled'
                    ),
                    array(
                        'id'     => 'section-left-sidebar-end',
                        'type'   => 'section',
                        'indent' => false,
                    ),
                    array(
                        'id' => 'section-right-sidebar-start',
                        'type' => 'section',
                        'title' => __('Right Sidebar', 'redux-framework-demo'),
                        'subtitle' => __('Adjust settings for the right sidebar region.', 'redux-framework-demo'),
                        'indent' => true,
                        'required' => array(
                            array('opt-layout', 'not', 1),
                            array('opt-layout', 'not', 2),
                        ),
                    ),
                    array( 
                        'id'       => 'opt-right-sidebar-bgcolor',
                        'type'     => 'color_rgba',
                        'title'    => __('Sidebar Background', 'redux-framework-demo'),
                        'subtitle' => __('Gives you the RGBA color.', 'redux-framework-demo'),
                        //'desc'     => __('This is the description field, again good for additional info.', 'redux-framework-demo'),
                        'default'  => array(
                            'color' => '#222222', 
                            'alpha' => '1.0'
                        ),
                        //'output'   => array('sb-right'),
                        'compiler' => array('.sb-right'),
                        'mode'     => 'background',
                    ),
                    array(
                        'id' => 'opt-right-sidebar-width',
                        'type' => 'dimensions',
                        'title' => __('Sidebar Width', 'redux-framework-demo'),
                        'subtitle' => __('Sets width for right sidebar.', 'redux-framework-demo'),
                        //'desc' => __('Slider description. Min: 0, max: 300, step: 5, default value: 75', 'redux-framework-demo'),
                        'units' => array('%', 'px', 'em'),
                        'height' => 'false',
                        'default' => array('width'=>'200'),
                        'compiler' => array('.sb-slidebar.sb-right'),
                    ),
                    array(
                        'id'       => 'opt-right-sidebar-widgetarea',
                        'type'     => 'switch', 
                        'title'    => __('Enable Widgets', 'redux-framework-demo'),
                        'subtitle' => __('Adds widget area to right sidebar', 'redux-framework-demo'),
                        'default'  => true,
                        'on'       => 'Enabled',
                        'off'      => 'Disabled'
                    ),
                    array(
                        'id'     => 'section-right-sidebar-end',
                        'type'   => 'section',
                        'indent' => false,
                    ),
                    
                    
                )
            );

            // Get available distros
            $distros = glob( dirname(__FILE__).'/assets/slidebars/distribution/*');
            $distros_out = array();
            foreach($distros as $distro) $distros_out[basename($distro)] = basename($distro);

            // Get current listed active distro
            $slidebars_opts = json_decode( file_get_contents( dirname(__FILE__).'/assets/slidebars/slidebars.jquery.json' ) );
            $slidebars_default = isset($slidebars_opts->version) ? $slidebars_opts->version : '0.9.4';

            $this->sections[] = array(
                'icon'      => 'el-icon-website',
                'title'     => __('Advanced Options', 'redux-wp-slidebars'),
                'subsection' => false,
                'fields'    => array(
                    array(
                        'id'       => 'opt-slidebars-version',
                        'type'     => 'select',
                        'title'    => __('Slidebars Version', 'redux-framework-demo'), 
                        'subtitle' => __('Select the active version of Slidebars to use', 'redux-framework-demo'),
                        'desc'     => __('Default is the version listed in the <em>slidebars.jquery.json</em> file.', 'redux-framework-demo'),
                        // Must provide key => value pairs for select options
                        'options'  => $distros_out,
                        'default'  => $slidebars_default,
                    ),
                    array(
                        'id'       => 'opt-slidebars-debug-mode',
                        'type'     => 'switch', 
                        'title'    => __('Debug Mode', 'redux-framework-demo'),
                        'subtitle' => __('Toggles between minified version', 'redux-framework-demo'),
                        'default'  => false,
                        'on'       => 'Enabled',
                        'off'      => 'Disabled'
                    ),
                    array(
                        'id'        => 'opt-ace-editor-css',
                        'type'      => 'ace_editor',
                        'title'     => __('CSS Code', 'redux-wp-slidebars'),
                        'subtitle'  => __('Paste your CSS code here.', 'redux-wp-slidebars'),
                        'mode'      => 'css',
                        'theme'     => 'monokai',
                        'desc'      => 'Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.',
                        'default'   => "#header{\nmargin: 0 auto;\n}",
                    ),
                    array(
                        'id'        => 'opt-ace-editor-js',
                        'type'      => 'ace_editor',
                        'title'     => __('JS Code', 'redux-wp-slidebars'),
                        'subtitle'  => __('Paste your JS code here.', 'redux-wp-slidebars'),
                        'mode'      => 'javascript',
                        'theme'     => 'chrome',
                        'desc'      => 'Possible modes can be found at <a href="http://ace.c9.io" target="_blank">http://ace.c9.io/</a>.',
                        'default'   => "jQuery(document).ready(function(){\n\n});"
                    ),                   

                    
                )
            );

            $this->sections[] = array(
                'title'     => __('Import / Export', 'redux-wp-slidebars'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'redux-wp-slidebars'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            );                     
                    
            $this->sections[] = array(
                'type' => 'divide',
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-info-sign',
                'title'     => __('Theme Information', 'redux-wp-slidebars'),
                'desc'      => __('<p class="description">About this theme</p>', 'redux-wp-slidebars'),
                'fields'    => array(
                    array(
                        'id'        => 'opt-raw-info',
                        'type'      => 'raw',
                        'content'   => $item_info,
                    )
                ),
            );

            if (file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
                $tabs['docs'] = array(
                    'icon'      => 'el-icon-book',
                    'title'     => __('Documentation', 'redux-wp-slidebars'),
                    'content'   => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
                );
            }
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'redux-wp-slidebars'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-wp-slidebars')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'redux-wp-slidebars'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-wp-slidebars')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'redux-wp-slidebars');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'wp_slidebars',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => $theme->get('Name'),     // Name that appears at the top of your panel
                'display_version'   => $theme->get('Version'),  // Version that appears at the top of your panel
                'menu_type'         => 'menu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => false,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('WP Slidebars', 'redux-wp-slidebars'),
                'page_title'        => __('WP Slidebars Options', 'redux-wp-slidebars'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => 'AIzaSyCC7XFYWwUbASZczEAgWDHbrZt3G3ko6pE', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => false,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => false,                    // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => null,            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => get_template_directory_uri().'/img/menu-icon.png',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => 'wp_slidebars_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                'footer_credit'     => 'Theme implementation by <a href="http://emc2innovation.com?linksource=wp-slidebars" target="_blank">EMC2 Innovation</a>, 
                    <a href="https://github.com/adchsm/Slidebars" target="_blank">Slidebars</a> plugin by <a href="http://www.adchsm.me/?linksource=wp-slidebars" target="_blank">Adam Smith</a> ',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://github.com/emcniece/wp-slidebars',
                'title' => 'Find on GitHub',
                'icon'  => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/emc2innovation',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://twitter.com/EMC2Innovation',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://www.linkedin.com/pub/eric-mcniece/44/42/536',
                'title' => 'Find us on LinkedIn',
                'icon'  => 'el-icon-linkedin'
            );

           

            // Add content after the form.
            //$this->args['footer_text'] = __('<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'redux-wp-slidebars');
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new Redux_Framework_wp_slidebars_config();
}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')):
    function redux_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')):
    function redux_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }
endif;
