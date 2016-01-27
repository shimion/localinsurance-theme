<?php
/**
 * Functions used on theme installation
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Our theme has advanced installation process with quick setup wizard.
 * We try to do all hard work automatically:
 * - Install bundled plugins
 * - Configure basic settings
 * 	> create system templates
 * 	> create basic menu and activate MegaMainMenu for it
 * 	> regenerate custom css
 * 	> setup LiveComposer tutorial pages
 * 	> setup default settings for bundled plugins
 * - Import demo content
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    http://themeforest.net/licenses
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* ----------------------------------------------------------------------
* Perform custom fucntions on theme activation
* http://wordpress.stackexchange.com/a/80320/34582
*/

/**
* ----------------------------------------------------------------------
* Theme has been just activated
*/
// update_option( LBMN_THEME_NAME . '_required_plugins_installed', false);

if ( is_admin() && $pagenow == "themes.php" ) {
	// Update theme option '_required_plugins_installed'
	// if URL has ?plugins=installed variable set
	if ( isset($_GET['plugins']) && $_GET['plugins'] == 'installed' ) {
		update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?basic_setup=completed variable set
	if ( isset($_GET['basic_setup']) && $_GET['basic_setup'] == 'completed' ) {
		update_option( LBMN_THEME_NAME . '_basic_config_done', true);
		define ('LBMN_THEME_CONFUGRATED', true);
	}

	// Update theme option '_basic_config_done'
	// if URL has ?demoimport=completed variable set
	if ( isset($_GET['demoimport']) && $_GET['demoimport'] == 'completed' ) {
		update_option( LBMN_THEME_NAME . '_democontent_imported', true);
	}

	if ( !get_option( LBMN_THEME_NAME . '_hide_quicksetup' ) ) {
		add_action( 'admin_notices', 'lbmn_setmessage_themeinstall' );
	}
}

/**
 * ----------------------------------------------------------------------
 * Check if required plugins were manually installed
 */

function lbmn_required_plugins_install_check() {
	if ( ! get_option( LBMN_THEME_NAME . '_required_plugins_installed' ) ) {
	// Proceed only if '_required_plugins_installed' not already market as true

		$current_tgmpa_message = '';
		$current_tgmpa_messages = get_settings_errors('tgmpa');

		foreach ($current_tgmpa_messages as $message) {
			$current_tgmpa_message = $message['message'];
		}

		// If message has no link to install-required-plugins page then all
		// required plugins has been installed
		if ( ! stristr($current_tgmpa_message, 'install-required-plugins') ) {
			// Update theme option '_required_plugins_installed'
			update_option( LBMN_THEME_NAME . '_required_plugins_installed', true);
		}
	}
}
add_action( 'admin_footer', 'lbmn_required_plugins_install_check' );
// get_settings_errors() do not return any results earlier than 'admin_footer'


/**
 * ----------------------------------------------------------------------
 * Output Theme Installer HTML
 */

function lbmn_setmessage_themeinstall() {
?>
<img src="<?php echo includes_url() . 'images/spinner.gif' ?>" class="theme-installer-spinner" style="position:fixed; left:50%; top:50%;" />
<style type="text/css">.lumberman-message.quick-setup{display:none;}</style>
<div class="updated lumberman-message quick-setup">
	<div class="message-container">
	<p class="before-header"><?php echo LBMN_THEME_NAME_DISPLAY; ?> Quick Setup</p>
	<h4>Thank you for creating with <a href="<?php echo LBMN_DEVELOPER_URL; ?>" target="_blank"><?php echo LBMN_DEVELOPER_NAME_DISPLAY; ?></a>!</h4>
	<h5>Just a few steps left to release the full power of our theme.</h5>


	<?php
		//Check for GZIP support

		if ( !is_callable( 'gzopen' ) ) {
			echo '<span class="error">Your server doesn\'t support file compression (GZIP). Please <a href="' . LBMN_SUPPORT_URL . '">contact us</a> for alternative installation package.</span>';
		}
	?>

	<!-- Step 1 -->
		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_required_plugins_installed') ) {
				echo '<p id="theme-setup-step-1" class="submit step-plugins">';
			} else {
				echo '<p id="theme-setup-step-1" class="submit step-plugins step-completed">';
			}
		?>
		<span class="step"><span class="number">1</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />

		<span class="step-body"><a href="<?php echo add_query_arg( array('page' => 'install-required-plugins'), admin_url('themes.php') ); ?>" class="button button-primary" id="do_plugins-install">Install required plugins</a>
		<?php /*<span class="step-body"><a href="<?php echo add_query_arg( array('page' => 'install-required-plugins', 'autoinstall' => '1'), admin_url('themes.php') ); ?>" class="button button-primary" id="do_plugins-install">Install required plugins</a> */ ?>
		<?php /* ajax verison <span class="step-body"><a href="#" class="button button-primary" id="do_plugins-install">Install required plugins</a> */ ?>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Installs Page Builder, Mega Menus, Slider, etc.
		</span></span><br />
		<span class="error" style="display:none">Automatic plugin installation failed. Please try to <a href="/wp-admin/themes.php?page=install-required-plugins">install required plugins manually</a>.</span>
		</p>

	<!-- Step 2 -->

		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_basic_config_done') ) {
				echo '<p id="theme-setup-step-2" class="submit step-basic_config">';
			} else {
				echo '<p id="theme-setup-step-2" class="submit step-basic_config step-completed">';
			}
		?>
		<span class="step"><span class="number">2</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body"><a href="#" class="button button-primary" id="do_basic-config" data-ajax-nonce="<?php echo wp_create_nonce( 'wie_import' ); ?>" >Integrate installed plugins</a>
		<span class="step-description">
		Required action to get 100% functionality.<br />
		Configures the plugins to work with our theme.
		</span></span><br />
		<span class="error" style="display:none">Something went wrong (<a href="#" class="show-error-log">show log</a>). Please <a href="<?php echo LBMN_SUPPORT_URL; ?>">contact us</a> for help.</span>
		</p>

	<!-- Step 3 -->

		<?php
			// Check is this step is already done
			if ( !get_option( LBMN_THEME_NAME . '_democontent_imported') ) {
				echo '<p id="theme-setup-step-3" class="submit step-demoimport">';
			} else {
				echo '<p id="theme-setup-step-3" class="submit step-demoimport step-completed">';
			}
		?>
		<span class="step"><span class="number">3</span></span>
		<img src="<?php echo includes_url() . '/images/spinner.gif' ?>" class="customspinner" />
		<span class="step-body">
		<a href="#" class="button button-primary" id="do_demo-import">Import all demo content</a>
		<span class="step-description">
		Optional step to recreate theme demo website<br />
		on your server.
		</span></span><br />
		<!--
		<span style="margin-right:15px;">OR</span>
		<a href="#" class="button button-secondary" id="do_basic-demo-import">Create only 3 basic pages </a>
		</p>
		-->

	<!-- Step 4 -->
		<p class="submit step-tour">
		<span class="step"><span class="number">4</span></span> 
		<span class="step-body">
			<a href="<?php echo add_query_arg('theme_tour', 'true', admin_url('themes.php') ); ?>" class="button  button-primary">Take a quick tour</a> 
			<span class="step-description">2 minutes interactive introduction<br /> 
			to our theme basic controls.  </span>
		</span>
		</p>


	<p class="submit action-skip"> <a class="skip button-primary" href="<?php echo add_query_arg('hide_quicksetup', 'true', admin_url('themes.php') ); ?>">Hide this message</a></p></div>
</div>
<style type="text/css">.theme-installer-spinner{display:none;}</style>
<style type="text/css">.lumberman-message.quick-setup{display:block;}</style>
<?php
}

