<?php
/*
Plugin Name: Anonim-Links
 * Plugin URI: http://anonim.es/plugins/
 * Description: Automatically anonymizes all the external links on your website, which prevents the original site from appearing as a referrer in the logfiles of the referred page. ------ Anonimiza automáticamente todos los enlaces externos de su sitio web, que evita que la página de origen aparezca en los ficheros log de la página enlazada.
 * Version: 1.0
 * Tested up to: 4.2.3
 * Author: anonim.es
 * Author URI: http://anonim.es/
*/

load_plugin_textdomain('Anonim-Links', false, basename( dirname( __FILE__ ) ) . '/lang' );

$__anonim_links = new anonim_links();

add_action('wp_footer', array($__anonim_links,'add_anonim_links_js'));
add_action('admin_menu', array($__anonim_links,'anonim_links_menu'));
add_action('wp_enqueue_scripts', array($__anonim_links, 'anonim_links_scripts'));
add_action('init', array($__anonim_links, 'anonim_links_init'));
$plugin_dir = basename(dirname(__FILE__));

register_activation_hook(__FILE__, array($__anonim_links,'anonim_links_activate'));
register_deactivation_hook(__FILE__, array($__anonim_links,'anonim_links_deactivate'));

function add_settings_link($links, $file) {
static $this_plugin;
if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
 
if ($file == $this_plugin){
$settings_link = '<a href="options-general.php?page=anonim_links-options">'.__("Settings", "anonim_links-options").'</a>';
 array_unshift($links, $settings_link);
}
return $links;
 }
add_filter('plugin_action_links', 'add_settings_link', 10, 2 );


final class anonim_links {
	
	
	public function anonim_links_init(){
		//wp_register_script('anonim_links-anonim_links', WP_PLUGIN_URL . '/anonymize-links/js/anonymize.js');
		wp_register_script('anonim_links-anonim_links', 'http://anonim.es/js/anonimiza.js');
	}
	
	public function anonim_links_activate(){
		$opt_name = 'anonim_links_service';
		$opt_val = get_option( $opt_name );		
		add_option("anonim_links_service", '', '', 'yes');
	}
	
	public function anonim_links_deactivate(){
		delete_option("anonim_links_service");
	}
	
	public function anonim_links_menu(){
		add_options_page(__('Anonymize Links Options'), __('Anonymize Links'), 'administrator', 'anonim_links-options', array($this,'anonim_links_options_page'));
	}	
	
	public function anonim_links_options_page(){
		if($_POST['protected_links']){
			echo '<div class="updated"><p><strong> '. __('Options saved.'). '</strong></p></div>';	
			update_option("anonim_links_service", $_POST['protected_links']);
		} elseif(isset($_POST['protected_links'])){
		   echo '<div class="updated"><p><strong> '. __('Options cleared.'). '</strong></p></div>';	
		   update_option("anonim_links_service", '');
		}
			
		echo '<div class="wrap">';
		echo '<h2>'. __('Anonymize Links Options') .'</h2>';
		//echo basename( dirname( __FILE__ ) ) . '/lang';
		?>			
		
		<form method="POST" action="">
		<p><?php echo __('Do not anonymize the following domains / keywords:'); ?></p>
		
		<table class="form-table">			
			<tr valign="top">				
				<td>
					<input type="text" class="anonym_input" id="protected_links" name="protected_links" size="100" value="<?php echo get_option('anonim_links_service')?>">		
					<p class="description"><?php echo __('Comma separated: domain1.tld, domain2.tld, keyword'); ?></p>		
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
		</p>
		<?php
		echo '</div>';
	}
	
	public function anonim_links_scripts(){		
		wp_enqueue_script('anonim_links-anonim_links');		
	}
	
	public function add_anonim_links_js(){
		$opt_val = get_option('anonim_links_service');	
		echo '<script type="text/javascript"><!--
		protected_links = "'.$opt_val.'";

		auto_anonyminize();
		//--></script>';
	}
}
?>