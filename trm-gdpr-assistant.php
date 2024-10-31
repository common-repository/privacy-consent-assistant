<?php
	/**
		* Plugin Name: Privacy & Consent Assistant
		* Description: This plugin provides an interface to assist with consent and privacy compliance. It is not guaranteed to satisfy all clauses in the GDPR or any other legal requirements.
		* Version:	   1.2.0.2
		* Author:	   Third River Marketing
		* Text Domain: trm-gdpr
		* License:	   GPL-3.0+
		* License URI: http://www.gnu.org/licenses/gpl-3.0.html

		*	Copyright Third River Marketing, LLC, Alex Demchak

		*	This program is free software; you can redistribute it and/or modify
		*	it under the terms of the GNU General Public License as published by
		*	the Free Software Foundation; either version 3 of the License, or
		*	(at your option) any later version.
	
		*	This program is distributed in the hope that it will be useful,
		*	but WITHOUT ANY WARRANTY; without even the implied warranty of
		*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
		*	GNU General Public License for more details.

		*	You should have received a copy of the GNU General Public License
		*	along with this program.  If not, see http://www.gnu.org/licenses.
	*/

	if ( ! defined( 'ABSPATH' ) ) exit;

	class TRM_GDPR {
		private static $instance;
		public static function get_instance() {
			if( null == self::$instance ) self::$instance = new TRM_GDPR();
			return self::$instance;
		}

		// Commas break cookies in iOS/Safari. Use Hyphens instead
		public static $versions = '1.2-1.2-1.2'; // Privacy, Terms, Cookie
		public static $revised  = 'May_25th_2018-June_3rd_2016-May_25th_2018'; // Privacy, Terms, Cookie

		public static $policies = ['Privacy Policy', 'Terms of Service', 'Cookie Policy'];
		public static $notices  = ['Form Consent', 'Consent Bar'];

		public static $option_fields = [
			'trm_gdpr_company_name'                   => 'string',
			'trm_gdpr_company_phone'                  => 'string',
			'trm_gdpr_company_email'                  => 'string',
			'trm_gdpr_company_address'                => 'string',
			'trm_gdpr_governing_state'                => 'string',
			'trm_gdpr_governing_country'              => 'string',
			'trm_gdpr_overwrite_privacy_policy'       => 'string',
			'trm_gdpr_overwrite_terms_of_service'     => 'string',
			'trm_gdpr_overwrite_cookie_policy'        => 'string',
			'trm_gdpr_custom_privacy_policy'          => 'string',
			'trm_gdpr_custom_terms_of_service'        => 'string',
			'trm_gdpr_custom_cookie_policy'           => 'string',
			'trm_gdpr_disable_form_consent'           => 'boolean',
			'trm_gdpr_disable_consent_bar'            => 'boolean',
			'trm_gdpr_disable_subfooter'              => 'boolean',
			'trm_gdpr_disable_hide_existing_links'    => 'boolean',
			'trm_gdpr_overwrite_notice_form_consent'  => 'textarea',
			'trm_gdpr_overwrite_notice_consent_bar'   => 'textarea',
			'trm_gdpr_close_consent_functions'        => 'textarea',
			'trm_gdpr_dynamic_style'                  => 'textarea_complex'
		];

		public function icon( $icon = '' ){
				 if( $icon == 'edit' )   return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>';
			else if( $icon == 'close' )  return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
			else if( $icon == 'delete' ) return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>';
			else if( $icon == 'cookie' ) return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 12.21 3 7 7 0 0 0 21 12.79z"></path><line x1="12" y1="16" x2="12" y2="16"></line><line x1="9" y1="12" x2="9" y2="12"></line><line x1="6" y1="10" x2="6" y2="10"></line><line y2="16" x2="17" y1="16" x1="17"></line></svg>';
			else if( $icon == 'search' ) return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>';
			else 						 return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12" y2="17"></line></svg>';
		}

		public function optionize( $input ){
			return str_replace( '-', '_', sanitize_title( $input ) );
		}

		public function stringify( $input ){
			return ucwords( str_replace( ['-', '_'], ' ', $input ) );
		}

		public function wrap_element( $input, $id = '', $class = '', $return = true, $initial_class = '' ){
			$id = ( $id ) ? "id='$id'" : '';

			$html = "<div $id class='trm-gdpr-ui $initial_class'>";
				$html .= ( $class ) ? "<div class='$class'>" : '';
					$html .= $input;
				$html .= ( $class ) ? '</div>' : '';
			$html .= '</div>';

			if( $return == true ) {
				return $html;
			} else {
				echo $html;
			}
		}

		public function __construct(){
			$this->init();
			add_action( 'wpmu_new_blog',		 [$this, 'new_blog_init'], 10, 6 );

			add_action( 'init',                  [$this, 'convert_dynamic_style'] );
			add_action( 'init',				     [$this, 'policy_post_type'] );
			add_action( 'init',				     [$this, 'register_shortcodes'] );
			add_action( 'wp_enqueue_scripts',	 [$this, 'wp_enqueue'] );
			add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue'] );
			add_action( 'single_template',	     [$this, 'policy_template'], 99 );
			add_action( 'template_redirect',	 [$this, 'insert_form_consent'] );
			add_action( 'template_redirect',	 [$this, 'dynamic_consent_delete'] );
			add_action( 'wp_footer',			 [$this, 'display_versions'] );
			add_action( 'wp_footer',			 [$this, 'render_consent_bar'] );
			add_action( 'wp_footer',			 [$this, 'display_subfooter'] );
			add_action( 'admin_menu',			 [$this, 'register_admin_page'] );
			add_action( 'admin_init',            [$this, 'check_version'] );
			add_action( 'admin_notices',         [$this, 'check_ccpa_compliance'] );

			add_filter( 'the_content',		     [$this, 'filter_policy_content'] );
			add_filter( 'body_class',			 [$this, 'add_gdpr_body_class'] );

			// Some themes seems to be loading scripts before wp_footer output.
			add_filter( 'script_loader_tag', function($tag, $handle){
				$deferred = ['trm-gdpr'];
				
				if( in_array( $handle, $deferred ) )
					$tag = str_replace( ' src', ' defer src', $tag );

				return $tag;
			}, 10, 2 );
		}

		public function check_ccpa_compliance(){
			#if ( false === ( $ccpa_compliance_last_checked = get_transient( '__ccpa_compliance_last_checked' ) ) ) {
				if( current_user_can('administrator') ){
					foreach( $this->policy_pages() as $label => $atts ){
						$policy_content = get_post_field( 'post_content', $atts['ID'] );

						if( $policy_content != '' ){
							$modified_time = get_the_modified_time('U', $atts['ID']);
							
							if( (time() - $modified_time) > YEAR_IN_SECONDS ){
								$message = sprintf( 'You\'re using a custom <strong>%s</strong>, and it hasn\'t been revised since %s. <a href="https://oag.ca.gov/privacy/ccpa" target="_blank">CCPA</a> requires policies be revised or looked at yearly. Please revisit your Policies and update them (just click on the Publish/Update button if no changes are required).', ucwords(str_replace('_', ' ',$label)), date('M jS, Y', $modified_time) );
								printf( '<div style="display:flex;justify-content:space-between;" class="notice notice-warning"><p>%s</p><a style="margin:6px 0 6px 6px;" href="%s" class="button">Review %s</a></div>', $message, esc_url( get_edit_post_link($atts['ID']) ), ucwords(str_replace('_', ' ',$label)) );
							}
						}
					}
				}

				#$ccpa_compliance_last_checked = time();
				#set_transient( '__ccpa_compliance_last_checked', $ccpa_compliance_last_checked, DAY_IN_SECONDS );
			#}

			// DEBUGGING ONLY
			#delete_transient( '__ccpa_compliance_last_checked' );
		}

		public function check_version(){
			if( !is_admin() )
				return false;
			
			$data    = get_plugin_data( __FILE__, false, false );
			$version = get_option('__trm_gdpr_version');

			if( get_option('__trm_gdpr_version') !== $data['Version'] ){
				$consent_bar = 'By using our website, you consent to our {cookie_policy}, {privacy_policy}, and {terms_of_service}.';
				
				update_option( 'trm_gdpr_default_notice_consent_bar', $consent_bar );
				update_option( '__trm_gdpr_version', $data['Version'] );
			}
		}

		private function init(){
			//register_activation_hook( __FILE__,  [$this, 'init_gdpr_assistant'] );
			register_activation_hook( __FILE__,  [$this, 'activate'] );
		}

		public function activate( $network_wide ){
			if( is_multisite() && $network_wide ){
				global $wpdb;

				foreach( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ){
					switch_to_blog( $blog_id );
					$this->init_gdpr_assistant();
					restore_current_blog();
				} 
			} else {
				$this->init_gdpr_assistant();
			}
		}

		function new_blog_init( $blog_id, $user_id, $domain, $path, $site_id, $meta ){
			$dir  = basename( __DIR__ );
			$file = basename( __FILE__ );

			if( is_plugin_active_for_network( "$dir/$file" ) ){
				switch_to_blog( $blog_id );
				$this->init_gdpr_assistant();
				restore_current_blog();
			}
		}

		public function convert_dynamic_style(){
			if( !( $dynamic_style = get_option( 'trm_gdpr_dynamic_style' ) ) ){
				// No Option Exists; Either Old Method or New Install
				if( $dynamic_file = @file_get_contents( plugin_dir_path( __FILE__ ) . 'css/dynamic.css' ) ){
					// Old Method, Convert to Option
					update_option( 'trm_gdpr_dynamic_style', $dynamic_file );
				} else {
					// New Install, Set a Basic Option
					update_option( 'trm_gdpr_dynamic_style', "/* TRM GDPR Dynamic Styles */\r\n" );
				}
			}
		}

		public function init_gdpr_assistant(){
			$this->convert_dynamic_style();

			// Get Inserted Policy Page IDs
			foreach( $this::$policies as $policy ){
				if( $existing_page = get_page_by_title( $policy, 'OBJECT', 'gdpr-policy' ) ){
					$id = $existing_page->ID;
				} else {
					$policy_page = [
						'post_title'	=> $policy,
						'post_content'  => '', // Necessary?
						'post_status'   => 'publish',
						'post_type'	 => 'gdpr-policy'
					];
					$id = wp_insert_post( $policy_page );
				}

				$option = $this->optionize( $policy );
				update_option( "trm_gdpr_inserted_$option", $id );
			}

			// Default Consent Notices
			$form_consent = 'By submitting this form, you agree to our {cookie_policy}, {privacy_policy}, and {terms_of_service}. You may receive email and/or SMS communications from us. You may opt out any time.';
			update_option( 'trm_gdpr_default_notice_form_consent', $form_consent );

			//$consent_bar = 'This website may use cookies to deliver its services and track user engagement. By using this website, you acknowledge that you have read and understand our revised {cookie_policy}, revised {privacy_policy}, and revised {terms_of_service}. Your use of this website is subject to these policies and terms.';
			$consent_bar = 'By using our website, you consent to our {cookie_policy}, {privacy_policy}, and {terms_of_service}.';
			update_option( 'trm_gdpr_default_notice_consent_bar', $consent_bar );

			flush_rewrite_rules();
		}

		public function policy_pages(){
			$policy_pages = new stdClass();

			foreach( $this::$policies as $policy ){
				$option	= $this->optionize( $policy ); // cookie_policy, terms_of_service, privacy_policy
				$overwrite = get_option( "trm_gdpr_overwrite_$option" );

				if( $overwrite == false || $overwrite == '' || $overwrite == 'default' ){
					$policy_pages->$option = [
						'ID'  => intval( get_option( "trm_gdpr_inserted_$option" ) ),
						'url' => get_permalink( intval( get_option( "trm_gdpr_inserted_$option" ) ) )
					];
				} else if( $overwrite == 'custom' ){
					$policy_pages->$option = [
						'ID'  => null,
						'url' => get_option( "trm_gdpr_custom_$option" )
					];
				} else {
					$policy_pages->$option = [
						'ID'  => intval( $overwrite ),
						'url' => get_permalink( intval( $overwrite ) )
					];
				}
			}

			return $policy_pages;
		}

		public function consent_notices(){
			$policy_pages	= $this->policy_pages();
			$consent_notices = new stdClass();

			foreach( $this::$notices as $notice ){
				$overwrite = false;
				$option	= $this->optionize( $notice );
				$overwrite = stripslashes( get_option( "trm_gdpr_overwrite_notice_$option" ) );

				// Determine Message (Default or Overwrite)
				$consent = ( $overwrite == false || $overwrite == '' ) ? get_option( "trm_gdpr_default_notice_$option") : $overwrite;

				// Replace Merge Tags with Links
				foreach( $policy_pages as $label => $atts ){
					$consent = str_replace( '{'.$label.'}', '<a href="'. $atts['url'] .'">'. $this->stringify( $label ) .'</a>', $consent );
				}

				$consent_notices->$option = $consent;
			}
			
			return $consent_notices;
		}

		public function policy_post_type(){
			register_post_type( 'gdpr-policy',
				array(
					'public'			 => true,
					'publicly_queryable' => true,
					'show_ui'			=> true,
					'show_in_menu'	   => true,
					'query_var'		  => true,
					'rewrite'			=> [
						'slug'		   	=> '/policies',
						'with_front'		=> false
					],
					'capability_type'	=> 'post',
					'has_archive'		=> true,
					'hierarchical'	   => true,
					'menu_icon'		  => 'dashicons-welcome-view-site',
					'menu_position'	  => null,
					'supports'		   => ['title', 'editor', 'revisions', 'page-attributes'],
					'labels'			 => array(
						'name'		   	=> __( 'Policies' ),
						'singular_name'  	=> __( 'Policy' )
					),
				)
			);

			flush_rewrite_rules();
		}


		public function policy_template( $single_template ){
			global $post;

			if( $post->post_type == 'gdpr-policy' ){
				if( $page_template = locate_template( ['page.php'] ) ){
					$single_template = $page_template;
				}
			}

			return $single_template;
		}

		public function wp_enqueue(){
			wp_enqueue_style( 'trm-gdpr', plugins_url( '/css/core.min.css', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'css/core.min.css' ) );
			echo '<style>'. get_option( 'trm_gdpr_dynamic_style' ) .'</style>';
			
			if( get_option( 'trm_gdpr_disable_hide_existing_links' ) != true )
				echo '<style>.trm-gdpr-active .creds a[href*="/terms"],.trm-gdpr-active .creds a[href*="/privacy"]{display:none}</style>';

			wp_enqueue_script( 'trm-gdpr', plugins_url( '/js/core.min.js', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'js/core.min.js' ), true );
		}

		public function admin_enqueue(){
			wp_register_style( 'trm-gdpr-admin', plugins_url( '/css/admin.min.css', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'css/admin.min.css' ) );

			wp_enqueue_script( 'trm-gdpr-editor', plugins_url( '/js/editor.min.js', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'js/editor.min.js' ), true );
			wp_register_script( 'trm-gdpr-admin', plugins_url( '/js/admin.min.js', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'js/admin.min.js' ) );
		}

		public function filter_policy_content( $content ){
			global $post;
			$policy_pages = $this->policy_pages();

			foreach( $policy_pages as $label => $atts ){
				if( $post->ID == $atts['ID'] ){
					if( trim($post->post_content) == '' ){
						if ( false === ( $response = get_transient( '__cached_svn_'.$label ) ) ) {
							$response = wp_remote_get( "https://plugins.svn.wordpress.org/privacy-consent-assistant/assets/policies/{$label}.txt" );
							set_transient( '__cached_svn_'.$label, $response, WEEK_IN_SECONDS );
						}

						$content  = wp_remote_retrieve_body( $response );
						$modified = wp_remote_retrieve_header( $response, 'last-modified' );

						$content = '<div style="font-size: 14px !important;">'. stripslashes( $this->replace_policy_variables( $content, $modified ) ) .'</div>';
					}

					break;
				}
			}
			
			return $content;
		}

		public function insert_form_consent(){
			if( get_option( 'trm_gdpr_disable_form_consent' ) != true ){
				ob_start( function( $buffer ){
					// Define our Consent Message
					$dynamic_delete = ( current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ) ? '<i class="dynamic-delete">'. $this->icon( 'delete' ) .'</i>' : '';
					$notice = '<div siteURL dynamicDeletePage dynamicDeleteIndex nonce="'. wp_create_nonce( 'dynamic_delete' ) .'"class="trm-gdpr-ui trm-gdpr-form-consent-wrap"><div class="consent-notice-form">'. $this->consent_notices()->form_consent . $dynamic_delete .'</div></div>';

					// Regex to find all "submit" buttons: https://regex101.com/r/UwsZOt/5
					$delete_index = 0;

					$expressions = [
						'/(?:<[^<]* data-optin_id=)/i', // Bloom
						'/(?:<[^<]* type=["\']submit["\'])/i', // Standard
					];

					foreach( $expressions as $regex ){
						$buffer = preg_replace_callback( $regex, function($matches) use($notice, &$delete_index){
							// Insert Message before Submit button (but inside parent container)
							$notice = str_replace( 'siteURL', 'site-url="'. site_url( '/' ) .'"', $notice );
							$notice = str_replace( 'dynamicDeletePage', 'dynamic-delete-page="'. get_the_ID() .'"', $notice );
							$notice = str_replace( 'dynamicDeleteIndex', "dynamic-delete-index='$delete_index'", $notice );

							++$delete_index;
							return $notice.$matches[0];
						}, $buffer);
					}

					return $buffer;
				});
			}
		}

		public function add_gdpr_body_class( $classes ){
			$classes[] = 'trm-gdpr-active';

			if( is_home() )$classes[] = 'trm-gdpr-home';
			if( is_front_page() ) $classes[] = 'trm-gdpr-front-page';

			$classes[] = 'trm-gdpr-'.get_the_ID();

			return $classes;
		}

		public function dynamic_consent_delete(){
			// Prevent unauthorized user levels from hiding these.
			if( isset( $_GET['trm_gdpr_method'] ) ){
				if( $_GET['trm_gdpr_method'] == 'dynamic-delete' ){
					$response = [];
					if( current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ){
						if( wp_verify_nonce( $_GET['nonce'], 'dynamic_delete' ) ){
							$response['status']  = 200;

							if( isset( $_GET['form_id'] ) ){
								// Remove junk characters from Form ID.
								$form_selector = '#'.preg_replace( '/[^\w-]/', '', $_GET['form_id'] );
								$element	   = $form_selector.' .trm-gdpr-ui.trm-gdpr-form-consent-wrap .consent-notice-form';
							} else {
								if( is_numeric( $_GET['dynamic-page'] ) && is_numeric( $_GET['dynamic-index'] ) ){
									$element  = '.trm-gdpr-ui.trm-gdpr-form-consent-wrap[dynamic-delete-page="'. $_GET['dynamic-page'] .'"][dynamic-delete-index="'. $_GET['dynamic-index'] .'"]';
								} else {
									$response['status']  = 400;
									$response['message'] = 'Consent Notice Could Not Be Hidden, invalid input.';
								}
							}
						} else {
							$response['status']  = 400;
							$response['message'] = 'Consent Notice Could Not Be Hidden, invalid nonce.';
						}
					} else {
						$response['status']  = 400;
						$response['message'] = 'Consent Notice Could Not Be Hidden, user lacking admin capabilities.';
					}

					if( $response['status'] == 200 ){
						// Nothing has gone wrong yet
						$dynamic_style = get_option( 'trm_gdpr_dynamic_style' );
						$comment_log   = '['. get_current_user_id() .'] '. get_the_title( intval( $_GET['dynamic-page'] ) ) .", Form #". ++$_GET['dynamic-index'].'';
						$added_style   = "/* $comment_log */ $element{display:none!important;height:0;width:0;overflow:hidden;position:absolute}\r\n";

						if( update_option( 'trm_gdpr_dynamic_style', $dynamic_style.$added_style ) ){
							$response['status']  = 200;
							$response['message'] = 'Consent Notice Hidden';
						} else {
							$response['status']  = 400;
							$response['message'] = 'Consent Notice Could Not Be Hidden, unknown error';
						}
					} else {
						if( $response['status'] != 400 ){
							$response['status']  = 400;
							$response['message'] = 'Unkown Error.';
						}
					}

					ob_start( function() use($response){
						http_response_code( $response['status'] );
						header( 'Content-Type: application/json' );
						return json_encode( $response );
					});

					wp_die();
				}
			}
		}

		public function display_versions(){
			// TO DO: Track revisions using `get_the_modified_date()` for custom pages`
			echo "<div id='trm-gdpr-versions' data-versions='{$this::$versions}' data-revised='{$this::$revised}'></div>";
		}

		/**
		 * As of version 1.0.8 we need to always to render the consent
		 * bar, and deal with it showing/hiding on the front end with
		 * JS or CSS. Our MWP server caches ANY consent click for ALL
		 * users, instead of locally. So rely on JS cookie only.
		 */
		public function render_consent_bar(){
			if( get_option( 'trm_gdpr_disable_consent_bar' ) != true ){
				$close_consent_functions = get_option( 'trm_gdpr_close_consent_functions' ) ? stripslashes( get_option( 'trm_gdpr_close_consent_functions' ) ) : '';

				echo $this->wrap_element(
					'<span>'. stripslashes( $this->consent_notices()->consent_bar ) .'</span><i onclick="closeTRMGDPRconsent(this); '. $close_consent_functions .' return false;" class="close-consent">'. $this->icon( 'close' ) .'</i>',
					'trm-gdpr-consent-bar',
					'consent-bar'
				);
			}
		}

		public function display_subfooter(){
			if( get_option( 'trm_gdpr_disable_subfooter') != true){
				$html = '';
				echo '<div id="trm-gdpr-subfooter" class="trm-gdpr-ui">';
					foreach( $this::$policies as $policy ){
						$option = $this->optionize( $policy );
						$html .= do_shortcode( "[trm_gdpr_$option] — " );
					}
					echo substr( $html, 0, -4 );
				echo '</div>';
			}
		}

		public function register_admin_page(){
			add_menu_page( 'Privacy & Consent', 'Privacy & Consent', 'manage_options', __FILE__, function(){ require_once plugin_dir_path( __FILE__ ) .'admin.php'; }, 'dashicons-clipboard' );
		}

		public function replace_policy_variables( $content, $last_modified = false ){
			// Site URL
			$content = str_replace( '{SITE_URL}', '<strong>'. str_replace( ['https://', 'http://'], '', site_url() ) .'</strong>', $content );

			// Current Year
			$content = str_replace( '{CURYEAR}', date('Y'), $content );

			// Versions Numbers
			$versions = explode( '-', str_replace( ' ', '', $this::$versions ) );
			$content  = str_replace( '{TERMS_VERSION}',   $versions[1], $content );
			$content  = str_replace( '{COOKIE_VERSION}',  $versions[2], $content );
			$content  = str_replace( '{PRIVACY_VERSION}', $versions[0], $content );

			// Revisions
			if( $last_modified ){
				$content   = preg_replace( '/{[A-Z]*_REVISED}/m', $last_modified, $content );
			} else {
				$revisions = explode( '-', str_replace( '_', ' ', $this::$revised ) );
				$content   = str_replace( '{TERMS_REVISED}',   $revisions[1], $content );
				$content   = str_replace( '{COOKIE_REVISED}',  $revisions[2], $content );
				$content   = str_replace( '{PRIVACY_REVISED}', $revisions[0], $content );
			}

			// Links
			$policy_pages = $this->policy_pages();
			$content	  = str_replace( '{TERMS_URL}',   $policy_pages->terms_of_service['url'], $content );
			$content	  = str_replace( '{COOKIE_URL}',  $policy_pages->cookie_policy['url'],	$content );
			$content	  = str_replace( '{PRIVACY_URL}', $policy_pages->privacy_policy['url'],   $content );

			// Governance
			if( $state = get_option( 'trm_gdpr_governing_state' ) ){
				$content = str_replace( '{governing_state}', "the state of $state,", $content );
			} else {
				$content = str_replace( '{governing_state}', '', $content );
			}

			if( $state = get_option( 'trm_gdpr_governing_country' ) ){
				$content = str_replace( '{governing_country}', $state, $content );
			} else {
				$content = str_replace( '{governing_country}', 'the USA', $content );
			}

			// Attempt to fill a Company name
			if( $company_name = get_option( 'trm_gdpr_company_name') ){
				// Overwritten Name, Nice
				$content = str_replace( '[Company Name]', $company_name, $content );
			} else if( function_exists('genesis_get_option') && $company_name = genesis_get_option( 'organization', 'child-settings' ) ){
				// Review Engine Name
				$content = str_replace( '[Company Name]', $company_name, $content );
			} else if( function_exists('genesis_get_option') && $company_name = genesis_get_option( 'lmp_gen_company_name', 'website-genesis-options' ) ){
				// TRD Name
				$content = str_replace( '[Company Name]', $company_name, $content );
			} else if( $company_name = get_bloginfo( 'name' ) ){
				// Attempt the Site Title
				$content = str_replace( '[Company Name]', end( explode( '|', $company_name ) ), $content );
			} else {
				// Fallback to something quasi-formal, the URL
				$content = str_replace( '[Company Name]', 'The Current Owners of '. str_replace( ['https://', 'http://'], '', site_url() ), $content );
			}

			// Attempt an Address
			if( $company_address = get_option( 'trm_gdpr_company_address') ){
				// Overwritten, Nice
				$content = str_replace( '[Company Address]', $company_address, $content );
			} else if( function_exists('genesis_get_option') && genesis_get_option( 'address', 'child-settings' ) || function_exists('genesis_get_option') && genesis_get_option( 'state', 'child-settings' ) ){
				// Review Engine Address
				$company_address = genesis_get_option( 'address', 'child-settings' ).' '.genesis_get_option( 'city', 'child-settings' ).' '.genesis_get_option( 'state', 'child-settings' ).' '.genesis_get_option( 'zip_code', 'child-settings' );
				$content = str_replace( '[Company Address]', $company_address, $content );
			} else if( function_exists('genesis_get_option') && genesis_get_option( 'lmp_gen_street_address', 'website-genesis-options' ) || function_exists('genesis_get_option') && genesis_get_option( 'lmp_gen_state', 'website-genesis-options' ) ){
				// TRD Address
				$company_address = genesis_get_option( 'lmp_gen_street_address', 'website-genesis-options' ).' '.genesis_get_option( 'lmp_gen_street_address_two', 'website-genesis-options' ).' '.genesis_get_option( 'lmp_gen_city', 'website-genesis-options' ).' '.genesis_get_option( 'lmp_gen_state', 'website-genesis-options' ).' '.genesis_get_option( 'lmp_gen_po_box', 'website-genesis-options' ).' '.genesis_get_option( 'lmp_gen_country', 'website-genesis-options' );
				$content = str_replace( '[Company Address]', $company_address, $content );
			} else {
				$content = str_replace( '[Company Address]', '', $content );
			}

			// Attempt a Phone Number
			if( $company_phone = get_option( 'trm_gdpr_company_phone' ) ){
				// Overwritten, Nice
				$content = str_replace( '[Company Phone]', $company_phone, $content );
			} else if( function_exists('genesis_get_option') && $company_phone = genesis_get_option( 'phone', 'child-settings' ) ){
				// Review Engine Phone
				$content = str_replace( '[Company Phone]', $company_phone, $content );
			} else if( function_exists('genesis_get_option') && $company_phone = genesis_get_option( 'lmp_gen_phone', 'website-genesis-options' ) ){
				// TRD Phone
				$content = str_replace( '[Company Phone]', $company_phone, $content );
			} else {
				$content = str_replace( '[Company Phone]', '', $content );
			}

			// Attempt an Email Address
			if( $company_email = get_option( 'trm_gdpr_company_email' ) ){
				// Overwritten, Nice
				$content = str_replace( '[Company Email]', $company_email, $content );
			} else if( function_exists('genesis_get_option') && $company_email = genesis_get_option( 'author_email', 'child-settings' ) ){
				// Review Engine Email
				$content = str_replace( '[Company Email]', $company_email, $content );
			} else if( $company_email = get_bloginfo( 'admin_email' ) ){
				// Admin Email
				$content = str_replace( '[Company Email]', $company_email, $content );
			} else {
				$content = str_replace( '[Company Email]', '', $content );
			}

			return $content;
		}

		public function register_shortcodes(){
			$policy_pages = $this->policy_pages();

			foreach( $policy_pages as $label => $atts ){
				add_shortcode( "trm_gdpr_$label", function() use ($label, $atts){
					return '<a href="'. $atts['url'] .'">'. $this->stringify( $label ) .'</a>';
				});
			}
		}

		public function validate_option( $input, $type ){
			if( !isset( $input ) ) return;

			if( $type == 'boolean' ){
				return filter_var( $input, FILTER_VALIDATE_BOOLEAN ) ? true : false;
			} else if( $type == 'url' ){
				return filter_var( $input, FILTER_VALIDATE_URL ) ? sanitize_text_field( $input ) : '';
			} else if( $type == 'string' ){
				return sanitize_text_field( $input );
			} else if( $type == 'textarea' ){
				$args = [ 'a' => ['href' => [], 'title' => []] ];
				$els = ['h1','h2','h3','h4','h5','h6','i','em','br','strong','div','span','ul','ol','li','p','pre','code','blockquote','hr'];

				foreach( $els as $el ){
					$args[$el] = [
						'id'    => [],
						'class' => [],
						'style' => []
					];
				}

				return wp_kses( $input, $args );
			} else if( $type == 'textarea_complex' ){
				return sanitize_textarea_field( $input ).PHP_EOL;
			}
		}
	}

	TRM_GDPR::get_instance();