/**
* ----------------------------------------------------------------------
* Start basic theme settings setup process
*/
add_action( 'admin_notices', 'pvt_wordpress_content_importer' );
function pvt_wordpress_content_importer() {
	$theme_dir = get_template_directory();

	if ( is_admin() && isset($_GET['importcontent']) ) {


		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				include $class_wp_importer;
			}
		}
		if ( ! class_exists('pvt_WP_Import') ) {
			$class_wp_import = $theme_dir . '/inc/importer/wordpress-importer.php';
			if ( file_exists( $class_wp_import ) ) {
				include $class_wp_import;
			}
		}
		if ( class_exists( 'WP_Importer' ) && class_exists( 'pvt_WP_Import' ) ) {
			$importer = new pvt_WP_Import();
			$files_to_import = array();

			// Live Composer has links to images hard-coded, so before importing
			// media we need to check that the Settings > Media >
			// 'Organize my uploads into month- and year-based folders' unchecked
			// as on demo server. After import is done we set back original state
			// of this setting.
			$setting_original_useyearmonthfolders = get_option( 'uploads_use_yearmonth_folders');
			update_option( 'uploads_use_yearmonth_folders', 0 );

			if ( $_GET['importcontent'] == 'basic-templates' ) {
				$import_path = $theme_dir . '/design/basic-config/';
				$files_to_import[] = $import_path . 'seowp-templates.xml.gz';
				$files_to_import[] = $import_path . 'seowp-themefooters.xml.gz';
				$files_to_import[] = $import_path . 'seowp-systempagetemplates.xml.gz';
				$files_to_import[] = $import_path . 'seowp-livecomposer-tutorials.xml.gz';
			}

			if ( $_GET['importcontent'] == 'alldemocontent' ) {
				$import_path = $theme_dir . '/design/demo-content/';
				$files_to_import[] = $import_path . 'seowp-media.xml.gz';
				$files_to_import[] = $import_path . 'seowp-homepages.xml.gz';
				$files_to_import[] = $import_path . 'seowp-predesignedpages-1.xml.gz';
				$files_to_import[] = $import_path . 'seowp-predesignedpages-2.xml.gz';
				$files_to_import[] = $import_path . 'seowp-predesignedpages-3.xml.gz';
				$files_to_import[] = $import_path . 'seowp-downloads.xml.gz';
				$files_to_import[] = $import_path . 'seowp-partners.xml.gz';
				$files_to_import[] = $import_path . 'seowp-staff.xml.gz';
				$files_to_import[] = $import_path . 'seowp-testimonials.xml.gz';
				$files_to_import[] = $import_path . 'seowp-posts.xml.gz';
				$files_to_import[] = $import_path . 'seowp-projects.xml.gz';
			}

			// Start Import

			if ( file_exists( $class_wp_importer ) ) {
				// Import included images
				$importer->fetch_attachments = true;

				foreach ($files_to_import as $import_file) {
					if( is_file($import_file) ) {
						ob_start();
							$importer->import( $import_file );

							$log = ob_get_contents();
						ob_end_clean();

						// output log in the hidden div
						echo '<div class="ajax-log">';
						echo $log;
						echo '</div>';


						if ( stristr($log, 'error') || !stristr($log, 'All done.') ) {
							// Set marker div that will be fildered by ajax request
							echo '<div class="ajax-request-error"></div>';

							// output log in the div
							echo '<div class="ajax-error-log">';
							echo $log;
							echo '</div>';
						}

					} else {
						// Set marker div that will be fildered by ajax request
						echo '<div class="ajax-request-error"></div>';

						// output log in the div
						echo '<div class="ajax-error-log">';
						echo "Can't open file: " . $import_file . "</ br>";
						echo '</div>';
					}
				}

			} else {
				// Set marker div that will be fildered by ajax request
				echo '<div class="ajax-request-error"></div>';

				// output log in the div
				echo '<div class="ajax-error-log">';
				echo "Failed to load: " . $class_wp_import . "</ br>";
				echo '</div>';
			}

			// Set 'Organize my uploads into month- and year-based folders' setting
			// to its original state
			update_option( 'uploads_use_yearmonth_folders', $setting_original_useyearmonthfolders );

		}

		/**
		 * ----------------------------------------------------------------------
		 * Basic configuration:
		 * Post import actions
		 */

		if ( $_GET['importcontent'] == 'basic-templates' ) {

			// 1. Import Menus
			// 2. Activate Mega Main Menu for menu locations
			// 3. Import Widgets
			// 4. Demo description for author
			// 5. Tutorial Pages for LiveComposer
			// 6. Newsletter Sign-Up Plugin Settings
			// 7. Rotating Tweets Default Options Setup
			// 8. Regenerate Custom CSS

			// Path to the folder with basic import files
			$import_path_basic_config = $theme_dir . '/design/basic-config/';

			// 1:
			// Import Top Bar menu
			// if no menu set for 'topbar' location
			if( !has_nav_menu('topbar') ) {
				if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
					wpfw_import_menu($import_path_basic_config . 'seowp-menu-topbar.txt', 'topbar');
				}
			}

			// Import Mega Main Menu menu
			// if no menu set for 'header-menu' location
			if( !has_nav_menu('header-menu') ) {
				if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
					wpfw_import_menu($import_path_basic_config . 'seowp-menu-megamainmenu.txt', 'header-menu');
				}
			}

			$locations = get_nav_menu_locations();
			set_theme_mod('nav_menu_locations', $locations);

			// Import Mobile Off-Canvas Menu
			if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
				wpfw_import_menu($import_path_basic_config . 'seowp-menu-mobile-offcanvas.txt');
			}

			// 2: Activate Mega Main Menu for 'topbar' and 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				lbmn_activate_mainmegamenu_locations ();
			}

			// Predefine Custom Sidebars in LiveComposer
			// First set new sidebars in options table
			update_option(
				'dslc_plugin_options_widgets_m',
				array(
					'sidebars' => 'Sidebar,404 Page Widgets,Comment Form Area,',
				)
			);
			// Then run LiveComposer function that creates sidebars dynamically
			dslc_sidebars();

			// 3: Import widgets
			$files_with_widgets_to_import = array();
			$files_with_widgets_to_import[] = $import_path_basic_config . 'seowp-widgets.wie';

			// Remove default widgets from 'mobile-offcanvas' widget area
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			if (is_array($sidebars_widgets['mobile-offcanvas'])) {
				$sidebars_widgets['mobile-offcanvas'] = NULL;
			}
			update_option( 'sidebars_widgets', $sidebars_widgets );

			// There are dynamic values in 'seowp-widgets.wie' that needs to be replaced
			// before import processing
			global $widget_strings_replace;
			$widget_strings_replace = array(
				'TOREPLACE_OFFCANVAS_MENUID' => lbmn_get_menuid_by_menutitle ( 'Mobile Off-canvas Menu' ),
			);

			foreach ($files_with_widgets_to_import as $file) {
				pvt_import_data( $file );
			}

			// 4: Put some demo description into current user info field
			// that used in the blog user boxes
			$user_ID = get_current_user_id();
			$user_info = get_userdata( $user_ID );

			if ( !$user_info->description ) {
				update_user_meta(
					$user_ID,
					'description',
					'This is author biographical info, ' .
					'that can be used to tell more about you, your iterests, ' .
					'background and experience. ' .
					'You can change it on <a href="/wp-admin/profile.php">Admin &gt; Users &gt; Your Profile &gt; Biographical Info</a> page."'
				);
			}

			// 5: Predefine Tutorial Pages in LiveComposer
			update_option(
				'dslc_plugin_options_tuts',
				array(
					'lc_tut_chapter_one' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-1'),
					'lc_tut_chapter_two' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-2'),
					'lc_tut_chapter_three' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-3'),
					'lc_tut_chapter_four' => lbmn_get_page_by_slug('live-composer-tutorials/chapter-4'),
				)
			);

			// 6: Newsletter Sign-Up Plugin Form Elements
			update_option(
				'nsu_form',
				array(
					'email_label' => '',
					'email_default_value' => 'Your email address...',
					'email_label' => '',
					'redirect_to' => get_site_url() . '/index.php?pagename=/lbmn_archive/thanks-for-signing-up/',
				)
			);

			// 7: Rotating Tweets Default Options Setup
			update_option(
				'rotatingtweets-api-settings',
				array(
					'key' => 'mxutw2QpMFxuXvW0puvEqKwuL',
					'secret' => 'xqmBL6MWYDkt2yjkbKe0VTlqdkMvsePkif6Z1zzqYFrpguEor4',
					'token' => '95497077-RlX1hhwHC1uz8NcmCCIdbbUm9zqH3wB4Wb44gvaTM',
					'token_secret' => '7LwXbdxJM1i32SUijrMteRNENysr09GfBiEp6RYKPpHRD',
					'cache_delay' => 86400,
					'js_in_footer' => 1,

				)
			);

			// Add custom Mega Main Menu options
			$mmm_options = get_option( 'mega_main_menu_options' );

			// Add custom Additional Mega Menu styles
			$mmm_options['additional_styles_presets'] = array(
				'1' => array(
							'style_name' => "Call to action item",
							'text_color' => "rgba(255,255,255,1)",
							'bg_gradient' => array(
														"color1" => "#A1C627",
														"start" => "0",
														"color2" => "#A1C627",
														"end" => "100",
														"orientation" => "top",
													),
							"text_color_hover" => "rgba(255,255,255,1)",
							"bg_gradient_hover" => array(
														"color1" => "#56AEE3",
														"start" => "0",
														"color2" => "#56AEE3",
														"end" => "100",
														"orientation" => "top",
													),
						 ),
				'2' => array(
							'style_name' => "Dropdown Heading",
							'text_color' => "rgba(0,0,0,1)",
							'bg_gradient' => array(
														"color1" => "",
														"start" => "0",
														"color2" => "",
														"end" => "100",
														"orientation" => "top",
													),
							"text_color_hover" => "rgba(0,0,0,1)",
							"bg_gradient_hover" => array(
														"color1" => "",
														"start" => "0",
														"color2" => "",
														"end" => "100",
														"orientation" => "top",
													),
						 ),
			);

			// Add custom icons
			$mmm_options['set_of_custom_icons'] = array(
				'1' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-spain.png'),
						 ),
				'2' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-italy.png'),
						 ),
				'3' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-france.png'),
						 ),
				'4' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-uk.png'),
						 ),
				'5' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-us.png'),
						 ),
				'6' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-austria.png'),
						 ),
				'7' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-belgium.png'),
						 ),
				'8' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-germany.png'),
						 ),
				'9' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-netherlands.png'),
						 ),
				'10' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-poland.png'),
						 ),
				'11' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-portugal.png'),
						 ),
				'12' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-romania.png'),
						 ),
				'13' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-russia.png'),
						 ),
				'14' => array(
							'custom_icon' => esc_url_raw (get_template_directory_uri() .'/images/flag-ukraine.png'),
						 ),
			);

			// Put Mega Main Menu options back
			update_option( 'mega_main_menu_options', $mmm_options );

			// 8: Regenerate Custom CSS
			lbmn_customized_css_cache_reset(false); // refresh custom css without printig css (false)

			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				// call the function that normaly starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}
		} // if $_GET['importcontent']


		/**
		 * ----------------------------------------------------------------------
		 * Demo Content: Full
		 */

		if ( $_GET['importcontent'] == 'alldemocontent' ) {
			$import_path_demo_content = $theme_dir . '/design/demo-content/';

			// Import Demo Mega Menu menu
			if( is_plugin_active('wpfw_menus_management/wpfw_menus_management.php') ) {
				wpfw_import_menu($import_path_demo_content . 'seowp-demomegamenu.txt', 'header-menu');
			}

			$locations = get_nav_menu_locations();
			set_theme_mod('nav_menu_locations', $locations);

			// Activate Mega Main Menu for 'header-menu' locations
			// See /inc/plugins-integration/megamainmenu.php for function source
			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				lbmn_activate_mainmegamenu_locations ();
			}

			// Import Nex-Forms plugin data
			if(is_plugin_active('Nex-Forms/main.php')){
				lbmn_debug_console( 'Import Nex-Forms plugin data' );
				global $wpdb;

				// Import 'Contact Us' form
				$wpdb->insert(
					$wpdb->prefix . 'wap_nex_forms',
					array(
						'Id' => 1,  // TODO: last form id in the db + 1
						'plugin' => 'shared',
						'publish' => 0,
						'added' => '0000-00-00 00:00',
						'last_update' => '2014-07-06 22:18:40',
						'title' => 'Contact Us',
						'description' => NULL,
						'mail_to' => 'info@example.com',
						'confirmation_mail_body' => 'Thank you for connecting with us. We will respond to you shortly.',
						'confirmation_mail_subject' => 'Thank you for connecting with us',
						'from_address' => 'info@example.com',
						'from_name' => 'WP Website',
						'on_screen_confirmation_message' => 'Thank you for connecting with us.',
						'confirmation_page' => '',
						'form_fields' => '<div class=\\"run_animation hidden\\">false</div><div class=\\"animation_time hidden\\">60</div><div class=\\"trash-can form_field grid ui-draggable dropped ui-droppable\\" style=\\"display: block; font-size: 47px;\\" id=\\"_73800\\"><div style=\\"\\" class=\\"form_object\\" id=\\"form_object\\"><div data-svg=\\"demo-input-1\\" class=\\"input-inner do_show\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-12\\">' . 
						'<div class=\\"panel panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><span class=\\"glyphicon glyphicon-trash\\"></span><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\" id=\\"_93222\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">1 Col&nbsp;&nbsp;&nbsp;<i class=\\"label label-primary\\">12</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-12\\"><div class=\\"panel grid-system grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"></div></div></div></div></div></div></div></div></div></div></div></div></div></div>' . 
						'<div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">2 Cols  <i class=\\"label label-success\\">6 6</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"><div class=\\"form_field text ui-draggable dropped required text_only\\" style=\\"display: block;\\" id=\\"_48449\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Name</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\" style=\\"display: none;\\"><label id=\\"title\\" class=\\"title ve_title\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon btn-xs glyphicon-star\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label style_bold\\" style=\\"color: rgb(0, 0, 0);\\">Name</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\">Sub text</small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" name=\\"_name\\" placeholder=\\"Name\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control required text_only input-lg\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Only text are allowed\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\" type=\\"text\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div><div class=\\"form_field text ui-draggable required email dropped\\" style=\\"display: block;\\" id=\\"_37971\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Email</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\" style=\\"display: none;\\"><label id=\\"title\\" class=\\"title ve_title\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label style_bold\\" style=\\"color: rgb(0, 0, 0);\\">Email</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\">Sub text</small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" name=\\"email\\" placeholder=\\"Email\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control required input-lg email\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Invalid e-mail format\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\" type=\\"text\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"><div class=\\"form_field text ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_67643\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Company</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\" style=\\"display: none;\\"><label id=\\"title\\" class=\\"title ve_title\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs hidden\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label style_bold\\" style=\\"color: rgb(0, 0, 0);\\">Company</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\">Sub text</small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" name=\\"company\\" placeholder=\\"Company\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control input-lg\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Only text are allowed\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\" type=\\"text\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div><div class=\\"form_field text ui-draggable phone_number dropped\\" style=\\"display: block;\\" id=\\"_26453\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Phone</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\" style=\\"display: none;\\"><label id=\\"title\\" class=\\"title ve_title\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs hidden\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label style_bold\\" style=\\"color: rgb(0, 0, 0);\\">Phone</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\">Sub text</small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" name=\\"phone\\" placeholder=\\"Phone\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control input-lg phone_number\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Invalid phone number format\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\" type=\\"text\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div></div></div></div></div><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_58810\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">1 Col&nbsp;&nbsp;&nbsp;<i class=\\"label label-primary\\">12</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-12\\"><div class=\\"panel grid-system grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field textarea ui-draggable required dropped\\" style=\\"display: block;\\" id=\\"_41512\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-align-justify\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Message</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row\\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\" style=\\"display: none;\\"><label id=\\"title\\" class=\\"title ve_title\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Message</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\">Sub text</small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><textarea name=\\"message\\" id=\\"textarea\\" placeholder=\\"Message\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" class=\\"error_message svg_ready the_input_element textarea pre-format form-control input-lg required\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" title=\\"\\" style=\\"color: rgb(85, 85, 85); border-color: rgb(220, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\"></textarea><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display:none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div></div></div></div></div><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block;\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">2 Cols  <i class=\\"label label-success\\">6 6</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field submit-button ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_55759\\"><div class=\\"draggable_object input-group \\" style=\\"display: none;\\"><button class=\\"btn btn-success btn-sm form-control\\"><i class=\\" glyphicon glyphicon glyphicon-send\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Submit Button</span></button><span class=\\"input-group-addon\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><button class=\\"nex-submit svg_ready the_input_element btn btn-primary input-lg\\" style=\\"color: rgb(255, 255, 255); border-color: rgb(86, 172, 227); outline-offset: 0px; outline-width: 1px; background: rgb(86, 172, 227);\\" data-onfocus-color=\\"#000000\\" data-original-title=\\"\\" title=\\"\\">Submit</button><br></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display:none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field check-group ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\" id=\\"_57100\\">' .
						'<div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-check\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Newsletter subscribe</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder radio-group no-pre-suffix\\"><div class=\\"row\\"><div class=\\"col-sm-12\\" id=\\"field_container\\">' . 
						'<div class=\\"row\\"><div class=\\"col-sm-2\\" style=\\"display: none;\\"><label id=\\"title\\" class=\\"title ve_title\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon btn-xs hidden glyphicon-star\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Newsletter subscribe</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\">Sub text</small></label></div><div class=\\"the-radios error_message col-sm-12\\" id=\\"the-radios\\" data-checked-color=\\"alert-success\\" data-checked-class=\\"fa-check\\" data-unchecked-class=\\"\\" data-placement=\\"bottom\\" data-content=\\"Please select one\\" title=\\"\\" data-original-title=\\"\\">' . 
						'<div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><label class=\\"checkbox-inline\\" for=\\"subscribe_me_to_the_the_newsletter\\" data-svg=\\"demo-input-1\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"svg_ready has-pretty-child\\"><div class=\\"clearfix prettycheckbox labelright  blue has-pretty-child\\"><input class=\\"check the_input_element\\" name=\\"newsletter_subscribe[]\\" id=\\"subscribe_me_to_the_the_newsletter\\" value=\\"Subscribe me to the the newsletter\\" style=\\"display: none; color: rgb(85, 85, 85); border-color: rgb(187, 187, 187); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-onfocus-color=\\"#000000\\" type=\\"checkbox\\"><a class=\\"\\" data-original-title=\\"\\" title=\\"\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"></a></div><span class=\\"input-label check-label\\">Subscribe me to the the newsletter</span></span></label></div><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div></div></div></div></div>',
						'visual_settings' => NULL,
						'google_analytics_conversion_code' => NULL,
						'colour_scheme' => NULL,
						'send_user_mail' => NULL,
						'user_email_field' => '',
						'on_form_submission' => 'message',
						'date_sent' => NULL,
						'is_form' => '1',
						'is_template' => '1',
					)
				);

				// Import 'Free analysis request' form
				$wpdb->insert(
					$wpdb->prefix . 'wap_nex_forms',
					array(
						'Id' => 2,  // TODO: last form id in the db + 2
						'plugin' => 'shared',
						'publish' => 0,
						'added' => '0000-00-00 00:00',
						'last_update' => '2014-07-06 22:18:40',
						'title' => 'Free analysis request',
						'description' => NULL,
						'mail_to' => 'info@example.com',
						'confirmation_mail_body' => 'Thank you for connecting with us. We will respond to you shortly.',
						'confirmation_mail_subject' => 'Thank you for connecting with us',
						'from_address' => 'info@example.com',
						'from_name' => 'WP Website',
						'on_screen_confirmation_message' => 'Thank you for connecting with us.',
						'confirmation_page' => '',
						'form_fields' => '<div class=\\"run_animation hidden\\">false</div><div class=\\"animation_time hidden\\">60</div><div class=\\"trash-can form_field grid ui-draggable dropped ui-droppable\\" style=\\"display: block; font-size: 47px;\\" id=\\"_73800\\"><div style=\\"\\" class=\\"form_object\\" id=\\"form_object\\"><div data-svg=\\"demo-input-1\\" class=\\"input-inner do_show\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-12\\"><div class=\\"panel panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><span class=\\"glyphicon glyphicon-trash\\"></span><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\" id=\\"_41470\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\">' .
						'<button class=\\"btn btn-default btn-sm form-control\\">1 Col&nbsp;&nbsp;&nbsp;<i class=\\"label label-primary\\">12</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-12\\"><div class=\\"panel grid-system grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"></div></div></div></div></div></div></div></div></div></div></div></div></div></div><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_11927\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">1 Col&nbsp;&nbsp;&nbsp;<i class=\\"label label-primary\\">12</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-12\\"><div class=\\"panel grid-system grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"><div class=\\"form_field text ui-draggable dropped required url\\" style=\\"display: block; z-index: 100;\\" id=\\"_61848\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Website</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\"><label id=\\"title\\" class=\\"title ve_title align_left input-lg\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon btn-xs glyphicon-star\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Website</span><br><small class=\\"sub-text\\" style=\\"color: rgb(216, 216, 216);\\">URL to review</small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" type=\\"text\\" name=\\"website\\" placeholder=\\"\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control input-lg required url\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Invalid url format\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\"><span class=\\"help-block hidden\\" style=\\"color: rgb(115, 115, 115); outline-offset: 0px; outline-width: 1px;\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_48105\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">2 Cols  <i class=\\"label label-success\\">6 6</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"><div class=\\"form_field text ui-draggable dropped required\\" style=\\"display: block; z-index: 100;\\" id=\\"_48822\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Name</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\"><label id=\\"title\\" class=\\"title ve_title input-lg\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon btn-xs glyphicon-star\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Name</span><br><small class=\\"sub-text\\" style=\\"color: rgb(216, 216, 216);\\"></small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" type=\\"text\\" name=\\"_name\\" placeholder=\\"\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control required input-lg\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\"><span class=\\"help-block hidden\\" style=\\"color: rgb(115, 115, 115); outline-offset: 0px; outline-width: 1px;\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable drag\\"><div class=\\"form_field text ui-draggable dropped email required\\" style=\\"display: block; z-index: 100;\\" id=\\"_55206\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Email</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\"><label id=\\"title\\" class=\\"title ve_title input-lg\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Email</span><br><small class=\\"sub-text\\" style=\\"color: rgb(216, 216, 216);\\"></small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" type=\\"text\\" name=\\"email\\" placeholder=\\"\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control input-lg email required\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Invalid e-mail format\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); border-color: rgb(221, 221, 221); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div></div></div></div></div><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_91512\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">2 Cols  <i class=\\"label label-success\\">6 6</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field text ui-draggable dropped phone_number\\" style=\\"display: block; z-index: 100;\\" id=\\"_39150\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Phone</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\"><label id=\\"title\\" class=\\"title ve_title input-lg\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs hidden\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Phone</span><br><small class=\\"sub-text\\" style=\\"color: rgb(216, 216, 216);\\"></small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" type=\\"text\\" name=\\"phone\\" placeholder=\\"\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control input-lg phone_number\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"Invalid phone number format\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); outline-offset: 0px; outline-width: 1px; border-color: rgb(221, 221, 221); background: rgb(255, 255, 255);\\" data-original-title=\\"\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field text ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\" id=\\"_97690\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-minus\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Company</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\"><label id=\\"title\\" class=\\"title ve_title input-lg align_left\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs hidden\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Company</span><br><small class=\\"sub-text\\" style=\\"color: rgb(216, 216, 216);\\"></small></label></div>' .
						'<div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><input id=\\"ve_text\\" type=\\"text\\" name=\\"company\\" placeholder=\\"\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" maxlength=\\"200\\" class=\\"error_message svg_ready the_input_element text pre-format form-control input-lg\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" data-secondary-message=\\"\\" title=\\"\\" style=\\"color: rgb(51, 51, 51); outline-offset: 0px; outline-width: 1px; border-color: rgb(221, 221, 221); background: rgb(255, 255, 255);\\" data-original-title=\\"\\"><span class=\\"help-block hidden\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(115, 115, 115);\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div></div></div></div></div><div class=\\"form_field textarea ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_96266\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\" glyphicon glyphicon-align-justify\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Details</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row\\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-2\\"><label id=\\"title\\" class=\\"title ve_title input-lg\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><span class=\\"is_required glyphicon glyphicon-star btn-xs hidden\\" style=\\"color: rgb(0, 0, 0);\\"></span><span class=\\"the_label\\" style=\\"color: rgb(0, 0, 0);\\">Details</span><br><small class=\\"sub-text style_italic\\" style=\\"color: rgb(153, 153, 153);\\"></small></label></div><div class=\\"col-sm-12\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><textarea name=\\"details\\" id=\\"textarea\\" placeholder=\\"Ideas, special requirements, etc.\\" data-maxlength-color=\\"label label-success\\" data-maxlength-position=\\"bottom\\" data-maxlength-show=\\"false\\" data-default-value=\\"\\" class=\\"error_message svg_ready the_input_element textarea pre-format form-control\\" data-onfocus-color=\\"#66afe9\\" data-drop-focus-swadow=\\"1\\" data-placement=\\"bottom\\" data-content=\\"Please enter a value\\" title=\\"\\" style=\\"color: rgb(85, 85, 85); border-color: rgb(204, 204, 204); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-original-title=\\"\\"></textarea><span class=\\"help-block hidden\\" style=\\"color: rgb(115, 115, 115); outline-offset: 0px; outline-width: 1px;\\">Help text...</span></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display:none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div><div class=\\"form_field paragraph ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_59064\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\"fa fa-align-justify\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Paragraph</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder\\"><div class=\\"input-inner svg_ready\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-12\\"><div class=\\"input-group date svg_ready\\"><p class=\\"the_input_element\\" style=\\"color: rgb(255, 255, 255); border-color: rgb(46, 111, 143); outline-offset: 0px; outline-width: 1px; background: rgb(255, 255, 255);\\" data-onfocus-color=\\"#000000\\" data-original-title=\\"\\" title=\\"\\">.\n</p><div style=\\"clear:both;\\"></div></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display:none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div><div class=\\"form_field grid grid-system ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\" id=\\"_52745\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\">2 Cols  <i class=\\"label label-success\\">6 6</i></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input-inner\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field submit-button ui-draggable dropped\\" style=\\"display: block; z-index: 100;\\" id=\\"_55853\\"><div class=\\"draggable_object input-group \\" style=\\"display: none;\\"><button class=\\"btn btn-success btn-sm form-control\\"><i class=\\" glyphicon glyphicon glyphicon-send\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Submit Button</span></button><span class=\\"input-group-addon\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder no-pre-suffix\\"><div class=\\"row \\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-12\\"><div class=\\"input-inner align_left\\" data-svg=\\"demo-input-1\\"><button class=\\"nex-submit svg_ready the_input_element btn btn-primary input-lg align_left\\" style=\\"color: rgb(255, 255, 255); border-color: rgb(86, 172, 227); outline-offset: 0px; outline-width: 1px; background: rgb(86, 172, 227);\\" data-onfocus-color=\\"#000000\\" value=\\"Hear from an expert\\" data-default-value=\\"Hear from an expert\\" data-original-title=\\"\\" title=\\"\\">Hear from an expert</button><br></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display: none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div><div class=\\"input_holder col-sm-6\\"><div class=\\"panel grid-system panel-default\\"><div class=\\"panel-body ui-droppable ui-sortable\\"><div class=\\"form_field paragraph ui-draggable dropped\\" style=\\"display: block;\\" id=\\"_83290\\"><div class=\\"draggable_object input-group\\" style=\\"display: none;\\"><button class=\\"btn btn-default btn-sm form-control\\"><i class=\\"fa fa-align-justify\\"></i>&nbsp;&nbsp;<span class=\\"field_title\\">Paragraph</span></button><span class=\\"input-group-addon btn-default\\"><i class=\\" glyphicon glyphicon-move\\"></i></span></div><div id=\\"form_object\\" class=\\"form_object\\" style=\\"\\"><div class=\\"input_holder\\"><div class=\\"input-inner svg_ready\\" data-svg=\\"demo-input-1\\"><div class=\\"row\\"><div class=\\"col-sm-12\\" id=\\"field_container\\"><div class=\\"row\\"><div class=\\"col-sm-12\\"><div class=\\"input-group date svg_ready\\"><p class=\\"the_input_element\\" style=\\"outline-offset: 0px; outline-width: 1px; color: rgb(170, 170, 170); border-color: rgb(47, 111, 143); background: rgb(255, 255, 255);\\" data-onfocus-color=\\"#000000\\">Please let us know in the \\"Details\\" filed the best time to contact you.</p><div style=\\"clear:both;\\"></div></div></div></div></div><div class=\\"field_settings bs-callout bs-callout-info\\" style=\\"display:none;\\"><button class=\\"btn btn-danger btn-sm delete\\"><i class=\\"glyphicon glyphicon-remove\\"></i></button><button class=\\"btn btn-info btn-sm edit\\" style=\\"outline-offset: 0px; outline-width: 1px;\\"><i class=\\"glyphicon glyphicon-pencil\\"></i></button></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div></div>',
						'visual_settings' => NULL,
						'google_analytics_conversion_code' => NULL,
						'colour_scheme' => NULL,
						'send_user_mail' => NULL,
						'user_email_field' => '',
						'on_form_submission' => 'message',
						'date_sent' => NULL,
						'is_form' => '1',
						'is_template' => '1',
					)
				);

			}

			lbmn_debug_console( 'ESSB Easy Social Share Buttons' );
			// ESSB Easy Social Share Buttons
			// Setup 'Social Fans Counter' settings
			if(is_plugin_active('easy-social-share-buttons/easy-social-share-buttons.php')){
				update_option(
					'essb-fans-options',
					array(
						'social' => array(
							'facebook' => array(
									'id' => 'envato',
									'text' => 'Fans',
									),
							'twitter' => array(
									'id' => '@lumbermandesign',
									'text' => 'Followers',
									'key' => 'mxutw2QpMFxuXvW0puvEqKwuL',
									'secret' => 'xqmBL6MWYDkt2yjkbKe0VTlqdkMvsePkif6Z1zzqYFrpguEor4',
									'token' => '95497077-RlX1hhwHC1uz8NcmCCIdbbUm9zqH3wB4Wb44gvaTM',
									'tokensecret' => '7LwXbdxJM1i32SUijrMteRNENysr09GfBiEp6RYKPpHRD',
									),
							'google' => array(
									'id' => '+themeforest',
									'text' => 'Followers',
									'type' => 'Page',
									'counter_type' => 'circledByCount+plusOneCount',
									),
							'youtube' => array(
								'id' => 'UCJr72fY4cTaNZv7WPbvjaSw',
								'text' => 'Subscribers',
								'type' => 'User',
								),
							'dribbble' => array(
								'id' => 'lumbermandesigns',
								'text' => 'Followers',
								),
							'pinterest' => array(
								'id' => 'vladesigner',
								'text' => 'Followers',
								),
						),
						'cache' => 24,
						'data' => array(
								'facebook' => 47742,
								'twitter' => 656,
								'google' => '3676',
								'youtube' => 1898,
								'dribbble' => 5,
								'pinterest' => '9',
						),
						'sort' => array(
								0 => 'facebook',
								1 => 'twitter',
								2 => 'google',
								3 => 'youtube',
								4 => 'dribbble',
								5 => 'pinterest',
						),

					)
				);
			}

			lbmn_debug_console( 'Import pre-defined Maps' );
			// Import pre-defined Maps
			// Check if Maps plugin is active
			if(is_plugin_active('cetabo-googlemaps/cetabo-googlemaps.php')){
				global $wpdb;

				$wpdb->insert(
					$wpdb->prefix . 'maps',
					array(
						'ID' => 1, // TODO: last map id in the db + 1
						'name' => 'Contact Us Map',
						'configuration' => 'YToxNTp7czoyOiJpZCI7czoxOiIxIjtzOjQ6Im5hbWUiO3M6MTQ6IkNvbnRhY3QgVXMgTWFwIjtzOjM6ImxhdCI7czoxODoiNTIuMjEyNTM5NTM5MDE5Njk0IjtzOjM6ImxuZyI7czoxOToiMC4xMTUyNjU0MTUyMDUzNjc4MiI7czo0OiJ6b29tIjtzOjI6IjE1IjtzOjU6IndpZHRoIjtzOjQ6IjEwMCUiO3M6NjoiaGVpZ2h0IjtzOjU6IjM4MHB4IjtzOjQ6InR5cGUiO3M6Nzoicm9hZG1hcCI7czo0OiJtb2RlIjtzOjE6IjEiO3M6NzoiaGVhZGluZyI7czoyOiIzNCI7czo1OiJwaXRjaCI7czoyOiIxMCI7czo5OiJkaXJlY3Rpb24iO3M6NToiZmFsc2UiO3M6OToic2hvd3N0ZXBzIjtzOjQ6InRydWUiO3M6MTQ6Im92ZXJsYXlfZW5hYmxlIjtzOjU6ImZhbHNlIjtzOjY6InBsYWNlcyI7YToxOntpOjA7YTo4OntzOjQ6Im5hbWUiO3M6ODI6IjExIENoZXN0ZXJ0b24gTGFuZSwgVW5pdmVyc2l0eSBvZiBDYW1icmlkZ2UsIENhbWJyaWRnZSwgQ2FtYnJpZGdlc2hpcmUgQ0I0IDNBQSwgVUsiO3M6MzoibGF0IjtzOjEwOiI1Mi4yMTE5OTM5IjtzOjM6ImxuZyI7czoxOToiMC4xMTYwOTY5MDAwMDAwMDIyOCI7czo0OiJpY29uIjtzOjEwODoiaHR0cDovL3Nlb3dwLmx1bWJlcm1hbmRlc2lnbnMuY29tL3dwLWNvbnRlbnQvcGx1Z2lucy9jZXRhYm8tZ29vZ2xlbWFwcy8vaW1nL2NldGFib19pY29ucy9waW5fc3Rhcl9ibHVlLTIucG5nIjtzOjE0OiJpY29uQ3VzdG9tTmFtZSI7czoxMToiU3RhciBibHVlIDIiO3M6MTI6Imljb25DdXN0b21JZCI7czoyOiI3MiI7czo3OiJkZXRhaWxzIjtzOjYzOiI1MSBTb21lc3RyZWV0CkNhbWJyaWRnZSwKQ2FtYnJpZGdlc2hpcmUgQ0I0IDNBQSwKVW5pdGVkIEtpbmdkb20iO3M6OToiYW5pbWF0aW9uIjtzOjE6IjIiO319fQ==',
					)
				);

				$wpdb->insert(
					$wpdb->prefix . 'maps',
					array(
						'ID' => 2,
						'name' => 'Multimap Contact page - UK Address',
						'configuration' => 'YToxNDp7czoyOiJpZCI7czoxOiIyIjtzOjQ6Im5hbWUiO3M6MzY6Ik11bHRpbWFwIENvbnRhY3QgcGFnZSDigJMgVUsgQWRkcmVzcyI7czozOiJsYXQiO3M6MTA6IjUyLjIxMjIwODkiO3M6MzoibG5nIjtzOjE5OiIwLjExNzE0NjQwMDAwMDAyNDE5IjtzOjQ6Inpvb20iO3M6MjoiMTUiO3M6NToid2lkdGgiO3M6NDoiMTAwJSI7czo2OiJoZWlnaHQiO3M6NToiMzYwcHgiO3M6NDoidHlwZSI7czo3OiJyb2FkbWFwIjtzOjQ6Im1vZGUiO3M6MToiMSI7czo3OiJoZWFkaW5nIjtzOjI6IjM0IjtzOjU6InBpdGNoIjtzOjI6IjEwIjtzOjk6ImRpcmVjdGlvbiI7czo1OiJmYWxzZSI7czo5OiJzaG93c3RlcHMiO3M6NDoidHJ1ZSI7czo2OiJwbGFjZXMiO2E6MTp7aTowO2E6ODp7czo0OiJuYW1lIjtzOjEwMToiMTEgQ2hlc3RlcnRvbiBSb2FkLCBVbml2ZXJzaXR5IG9mIENhbWJyaWRnZSwgTWFnZGFsZW5lIENvbGxlZ2UsIENhbWJyaWRnZSwgQ2FtYnJpZGdlc2hpcmUgQ0I0IDNBRCwgVUsiO3M6MzoibGF0IjtzOjEwOiI1Mi4yMTIyMDg5IjtzOjM6ImxuZyI7czoxOToiMC4xMTcxNDY0MDAwMDAwMjQxOSI7czo0OiJpY29uIjtzOjEwNjoiaHR0cDovL3Nlb3dwLmx1bWJlcm1hbmRlc2lnbnMuY29tL3dwLWNvbnRlbnQvcGx1Z2lucy9jZXRhYm8tZ29vZ2xlbWFwcy8vaW1nL2NldGFib19pY29ucy9waW5fc3Rhcl9ibHVlLnBuZyI7czoxNDoiaWNvbkN1c3RvbU5hbWUiO3M6MTE6IlN0YXIgYmx1ZSAxIjtzOjEyOiJpY29uQ3VzdG9tSWQiO3M6MjoiNzEiO3M6NzoiZGV0YWlscyI7czo2MzoiNTEgU29tZXN0cmVldCBDYW1icmlkZ2UsCkNhbWJyaWRnZXNoaXJlIENCNCAzQUEsClVuaXRlZCBLaW5nZG9tIjtzOjk6ImFuaW1hdGlvbiI7czoxOiIyIjt9fX0=',
					)
				);

				$wpdb->insert(
					$wpdb->prefix . 'maps',
					array(
						'ID' => 3,
						'name' => 'Multimap Contact page - Canada Address',
						'configuration' => 'YToxNDp7czoyOiJpZCI7czoxOiIzIjtzOjQ6Im5hbWUiO3M6NDA6Ik11bHRpbWFwIENvbnRhY3QgcGFnZSDigJMgQ2FuYWRhIEFkZHJlc3MiO3M6MzoibGF0IjtzOjE4OiI0My42NzcwNDg4MjM0NTU1NjQiO3M6MzoibG5nIjtzOjE4OiItNzkuMzQ0MDY3MDU5Mjc3MzQiO3M6NDoiem9vbSI7czoyOiIxMiI7czo1OiJ3aWR0aCI7czo0OiIxMDAlIjtzOjY6ImhlaWdodCI7czo1OiIzNjBweCI7czo0OiJ0eXBlIjtzOjc6InJvYWRtYXAiO3M6NDoibW9kZSI7czoxOiIxIjtzOjc6ImhlYWRpbmciO3M6MjoiMzQiO3M6NToicGl0Y2giO3M6MjoiMTAiO3M6OToiZGlyZWN0aW9uIjtzOjU6ImZhbHNlIjtzOjk6InNob3dzdGVwcyI7czo0OiJ0cnVlIjtzOjY6InBsYWNlcyI7YToxOntpOjA7YTo4OntzOjQ6Im5hbWUiO3M6MzA6IkVhc3QgWW9yaywgVG9yb250bywgT04sIENhbmFkYSI7czozOiJsYXQiO3M6MTc6IjQzLjY5MTIwMDU5OTk5OTk5IjtzOjM6ImxuZyI7czoxODoiLTc5LjM0MTY2Mzc5OTk5OTk5IjtzOjQ6Imljb24iO3M6MTA2OiJodHRwOi8vc2Vvd3AubHVtYmVybWFuZGVzaWducy5jb20vd3AtY29udGVudC9wbHVnaW5zL2NldGFiby1nb29nbGVtYXBzLy9pbWcvY2V0YWJvX2ljb25zL3Bpbl9zdGFyX2JsdWUucG5nIjtzOjE0OiJpY29uQ3VzdG9tTmFtZSI7czoxMToiU3RhciBibHVlIDEiO3M6MTI6Imljb25DdXN0b21JZCI7czoyOiI3MSI7czo3OiJkZXRhaWxzIjtzOjYzOiI1MSBTb21lc3RyZWV0IENhbWJyaWRnZSwKQ2FtYnJpZGdlc2hpcmUgQ0I0IDNBQSwKVW5pdGVkIEtpbmdkb20iO3M6OToiYW5pbWF0aW9uIjtzOjE6IjIiO319fQ==',
					)
				);

			}


			// Import pre-designed MasterSlider Slides
			// Check if MasterSlider is active

			// http://support.averta.net/envato/support/ticket/regenerate-custom-css-programatically/#post-16478
			if ( defined('MSWP_AVERTA_VERSION') ) {
				global $wpdb;

				$wpdb->insert(
					$wpdb->prefix . 'masterslider_sliders',
					array(
						'ID' => 1, // TODO: last slider id in the db + 1
						'title' => 'SEO Agency Flat',
						'type' => 'custom',
						'slides_num' => 4,
						'date_created' => '2014-06-25 12:53:53',
						'date_modified' => '2014-06-30 19:16:35',
						'params' => 
						"eyJtZXRhIjp7IlNldHRpbmdzIWlkcyI6IjEiLCJTZXR0aW5ncyFuZXh0SWQiOjIsIlNsaWRlIWlkcyI6IjEsNCw1IiwiU2xpZGUhbmV4dElkIjo2LCJTdHlsZSFpZHMiOiIzLDQsNSw3LDgsOSwxMSwxMiwxMywxNCwxNSwxNiwyMSwyMiwyMywzNCwzOCwzOSw0MSw0Miw0Myw0Nyw0OCw0OSw1MSw1Miw1Myw1NCw1NSw1Niw1Nyw1OCw1OSw2MCw2MSw2Miw2Myw2NCw2NSw3OSw4MCw4MSw4Miw4Myw4NCw4NSw4Niw4Nyw4OCw4OSw5MCIsIlN0eWxlIW5leHRJZCI6OTE" . 
						"sIkVmZmVjdCFpZHMiOiI1LDYsNyw4LDksMTAsMTMsMTQsMTUsMTYsMTcsMTgsMjEsMjIsMjMsMjQsMjUsMjYsMjcsMjgsMjksMzAsMzEsMzIsNDEsNDIsNDMsNDQsNDUsNDYsNjcsNjgsNzUsNzYsNzcsNzgsODEsODIsODMsODQsODUsODYsOTMsOTQsOTUsOTYsOTcsOTgsMTAxLDEwMiwxMDMsMTA0LDEwNSwxMDYsMTA3LDEwOCwxMDksMTEwLDExMSwxMTIsMTEzLDExNCwxMTUsMTE2LDExNywxMTgsMTE5LDEyMCwxMjEsMTIyLDEyMywxMjQsMTI1LDEyNiwxMjcsMT" . 
						"I4LDEyOSwxMzAsMTU3LDE1OCwxNTksMTYwLDE2MSwxNjIsMTYzLDE2NCwxNjUsMTY2LDE2NywxNjgsMTY5LDE3MCwxNzEsMTcyLDE3MywxNzQsMTc1LDE3NiwxNzcsMTc4LDE3OSwxODAiLCJFZmZlY3QhbmV4dElkIjoxODEsIkxheWVyIWlkcyI6IjMsNCw1LDcsOCw5LDExLDEyLDEzLDE0LDE1LDE2LDIxLDIyLDIzLDM0LDM4LDM5LDQxLDQyLDQzLDQ3LDQ4LDQ5LDUxLDUyLDUzLDU0LDU1LDU2LDU3LDU4LDU5LDYwLDYxLDYyLDYzLDY0LDY1LDc5LDgwLDgxLDgyL" . 
						"DgzLDg0LDg1LDg2LDg3LDg4LDg5LDkwIiwiTGF5ZXIhbmV4dElkIjo5MSwiQ29udHJvbCFpZHMiOiIxLDMiLCJDb250cm9sIW5leHRJZCI6Nn0sIk1TUGFuZWwuU2V0dGluZ3MiOnsiMSI6IntcImlkXCI6XCIxXCIsXCJzbmFwcGluZ1wiOnRydWUsXCJuYW1lXCI6XCJGbGF0IERlc2lnbiBTdHlsZVwiLFwid2lkdGhcIjoxMjAwLFwiaGVpZ2h0XCI6MzYwLFwid3JhcHBlcldpZHRoXCI6MTAwLFwid3JhcHBlcldpZHRoVW5pdFwiOlwiJVwiLFwiYXV0b0Nyb3BcIjpm" . 
						"YWxzZSxcInR5cGVcIjpcImN1c3RvbVwiLFwic2xpZGVySWRcIjpcIjRcIixcImxheW91dFwiOlwiZnVsbHdpZHRoXCIsXCJhdXRvSGVpZ2h0XCI6ZmFsc2UsXCJ0clZpZXdcIjpcImZhZGVcIixcInNwZWVkXCI6NSxcInNwYWNlXCI6MCxcInN0YXJ0XCI6MSxcImdyYWJDdXJzb3JcIjp0cnVlLFwic3dpcGVcIjp0cnVlLFwibW91c2VcIjp0cnVlLFwid2hlZWxcIjpmYWxzZSxcImF1dG9wbGF5XCI6dHJ1ZSxcImxvb3BcIjp0cnVlLFwic2h1ZmZsZVwiOmZhbHNlLFw" . 
						"icHJlbG9hZFwiOjEsXCJvdmVyUGF1c2VcIjpmYWxzZSxcImVuZFBhdXNlXCI6ZmFsc2UsXCJoaWRlTGF5ZXJzXCI6ZmFsc2UsXCJkaXJcIjpcInZcIixcInBhcmFsbGF4TW9kZVwiOlwib2ZmXCIsXCJjZW50ZXJDb250cm9sc1wiOnRydWUsXCJpbnN0YW50U2hvd0xheWVyc1wiOmZhbHNlLFwic2tpblwiOlwibXMtc2tpbi1kZWZhdWx0XCIsXCJtc1RlbXBsYXRlXCI6XCJjdXN0b21cIixcIm1zVGVtcGxhdGVDbGFzc1wiOlwiXCIsXCJ1c2VkRm9udHNcIjpcIlJvYm" . 
						"90bzozMDAscmVndWxhclwifSJ9LCJNU1BhbmVsLlNsaWRlIjp7IjEiOiJ7XCJpZFwiOlwiMVwiLFwidGltZWxpbmVfaFwiOjM5NixcIm9yZGVyXCI6MCxcImR1cmF0aW9uXCI6OSxcImZpbGxNb2RlXCI6XCJmaWxsXCIsXCJiZ0NvbG9yXCI6XCIjMDg4YWRhXCIsXCJiZ3ZfZmlsbG1vZGVcIjpcImZpbGxcIixcImJndl9sb29wXCI6dHJ1ZSxcImJndl9tdXRlXCI6dHJ1ZSxcImJndl9hdXRvcGF1c2VcIjpmYWxzZSxcImxheWVyX2lkc1wiOlszLDQsNSw3LDgsOSwxM" . 
						"SwxMiwxMywxNCwxNSwxNiw0MSw0Miw0Myw0Nyw0OCw0OSw2MSw2Miw2M119IiwiNCI6IntcImlkXCI6NCxcInRpbWVsaW5lX2hcIjo0MTIsXCJvcmRlclwiOjEsXCJkdXJhdGlvblwiOjksXCJmaWxsTW9kZVwiOlwiZmlsbFwiLFwiYmdDb2xvclwiOlwiIzVhYTUxN1wiLFwiYmd2X2ZpbGxtb2RlXCI6XCJmaWxsXCIsXCJiZ3ZfbG9vcFwiOnRydWUsXCJiZ3ZfbXV0ZVwiOnRydWUsXCJiZ3ZfYXV0b3BhdXNlXCI6ZmFsc2UsXCJsYXllcl9pZHNcIjpbMjEsMjIsMjMs" . 
						"MzQsMzgsMzksNTEsNTIsNTMsNTQsNTUsNTYsNTcsNTgsNTksNjBdfSIsIjUiOiJ7XCJpZFwiOjUsXCJ0aW1lbGluZV9oXCI6Mzk2LFwib3JkZXJcIjoyLFwiZHVyYXRpb25cIjo5LFwiZmlsbE1vZGVcIjpcImZpbGxcIixcImJnQ29sb3JcIjpcIiNhZDNjZGNcIixcImJndl9maWxsbW9kZVwiOlwiZmlsbFwiLFwiYmd2X2xvb3BcIjp0cnVlLFwiYmd2X211dGVcIjp0cnVlLFwiYmd2X2F1dG9wYXVzZVwiOmZhbHNlLFwibGF5ZXJfaWRzXCI6WzY0LDY1LDc5LDgwLDg" . 
						"xLDgyLDgzLDg0LDg1LDg2LDg3LDg4LDg5LDkwXX0ifSwiTVNQYW5lbC5TdHlsZSI6eyIzIjoie1wiaWRcIjozLFwidHlwZVwiOlwiY3VzdG9tXCIsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi00LTNcIixcImZvbnRGYW1pbHlcIjpcIlJvYm90b1wiLFwiZm9udFdlaWdodFwiOlwiMzAwXCIsXCJmb250U2l6ZVwiOjIwLFwibGV0dGVyU3BhY2luZ1wiOjAsXCJsaW5lSGVpZ2h0XCI6XCIzMHB4XCIsXCJjb2xvclwiOlwiI2ZmZmZmZlwiLFwiY3VzdG9tXCI6XCJcIn0iLC" . 
						"I0Ijoie1wiaWRcIjo0LFwidHlwZVwiOlwiY3VzdG9tXCIsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi00LTRcIixcImZvbnRGYW1pbHlcIjpcIlJvYm90b1wiLFwiZm9udFdlaWdodFwiOlwiMzAwXCIsXCJmb250U2l6ZVwiOjQ4LFwibGV0dGVyU3BhY2luZ1wiOi0xLFwibGluZUhlaWdodFwiOlwiNDhweFwiLFwiY29sb3JcIjpcIiNmZmZmZmZcIixcImN1c3RvbVwiOlwiZm9udC13ZWlnaHQ6MjAwO1wifSIsIjUiOiJ7XCJpZFwiOjUsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjciOiJ7XCJpZFwiOjcsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjgiOiJ7XCJpZFwiOjgsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjkiOiJ7XCJpZFwiOjksXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjExIjoie1wiaWRcIjoxMSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiMTIiOiJ7XCJpZFwiOjEyLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCIxMyI6IntcImlkXCI6MTMsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjE0Ijoie1wiaWRcIjoxNCxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiMTUiOiJ7XCJpZFwiOjE1LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCIxNiI6IntcImlkXCI6MTYsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjIxIjoie1wiaWRcIjoyMSxcInR5cGVcIjpcImN1c3RvbVwiLFwiY2xhc3NOYW1lXCI6X" . 
						"CJtc3AtY24tNC0yMVwiLFwiZm9udEZhbWlseVwiOlwiUm9ib3RvXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6MjAsXCJsZXR0ZXJTcGFjaW5nXCI6MCxcImxpbmVIZWlnaHRcIjpcIjMwcHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcIlwifSIsIjIyIjoie1wiaWRcIjoyMixcInR5cGVcIjpcImN1c3RvbVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtY24tNC0yMlwiLFwiZm9udEZhbWlseVwiOlwiUm9ib3RvXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6NDgsXCJsZXR0ZXJTcGFjaW5nXCI6LTEsXCJsaW5lSGVpZ2h0XCI6XCI0OHB4XCIsXCJjb2xvclwiOlwiI2ZmZmZmZlwiLFwiY3VzdG9tXCI6XCJmb250LXdlaWdodDoyMDA7XCJ9IiwiMjMiOiJ7XCJpZFwiOjIzLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCIzNCI6IntcImlkXCI6MzQsXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250RmFtaWx5XCI6XCJSb2JvdG9cIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCIzOCI6IntcImlkXCI6MzgsXCJmb250V2VpZ2h0XCI6XCJub3Jt" . 
						"YWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjM5Ijoie1wiaWRcIjozOSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNDEiOiJ7XCJpZFwiOjQxLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI0MiI6IntcImlkXCI6NDIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjQzIjoie1wiaWRcIjo0MyxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNDciOiJ7XCJpZFwiOjQ3LFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcImJhY2tncm91bmRDb2xvclwiOm51bGwsXCJwYWRkaW5nVG9wXCI6NCxcInBhZGRpbmdSaWdodFwiOjYsXCJwYWRkaW5nQm90dG9tXCI6NCxcInBhZGRpbmdMZWZ0XCI6NixcImJvcmRlclRvcFwiOjIsXCJib3JkZXJSaWdodFwiOjIsXCJib3JkZXJCb3R0b21cIjoyLFwiYm9yZGVyTGVmdFwiOjIsXCJib3JkZXJDb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjQ2KVwiLFwiYm9yZGVyUmFkaXVzXCI6NCxcImJvcmRlclN0eWxlXCI6XCJzb2xpZFwiLFwiZm9udEZhbWlseVwiOlwiUm9ib3RvXCIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImZvbnRTaXplXCI6MTIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIixcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNilcIixcImN1c3RvbVwiOlwiXCJ9IiwiNDgiOiJ7XCJpZFwiOjQ4LFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcImJhY2tncm91bmRDb2xvclwiOm51bGwsXCJwYWRkaW5nVG9wXCI6NCxcInBhZGRpbmdSaWdodFwiOjYsXCJwYWRkaW5nQm90dG9tXCI6NCxcInBhZGRpbmdMZWZ0XCI6NixcImJvcmRlclRvcFwiOjIsXCJib3JkZXJSaWdodFwiOjIsXCJib3JkZXJCb3R0b21cIjoyLFwiYm9yZGVyTGVmdFwiOjIsXCJib3JkZXJDb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjQ2KVwiLFwiYm9yZGVyUmFkaXVzXCI6NCxcImJvcmRlclN0eWxlXCI6XCJzb2xpZFwiLFwiZm9udEZhbWlseVwiOlwiUm9ib3RvXCIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImZvbnRTaXplXCI6MTIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIixcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNilcIixcImN1c3RvbVwiOlwiXCJ9IiwiNDkiOiJ7XCJpZFwiOjQ5LFwidHlwZVwiOlwiY29weVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcImJhY2tncm91bmRDb2xvclwiOm51bGwsXCJwYWRkaW5nVG9wXCI6NCxcInBhZGRpbmdSaWdodFwiOjYsXCJwYWRkaW5nQm90dG9tXCI6NCxcInBhZGRpbmdMZWZ0XCI6NixcImJvcmRlclRvcFwiOjIsXCJib3JkZXJSaWdodFwiOjIsXCJib3JkZXJCb3R0b21cIjoyLFwiYm9yZGVyTGVmdFwiOjIsXCJib3JkZXJDb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjQ2KVwiLFwiYm9yZGVyUmFkaXVzXCI6NCxcImJvcmRlclN0eWxlXCI6XCJzb2xpZFwiLFwiZm9udEZhbWlseVwiOlwiUm9ib3RvXCIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImZvbnRTaXplXCI6MTIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIixcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNilcIixcImN1c3RvbVwiOlwiXCJ9IiwiNTEiOiJ7XCJpZFwiOjUxLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI1MiI6IntcImlkXCI6NTIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjUzIjoie1wiaWRcIjo1MyxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNTQiOiJ7XCJpZFwiOjU0LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI1NSI6IntcImlkXCI6NTUsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjU2Ijoie1wiaWRcIjo1NixcInR5cGVcIjpcImNvcHlcIixcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC03XCIsXCJiYWNrZ3JvdW5kQ29sb3JcIjpudWxsLFwicGFkZGluZ1RvcFwiOjQsXCJwYWRkaW5nUmlnaHRcIjo2LFwicGFkZGluZ0JvdHRvbVwiOjQsXCJwYWRkaW5nTGVmdFwiOjYsXCJib3JkZXJUb3BcIjoyLFwiYm9yZGVyUmlnaHRcIjoyLFwiYm9yZGVyQm90dG9tXCI6MixcImJvcmRlckxlZnRcIjoyLFwiYm9yZGVyQ29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC40NilcIixcImJvcmRlclJhZGl1c1wiOjQsXCJib3JkZXJTdHlsZVwiOlwic29saWRcIixcImZvbnRGYW1pbHlcIjpcIlJvYm90b1wiLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJmb250U2l6ZVwiOjEyLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCIsXCJjb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjYpXCIsXCJjdXN0b21cIjpcIlwifSIsIjU3Ijoie1wiaWRcIjo1NyxcInR5cGVcIjpcImNvcHlcIixcImNsYXNzTmFtZVwiOlwibXNwLXByZXNldC03XCIsXCJiYWNrZ3JvdW5kQ29sb3JcIjpudWxsLFwicGFkZGluZ1RvcFwiOjQsXCJwYWRkaW5nUmlnaHRcIjo2LFwicGFkZGluZ0JvdHRvbVwiOjQsXCJwYWRkaW5nTGVmdFwiOjYsXCJib3JkZXJUb3BcIjoyLFwiYm9yZGVyUmlnaHRcIjoyLFwiYm9yZGVyQm90dG9tXCI6MixcImJvcmRlckxlZnRcIjoyLFwiYm9yZGVyQ29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC40NilcIixcImJvcmRlclJhZGl1c1wiOjQsXCJib3JkZXJTdHlsZVwiOlwic29saWRcIixcImZvbnRGYW1pbHlcIjpcIlJvYm90b1wiLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJmb250U2l6ZVwiOjEyLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCIsXCJjb2xvclwiOlwicmdiYSgyNTUsIDI1NSwgMjU1LCAwLjYpXCIsXCJjdXN0b21cIjpcIlwifSIsIjU4Ijoie1wiaWRcIjo1OCxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNTkiOiJ7XCJpZFwiOjU5LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI2MCI6IntcImlkXCI6NjAsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjYxIjoie1wiaWRcIjo2MSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiNjIiOiJ7XCJpZFwiOjYyLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI2MyI6IntcImlkXCI6NjMsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjY0Ijoie1wiaWRcIjo2NCxcInR5cGVcIjpcImN1c3RvbVwiLFwiY2xhc3NOYW1lXCI6XCJtc3AtY24tNC02NFwiLFwiZm9udEZhbWlseVwiOlwiUm9ib3RvXCIsXCJmb250V2VpZ2h0XCI6XCIzMDBcIixcImZvbnRTaXplXCI6MjAsXCJ0ZXh0QWxpZ25cIjpcImNlbnRlclwiLFwibGV0dGVyU3BhY2luZ1wiOjAsXCJsaW5lSGVpZ2h0XCI6XCIzMHB4XCIsXCJjb2xvclwiOlwiI2ZmZmZmZlwiLFwiY3VzdG9tXCI6XCJcIn0iLCI2NSI6IntcImlkXCI6NjUsXCJ0eXBlXCI6XCJjdXN0b21cIixcImNsYXNzTmFtZVwiOlwibXNwLWNuLTQtNjVcIixcImZvbnRGYW1pbHlcIjpcIlJvYm90b1wiLFwiZm9udFdlaWdodFwiOlwiMzAwXCIsXCJmb250U2l6ZVwiOjQ4LFwidGV4dEFsaWduXCI6XCJjZW50ZXJcIixcImxldHRlclNwYWNpbmdcIjotMSxcImxpbmVIZWlnaHRcIjpcIjQ4cHhcIixcImNvbG9yXCI6XCIjZmZmZmZmXCIsXCJjdXN0b21cIjpcImZvbnQtd2VpZ2h0OjIwMDtcIn0iLCI3OSI6IntcImlkXCI6NzksXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250RmFtaWx5XCI6XCJSb2JvdG9cIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCI4MCI6IntcImlkXCI6ODAsXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250RmFtaWx5XCI6XCJSb2JvdG9cIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCI4MSI6IntcImlkXCI6ODEsXCJ0eXBlXCI6XCJjb3B5XCIsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwiYmFja2dyb3VuZENvbG9yXCI6bnVsbCxcInBhZGRpbmdUb3BcIjo0LFwicGFkZGluZ1JpZ2h0XCI6NixcInBhZGRpbmdCb3R0b21cIjo0LFwicGFkZGluZ0xlZnRcIjo2LFwiYm9yZGVyVG9wXCI6MixcImJvcmRlclJpZ2h0XCI6MixcImJvcmRlckJvdHRvbVwiOjIsXCJib3JkZXJMZWZ0XCI6MixcImJvcmRlckNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNDYpXCIsXCJib3JkZXJSYWRpdXNcIjo0LFwiYm9yZGVyU3R5bGVcIjpcInNvbGlkXCIsXCJmb250RmFtaWx5XCI6XCJSb2JvdG9cIixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwiZm9udFNpemVcIjoxMixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwiLFwiY29sb3JcIjpcInJnYmEoMjU1LCAyNTUsIDI1NSwgMC42KVwiLFwiY3VzdG9tXCI6XCJcIn0iLCI4MiI6IntcImlkXCI6ODIsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjgzIjoie1wiaWRcIjo4MyxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiODQiOiJ7XCJpZFwiOjg0LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI4NSI6IntcImlkXCI6ODUsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjg2Ijoie1wiaWRcIjo4NixcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiODciOiJ7XCJpZFwiOjg3LFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0iLCI4OCI6IntcImlkXCI6ODgsXCJmb250V2VpZ2h0XCI6XCJub3JtYWxcIixcImxpbmVIZWlnaHRcIjpcIm5vcm1hbFwifSIsIjg5Ijoie1wiaWRcIjo4OSxcImZvbnRXZWlnaHRcIjpcIm5vcm1hbFwiLFwibGluZUhlaWdodFwiOlwibm9ybWFsXCJ9IiwiOTAiOiJ7XCJpZFwiOjkwLFwiZm9udFdlaWdodFwiOlwibm9ybWFsXCIsXCJsaW5lSGVpZ2h0XCI6XCJub3JtYWxcIn0ifSwiTVNQYW5lbC5FZmZlY3QiOnsiNSI6IntcImlkXCI6NSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCI2Ijoie1wiaWRcIjo2LFwiZmFkZVwiOnRydWV9IiwiNyI6IntcImlkXCI6NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCI4Ijoie1wiaWRcIjo4LFwiZmFkZVwiOnRydWV9IiwiOSI6IntcImlkXCI6OSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCIxMCI6IntcImlkXCI6MTAsXCJmYWRlXCI6dHJ1ZX0iLCIxMyI6IntcImlkXCI6MTMsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxNTB9IiwiMTQiOiJ7XCJpZFwiOjE0LFwiZmFkZVwiOnRydWV9IiwiMTUiOiJ7XCJpZFwiOjE1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTEyMCxcInRyYW5zbGF0ZVlcIjozMCxcInNjYWxlWFwiOjAsXCJzY2FsZVlcIjowfSIsIjE2Ijoie1wiaWRcIjoxNixcImZhZGVcIjp0cnVlfSIsIjE3Ijoie1wiaWRcIjoxNyxcImZhZGVcIjp0cnVlLFwic2NhbGVYXCI6MCxcInNjYWxlWVwiOjB9IiwiMTgiOiJ7XCJpZFwiOjE4LFwiZmFkZVwiOnRydWV9IiwiMjEiOiJ7XCJpZFwiOjIxLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTcwLFwidHJhbnNsYXRlWVwiOi00MCxcInRyYW5zbGF0ZVpcIjotMixcInNjYWxlWFwiOjAsXCJzY2FsZVlcIjowfSIsIjIyIjoie1wiaWRcIjoyMixcImZhZGVcIjp0cnVlfSIsIjIzIjoie1wiaWRcIjoyMyxcImZhZGVcIjp0cnVlLFwic2NhbGVYXCI6MCxcInNjYWxlWVwiOjB9IiwiMjQiOiJ7XCJpZFwiOjI0LFwiZmFkZVwiOnRydWV9IiwiMjUiOiJ7XCJpZFwiOjI1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTcwLFwidHJhbnNsYXRlWVwiOjUwfSIsIjI2Ijoie1wiaWRcIjoyNixcImZhZGVcIjp0cnVlfSIsIjI3Ijoie1wiaWRcIjoyNyxcImZhZGVcIjp0cnVlLFwic2NhbGVYXCI6MCxcInNjYWxlWVwiOjB9IiwiMjgiOiJ7XCJpZFwiOjI4LFwiZmFkZVwiOnRydWV9IiwiMjkiOiJ7XCJpZFwiOjI5LFwiZmFkZV" . 
						"wiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTg1LFwidHJhbnNsYXRlWVwiOjYwLFwic2NhbGVYXCI6MCxcInNjYWxlWVwiOjB9IiwiMzAiOiJ7XCJpZFwiOjMwLFwiZmFkZVwiOnRydWV9IiwiMzEiOiJ7XCJpZFwiOjMxLFwiZmFkZVwiOnRydWUsXCJzY2FsZVhcIjowLFwic2NhbGVZXCI6MH0iLCIzMiI6IntcImlkXCI6MzIsXCJmYWRlXCI6dHJ1ZX0iLCI0MSI6IntcImlkXCI6NDEsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxNTB9IiwiNDIiOiJ7XCJpZFwiOjQyLFwiZmFkZVwiOnRydWV9IiwiNDMiOiJ7XCJpZFwiOjQzLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTUwfSIsIjQ0Ijoie1wiaWRcIjo0NCxcImZhZGVcIjp0cnVlfSIsIjQ1Ijoie1wiaWRcIjo0NSxcImZhZGVcIjp0cnVlLFwicm90YXRlWVwiOjkwfSIsIjQ2Ijoie1wiaWRcIjo0NixcImZhZGVcIjp0cnVlfSIsIjY3Ijoie1wiaWRcIjo2NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjY4Ijoie1wiaWRcIjo2OCxcImZhZGVcIjp0cnVlfSIsIjc1Ijoie1wiaWRcIjo3NSxcImZhZGVcIjp0cnVlLFwicm90YXRlWVwiOjkwfSIsIjc2Ijoie1wiaWRcIjo3NixcImZhZGVcIjp0cnVlfSIsIjc3Ijoie1wiaWRcIjo3NyxcImZhZGVcIjp0cnVlLFwicm90YXRlWVwiOjkwfSIsIjc4Ijoie1wiaWRcIjo3OCxcImZhZGVcIjp0cnVlfSIsIjgxIjoie1wiaWRcIjo4MSxcImZhZGVcIjp0cnVlLFwicm90YXRlXCI6LTEwODB9IiwiODIiOiJ7XCJpZFwiOjgyLFwiZmFkZVwiOnRydWV9IiwiODMiOiJ7XCJpZFwiOjgzLFwiZmFkZVwiOnRydWUsXCJyb3RhdGVcIjotMTM5MH0iLCI4NCI6IntcImlkXCI6ODQsXCJmYWRlXCI6dHJ1ZX0iLCI4NSI6IntcImlkXCI6ODUsXCJmYWRlXCI6dHJ1ZSxcInJvdGF0ZVwiOi03MjB9IiwiODYiOiJ7XCJpZFwiOjg2LFwiZmFkZVwiOnRydWV9IiwiOTMiOiJ7XCJpZFwiOjkzLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NDB9IiwiOTQiOiJ7XCJpZFwiOjk0LFwiZmFkZVwiOnRydWV9IiwiOTUiOiJ7XCJpZFwiOjk1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NDB9IiwiOTYiOiJ7XCJpZFwiOjk2LFwiZmFkZVwiOnRydWV9IiwiOTciOiJ7XCJpZFwiOjk3LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NDB9IiwiOTgiOiJ7XCJpZFwiOjk4LFwiZmFkZVwiOnRydWV9IiwiMTAxIjoie1wiaWRcIjoxMDEsXCJmYWRlXCI6ZmFsc2UsXCJ0cmFuc2xhdGVZXCI6NTAwfSIsIjEwMiI6IntcImlkXCI6MTAyLFwiZmFkZVwiOnRydWV9IiwiMTAzIjoie1wiaWRcIjoxMDMsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjo1MCxcInNjYWxlWVwiOjB9IiwiMTA0Ijoie1wiaWRcIjoxMDQsXCJmYWRlXCI6dHJ1ZX0iLCIxMDUiOiJ7XCJpZFwiOjEwNSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjUwLFwic2NhbGVZXCI6MH0iLCIxMDYiOiJ7XCJpZFwiOjEwNixcImZhZGVcIjp0cnVlfSIsIjEwNyI6IntcImlkXCI6MTA3LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NTAsXCJzY2FsZVlcIjowfSIsIjEwOCI6IntcImlkXCI6MTA4LFwiZmFkZVwiOnRydWV9IiwiMTA5Ijoie1wiaWRcIjoxMDksXCJmYWRlXCI6dHJ1ZSxcInNjYWxlWFwiOjAsXCJzY2FsZVlcIjowfSIsIjExMCI6IntcImlkXCI6MTEwLFwiZmFkZVwiOnRydWUsXCJzY2FsZVhcIjowLFwic2NhbGVZXCI6MH0iLCIxMTEiOiJ7XCJpZFwiOjExMSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjExMiI6IntcImlkXCI6MTEyLFwiZmFkZVwiOnRydWV9IiwiMTEzIjoie1wiaWRcIjoxMTMsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjo0MH0iLCIxMTQiOiJ7XCJpZFwiOjExNCxcImZhZGVcIjp0cnVlfSIsIjExNSI6IntcImlkXCI6MTE1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTEwMCxcInJvdGF0ZVpcIjoyNX0iLCIxMTYiOiJ7XCJpZFwiOjExNixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjExNyI6IntcImlkXCI6MTE3LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTAwLFwicm90YXRlWlwiOjI1fSIsIjExOCI6IntcImlkXCI6MTE4LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTEwMCxcInJvdGF0ZVpcIjotMjV9IiwiMTE5Ijoie1wiaWRcIjoxMTksXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTAwLFwicm90YXRlWlwiOi0zNX0iLCIxMjAiOiJ7XCJpZFwiOjEyMCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjEyMSI6IntcImlkXCI6MTIxLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTEwMCxcInJvdGF0ZVpcIjotMjV9IiwiMTIyIjoie1wiaWRcIjoxMjIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTUwLFwicm90YXRlWlwiOi0yNX0iLCIxMjMiOiJ7XCJpZFwiOjEyMyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjEwMCxcInJvdGF0ZVpcIjoyNX0iLCIxMjQiOiJ7XCJpZFwiOjEyNCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xNTAsXCJyb3RhdGVaXCI6LTI1fSIsIjEyNSI6IntcImlkXCI6MTI1LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTEwMCxcInJvdGF0ZVpcIjotMjV9IiwiMTI2Ijoie1wiaWRcIjoxMjYsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTUwLFwicm90YXRlWlwiOi0yNX0iLCIxMjciOiJ7XCJpZFwiOjEyNyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjE1MH0iLCIxMjgiOiJ7XCJpZFwiOjEyOCxcImZhZGVcIjp0cnVlfSIsIjEyOSI6IntcImlkXCI6MTI5LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTUwfSIsIjEzMCI6IntcImlkXCI6MTMwLFwiZmFkZVwiOnRydWV9IiwiMTU3Ijoie1wiaWRcIjoxNTcsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjo0MH0iLCIxNTgiOiJ7XCJpZFwiOjE1OCxcImZhZGVcIjp0cnVlfSIsIjE1OSI6IntcImlkXCI6MTU5LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NDB9IiwiMTYwIjoie1wiaWRcIjoxNjAsXCJmYWRlXCI6dHJ1ZX0iLCIxNjEiOiJ7XCJpZFwiOjE2MSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjQwfSIsIjE2MiI6IntcImlkXCI6MTYyLFwiZmFkZVwiOnRydWV9IiwiMTYzIjoie1wiaWRcIjoxNjMsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjE2NCI6IntcImlkXCI6MTY0LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTE1MCxcInJvdGF0ZVpcIjotMjV9IiwiMTY1Ijoie1wiaWRcIjoxNjUsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoxMDAsXCJyb3RhdGVaXCI6MjV9IiwiMTY2Ijoie1wiaWRcIjoxNjYsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTUwLFwicm90YXRlWlwiOi0yNX0iLCIxNjciOiJ7XCJpZFwiOjE2NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0xMDAsXCJyb3RhdGVaXCI6LTI1fSIsIjE2OCI6IntcImlkXCI6MTY4LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6LTE1MCxcInJvdGF0ZVpcIjotMjV9IiwiMTY5Ijoie1wiaWRcIjoxNjksXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMTMwLFwic2NhbGVYXCI6MC41LFwic2NhbGVZXCI6MC41fSIsIjE3MCI6IntcImlkXCI6MTcwLFwiZmFkZVwiOnRydWV9IiwiMTcxIjoie1wiaWRcIjoxNzEsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMTMwLFwidHJhbnNsYXRlWVwiOi04MCxcInNjYWxlWFwiOjAuNSxcInNjYWxlWVwiOjAuNX0iLCIxNzIiOiJ7XCJpZFwiOjE3MixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOi0xMzAsXCJ0cmFuc2xhdGVZXCI6LTgwLFwic2NhbGVYXCI6MS41LFwic2NhbGVZXCI6MS41fSIsIjE3MyI6IntcImlkXCI6MTczLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6MTMwLFwic2NhbGVYXCI6MC41LFwic2NhbGVZXCI6MC41fSIsIjE3NCI6IntcImlkXCI6MTc0LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTE3MCxcInNjYWxlWFwiOjEuNSxcInNjYWxlWVwiOjEuNX0iLCIxNzUiOiJ7XCJpZFwiOjE3NSxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOjEzMCxcInRyYW5zbGF0ZVlcIjotODAsXCJzY2FsZVhcIjowLjUsXCJzY2FsZVlcIjowLjV9IiwiMTc2Ijoie1wiaWRcIjoxNzYsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMTMwLFwidHJhbnNsYXRlWVwiOi04MCxcInNjYWxlWFwiOjEuNSxcInNjYWxlWVwiOjEuNX0iLCIxNzciOiJ7XCJpZFwiOjE3NyxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOjEzMCxcInRyYW5zbGF0ZVlcIjo4MCxcInNjYWxlWFwiOjAuNSxcInNjYWxlWVwiOjAuNX0iLCIxNzgiOiJ7XCJpZFwiOjE3OCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOi0xMzAsXCJ0cmFuc2xhdGVZXCI6LTgwLFwic2NhbGVYXCI6MS41LFwic2NhbGVZXCI6MS41fSIsIjE3OSI6IntcImlkXCI6MTc5LFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6LTEzMCxcInRyYW5zbGF0ZVlcIjo4MCxcInNjYWxlWFwiOjAuNSxcInNjYWxlWVwiOjAuNX0iLCIxODAiOiJ7XCJpZFwiOjE4MCxcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOi0xMzAsXCJ0cmFuc2xhdGVZXCI6LTgwLFwic2NhbGVYXCI6MS41LFwic2NhbGVZXCI6MS41fSJ9LCJNU1BhbmVsLkxheWVyIjp7IjMiOiJ7XCJpZFwiOjMsXCJuYW1lXCI6XCJTZWNvbmRhcnkgVGV4dFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTUwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6OCxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIkRvbmVjIHNlZCBvZGlvIGR1aS4gRnVzY2UgZGFwaWJ1cywgdGVsbHVzIGFjIGN1cnN1cyBjb21tb2RvLCB0b3J0b3IgbWF1cmlzIGNvbmRpbWVudHVtIG5pYmgsIHV0IGZlcm1lbnR1bSBtYXNzYSBqdXN0by5cIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6MjAwLFwid2lkdGhcIjo1MDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi00LTNcIixcInNob3dEdXJhdGlvblwiOjEuMjg3NSxcInNob3dEZWxheVwiOjAuMixcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6MyxcInNob3dFZmZlY3RcIjo1LFwiaGlkZUVmZmVjdFwiOjZ9IiwiNCI6IntcImlkXCI6NCxcIm5hbWVcIjpcIkhlYWRpbmdcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjksXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJjb250ZW50XCI6XCJHdWFyYW50ZWVkIGluY3JlYXNlIG9mwqB5b3VyIHdlYnNpdGUgc2FsZXNcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6NzUsXCJ3aWR0aFwiOjUwMCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcImNsYXNzTmFtZVwiOlwibXNwLWNuLTQtNFwiLFwic2hvd0R1cmF0aW9uXCI6MS4yODc1LFwic2hvd0RlbGF5XCI6MCxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NCxcInNob3dFZmZlY3RcIjo3LFwiaGlkZUVmZmVjdFwiOjh9IiwiNSI6IntcImlkXCI6NSxcIm5hbWVcIjpcIk1hY0Jvb2tcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1tYWNib29rLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NCxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW" . 
						"50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24tbWFjYm9vay5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjI5OSxcIm9mZnNldFlcIjoyOCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJiclwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNjI1LFwic2hvd0RlbGF5XCI6MC4zNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sMTUwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjUsXCJzaG93RWZmZWN0XCI6OSxcImhpZGVFZmZlY3RcIjoxMH0iLCI3Ijoie1wiaWRcIjo3LFwibmFtZVwiOlwiaU1hY1wiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTUwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZhbHQtaWxsdXN0cmF0aW9uLWltYWMtMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjozLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mYWx0LWlsbHVzdHJhdGlvbi1pbWFjLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NDksXCJvZmZzZXRZXCI6MjksXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYnJcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjg4NzUsXCJzaG93RGVsYXlcIjowLjcsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sMTUwLG4sbixuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjcsXCJzaG93RWZmZWN0XCI6MTMsXCJoaWRlRWZmZWN0XCI6MTR9IiwiOCI6IntcImlkXCI6OCxcIm5hbWVcIjpcImxpbmUtNFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEyMHB4KSB0cmFuc2xhdGVZKDMwcHgpIHNjYWxlWCgwKSBzY2FsZVkoMCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24tZ3JhcGgtbGluZS0zLTE1MHg3Mi5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxMyxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24tZ3JhcGgtbGluZS0zLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MjksXCJvZmZzZXRZXCI6MjA4LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcImJyXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MC43NzUsXCJzaG93RGVsYXlcIjozLjExMjUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLC0xMjAsMzAsbixuLG4sbixuLDAsMCxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6OCxcInNob3dFZmZlY3RcIjoxNSxcImhpZGVFZmZlY3RcIjoxNn0iLCI5Ijoie1wiaWRcIjo5LFwibmFtZVwiOlwiYnVsbGV0LTRcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSBzY2FsZVgoMCkgc2NhbGVZKDApIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWJ1bGxldC0xLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjE3LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1ncmFwaC1idWxsZXQtMS5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjE4LFwib2Zmc2V0WVwiOjI2MSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJiclwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjAuODc1LFwic2hvd0RlbGF5XCI6Mi43ODc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbixuLG4sbixuLDAsMCxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6OSxcInNob3dFZmZlY3RcIjoxNyxcImhpZGVFZmZlY3RcIjoxOH0iLCIxMSI6IntcImlkXCI6MTEsXCJuYW1lXCI6XCJsaW5lLTJcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKC03MHB4KSB0cmFuc2xhdGVZKC00MHB4KSB0cmFuc2xhdGVaKC0ycHgpc2NhbGVYKDApIHNjYWxlWSgwKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1ncmFwaC1saW5lLTIucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTEsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWxpbmUtMi5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjQ5NixcIm9mZnNldFlcIjo1NyxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJiclwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjAuNzc1LFwic2hvd0RlbGF5XCI6Mi40LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwtNzAsLTQwLC0yLG4sbixuLG4sMCwwLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjoxMSxcInNob3dFZmZlY3RcIjoyMSxcImhpZGVFZmZlY3RcIjoyMn0iLCIxMiI6IntcImlkXCI6MTIsXCJuYW1lXCI6XCJidWxsZXQtMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHNjYWxlWCgwKSBzY2FsZVkoMCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24tZ3JhcGgtYnVsbGV0LTEucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTQsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWJ1bGxldC0xLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NjAxLFwib2Zmc2V0WVwiOjEwNCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJiclwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjAuOTEyNSxcInNob3dEZWxheVwiOjEuNCxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbixuLG4sbixuLG4sbiwwLDAsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjEyLFwic2hvd0VmZmVjdFwiOjIzLFwiaGlkZUVmZmVjdFwiOjI0fSIsIjEzIjoie1wiaWRcIjoxMyxcIm5hbWVcIjpcImxpbmUtMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTcwcHgpIHRyYW5zbGF0ZVkoNTBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24tZ3JhcGgtbGluZS0xLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTAsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWxpbmUtMS5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOi0xMTgsXCJvZmZzZXRZXCI6LTM2LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcImJjXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MC42LFwic2hvd0RlbGF5XCI6Mi4wMTI1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwtNzAsNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6MTMsXCJzaG93RWZmZWN0XCI6MjUsXCJoaWRlRWZmZWN0XCI6MjZ9IiwiMTQiOiJ7XCJpZFwiOjE0LFwibmFtZVwiOlwiYnVsbGV0LTJcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSBzY2FsZVgoMCkgc2NhbGVZKDApIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWJ1bGxldC0xLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjE1LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1ncmFwaC1idWxsZXQtMS5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjQ5MSxcIm9mZnNldFlcIjo1NCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJiclwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjAuOTI1LFwic2hvd0RlbGF5XCI6MS44ODc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbixuLG4sbixu" . 
						"LDAsMCxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6MTQsXCJzaG93RWZmZWN0XCI6MjcsXCJoaWRlRWZmZWN0XCI6Mjh9IiwiMTUiOiJ7XCJpZFwiOjE1LFwibmFtZVwiOlwibGluZS0zXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgtODVweCkgdHJhbnNsYXRlWSg2MHB4KSBzY2FsZVgoMCkgc2NhbGVZKDApIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWxpbmUtMS0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjEyLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1ncmFwaC1saW5lLTEucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoyMDgsXCJvZmZzZXRZXCI6NjEsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYmNcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjowLjc3NSxcInNob3dEZWxheVwiOjIuNzc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwtODUsNjAsbixuLG4sbixuLDAsMCxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6MTUsXCJzaG93RWZmZWN0XCI6MjksXCJoaWRlRWZmZWN0XCI6MzB9IiwiMTYiOiJ7XCJpZFwiOjE2LFwibmFtZVwiOlwiYnVsbGV0LTNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSBzY2FsZVgoMCkgc2NhbGVZKDApIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWdyYXBoLWJ1bGxldC0xLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjE2LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1ncmFwaC1idWxsZXQtMS5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjI3OCxcIm9mZnNldFlcIjoyMDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYnJcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjowLjg4NzUsXCJzaG93RGVsYXlcIjoyLjMzNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sbixuLG4sbixuLG4sMCwwLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjoxNixcInNob3dFZmZlY3RcIjozMSxcImhpZGVFZmZlY3RcIjozMn0iLCIyMSI6IntcImlkXCI6MjEsXCJuYW1lXCI6XCJTZWNvbmRhcnkgVGV4dFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTUwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6OSxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIkRvbmVjIHNlZCBvZGlvIGR1aS4gRnVzY2UgZGFwaWJ1cywgdGVsbHVzIGFjIGN1cnN1cyBjb21tb2RvLCB0b3J0b3IgbWF1cmlzIGNvbmRpbWVudHVtIG5pYmgsIHV0IGZlcm1lbnR1bSBtYXNzYSBqdXN0by5cIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6MjAwLFwid2lkdGhcIjo1MDAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidHJcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1jbi00LTIxXCIsXCJzaG93RHVyYXRpb25cIjoxLjI4NzUsXCJzaG93RGVsYXlcIjowLjE1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDE1MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjIxLFwic2hvd0VmZmVjdFwiOjQxLFwiaGlkZUVmZmVjdFwiOjQyfSIsIjIyIjoie1wiaWRcIjoyMixcIm5hbWVcIjpcIkhlYWRpbmdcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjEwLFwidHlwZVwiOlwidGV4dFwiLFwiY29udGVudFwiOlwiTW9iaWxlLU9yaWVudGVkwqBcXG5QUEPCoENhbXBhaWduc1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MCxcIm9mZnNldFlcIjo3NSxcIndpZHRoXCI6NTAwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRyXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtY24tNC0yMlwiLFwic2hvd0R1cmF0aW9uXCI6MS4yODc1LFwic2hvd0RlbGF5XCI6MCxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjoyMixcInNob3dFZmZlY3RcIjo0MyxcImhpZGVFZmZlY3RcIjo0NH0iLCIyMyI6IntcImlkXCI6MjMsXCJuYW1lXCI6XCJpcGFkXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgcm90YXRlWSg5MGRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24taXBhZC0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjcsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWlwYWQucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjozOTAsXCJvZmZzZXRZXCI6NTUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYmxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjA1LFwic2hvd0RlbGF5XCI6MS42Mzc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbixuLG4sOTAsbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzbGlkZVwiOm51bGwsXCJzdHlsZU1vZGVsXCI6MjMsXCJzaG93RWZmZWN0XCI6NDUsXCJoaWRlRWZmZWN0XCI6NDZ9IiwiMzQiOiJ7XCJpZFwiOjM0LFwibmFtZVwiOlwiTGFiZWwgM1wiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxMyxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIlNNTVwiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6Mjg5LFwib2Zmc2V0WVwiOjU0LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjozLjE3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjM0LFwic2hvd0VmZmVjdFwiOjY3LFwiaGlkZUVmZmVjdFwiOjY4fSIsIjM4Ijoie1wiaWRcIjozOCxcIm5hbWVcIjpcImlwaG9uZVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZVkoOTBkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWlwaG9uZS05M3gxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MyxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24taXBob25lLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6OTksXCJvZmZzZXRZXCI6NTUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYmxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjA1LFwic2hvd0RlbGF5XCI6MS4xMjUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sbixuLG4sbiw5MCxuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjozOCxcInNob3dFZmZlY3RcIjo3NSxcImhpZGVFZmZlY3RcIjo3Nn0iLCIzOSI6IntcImlkXCI6MzksXCJuYW1lXCI6XCJpcGFkbWluaVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZVkoOTBkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWlwYWRtaW5pLTEy" . 
						"N3gxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NSxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvZmxhdC1pbGx1c3RyYXRpb24taXBhZG1pbmkucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoyMjgsXCJvZmZzZXRZXCI6NTUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwiYmxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjA1LFwic2hvd0RlbGF5XCI6MS4zNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sbixuLG4sbiw5MCxuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjozOSxcInNob3dFZmZlY3RcIjo3NyxcImhpZGVFZmZlY3RcIjo3OH0iLCI0MSI6IntcImlkXCI6NDEsXCJuYW1lXCI6XCJjb2ctMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZSgtMTA4MGRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtY29nLTEucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NSxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtY29nLTEucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo5NDAsXCJvZmZzZXRZXCI6MTA4LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MTMuNDM3NSxcInNob3dEZWxheVwiOjEuOTYyNSxcInNob3dFYXNlXCI6XCJsaW5lYXJcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbixuLG4sLTEwODAsbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjo0MSxcInNob3dFZmZlY3RcIjo4MSxcImhpZGVFZmZlY3RcIjo4Mn0iLCI0MiI6IntcImlkXCI6NDIsXCJuYW1lXCI6XCJjb2ctMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHJvdGF0ZSgtMTM5MGRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtY29nLTIucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NixcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtY29nLTIucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo2OTYsXCJvZmZzZXRZXCI6MjA1LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MTMuMDI1LFwic2hvd0RlbGF5XCI6Mi4wNjI1LFwic2hvd0Vhc2VcIjpcImxpbmVhclwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbiwtMTM5MCxuLG4sbixuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjQyLFwic2hvd0VmZmVjdFwiOjgzLFwiaGlkZUVmZmVjdFwiOjg0fSIsIjQzIjoie1wiaWRcIjo0MyxcIm5hbWVcIjpcImNvZy0zXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgcm90YXRlKC03MjBkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LWNvZy0zLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjcsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NsaWRlci1lbGVtZW50LWNvZy0zLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NzMwLFwib2Zmc2V0WVwiOjIyMSxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEyLjg4NzUsXCJzaG93RGVsYXlcIjoyLjE1LFwic2hvd0Vhc2VcIjpcImxpbmVhclwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbiwtNzIwLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NDMsXCJzaG93RWZmZWN0XCI6ODUsXCJoaWRlRWZmZWN0XCI6ODZ9IiwiNDciOiJ7XCJpZFwiOjQ3LFwibmFtZVwiOlwiTGFiZWwgM1wiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoyMCxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIkNST1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NjgzLFwib2Zmc2V0WVwiOjgzLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjozLjIzNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sNDAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NDcsXCJzaG93RWZmZWN0XCI6OTMsXCJoaWRlRWZmZWN0XCI6OTR9IiwiNDgiOiJ7XCJpZFwiOjQ4LFwibmFtZVwiOlwiTGFiZWwgMVwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxOCxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIlNFT1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NTYxLFwib2Zmc2V0WVwiOjg0LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjoxLjk4NzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sNDAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NDgsXCJzaG93RWZmZWN0XCI6OTUsXCJoaWRlRWZmZWN0XCI6OTZ9IiwiNDkiOiJ7XCJpZFwiOjQ5LFwibmFtZVwiOlwiTGFiZWwgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxOSxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIlNNTVwiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NjE5LFwib2Zmc2V0WVwiOjg0LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjoyLjU3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpcIjFcIixcInN0eWxlTW9kZWxcIjo0OSxcInNob3dFZmZlY3RcIjo5NyxcImhpZGVFZmZlY3RcIjo5OH0iLCI1MSI6IntcImlkXCI6NTEsXCJuYW1lXCI6XCJIYW5kXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSg1MDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjpmYWxzZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2ZsYXQtaWxsdXN0cmF0aW9uLWhhbmQtc21hbGxlci0xMTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjE1LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9mbGF0LWlsbHVzdHJhdGlvbi1oYW5kLXNtYWxsZXIucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoyODAsXCJvZmZzZXRZXCI6MTQ0LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjcuMDUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidChmYWxzZSxuLDUwMCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjUxLFwic2hvd0VmZmVjdFwiOjEwMSxcImhpZGVFZmZlY3RcIjoxMDJ9IiwiNTIiOiJ7XCJpZFwiOjUyLFwibmFtZVwiOlwiaXBhZCBjb250ZW50XCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSg1MHB4KSBzY2FsZVkoMCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNT" . 
						"AlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvaXBhZC1jb250ZW50LnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjgsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2lwYWQtY29udGVudC5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjQyNSxcIm9mZnNldFlcIjoxMDIsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLFwic2hvd0RlbGF5XCI6Mi4yLFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDUwLG4sbixuLG4sbixuLDAsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6MSxcImhpZGVEZWxheVwiOjEsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzbGlkZVwiOjQsXCJzdHlsZU1vZGVsXCI6NTIsXCJzaG93RWZmZWN0XCI6MTAzLFwiaGlkZUVmZmVjdFwiOjEwNH0iLCI1MyI6IntcImlkXCI6NTMsXCJuYW1lXCI6XCJpcGhvbmUgY29udGVudFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNTBweCkgc2NhbGVZKDApIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL2lwaG9uZS1jb250ZW50LnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjQsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2lwaG9uZS1jb250ZW50LnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MTIwLFwib2Zmc2V0WVwiOjE2OCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjoxLjczNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sNTAsbixuLG4sbixuLG4sMCxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjo1MyxcInNob3dFZmZlY3RcIjoxMDUsXCJoaWRlRWZmZWN0XCI6MTA2fSIsIjU0Ijoie1wiaWRcIjo1NCxcIm5hbWVcIjpcImlwYWRtaW5pIGNvbnRlbnRcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDUwcHgpIHNjYWxlWSgwKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9pcGFkbWluaS1jb250ZW50LnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjYsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL2lwYWRtaW5pLWNvbnRlbnQucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoyNDQsXCJvZmZzZXRZXCI6MTQ0LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjEuNzM3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw1MCxuLG4sbixuLG4sbiwwLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjU0LFwic2hvd0VmZmVjdFwiOjEwNyxcImhpZGVFZmZlY3RcIjoxMDh9IiwiNTUiOiJ7XCJpZFwiOjU1LFwibmFtZVwiOlwiVG91Y2hcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSBzY2FsZVgoMCkgc2NhbGVZKDApIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgc2NhbGVYKDApIHNjYWxlWSgwKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXRvdWNoLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjE0LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC10b3VjaC5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjI5MSxcIm9mZnNldFlcIjoxMjMsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjowLjQ2MjUsXCJzaG93RGVsYXlcIjo3Ljc3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbixuLG4sbixuLG4sbiwwLDAsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOnRydWUsXCJoaWRlRHVyYXRpb25cIjowLjMzNzUsXCJoaWRlRGVsYXlcIjowLjA1LFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLG4sbixuLG4sbixuLDAsMCxuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjQsXCJzdHlsZU1vZGVsXCI6NTUsXCJzaG93RWZmZWN0XCI6MTA5LFwiaGlkZUVmZmVjdFwiOjExMH0iLCI1NiI6IntcImlkXCI6NTYsXCJuYW1lXCI6XCJMYWJlbCAxXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSg0MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjExLFwidHlwZVwiOlwidGV4dFwiLFwiY29udGVudFwiOlwiUFBDXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoxMTIsXCJvZmZzZXRZXCI6NTQsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjEuOTg3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjU2LFwic2hvd0VmZmVjdFwiOjExMSxcImhpZGVFZmZlY3RcIjoxMTJ9IiwiNTciOiJ7XCJpZFwiOjU3LFwibmFtZVwiOlwiTGFiZWwgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxMixcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIlJlc3BvbnNpdmUgQWRzXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoxNjksXCJvZmZzZXRZXCI6NTQsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjIuNTYyNSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjU3LFwic2hvd0VmZmVjdFwiOjExMyxcImhpZGVFZmZlY3RcIjoxMTR9IiwiNTgiOiJ7XCJpZFwiOjU4LFwibmFtZVwiOlwid2ViIDNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigyNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTMtMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMy5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjM2NixcIm9mZnNldFlcIjotNjcsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo3LjczNzUsXCJzaG93RGVsYXlcIjozLjM4NzUsXCJzaG93RWFzZVwiOlwibGluZWFyXCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sLTEwMCxuLG4sbixuLDI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjozLjUxMjUsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImxpbmVhclwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xMDAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6NCxcInN0eWxlTW9kZWxcIjo1OCxcInNob3dFZmZlY3RcIjoxMTUsXCJoaWRlRWZmZWN0XCI6MTE2fSIsIjU5Ijoie1wiaWRcIjo1OSxcIm5hbWVcIjpcIndlYiAxXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgxMDBweCkgcm90YXRlWigyNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTItMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjowLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMi5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjQxLFwib2Zmc2V0WVwiOjE5Mi" . 
						"xcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjcuNzM3NSxcInNob3dEZWxheVwiOjMuMzYyNSxcInNob3dFYXNlXCI6XCJsaW5lYXJcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxMDAsbixuLG4sbiwyNSxuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6My41MTI1LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJsaW5lYXJcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTAwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjQsXCJzdHlsZU1vZGVsXCI6NTksXCJzaG93RWZmZWN0XCI6MTE3LFwiaGlkZUVmZmVjdFwiOjExOH0iLCI2MCI6IntcImlkXCI6NjAsXCJuYW1lXCI6XCJ3ZWIgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTEwMHB4KSByb3RhdGVaKC0zNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTItMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoyLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMi5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjg4OCxcIm9mZnNldFlcIjotMTMwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6Ny42NSxcInNob3dEZWxheVwiOjMuNDI1LFwic2hvd0Vhc2VcIjpcImxpbmVhclwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xMDAsbixuLG4sbiwtMzUsbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjMuNTEyNSxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwibGluZWFyXCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLG4sLTEwMCxuLG4sbixuLC0yNSxuLG4sbixuLG4sbixuKVwiLFwic2xpZGVcIjo0LFwic3R5bGVNb2RlbFwiOjYwLFwic2hvd0VmZmVjdFwiOjExOSxcImhpZGVFZmZlY3RcIjoxMjB9IiwiNjEiOiJ7XCJpZFwiOjYxLFwibmFtZVwiOlwid2ViIDNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xMDBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgtMTUwcHgpIHJvdGF0ZVooLTI1ZGVnKSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0xLTE1MHgxNTAucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MixcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTEucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo3MDksXCJvZmZzZXRZXCI6MTcxLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6OC45Mzc1LFwic2hvd0RlbGF5XCI6My4yNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sLTEwMCxuLG4sbixuLC0yNSxuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6Mi44LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTUwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJzbGlkZVwiOlwiMVwiLFwic3R5bGVNb2RlbFwiOjYxLFwic2hvd0VmZmVjdFwiOjEyMSxcImhpZGVFZmZlY3RcIjoxMjJ9IiwiNjIiOiJ7XCJpZFwiOjYyLFwibmFtZVwiOlwid2ViIDFcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDEwMHB4KSByb3RhdGVaKDI1ZGVnKSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTE1MHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMy0xNTB4MTUwLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjAsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NsaWRlci1lbGVtZW50LXdlYi0zLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6MzE5LFwib2Zmc2V0WVwiOjE2MCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bFwiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjguOTM3NSxcInNob3dEZWxheVwiOjMuMjc1LFwic2hvd0Vhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSxuLDEwMCxuLG4sbixuLDI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjgsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xNTAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NjIsXCJzaG93RWZmZWN0XCI6MTIzLFwiaGlkZUVmZmVjdFwiOjEyNH0iLCI2MyI6IntcImlkXCI6NjMsXCJuYW1lXCI6XCJ3ZWIgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTEwMHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xNTBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTItMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMi5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjU3NixcIm9mZnNldFlcIjotNjgsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo4LjkzNzUsXCJzaG93RGVsYXlcIjozLjI3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTAwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjgsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xNTAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6XCIxXCIsXCJzdHlsZU1vZGVsXCI6NjMsXCJzaG93RWZmZWN0XCI6MTI1LFwiaGlkZUVmZmVjdFwiOjEyNn0iLCI2NCI6IntcImlkXCI6NjQsXCJuYW1lXCI6XCJTZWNvbmRhcnkgVGV4dFwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoMTUwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MyxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIkRvbmVjIHNlZCBvZGlvIGR1aS4gRnVzY2UgZGFwaWJ1cywgdGVsbHVzIGFjIGN1cnN1cyBjb21tb2RvLCB0b3J0b3IgbWF1cmlzIGNvbmRpbWVudHVtIG5pYmgsIHV0IGZlcm1lbnR1bSBtYXNzYSBqdXN0by5cIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6NTAsXCJ3aWR0aFwiOjUwMCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJtY1wiLFwic3RheUhvdmVyXCI6dHJ1ZSxcImNsYXNzTmFtZVwiOlwibXNwLWNuLTQtNjRcIixcInNob3dEdXJhdGlvblwiOjEuMjg3NSxcInNob3dEZWxheVwiOjAuMixcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo2NCxcInNob3dFZmZlY3RcIjoxMjcsXCJoaWRlRWZmZWN0XCI6MTI4fSIsIjY1Ijoie1wiaWRcIjo2NSxcIm5hbWVcIjpcIkhlYWRpbmdcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDE1MHB4KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJcIixcImhpZGVPcmlnaW5cIjpcIlwiLFwiaGlkZUZhZGVcIjp0cnVlLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjQsXCJ0eXBlXCI6XCJ0ZXh0XCIsXCJjb250ZW50XCI6XCJTb2NpYWwgTWVkaWEgT3B0aW1pemF0aW9uXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjowLFwib2Zmc2V0WVwiOjEyMyxcIndpZHRoXCI6NjAwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRjXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtY24tNC02NVwiLFwic2hvd0R1cmF0aW9uXCI6MS4yODc1LFwic2hvd0RlbGF5XCI6MCxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxNTAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo2NSxcInNob3dFZmZlY3RcIjoxMjksXCJoaWRlRWZmZWN0XCI6MTMwfSIsIjc5Ijoie1wiaWRcIjo3OSxcIm5hbWVcIjpcIkxhYmVsIDNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDQwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NyxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIkNST1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6NjAsXCJvZmZzZXRZXCI6NjAsXCJyZXNp" . 
						"emVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGNcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwic2hvd0R1cmF0aW9uXCI6MS4wMjUsXCJzaG93RGVsYXlcIjozLjIzNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sNDAsbixuLG4sbixuLG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo3OSxcInNob3dFZmZlY3RcIjoxNTcsXCJoaWRlRWZmZWN0XCI6MTU4fSIsIjgwIjoie1wiaWRcIjo4MCxcIm5hbWVcIjpcIkxhYmVsIDFcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDQwcHgpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6NSxcInR5cGVcIjpcInRleHRcIixcImNvbnRlbnRcIjpcIlNFT1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6LTYwLjUsXCJvZmZzZXRZXCI6NjAsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGNcIixcInN0YXlIb3ZlclwiOnRydWUsXCJjbGFzc05hbWVcIjpcIm1zcC1wcmVzZXQtN1wiLFwic2hvd0R1cmF0aW9uXCI6MSxcInNob3dEZWxheVwiOjEuOTg3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjgwLFwic2hvd0VmZmVjdFwiOjE1OSxcImhpZGVFZmZlY3RcIjoxNjB9IiwiODEiOiJ7XCJpZFwiOjgxLFwibmFtZVwiOlwiTGFiZWwgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoNDBweCkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwiXCIsXCJoaWRlT3JpZ2luXCI6XCJcIixcImhpZGVGYWRlXCI6dHJ1ZSxcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo2LFwidHlwZVwiOlwidGV4dFwiLFwiY29udGVudFwiOlwiU01NXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjowLFwib2Zmc2V0WVwiOjYwLFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRjXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwiY2xhc3NOYW1lXCI6XCJtc3AtcHJlc2V0LTdcIixcInNob3dEdXJhdGlvblwiOjEsXCJzaG93RGVsYXlcIjoyLjU3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiw0MCxuLG4sbixuLG4sbixuLG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjEsXCJoaWRlRGVsYXlcIjoxLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwic2xpZGVcIjpudWxsLFwic3R5bGVNb2RlbFwiOjgxLFwic2hvd0VmZmVjdFwiOjE2MSxcImhpZGVFZmZlY3RcIjoxNjJ9IiwiODIiOiJ7XCJpZFwiOjgyLFwibmFtZVwiOlwid2ViIDNcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKDEwMHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xNTBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTEtMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoyLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMS5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjAsXCJvZmZzZXRZXCI6MjE3LFwicmVzaXplXCI6dHJ1ZSxcImZpeGVkXCI6ZmFsc2UsXCJ3aWR0aGxpbWl0XCI6XCIwXCIsXCJvcmlnaW5cIjpcInRsXCIsXCJzdGF5SG92ZXJcIjp0cnVlLFwic2hvd0R1cmF0aW9uXCI6OC45Mzc1LFwic2hvd0RlbGF5XCI6My4yNzUsXCJzaG93RWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLG4sMTAwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjgsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xNTAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo4MixcInNob3dFZmZlY3RcIjoxNjMsXCJoaWRlRWZmZWN0XCI6MTY0fSIsIjgzIjoie1wiaWRcIjo4MyxcIm5hbWVcIjpcIndlYiAxXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWSgxMDBweCkgcm90YXRlWigyNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xNTBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTMtMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjowLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMy5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjc4NixcIm9mZnNldFlcIjoxODUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo4LjkzNzUsXCJzaG93RGVsYXlcIjozLjI3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwxMDAsbixuLG4sbiwyNSxuLG4sbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6Mi44LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTUwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJzbGlkZVwiOm51bGwsXCJzdHlsZU1vZGVsXCI6ODMsXCJzaG93RWZmZWN0XCI6MTY1LFwiaGlkZUVmZmVjdFwiOjE2Nn0iLCI4NCI6IntcImlkXCI6ODQsXCJuYW1lXCI6XCJ3ZWIgMlwiLFwiaXNMb2NrZWRcIjpmYWxzZSxcImlzSGlkZWRcIjpmYWxzZSxcImlzU29sb2VkXCI6ZmFsc2UsXCJzaG93VHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVkoLTEwMHB4KSByb3RhdGVaKC0yNWRlZykgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVZKC0xNTBweCkgcm90YXRlWigtMjVkZWcpIFwiLFwiaGlkZU9yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcImhpZGVGYWRlXCI6dHJ1ZSxcImltZ1RodW1iXCI6XCIvc2xpZGVyLWVsZW1lbnQtd2ViLTItMTUweDE1MC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjoxLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zbGlkZXItZWxlbWVudC13ZWItMi5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjQ0My41LFwib2Zmc2V0WVwiOi0xNTUsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjo4LjkzNzUsXCJzaG93RGVsYXlcIjozLjI3NSxcInNob3dFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsbiwtMTAwLG4sbixuLG4sLTI1LG4sbixuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjgsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSxuLC0xNTAsbixuLG4sbiwtMjUsbixuLG4sbixuLG4sbilcIixcInNsaWRlXCI6bnVsbCxcInN0eWxlTW9kZWxcIjo4NCxcInNob3dFZmZlY3RcIjoxNjcsXCJoaWRlRWZmZWN0XCI6MTY4fSIsIjg1Ijoie1wiaWRcIjo4NSxcIm5hbWVcIjpcInNvY2lhbCBpY29uIOKAkyBmYWNlYm9rXCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgtMTMwcHgpIHNjYWxlWCgwLjUpIHNjYWxlWSgwLjUpIFwiLFwic2hvd09yaWdpblwiOlwiNTAlIDUwJSAwcHhcIixcInNob3dGYWRlXCI6dHJ1ZSxcImhpZGVUcmFuc2Zvcm1cIjpcIlwiLFwiaGlkZU9yaWdpblwiOlwiXCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NvY2lhbC1pY29uLWZhY2Vib29rLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjEyLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zb2NpYWwtaWNvbi1mYWNlYm9vay5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjk4NixcIm9mZnNldFlcIjoxMjcsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjQ1LFwic2hvd0RlbGF5XCI6Mi4zMzc1LFwic2hvd0Vhc2VcIjpcImVhc2VJblF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLC0xMzAsbixuLG4sbixuLG4sMC41LDAuNSxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoxLFwiaGlkZURlbGF5XCI6MSxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcInNsaWRlXCI6NSxcInN0eWxlTW9kZWxcIjo4NSxcInNob3dFZmZlY3RcIjoxNjksXCJoaWRlRWZmZWN0XCI6MTcwfSIsIjg2Ijoie1wiaWRcIjo4NixcIm5hbWVcIjpcInNvY2lhbCBpY29uIOKAkyBwaW50ZXJlc3RcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKC0xMzBweCkgdHJhbnNsYXRlWSgtODBweCkgc2NhbGVYKDAuNSkgc2NhbGVZKDAuNSkgXCIsXCJzaG93T3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwic2hvd0ZhZGVcIjp0cnVlLFwiaGlkZVRyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKC0xMzBweCkgdHJhbnNsYXRlWSgtODBweCkgc2NhbGVYKDEuNSkgc2NhbGVZKDEuNSkgXCIsXCJoaWRlT3JpZ2luXCI6XCI1MCUgNTAlIDBweFwiLFwiaGlkZUZhZGVcIjp0cnVlLFwiaW1nVGh1bWJcIjpcIi9zb2NpYWwtaWNvbnMtZ29vZ2xlLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjEzLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zb2NpYWwtaWNvbnMtZ29vZ2xlLnBuZ1wiLFwidmlkZW9cIjpcImh0dHA6Ly9wbGF5ZXIudmltZW8uY29tL3ZpZGVvLzExNzIxMjQyXCIsXCJhbGlnblwiOlwidG9wXCIsXCJvZmZzZXRYXCI6ODg2LFwib2Zmc2V0WVwiOjIwOCxcInJlc2l6ZVwiOnRydWUsXCJmaXhlZFwiOmZhbHNlLFwid2lkdGhsaW1pdFwiOlwiMFwiLFwib3JpZ2luXCI6XCJ0bF" . 
						"wiLFwic3RheUhvdmVyXCI6dHJ1ZSxcInNob3dEdXJhdGlvblwiOjEuNDUsXCJzaG93RGVsYXlcIjoyLjY2MjUsXCJzaG93RWFzZVwiOlwiZWFzZUluUXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsLTEzMCwtODAsbixuLG4sbixuLDAuNSwwLjUsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6Mi4wNjI1LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsLTEzMCwtODAsbixuLG4sbixuLDEuNSwxLjUsbixuLG4sbixuKVwiLFwic2xpZGVcIjo1LFwic3R5bGVNb2RlbFwiOjg2LFwic2hvd0VmZmVjdFwiOjE3MSxcImhpZGVFZmZlY3RcIjoxNzJ9IiwiODciOiJ7XCJpZFwiOjg3LFwibmFtZVwiOlwic29jaWFsIGljb24g4oCTIHR3aXR0ZXJcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKDEzMHB4KSBzY2FsZVgoMC41KSBzY2FsZVkoMC41KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTE3MHB4KSBzY2FsZVgoMS41KSBzY2FsZVkoMS41KSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NvY2lhbC1pY29uLXR3aXR0ZXIucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6OSxcInR5cGVcIjpcImltYWdlXCIsXCJjb250ZW50XCI6XCJMb3JlbSBJcHN1bVwiLFwiaW1nXCI6XCIvc29jaWFsLWljb24tdHdpdHRlci5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjExOCxcIm9mZnNldFlcIjoxMTksXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjQ1LFwic2hvd0RlbGF5XCI6MS4zNSxcInNob3dFYXNlXCI6XCJlYXNlSW5RdWludFwiLFwic2hvd0VmZkZ1bmNcIjpcInQodHJ1ZSwxMzAsbixuLG4sbixuLG4sMC41LDAuNSxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjA3NSxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLC0xNzAsbixuLG4sbixuLG4sMS41LDEuNSxuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjUsXCJzdHlsZU1vZGVsXCI6ODcsXCJzaG93RWZmZWN0XCI6MTczLFwiaGlkZUVmZmVjdFwiOjE3NH0iLCI4OCI6IntcImlkXCI6ODgsXCJuYW1lXCI6XCJzb2NpYWwgaWNvbiDigJMgbGlua2VkaW5cIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKDEzMHB4KSB0cmFuc2xhdGVZKC04MHB4KSBzY2FsZVgoMC41KSBzY2FsZVkoMC41KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEzMHB4KSB0cmFuc2xhdGVZKC04MHB4KSBzY2FsZVgoMS41KSBzY2FsZVkoMS41KSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NvY2lhbC1pY29uLWxpbmtlZGluLnBuZ1wiLFwic3RhZ2VPZmZzZXRYXCI6MCxcInN0YWdlT2Zmc2V0WVwiOjAsXCJvcmRlclwiOjEwLFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zb2NpYWwtaWNvbi1saW5rZWRpbi5wbmdcIixcInZpZGVvXCI6XCJodHRwOi8vcGxheWVyLnZpbWVvLmNvbS92aWRlby8xMTcyMTI0MlwiLFwiYWxpZ25cIjpcInRvcFwiLFwib2Zmc2V0WFwiOjIxNyxcIm9mZnNldFlcIjoyMTcsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjQ1LFwic2hvd0RlbGF5XCI6MS42NzUsXCJzaG93RWFzZVwiOlwiZWFzZUluUXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsMTMwLC04MCxuLG4sbixuLG4sMC41LDAuNSxuLG4sbixuLG4pXCIsXCJ1c2VIaWRlXCI6ZmFsc2UsXCJoaWRlRHVyYXRpb25cIjoyLjA2MjUsXCJoaWRlRGVsYXlcIjowLFwiaGlkZUVhc2VcIjpcImVhc2VPdXRRdWludFwiLFwiaGlkZUVmZkZ1bmNcIjpcInQodHJ1ZSwtMTMwLC04MCxuLG4sbixuLG4sMS41LDEuNSxuLG4sbixuLG4pXCIsXCJzbGlkZVwiOjUsXCJzdHlsZU1vZGVsXCI6ODgsXCJzaG93RWZmZWN0XCI6MTc1LFwiaGlkZUVmZmVjdFwiOjE3Nn0iLCI4OSI6IntcImlkXCI6ODksXCJuYW1lXCI6XCJzb2NpYWwgaWNvbiDigJMgcGludGVyZXN0XCIsXCJpc0xvY2tlZFwiOmZhbHNlLFwiaXNIaWRlZFwiOmZhbHNlLFwiaXNTb2xvZWRcIjpmYWxzZSxcInNob3dUcmFuc2Zvcm1cIjpcInBlcnNwZWN0aXZlKDIwMDBweCkgdHJhbnNsYXRlWCgxMzBweCkgdHJhbnNsYXRlWSg4MHB4KSBzY2FsZVgoMC41KSBzY2FsZVkoMC41KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEzMHB4KSB0cmFuc2xhdGVZKC04MHB4KSBzY2FsZVgoMS41KSBzY2FsZVkoMS41KSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NvY2lhbC1pY29uLXBpbnRlcmVzdC5wbmdcIixcInN0YWdlT2Zmc2V0WFwiOjAsXCJzdGFnZU9mZnNldFlcIjowLFwib3JkZXJcIjo4LFwidHlwZVwiOlwiaW1hZ2VcIixcImNvbnRlbnRcIjpcIkxvcmVtIElwc3VtXCIsXCJpbWdcIjpcIi9zb2NpYWwtaWNvbi1waW50ZXJlc3QucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjoyMTUsXCJvZmZzZXRZXCI6MzIsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjQ1LFwic2hvd0RlbGF5XCI6MS4wNzUsXCJzaG93RWFzZVwiOlwiZWFzZUluUXVpbnRcIixcInNob3dFZmZGdW5jXCI6XCJ0KHRydWUsMTMwLDgwLG4sbixuLG4sbiwwLjUsMC41LG4sbixuLG4sbilcIixcInVzZUhpZGVcIjpmYWxzZSxcImhpZGVEdXJhdGlvblwiOjIuMDYyNSxcImhpZGVEZWxheVwiOjAsXCJoaWRlRWFzZVwiOlwiZWFzZU91dFF1aW50XCIsXCJoaWRlRWZmRnVuY1wiOlwidCh0cnVlLC0xMzAsLTgwLG4sbixuLG4sbiwxLjUsMS41LG4sbixuLG4sbilcIixcInNsaWRlXCI6NSxcInN0eWxlTW9kZWxcIjo4OSxcInNob3dFZmZlY3RcIjoxNzcsXCJoaWRlRWZmZWN0XCI6MTc4fSIsIjkwIjoie1wiaWRcIjo5MCxcIm5hbWVcIjpcInNvY2lhbCBpY29uIOKAkyBwaW50ZXJlc3RcIixcImlzTG9ja2VkXCI6ZmFsc2UsXCJpc0hpZGVkXCI6ZmFsc2UsXCJpc1NvbG9lZFwiOmZhbHNlLFwic2hvd1RyYW5zZm9ybVwiOlwicGVyc3BlY3RpdmUoMjAwMHB4KSB0cmFuc2xhdGVYKC0xMzBweCkgdHJhbnNsYXRlWSg4MHB4KSBzY2FsZVgoMC41KSBzY2FsZVkoMC41KSBcIixcInNob3dPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJzaG93RmFkZVwiOnRydWUsXCJoaWRlVHJhbnNmb3JtXCI6XCJwZXJzcGVjdGl2ZSgyMDAwcHgpIHRyYW5zbGF0ZVgoLTEzMHB4KSB0cmFuc2xhdGVZKC04MHB4KSBzY2FsZVgoMS41KSBzY2FsZVkoMS41KSBcIixcImhpZGVPcmlnaW5cIjpcIjUwJSA1MCUgMHB4XCIsXCJoaWRlRmFkZVwiOnRydWUsXCJpbWdUaHVtYlwiOlwiL3NvY2lhbC1pY29uLXlvdXR1YmUucG5nXCIsXCJzdGFnZU9mZnNldFhcIjowLFwic3RhZ2VPZmZzZXRZXCI6MCxcIm9yZGVyXCI6MTEsXCJ0eXBlXCI6XCJpbWFnZVwiLFwiY29udGVudFwiOlwiTG9yZW0gSXBzdW1cIixcImltZ1wiOlwiL3NvY2lhbC1pY29uLXlvdXR1YmUucG5nXCIsXCJ2aWRlb1wiOlwiaHR0cDovL3BsYXllci52aW1lby5jb20vdmlkZW8vMTE3MjEyNDJcIixcImFsaWduXCI6XCJ0b3BcIixcIm9mZnNldFhcIjo5MTMsXCJvZmZzZXRZXCI6MzYsXCJyZXNpemVcIjp0cnVlLFwiZml4ZWRcIjpmYWxzZSxcIndpZHRobGltaXRcIjpcIjBcIixcIm9yaWdpblwiOlwidGxcIixcInN0YXlIb3ZlclwiOnRydWUsXCJzaG93RHVyYXRpb25cIjoxLjQ1LFwic2hvd0RlbGF5XCI6MS45ODc1LFwic2hvd0Vhc2VcIjpcImVhc2VJblF1aW50XCIsXCJzaG93RWZmRnVuY1wiOlwidCh0cnVlLC0xMzAsODAsbixuLG4sbixuLDAuNSwwLjUsbixuLG4sbixuKVwiLFwidXNlSGlkZVwiOmZhbHNlLFwiaGlkZUR1cmF0aW9uXCI6Mi4wNjI1LFwiaGlkZURlbGF5XCI6MCxcImhpZGVFYXNlXCI6XCJlYXNlT3V0UXVpbnRcIixcImhpZGVFZmZGdW5jXCI6XCJ0KHRydWUsLTEzMCwtODAsbixuLG4sbixuLDEuNSwxLjUsbixuLG4sbixuKVwiLFwic2xpZGVcIjo1LFwic3R5bGVNb2RlbFwiOjkwLFwic2hvd0VmZmVjdFwiOjE3OSxcImhpZGVFZmZlY3RcIjoxODB9In0sIk1TUGFuZWwuQ29udHJvbCI6eyIxIjoie1wiaWRcIjpcIjFcIixcImxhYmVsXCI6XCJMaW5lIFRpbWVyXCIsXCJuYW1lXCI6XCJ0aW1lYmFyXCIsXCJhdXRvSGlkZVwiOmZhbHNlLFwib3ZlclZpZGVvXCI6dHJ1ZSxcImNvbG9yXCI6XCJyZ2JhKDI1NSwgMjU1LCAyNTUsIDAuNSlcIixcIndpZHRoXCI6MyxcImFsaWduXCI6XCJib3R0b21cIixcImluc2V0XCI6dHJ1ZX0iLCIzIjoie1wiaWRcIjozLFwibGFiZWxcIjpcIkJ1bGxldHNcIixcIm5hbWVcIjpcImJ1bGxldHNcIixcImF1dG9IaWRlXCI6ZmFsc2UsXCJvdmVyVmlkZW9cIjp0cnVlLFwibWFyZ2luXCI6MjAsXCJkaXJcIjpcImhcIixcImFsaWduXCI6XCJib3R0b21cIixcImluc2V0XCI6dHJ1ZX0ifX0=", // get import file content

						'custom_styles' => ".msp-cn-4-3 { font-family:'Roboto';font-weight:300;font-size:20px;line-height:30px;color:#ffffff; } " .
".msp-cn-4-4 { font-family:'Roboto';font-weight:300;font-size:48px;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; } " .
".msp-cn-4-21 { font-family:'Roboto';font-weight:300;font-size:20px;line-height:30px;color:#ffffff; } " .
".msp-cn-4-22 { font-family:'Roboto';font-weight:300;font-size:48px;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; } " .
".msp-cn-4-64 { font-family:'Roboto';font-weight:300;font-size:20px;text-align:center;line-height:30px;color:#ffffff; } " .
".msp-cn-4-65 { font-family:'Roboto';font-weight:300;font-size:48px;text-align:center;letter-spacing:-1px;line-height:48px;color:#ffffff;font-weight:200; } ",
						'custom_fonts' => 'Roboto:300,regular',
						'status' => 'published',
					)
				);

				// Force Custom CSS regeneration
				if ( defined('MSWP_AVERTA_VERSION') ) {
					include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/msp-admin-functions.php');
					msp_update_slider_custom_css_and_fonts( 1 );  //$new_slider_id
				}
			}

			// Use a static front page
			$home_page = get_page_by_title( LBMN_HOME_TITLE );
			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			// Set the blog page (not needed)
			// $blog = get_page_by_title( LBMN_BLOG_TITLE );
			// update_option( 'page_for_posts', $blog->ID );

			lbmn_debug_console( 'lbmn_customized_css_cache_reset' );
			// Regenerate Custom CSS
			lbmn_customized_css_cache_reset(false); // refresh custom css without printig css (false)

			if(is_plugin_active('mega_main_menu/mega_main_menu.php')){
				// call the function that normally starts only in Theme Customizer
				lbmn_mainmegamenu_customizer_integration();
			}

			lbmn_debug_console( 'Search & Replace image URLS' );
			// Search & Replace image URLS
			lbmn_lcsearchreplace();

		} // if $_GET['importcontent']

	} // is isset($_GET['importcontent'])
}

