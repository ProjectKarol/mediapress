<?php
//based on the code of Send System Info plugin
//https://wordpress.org/plugins/send-system-info/
//by John Regan
?>
<?php $browser = new Browser();
global $wpdb;
?>

// Generated by the MediaPress Debug tool //

Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

SITE_URL:                 <?php echo site_url() . "\n"; ?>
HOME_URL:                 <?php echo home_url() . "\n"; ?>

WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
Permalink Structure:      <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
<?php 
	$theme_data = wp_get_theme();
	$theme      = $theme_data->Name . ' ' . $theme_data->Version;
	$parent_theme = '';
	if( is_child_theme() ) {
		$parent_theme_data = wp_get_theme( get_template() );
		$parent_theme      = $theme_data->Name . ' ' . $theme_data->Version;
	}
?>			
Active Theme:             <?php echo $theme . "\n"; ?>
<?php if( $parent_theme ):?>
Parent Theme:             <?php echo $parent_theme . "\n"; ?>
<?php endif;?>

Registered Post Stati:    <?php echo implode( ', ', get_post_stati() ) . "\n\n"; ?>

// Browser Details //
<?php echo strip_tags($browser) ; ?>

// End Browser Section //

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
<?php
if ( $wpdb->use_mysqli ) {
	$mysql_ver = @mysqli_get_server_info( $wpdb->dbh );
} else {
	$mysql_ver = @mysql_get_server_info();
}
?>
MySQL Version:            <?php echo $mysql_ver . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:   <?php echo  WP_MEMORY_LIMIT  ?><?php echo "\n"; ?>
PHP Safe Mode:            <?php echo ini_get( 'safe_mode' ) ? "Yes" : "No\n"; ?>
PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Upload Max Size:      <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:  <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:        <?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:  <?php echo ini_get( 'allow_url_fopen' ) ? "Yes" : "No\n"; ?>

WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
SCRIPT_DEBUG:             <?php echo defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>
						  	
// Table Prefix //
<?php if( is_multisite() ):?>
Table Base Prefix:        <?php echo  $wpdb->base_prefix ; echo "\n"; ?>
<?php endif;?>
Table Prefix:             <?php echo  $wpdb->prefix ; echo "\n"; ?>

Show On Front:            <?php echo get_option( 'show_on_front' ) . "\n" ?>
Page On Front:            <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
Page For Posts:           <?php $id = get_option( 'page_for_posts' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>

Session:                  <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:              <?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only Cookies:         <?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>

DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL:                     <?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
SOAP Client:              <?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
SUHOSIN:                  <?php echo ( extension_loaded( 'suhosin' ) ) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n"; ?>

ACTIVE PLUGINS:

<?php $plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins ) )
		continue;

	echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}

if ( is_multisite() ) : ?>

NETWORK ACTIVE PLUGINS:

	<?php $plugins  = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

	foreach ( $plugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) )
			continue;

		$plugin = get_plugin_data( $plugin_path );

echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
	}
endif;
?>

// MediaPress Settings //

<?php $settings = mpp_get_all_options();

foreach( $settings as $key => $value ) {
	echo $key . ':		' . mpp_array_to_string($value) ."\n";
	
}

?>

// End of MediaPress Settings //

<?php 
if ( has_action( 'mpp_admin_debug_extra' ) ) {
	echo "\n";
	do_action( 'mpp_admin_debug_extra' );
}
