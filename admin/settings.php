<?php
class adsforwp_admin_settings{
    
    private $_imported_current_status = false;
    
public function __construct() {
      add_action( 'admin_menu', array($this, 'adsforwp_add_menu_links'));        
      add_action('admin_init', array($this, 'adsforwp_settings_init'));
    }
public function adsforwp_add_menu_links() {	
	// Settings page - Same as main menu page
	add_submenu_page( 'edit.php?post_type=adsforwp',
                esc_html__( 'Ads for wp', 'ads-for-wp' ),
                esc_html__( 'Settings', 'ads-for-wp' ),
                'manage_options',
                'adsforwp',
                array($this, 'adsforwp_admin_interface_render'));	
}


public function adsforwp_admin_interface_render(){
    
	// Authentication
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}     	       
	// Handing save settings
	if ( isset( $_GET['settings-updated'] ) ) {	
            $settings = adsforwp_defaultSettings();  
            if(isset($settings['advnc_ads_import_check'])){
                $common_function_obj = new adsforwp_admin_common_functions();
                $result = $common_function_obj->adsforwp_import_all_advanced_ads(); 
                $this->_imported_current_status = $result;
            }
            $file_creation = new adsforwp_file_creation();
            if(isset($settings['ad_blocker_support'])){                
                $result = $file_creation->adsforwp_create_adblocker_support_js();                
            }else{
                $result = $file_creation->adsforwp_delete_adblocker_support_js(); 
            }
            
		settings_errors();
	}
	       $tab = adsforwp_get_tab('dashboard', array('dashboard','general', 'support'));
        
	?>
		                            
		<h1><?php echo esc_html__('Ads for wp', 'ads-for-wp'); ?></h1>
		<h2 class="nav-tab-wrapper adsforwp-tabs">
			<?php

