<?php
/*
Plugin Name: Wordpress Plugin Base
Plugin URI: 
Description: Your plugin awesome description here
Version: 0.0.1
Author: Leandro Martins Guimarães
Author URI: https://profiles.wordpress.org/leandropl
License: 
*/

if(!class_exists('NetSisSite'))
{
	class NetSisSite
	{
		//incremental DB version control for easy DB structure upgrade when this is needed
		const DB_VERSION = 1;

		public static function Run()
		{
			//activation hook is not fired when a plugin is updated: https://make.wordpress.org/core/2010/10/27/plugin-activation-hooks-no-longer-fire-for-updates/
			//that's why we need this check here
			add_action('plugins_loaded', 'NetSisSite::plugins_loaded', 1);

			// AJAX and regular functions
			add_action('init', 'NetSisSite::init');

			if (defined('DOING_AJAX') && DOING_AJAX)
			{
				// AJAX only functions
			}
			else
			{
				// regular only functions

				if (is_admin())
				{
					// admin only functions
				}
			}
		}

		public static function onActivation()
		{
			//does the plugin have any prerequisite, and if so, are then met?
			$errors = NetSisSite::check_prerequisites();
			if (is_wp_error($errors))
			{
				deactivate_plugins(plugin_basename( __FILE__ ));
				die(implode('<br />', $errors->get_error_messages()));
			}

			NetSisSite::init_options();

			// uncomment below lines if you have registered any Custon Post Type
			//NetSisSite::register_CustomPostTypes();
			//flush_rewrite_rules();
		}

		public static function onDeactivation()
		{

		}

		public static function onUninstall()
		{

		}

		public static function init()
		{
			//NetSisSite::register_CustomPostTypes();
		}

		public static function register_CustomPostTypes()
		{
			//register_post_type()
		}

		/**
		*	Init plugin options with default values
		*/
		public static function init_options()
		{
			//DO NOT use update_option() for NetSisSite::DB_VERSION here
			//due to upgrade control, you may need previous version info to right handle update routines when it needed
			add_option('netsis_site_db_version', NetSisSite::DB_VERSION);
		}

		public static function plugins_loaded()
		{
			//does the plugin have any prerequisite, and if so, are then met?
			$errors = NetSisSite::check_prerequisites();
			if (is_wp_error($errors))
			{
				require_once(ABSPATH.'wp-admin/includes/plugin.php');
				deactivate_plugins(plugin_basename( __FILE__ ));
				add_action('admin_notices', 'NetSisSite::prerequisite_deactivated_notice');
			}
			else
			{
				//needs to update?
				if (get_option('netsis_site_db_version') < NetSisSite::DB_VERSION)
				{
					//require_once(__DIR__.'/update.php');
	 				//NetSisSiteUpdate::Run();
				}
			}
		}

		public static function check_prerequisites()
		{
			//$errors = new WP_Error();

			//if (!is_plugin_active('some/plugin.php'))
			//	$errors->add('some_plugin_missing', __('<strong>Some Plugin</strong> plugin not installed or not activated.'), 'netsissite_plugin');

			//if (count($errors->get_error_messages()) > 0)
			//	return $errors;
			//else
			//	return true;

			return true;
		}

		//deactivated plugin message feedback based on non met prerequisites
		public static function prerequisite_deactivated_notice()
		{
			$plugin_info = get_plugin_data(__FILE__);
			echo '<div class="error"><p>'.__(sprintf('<strong>%s</strong> deactivated due to a prerequisite plugin not installed or not activated.', $plugin_info['Name']), 'netsissite_plugin').'</p></div>';
		}

		public static function check_dependencies()
		{
			//There are four ways to check whether another plugin or theme is activated:

			// - Use function_exists() to check whether a function of that plugin has been loaded.
			// - Use class_exists() to check whether a class defined by that plugin exists.
			// - Use is_defined() to check whether a constant of that plugin has been defined.
			// - Use is_plugin_active(). However this is not recommended because it requires a plugin base path, which obviously
			//   changes based on the folder name of the dependent plugin (which the user can set to anything). So this cannot
			//   be used as reliably as the first two ways.

			//REF: http://solislab.com/blog/plugin-activation-checklist
			//     thanks Gary Cao (https://profiles.wordpress.org/garyc40)!

			// sample
			//if (!class_exists('SweetnessClass'))
			//	trigger_error('Sorry! No candies for you...', E_USER_ERROR);
		}
	}

	register_activation_hook(__FILE__, array('NetSisSite', 'onActivation'));
	register_deactivation_hook(__FILE__, array('NetSisSite', 'onDeactivation'));
	register_uninstall_hook(__FILE__, array('NetSisSite', 'onUninstall'));

	NetSisSite::Run();
}