/**
* ----------------------------------------------------------------------
* Start a theme tour
*/

if ( is_admin() && isset($_GET['theme_tour'] ) && $pagenow == "themes.php" ) {
	// Register the pointer styles and scripts
	add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

	// Add pointer javascript
	add_action( 'admin_print_footer_scripts', 'add_pointer_scripts' );

	// enqueue javascripts and styles
	function enqueue_scripts()
	{
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	// Add the pointer javascript
	function add_pointer_scripts()
	{
		$pointer_content = '<h3>We use a theme customizer</h3>';
		$pointer_content .= '<p>All theme options available for customization in theme customizer.</p>';
	?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('#menu-appearance a[href="customize.php"]').pointer({
				// pointer_id: 'customizer_menu_link',
				content: '<?php echo $pointer_content; ?>',
				position: {
					 edge: 'left', //top, bottom, left, right
					 align: 'middle' //top, bottom, left, right, middle
				 },
				buttons: function( event, t ) {

					var $buttonClose = jQuery('<a class="button-secondary" style="margin-right:10px;" href="#">End Tour</a>');
					$buttonClose.bind( 'click.pointer', function() {

						t.element.pointer('close');
					});

					var buttons = $('<div class="tiptour-buttons">');
					buttons.append($buttonClose);
					buttons.append('<a class="button-primary" style="margin-right:10px;" href="<?php echo admin_url('customize.php#first-time-visit'); ?>">Go to Theme Customizer</a>');
					return buttons;
				},

				close: function() {
					// Once the close button is hit
					$.post( ajaxurl, {
						pointer: 'customizer_menu_link',
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');

			$(".lumberman-message.quick-setup .step-tour").addClass("step-completed");
		});
		//]]>
		</script>
	<?php
	}
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}

/* Hide quick tour message block */
if ( is_admin() && isset($_GET['hide_quicksetup'] ) && $pagenow == "themes.php" ) {
	update_option( LBMN_THEME_NAME . '_hide_quicksetup', true ); // set option to not show quick setup block anymore
}


/**
 * ----------------------------------------------------------------------
 * Page redirects for LiveComposer Tutorials
 */
add_action( 'template_redirect', 'lbmn_lc_tutorial_redirect' );
function lbmn_lc_tutorial_redirect() {
	if(
		is_user_logged_in() && !isset($_GET['dslc']) &&
		( is_page( 'chapter-1' ) || is_page( 'chapter-2' ) || is_page( 'chapter-3' )  || is_page( 'chapter-4' ) )
	) {
		$arr_params = array( 'dslc' => 'active', 'dslc_tut' => 'start' );
		wp_redirect( add_query_arg($arr_params, get_permalink()) );
		exit();
	}
}

/**
 * ----------------------------------------------------------------------
 * In some situations on theme switch WordPress forget menus
 * that assigned to menu locations
 *
 * The next code block remember [menu id > location] pairs before theme
 * switch and redeclare it when users activate our theme again
 */

add_action( 'current_screen', 'lbmn_save_menu_locations' );
function lbmn_save_menu_locations($current_screen)
{
	// If Apperance > Menu screen visited
	if ( $current_screen->id == 'nav-menus' ) {
		// Remember menus assigned to our locations
		$locations = get_nav_menu_locations();
		update_option( LBMN_THEME_NAME . '_menuid_topbar', $locations['topbar'] );
		update_option( LBMN_THEME_NAME . '_menuid_header', $locations['header-menu'] );
	}
}

add_action('after_switch_theme', 'lbmn_redeclare_menu_locations' );
function lbmn_redeclare_menu_locations () {

	// check if 'header' locaiton has no menu assigned
	$menuid_header = get_option( LBMN_THEME_NAME . '_menuid_header' );
	if( !has_nav_menu('header-menu') && isset($menuid_header) ) {
		// Attach saved before menu id to 'topbar' location
		$locations = get_nav_menu_locations();
		$locations['header-menu'] = $menuid_header;
		set_theme_mod('nav_menu_locations', $locations);
	}

	// check if 'topbar' locaiton has no menu assigned
	$menuid_topbar = get_option( LBMN_THEME_NAME . '_menuid_topbar' );
	if( !has_nav_menu('topbar') && isset($menuid_topbar) ) {
		// Attach saved before menu id to 'topbar' location
		$locations = get_nav_menu_locations();
		$locations['topbar'] = $menuid_topbar;
		set_theme_mod('nav_menu_locations', $locations);
	}
}

// Replace dynamic values of widgets import (called from widgets-importer.php)
function lbmn_strreplace_on_widgetsimport($data) {
	if ($data) {
		global $widget_strings_replace;
		foreach ($widget_strings_replace as $search => $replace) {
			$data = str_replace($search, $replace, $data);
		}
	}
	return $data;
}

/**
 * ----------------------------------------------------------------------
 * LiveComposer image src attribute search and replace
 * Problem: LiveComposer image module has image urls hardcoded, that makes
 * impossilbe to demo import content with images pointing to the new server
 * instead of the demo one.
 * Solution: after images imported go through pages and scan their LiveComposer
 * code for image urls to replace
 */

function lbmn_lcsearchreplace() {
	ini_set('max_execution_time', 360);

	global $wpdb;

	$wpdb_prefix = $wpdb->prefix;
	$replace_before = '//export.seowptheme.com';
	$replace_after = str_replace( array('http://', 'https://'), '//', get_bloginfo( 'url' )); // current website url

	if ( !$replace_before || !$replace_after ) {
		die();
	}

	$results = $wpdb->get_results( "SELECT * FROM `" . $wpdb->postmeta . "` WHERE `meta_key` = 'dslc_code' ORDER BY `meta_key`", OBJECT );
	foreach ( $results as $post ) {
		// echo $post->post_id;
		// echo "<hr />";
		// echo "Page: <strong>";
		// echo get_the_title( $post->post_id  );
		// echo "</strong><br />";

		$raw_lc_code = $post->meta_value;

		// First search replace unserialized urls
		$count = 0;
		$raw_lc_code = str_replace($replace_before, $replace_after, $raw_lc_code, $count);
		// if ( $count ) {
		// 	echo 'Performed '. $count .' replaces of unserialized urls.<br />';
		// }

		$scount = 0;

		if ( $raw_lc_code ) {

			$raw_lc_code_processed = '';
			$module_open_pos = strpos($raw_lc_code, '[dslc_module]');

			while ( $module_open_pos != false ) {
				// var_dump($module_open_pos);
				// echo 'while ( $module_open_pos !== false )<br />';
				$module_open_pos = intval($module_open_pos) + strlen('[dslc_module]');
				$scount = $scount + 1;

				$raw_lc_code_head = substr($raw_lc_code, 0, $module_open_pos );
				$module_close_pos = strpos($raw_lc_code, '[/dslc_module]', $module_open_pos);
				$raw_lc_code_encodedbody = substr($raw_lc_code, $module_open_pos, $module_close_pos - $module_open_pos );
				$raw_lc_code_tail = substr($raw_lc_code, $module_close_pos);

				// Perform search/replace of all image URLs found in encoded body code
				$raw_lc_code_encodedbody = lbmn_lcsearchreplace_image( $raw_lc_code_encodedbody, $replace_before, $replace_after);

				$raw_lc_code_processed .= $raw_lc_code_head . $raw_lc_code_encodedbody;
				$raw_lc_code = $raw_lc_code_tail;
				$module_open_pos = strpos($raw_lc_code, '[dslc_module]');
				if (!$module_close_pos) {
					$module_open_pos = false;
				}
			}

			if ( $raw_lc_code_processed && $raw_lc_code_tail ) {
				$raw_lc_code_processed .= $raw_lc_code_tail;
			}

			if ( $raw_lc_code_processed ) {

				// put data back to database
				$update_result = $wpdb->update(
					$wpdb->postmeta, // table
					array(
							'meta_value' => $raw_lc_code_processed, // data to update
					),
					array( 'meta_id' => $post->meta_id ) //where
				);

				// if ( !$update_result ) {
				// 	echo "Error updating 'postmeta' database";
				// }
			}
		}
	}

	// die();
}

function lbmn_lcsearchreplace_image($module_code_serialized, $replace_before, $replace_after) {

	// Decode and unpack
	$decoded_temp = maybe_unserialize( base64_decode($module_code_serialized) );


	if ( isset($decoded_temp['image']) ) {
		// echo "<br />";
		// echo "Old image src URL replaced: <br />" . $decoded_temp['image'] . "<br />";
		$decoded_temp['image'] = str_replace($replace_before, $replace_after, $decoded_temp['image']);
		// echo $decoded_temp['image'] . "<br />";

		// Encode and pack it back
		$decoded_temp = base64_encode( serialize($decoded_temp) );


		return $decoded_temp;
	} else {
		return $module_code_serialized;
	}
}