<?php
/*
Plugin Name: AT Search Console
Plugin URI: https://adriantoro.com/wordpress-plugins/at-search-console/
Description: Jump into monitoring your website's clicks, positions, and optimize your site for better search performance for any page with just one click
Version: 1.0.1
Author: Adrian Toro
Author URI: https://adriantoro.com
*/

class wp_search_console_class {
	
	public function __construct(){
		
		add_action('admin_menu', array($this, 'add_menu_page'));
		add_action('admin_bar_menu', array( $this, 'add_item'), 100);
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_settings_link' ) );
   
	}
	
	
	
	public function add_item( $admin_bar ){
	  	global $pagenow;
		$focus_keyword = '';
		
		if ( 
			!is_admin() && 
			current_user_can('manage_options')
		   ):
		

			$focus_keyword = get_post_meta( get_the_ID(), '_yoast_wpseo_focuskw', true );
			$property = get_option('at_search_console_option', 'regular');
			$site_url = urlencode(get_site_url());
			$permalink = urlencode(get_permalink());

			
			if ( $property === 'regular') {
				// Regular Property:
				$href = 'https://search.google.com/search-console/performance/search-analytics?resource_id='.urlencode(get_site_url()).'%2F&metrics=CLICKS%2CPOSITION)&page=*' . urlencode(get_permalink()) . '&query=*' . $focus_keyword;
				
			}else{
		
				// Domain Property:
				$href = 'https://search.google.com/search-console/performance/search-analytics?resource_id=sc-domain%3A'.urlencode(str_replace('https://','',get_site_url())).'&metrics=CLICKS%2CPOSITION)&page=*' . urlencode(str_replace('https://','',get_permalink()));
			}

			$args = array( 
				'id'	=>		'view-gsc',
				'title'	=>	'View in Search Console',
				'meta' 	=> 	array('target' => '_blank' ),
				'href'	=>	$href
			);

			$admin_bar->add_menu( $args );
		
		endif;
	}
	

	public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=at-search-console' ) . '">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
	
	public function add_menu_page() {
		add_options_page(
		  'AT Search Console',
		  'AT Search Console',
		  'manage_options',
		  'at-search-console',
		  array($this, 'render_page')
		);
	}

	public function render_page() {
		if (isset($_POST['submit'])) {
		  update_option('at_search_console_option', $_POST['property']);
		}

		$property = get_option('at_search_console_option');
		?>
		  <div class="wrap">
			<img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/icon-256x256.png'; ?>" style="    width: 60px; padding-right: 12px; vertical-align: middle;">
			<h1>AT Search Console</h1>
			  
			  <p>If you have set up your domain as a regular property in Google Search Console, you are good to go. On the other hand, if you have set up your domain as a domain property in Google Search Console, you will need to select "Domain Property".</p>
			<form action="" method="post">
			  <table class="form-table">
				<tbody>
				  <tr>
					<th scope="row">Select:</th>
					<td>
					  <label>
						<input type="radio" name="property" value="regular" <?php echo $property !== 'domain' ? 'checked' : ''; ?>>
						Regular property
					  </label>
					  <br>
					  <label>
						<input type="radio" name="property" value="domain" <?php echo $property === 'domain' ? 'checked' : ''; ?>>
						Domain property
					  </label>
					</td>
				  </tr>
				</tbody>
			  </table>
			  <p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
			  </p>
			</form>
			  <p>Once you select your type of property, just visit any front page of your website and you will see the "View in Search Console" at the top black nav bar.  Click it and it will take you right to the performance page for the specific page.</p>
			  <img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/screenshot-1.png'; ?>" style="width:680px;">
		  </div>
		<?php
	}


	
}

$wp_search_console_class = new wp_search_console_class();
