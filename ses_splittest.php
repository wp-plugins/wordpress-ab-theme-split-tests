<?
/*
Plugin Name: SES Theme Split Test
Plugin URI: http://www.leewillis.co.uk/wordpress-plugins/
Description: Split test your wordpress theme, and track test using Google Analytics user defined values. Based on an idea by David Dellanave (http://www.dellanave.com/)
Author: Lee Willis
Version: 0.1
Author URI: http://www.leewillis.co.uk
*/

function ses_splittest_menu() {
	add_options_page('SES Theme Split Test Settings', 'SES Theme Split Test', 10, basename(__FILE__), 'ses_splittest_options');
}
add_action("admin_menu", "ses_splittest_menu");

function ses_splittest_options() {
	if ( isset($_POST['submit']) ) {
		if (!current_user_can( 'manage_options' ))
			die(__( 'You cannot edit the options.' ));
                check_admin_referer( 'ses-splittest-updatesettings' );
		
		if ($_POST['sesthemes'] != "") {
			foreach(array_keys($_POST['sesthemes']) as $key) {
				$ses_theme_list[] = $key;
			}
		}

		update_option("ses_splittest_themes", $ses_theme_list);

		echo '<div id="message" class="updated"><p>'.__('Settings updated.').'</p></div>';

	}

	$ses_theme_list = "";
	$ses_theme_list = get_option("ses_splittest_themes");
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php _e("SES Theme Split Test"); ?></h2>
		<form action="" method="post" id="ses-splittest-conf">
			<h3>SES Theme Split Test Settings</h3>
			Choose which themes will be used on your site for the split test.
			<table class="form-table">
				<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('ses-splittest-updatesettings'); } ?>
				<?php 
					$themes = get_themes();
	
					foreach($themes as $theme) {

						//echo "<PRE>"; print_r($theme); echo "</PRE>";
						?>
				<tr>
					<th style="vertical-align:top;width:35%;">
						<?php echo htmlentities($theme['Title']); ?>
					</th>
					<td style="vertical-align:top;"><input type="checkbox" name="sesthemes[<?php echo htmlentities($theme['Template']); ?>]" value="<?php echo $taxonomy['name']; ?>" <?php if($ses_theme_list != "" && in_array($theme['Template'],$ses_theme_list)) echo "checked"; ?>/></td>
				</tr>

				<?php
					}
				
				?>
			</table>
			<br/>
			<span class="submit" style="border: 0;"><input type="submit" name="submit" value="<?php _e("Save Settings"); ?>" /></span>
		</form>
	</div>
<?
}

class ses_splittest{
	var $splittest = "";
	
	function ses_splittest(){
		$this->splittest = false;
		add_action('plugins_loaded',array(&$this,'detectsplittest'));
		add_action('wp_footer',array(&$this, 'output_themesetvar'),99);
		add_filter('stylesheet',array(&$this,'get_stylesheet'));
		add_filter('template',array(&$this,'get_template'));
	}

	function detectsplittest($query){
		
		$ses_theme_list = get_option("ses_splittest_themes");

		if (count($ses_theme_list) < 2) {
			return;
		}
		
		if($_COOKIE['wp_splittest1']) {
			$theme = $_COOKIE['wp_splittest1'];
		}

		// No cookie, or Theme is no longer part of split test - assign a new one
		if (!isset($theme) || !in_array($theme, $ses_theme_list)) {
			$id = rand(0,count($ses_theme_list)-1);
			$theme = $ses_theme_list[$id];
		}

		setcookie("wp_splittest1",$theme,time()+(60*60*24*30),'/');
		$this->splittest = $theme;		


	}
	
	function get_stylesheet($stylesheet) {
		if ($this->splittest != "") {
			return $this->splittest;
		} else {
			return $stylesheet;
		}
	}
	
	function get_template($template) {
		if ($this->splittest != "") {
			return $this->splittest;
		} else {
			return $template;
		}
	}

	function output_themesetvar() {
		if ($this->splittest != "") {
			echo '<script type="text/javascript">pageTracker._setVar("';
			echo htmlentities($this->splittest);
			echo '");</script>';
		}
	}
}

$ses_splittest = new ses_splittest();

?>
