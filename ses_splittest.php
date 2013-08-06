<?php
/*
Plugin Name: SES Theme Split Test
Plugin URI: http://www.leewillis.co.uk/wordpress-plugins/?utm_source=wordpress&utm_medium=www&utm_campaign=wordpress-ab-theme-split-tests
Description: Split test your wordpress theme, and track test using Google Analytics user defined values. Based on an idea by David Dellanave (http://www.dellanave.com/)
Author: Lee Willis
Version: 1.3
Author URI: http://www.leewillis.co.uk/?utm_source=wordpress&utm_medium=www&utm_campaign=wordpress-ab-theme-split-tests
*/

function ses_splittest_menu() {
	add_options_page( 'SES Theme Split Test Settings', 'SES Theme Split Test', 'manage_options', basename( __FILE__ ), 'ses_splittest_options' );
}
add_action( 'admin_menu', 'ses_splittest_menu' );

function ses_splittest_options() {
	if ( isset($_POST['submit']) ) {
		if ( ! current_user_can( 'manage_options' ) )
			die( __( 'You cannot edit the options.' ) );
                check_admin_referer( 'ses-splittest-updatesettings' );

		if ( $_POST['sesthemes'] != '' ) {
			update_option( 'ses_splittest_themes', $_POST['sesthemes'] );
		}

		echo '<div id="message" class="updated"><p>'.__( 'Settings updated.' ).'</p></div>';
	}

	$ses_theme_list = '';
	$ses_theme_list = get_option( 'ses_splittest_themes' );
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e( 'SES Theme Split Test' ); ?></h2>
		<form action="" method="post" id="ses-splittest-conf">
			<h3>SES Theme Split Test Settings</h3>
			Choose which themes will be used on your site for the split test.
			<table class="form-table">
				<?php if ( function_exists( 'wp_nonce_field' ) ) {
						wp_nonce_field( 'ses-splittest-updatesettings' );
					} ?>
				<?php
					$themes = wp_get_themes();
					foreach ( $themes as $theme ) {
						?>
				<tr>
					<th style="vertical-align:top;width:35%;">
						<?php echo esc_html( $theme['Title'] ); ?>
					</th>
					<td style="vertical-align:top;"><input type="checkbox" name="sesthemes[<?php echo htmlentities( $theme['Stylesheet'] ); ?>]" value="<?php echo $theme['Template']; ?>" <?php if( $ses_theme_list != '' && in_array( $theme['Stylesheet'], array_keys($ses_theme_list) ) ) echo 'checked'; ?>/></td>
				</tr>
				<?php
					}

				?>
			</table>
			<br/>
			<span class="submit" style="border: 0;"><input type="submit" name="submit" value="<?php _e( 'Save Settings' ); ?>" /></span>
		</form>
	</div>
<?php
}

class ses_splittest {
	var $splittest = '';

	function ses_splittest(){
		$this->splittest = false;
		add_action( 'plugins_loaded', array( $this, 'detectsplittest' ) );
		add_filter( 'stylesheet', array( $this, 'get_stylesheet' ) );
		add_filter( 'template', array( $this, 'get_template' ) );
		if ( class_exists( 'GA_Filter' ) && method_exists( 'GA_Filter','spool_analytics' ) ) {
			// Google Analytics For Wordpress
			add_filter( 'yoast-ga-custom-vars', array( $this, 'gafw_setvar' ) );
		} else {
			add_action( 'wp_footer',array( $this, 'output_themesetvar' ),99 );
		}

	}

	function detectsplittest( $query ) {

		$ses_theme_list = get_option( 'ses_splittest_themes' );

		if ( count( $ses_theme_list ) < 2 ) {
			return;
		}

		if( $_COOKIE['wp_splittest1'] ) {
			$theme = $_COOKIE['wp_splittest1'];
		}

		// No cookie, or Theme is no longer part of split test - assign a new one
		if ( !isset( $theme ) || !in_array( $theme, array_keys( $ses_theme_list ) ) ) {
			$id = rand( 0, count( $ses_theme_list ) -1 );
			$stylesheet_list = array_keys( $ses_theme_list );
			$stylesheet = $stylesheet_list[$id];
			$theme = array( $stylesheet => $ses_theme_list[$stylesheet] );
		}

		if ( isset( $_GET['wp_splittest_reset'] ) ) {
			$reset = TRUE;
			setcookie( 'wp_splittest_force', '', time()-86400 );
		}
		// Override if a "force" cookie is set
		if ( isset( $_GET['wp_splittest_force'] ) && $_GET['wp_splittest_force'] != '' && !$reset ) {
			setcookie( 'wp_splittest_force', $_GET['wp_splittest_force'], time() + (60 * 60 * 24 * 30), '/' );
			$this->splittest = array( $_GET['wp_splittest_force'] => $ses_theme_list[$_GET['wp_splittest_force']] );
		} else if ( isset( $_COOKIE['wp_splittest_force'] ) && $_COOKIE['wp_splittest_force'] != '' && !$reset ) {
			setcookie( 'wp_splittest_force', $_COOKIE['wp_splittest_force'], time() + (60 * 60 * 24 * 30),'/' );
			$this->splittest = array( $_COOKIE['wp_splittest_force'] => $ses_theme_list[$_GET['wp_splittest_force']] );
		} else {
			setcookie( 'wp_splittest1', $theme, time() + (60 * 60 * 24 * 30),'/' );
			$this->splittest = array( $theme => $ses_theme_list[$theme] );
		}

	}

	function get_stylesheet( $stylesheet ) {
		if ( ! empty( $this->splittest ) ) {
			return key($this->splittest);
		} else {
			return $stylesheet;
		}
	}

	function get_template( $template ) {
		if ( !empty($this->splittest) ) {
			return current($this->splittest);
		} else {
			return $template;
		}
	}

	// Support for Google Analytics For WordPress
	function gafw_setvar( $push ) {
		if ( !empty( $this->splittest ) ) {
			reset( $this->splittest );
			$idx = 1;
			foreach ( $push as $item ) {
				if ( stristr( $item, 'setCustomVar' ) )
					$idx++;
			}
			$push[] = "'_setCustomVar',$idx,'SplitTestTheme','".key($this->splittest)."'";
		}
		$customVarIdx++;
		return $push;
	}

	function output_themesetvar() {
		if ( !empty( $this->splittest ) ) :
			reset( $this->splittest ); ?>
			<script type="text/javascript">
				try {
					_gaq.push(["_setVar", "<?php echo htmlentities( key( $this->splittest ) ); ?>"]);
                        	} catch(err) {
					try {
						pageTracker._setVar("<?php echo htmlentities( key( $this->splittest ) ); ?>");
					} catch (err2) {}
				}
			</script>
		<?php
		endif;
	}
}

$ses_splittest = new ses_splittest();