			echo '<a href="' . esc_url(adsforwp_admin_link('dashboard')) . '" class="nav-tab ' . esc_attr( $tab == 'dashboard' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-dashboard"></span> ' . esc_html__('Dashboard', 'ads-for-wp') . '</a>';

			echo '<a href="' . esc_url(adsforwp_admin_link('general')) . '" class="nav-tab ' . esc_attr( $tab == 'general' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-welcome-view-site"></span> ' . esc_html__('General','ads-for-wp') . '</a>';
                        
                        echo '<a href="' . esc_url(adsforwp_admin_link('support')) . '" class="nav-tab ' . esc_attr( $tab == 'support' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-welcome-view-site"></span> ' . esc_html__('Support','ads-for-wp') . '</a>';
			
			?>
		</h2>
                <form action="options.php" method="post" enctype="multipart/form-data" class="adsforwp-settings-form">		
			<div class="form-wrap">
			<?php
			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'adsforwp_setting_dashboard_group' );						
			
			echo "<div class='adsforwp-dashboard' ".( $tab != 'dashboard' ? 'style="display:none;"' : '').">";
			// Status
			do_settings_sections( 'adsforwp_dashboard_section' );	// Page slug
			echo "</div>";

			echo "<div class='adsforwp-general' ".( $tab != 'general' ? 'style="display:none;"' : '').">";
				// general Application Settings
		        do_settings_sections( 'adsforwp_general_section' );	// Page slug
			echo "</div>";
                        
                        echo "<div class='adsforwp-support' ".( $tab != 'support' ? 'style="display:none;"' : '').">";
				// general Application Settings
		        do_settings_sections( 'adsforwp_support_section' );	// Page slug
			echo "</div>";

			?>
                        </div>
			<div class="button-wrapper">                            
				<?php
				// Output save settings button
			submit_button( esc_html__('Save', 'ads-for-wp') );
				?>
			</div>
		</form>
	</div>
        
	<?php
           
}
/*
	WP Settings API
*/
public function adsforwp_settings_init(){
	register_setting( 'adsforwp_setting_dashboard_group', 'adsforwp_settings' );
	add_settings_section('adsforwp_dashboard_section', 'Import', '__return_false', 'adsforwp_dashboard_section');		        
                if ( is_plugin_active('advanced-ads/advanced-ads.php')) {
                    
                  
                // the meta_key 'diplay_on_homepage' with the meta_value 'true'
                    $cc_args = array(
                        'posts_per_page'   => -1,
                        'post_type'        => 'adsforwp',
                        'meta_key'         => 'imported_from',
                        'meta_value'         => 'advance_ads',
                    );
                    $imported_from = new WP_Query( $cc_args );   
                    if($imported_from->post_count ==0){
                    add_settings_field(
                            'adsforwp_import_status',								// ID
                            'Advance Ads',			// Title
                             array($this, 'adsforwp_import_callback'),					// Callback
                            'adsforwp_dashboard_section',							// Page slug
                            'adsforwp_dashboard_section'							// Settings Section ID
                    );              
                    } 
                    
              add_settings_section('adsforwp_general_section', 'Settings', '__return_false', 'adsforwp_general_section');		              
                    add_settings_field(
                            'adsforwp_ad_blocker_support',								// ID
                            'Ad Blocker Support',			// Title
                             array($this, 'adsforwp_ad_blocker_support_callback'),					// Callback
                            'adsforwp_general_section',							// Page slug
                            'adsforwp_general_section'							// Settings Section ID
                    );
                 } 
               add_settings_section('adsforwp_general_section', 'Settings', '__return_false', 'adsforwp_general_section');		              
                    add_settings_field(
                            'adsforwp_ad_revenue_sharing',								// ID
                            '',			// Title
                             array($this, 'adsforwp_ad_revenue_sharing_callback'),					// Callback
                            'adsforwp_general_section',							// Page slug
                            'adsforwp_general_section'							// Settings Section ID
                    );   
                    
                add_settings_section('adsforwp_support_section', 'Contact Us', '__return_false', 'adsforwp_support_section');		              
                    add_settings_field(
                            'adsforwp_contact_us_form',								// ID
                            '',			// Title
                             array($this, 'adsforwp_contact_us_form_callback'),					// Callback
                            'adsforwp_support_section',							// Page slug
                            'adsforwp_support_section'							// Settings Section ID
                    );       
                          		               
}

public function adsforwp_import_callback(){
	        
	$settings = adsforwp_defaultSettings();          
        ?>	
	<fieldset>
            <?php
            if($this->_imported_current_status){
             echo '<p>'.esc_html__('Imported Successfully', 'ads-for-wp').'</p>';   
            }else{
            if(isset($settings['advnc_ads_import_check'])){
                echo '<input type="checkbox" name="adsforwp_settings[advnc_ads_import_check]" class="regular-text afw_advnc_ads_import" value="1" checked> ';
            }else{
                echo '<input type="checkbox" name="adsforwp_settings[advnc_ads_import_check]" class="regular-text afw_advnc_ads_import" value="1" >';
            }    
            }
            
            ?>
		
	</fieldset>

	<?php
        
}
public function adsforwp_ad_blocker_support_callback(){
	        
	$settings = adsforwp_defaultSettings();           
        ?>	
	<fieldset>
            <?php
          
            if(isset($settings['ad_blocker_support'])){
                echo '<input type="checkbox" name="adsforwp_settings[ad_blocker_support]" class="regular-text afw_advnc_ad_blocker_support" value="1" checked> ';
            }else{
                echo '<input type="checkbox" name="adsforwp_settings[ad_blocker_support]" class="regular-text afw_advnc_ad_blocker_support" value="1" >';
            }
           
            ?>
		
	</fieldset>

	<?php
        
}
public function adsforwp_ad_revenue_sharing_callback(){	        
	$settings = adsforwp_defaultSettings();   
        
        ?>	
	<fieldset>
            <?php
           
            if(isset($settings['ad_revenue_sharing'])){
                echo '<input type="checkbox" name="adsforwp_settings[ad_revenue_sharing]" class="regular-text afw_ad_revenue_sharing" value="1" checked> ';
            }else{
                echo '<input type="checkbox" name="adsforwp_settings[ad_revenue_sharing]" class="regular-text afw_ad_revenue_sharing" value="1" >';
            }
            
            ?>		
	</fieldset>
        <div class="afw_revenue_divider"><p><?php echo esc_html__('How do you want to share this revenue (In every Minutes)', 'ads-for-wp') ?></p>
            <strong><?php echo esc_html__('Owner', 'ads-for-wp') ?></strong> <input type="number" placeholder="percentage" id="adsforwp_owner_revenue_per" name="adsforwp_settings[ad_owner_revenue_per]" value="<?php echo isset( $settings['ad_owner_revenue_per'] ) ? esc_attr( $settings['ad_owner_revenue_per']) : ''; ?>">
           <strong><?php echo esc_html__('Author', 'ads-for-wp') ?></strong> <input type="number"  placeholder="percentage" id="adsforwp_author_revenue_per" name="adsforwp_settings[ad_author_revenue_per]" value="<?php echo isset( $settings['ad_author_revenue_per'] ) ? esc_attr( $settings['ad_author_revenue_per']) : ''; ?>">
        </div>
	<?php        
}

public function adsforwp_contact_us_form_callback(){	        	        
        ?>		
        
        <div class="afw_contact_us_div">
        <strong><?php echo esc_html__('If you have any query, please write the query in below box. We will reply to your email address shortly', 'ads-for-wp') ?></strong>
       
            <ul>
                <li>
                    <textarea rows="5" cols="60" id="adsforwp_query_message" name="adsforwp_query_message"> </textarea>
                    <br>
                    <span class="afw-query-success afw_hide"><?php echo esc_html__('Message sent successfully, Please wait we will get back to you shortly', 'ads-for-wp'); ?></span>
                    <span class="afw-query-error afw_hide"><?php echo esc_html__('Message not sent. please check your network connection', 'ads-for-wp'); ?></span>
                </li> 
                <li><button class="button afw-send-query"><?php echo esc_html('Send Message', 'ads-for-wp'); ?></button></li>
            </ul>            
                   
        </div>
	<?php        
}
    
}
if (class_exists('adsforwp_admin_settings')) {
	new adsforwp_admin_settings;
};








/**
 * Enqueue CSS and JS
 */
