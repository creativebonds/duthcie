<?php

class DutchieSettings {
	private $dutchie_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'dutchie_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'dutchie_page_init' ) );
	}

	public function dutchie_add_plugin_page() {
		add_options_page(
			'Dutchie', // page_title
			'Dutchie', // menu_title
			'manage_options', // capability
			'dutchie', // menu_slug
			array( $this, 'dutchie_create_admin_page' ) // function
		);
	}

	public function dutchie_create_admin_page() {
		$this->dutchie_options = get_option( 'dutchie_option_name' ); ?>

		<div class="wrap">
			<h2>Dutchie</h2>
			<p>Woocommerce Dutchie Integration</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'dutchie_option_group' );
					do_settings_sections( 'dutchie-admin' );
					submit_button();
				?>
			</form>
			
		</div>
	<?php }

	public function dutchie_page_init() {
		register_setting(
			'dutchie_option_group', // option_group
			'dutchie_option_name', // option_name
			array( $this, 'dutchie_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'dutchie_setting_section', // id
			'Settings', // title
			array( $this, 'dutchie_section_info' ), // callback
			'dutchie-admin' // page
		);

		add_settings_field(
			'dutchie_retailer_id', // id
			'Retailer ID', // title
			array( $this, 'dutchie_retailer_id_callback' ), // callback
			'dutchie-admin', // page
			'dutchie_setting_section' // section
		);

		add_settings_field(
			'dutchie_dispensary_id', // id
			'Dispensary ID', // title
			array( $this, 'dutchie_dispensary_id_callback' ), // callback
			'dutchie-admin', // page
			'dutchie_setting_section' // section
		);

		add_settings_field(
			'dutchie_api_url', // id
			'API URL', // title
			array( $this, 'dutchie_api_url_callback' ), // callback
			'dutchie-admin', // page
			'dutchie_setting_section' // section
		);

		add_settings_field(
			'dutchie_api_public_key', // id
			'Public Bearer Token', // title
			array( $this, 'dutchie_api_public_key_callback' ), // callback
			'dutchie-admin', // page
			'dutchie_setting_section' // section
		);
                add_settings_field(  
                        'dutchie_checkout_active',  
                        'Dutchie Checout',  
                        array( $this, 'dutchie_checkout_active_callback' ), // callback
                        'dutchie-admin', // page
                        'dutchie_setting_section'  
                    );

	}

	public function dutchie_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['dutchie_retailer_id'] ) ) {
			$sanitary_values['dutchie_retailer_id'] = sanitize_text_field( $input['dutchie_retailer_id'] );
		}

		if ( isset( $input['dutchie_dispensary_id'] ) ) {
			$sanitary_values['dutchie_dispensary_id'] = sanitize_text_field( $input['dutchie_dispensary_id'] );
		}

		if ( isset( $input['dutchie_api_url'] ) ) {
			$sanitary_values['dutchie_api_url'] = sanitize_text_field( $input['dutchie_api_url'] );
		}

		if ( isset( $input['dutchie_api_public_key'] ) ) {
			$sanitary_values['dutchie_api_public_key'] = sanitize_text_field( $input['dutchie_api_public_key'] );
		}
                if ( isset( $input['dutchie_checkout_active'] ) ) {
			$sanitary_values['dutchie_checkout_active'] = sanitize_text_field( $input['dutchie_checkout_active'] );
		}

		return $sanitary_values;
	}

	public function dutchie_section_info() {
		
	}

	public function dutchie_retailer_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="dutchie_option_name[dutchie_retailer_id]" id="dutchie_retailer_id" value="%s">',
			isset( $this->dutchie_options['dutchie_retailer_id'] ) ? esc_attr( $this->dutchie_options['dutchie_retailer_id']) : ''
		);
	}

	public function dutchie_dispensary_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="dutchie_option_name[dutchie_dispensary_id]" id="dutchie_dispensary_id" value="%s">',
			isset( $this->dutchie_options['dutchie_dispensary_id'] ) ? esc_attr( $this->dutchie_options['dutchie_dispensary_id']) : ''
		);
	}

	public function dutchie_api_url_callback() {
		printf(
			'<input class="regular-text" type="text" name="dutchie_option_name[dutchie_api_url]" id="dutchie_api_url" value="%s">',
			isset( $this->dutchie_options['dutchie_api_url'] ) ? esc_attr( $this->dutchie_options['dutchie_api_url']) : ''
		);
	}

	public function dutchie_api_public_key_callback() {
		printf(
			'<input class="regular-text" type="text" name="dutchie_option_name[dutchie_api_public_key]" id="dutchie_api_public_key" value="%s">',
			isset( $this->dutchie_options['dutchie_api_public_key'] ) ? esc_attr( $this->dutchie_options['dutchie_api_public_key']) : ''
		);
	}
        public  function dutchie_checkout_active_callback() {
            $options = $this->dutchie_options['dutchie_checkout_active'];
            $html = '<input type="checkbox" id="dutchie_checkout_active" name="dutchie_option_name[dutchie_checkout_active]" value="1"' . checked( 1, $options, false ) . '/>';
            $html .= '<label for="dutchie_checkout_active">Use Dutchie Checkout</label>';
            echo $html;
        }

}

