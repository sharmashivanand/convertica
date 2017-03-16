<?php

class Convertica_Import_Export extends Convertica_Settings_Base {
	
	function __construct() {
		
		$this->page_type = 'basic';
		$this->page_title      = __( 'Convertica Import / Export', 'convertica-td' );
		$this->menu_title      = __( 'Import / Export', 'convertica-td' );
		$this->slug            = 'convertica-import-export';
		
		$this->init();
				
		add_action( 'admin_init', array( $this, 'export' ) );
		add_action( 'admin_init', array( $this, 'import' ) );
		
		add_action( 'admin_notices', array( $this, 'import_notices' ) );
		
	}
	
	/* Adds custom meta boxes to the page. */
	function meta_boxes() {
		
		/* Add meta box for Import / Export settings */
		add_meta_box( 'convertica-import-export-box', __( 'Import / Export', 'convertica-td' ), array( $this, 'convertica_do_import_export' ), 'appearance_page_' . $this->slug, 'normal', 'high' );
		
	}
	
	function convertica_do_import_export(){
		?>
		<h4><?php printf( __( 'Export %s Settings', 'convertica-td' ), convertica_get_theme_name() ); ?></h4>
		<p><?php printf( __( 'Click the button below to export all of your theme settings to a data file that can be saved and later imported on another site using %s theme to clone these settings.' ), convertica_get_theme_name() ); ?></p>
		
		<form method="post" action="<?php echo menu_page_url( $this->slug, 0 ); ?>">
			<input type="hidden" name="convertica_export" value="convertica_export_settings" />
			<?php
			wp_nonce_field( 'convertica_settings_export', 'convertica_settings_export_nonce' );
			submit_button( __( 'Download Settings File', 'convertica-td' ), 'secondary', 'download' );
			?>
		</form>
		<hr>
		<h4><?php printf( __( 'Import %s Settings', 'convertica-td' ), convertica_get_theme_name() ); ?></h4>
		<p><?php printf( __( 'Upload the %s settings file (%s.json%s) and click on the button below to import all of your theme settings.', 'convertica-td' ), convertica_get_theme_name(), '<code>', '</code>' ); ?></p>
		<p><em><strong><?php _e( 'Note:', 'convertica-td' ) ?></strong> <?php _e( 'Importing settings will overwrite your current settings.', 'convertica-td' ); ?></em></p>
		
		<form enctype="multipart/form-data" method="post" action="<?php echo menu_page_url( $this->slug, 0 ); ?>">
			<?php wp_nonce_field( 'convertica-import-settings', 'convertica-import-settings-nonce' ); ?>
			<input type="hidden" name="convertica_import" value="convertica_import_settings" />
			<label for="convertica-import-settings-upload"><?php printf( __( 'Upload File (Maximum Size: %s): ', 'convertica-td' ), ini_get( 'post_max_size' ) ); ?></label>
			<input type="file" id="convertica-import-settings-upload" name="convertica-import-settings-upload" />
			<?php submit_button( __( 'Upload File and Import', 'convertica-td' ), 'secondary', 'upload' ); ?>
		</form>
		<?php
	}
	
	public function export() {
		
		if ( empty( $_POST['convertica_export'] ) || 'convertica_export_settings' != $_POST['convertica_export'] )
			return;
		
		check_admin_referer( 'convertica_settings_export', 'convertica_settings_export_nonce' );
		
		$prefix = 'convertica';
		
		$settings = get_option( CONVERTICA_SETTINGS );
		
		//clog($settings); die();
		
		if ( ! $settings )
			return;
		
		$output = wp_json_encode( (array) $settings );
		
		//clog($output); die();
		
		//* Prepare and send the export file to the browser
	    header( 'Content-Description: File Transfer' );
	    header( 'Cache-Control: public, must-revalidate' );
	    header( 'Pragma: hack' );
	    header( 'Content-Type: text/plain' );
	    header( 'Content-Disposition: attachment; filename="' . $prefix . '-' . date( 'Ymd-His' ) . '.json"' );
	    header( 'Content-Length: ' . mb_strlen( $output ) );
	    echo $output;
	    exit;
		
	}
	
	public function import() {
		
		if ( empty( $_POST['convertica_import'] ) || 'convertica_import_settings' != $_POST['convertica_import'] )
			return;
		
		check_admin_referer( 'convertica-import-settings', 'convertica-import-settings-nonce' );
		
		$uploaded_file = $_FILES['convertica-import-settings-upload']['tmp_name'];
		
		if ( empty( $uploaded_file ) ) {
			wp_die( __( 'Please upload a file to import' ) );
		}
		
		//clog($_FILES['convertica-import-settings-upload']['name']); die();
		
		$filename = explode( '.', $_FILES['convertica-import-settings-upload']['name'] );
		$extension = end( $filename );
		
		if( $extension != 'json' ) {
			wp_die( __( 'Please upload a valid .json file' ) );
		}
		
		$upload = file_get_contents( $uploaded_file );
		
		$settings = (array) json_decode( $upload, true );
		
		// Check for errors
		if ( ! $settings || $_FILES['convertica-import-settings-upload']['error'] ) {
			convertica_admin_redirect( $this->slug, array( 'error' => 'true' ) );
			exit;
		}
		
		update_option( CONVERTICA_SETTINGS, $settings );
		
		//* Redirect, add success flag to the URI
		convertica_admin_redirect( $this->slug, array( 'imported' => 'true' ) );
		exit;
		
	}
	
	public function import_notices() {
		
		if ( isset( $_REQUEST['imported'] ) && 'true' === $_REQUEST['imported'] ) {
			
			?>
			<div id="message" class="updated highlight">
				<p><strong><?php _e( 'Theme settings successfuly imported!', 'convertica-td' ); ?></strong></p>
			</div>
			<?php
			
		}
		
	}
	
}