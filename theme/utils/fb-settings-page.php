<?php

class FPTCSettingsPage {

	private $options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_plugin_page() {
		add_submenu_page(
			'options-general.php',
			'Nastavení FB',
			'Nastavení FB',
			'manage_options',
			'options',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		$this->options = get_option( 'fptc_option' );
		?>
		<div class="wrap">
			<h1>Nastavení FB</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'fptc_option_group' );
				do_settings_sections( 'fptc-setting-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function page_init() {

		register_setting(
			'fptc_option_group',
			'fptc_option',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'fptc_setting_section',
			'',
			'',
			'fptc-setting-admin'
		);

		add_settings_field(
			'app_id',
			'App ID',
			array( $this, 'app_id_callback' ),
			'fptc-setting-admin',
			'fptc_setting_section'
		);

		add_settings_field(
			'app_secret',
			'App secret',
			array( $this, 'app_secret_callback' ),
			'fptc-setting-admin',
			'fptc_setting_section'
		);

		add_settings_field(
			'page_id',
			'Page ID',
			array( $this, 'page_id_callback' ),
			'fptc-setting-admin',
			'fptc_setting_section'
		);

		add_settings_field(
			'access_token',
			'Access token',
			array( $this, 'access_token_callback' ),
			'fptc-setting-admin',
			'fptc_setting_section'
		);
	}

	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['app_id'] ) ) {
			$new_input['app_id'] = absint( $input['app_id'] );
		}
		if ( isset( $input['app_secret'] ) ) {
			$new_input['app_secret'] = absint( $input['app_secret'] );
		}
		if ( isset( $input['page_id'] ) ) {
			$new_input['page_id'] = absint( $input['page_id'] );
		}
		if ( isset( $input['access_token'] ) ) {
			$new_input['access_token'] = absint( $input['access_token'] );
		}
		return $input;
	}

	public function app_id_callback() {
		printf(
			'<input type="text" id="app_id" name="fptc_option[app_id]" value="%s" />',
			isset( $this->options['app_id'] ) ? esc_attr( $this->options['app_id'] ) : ''
		);
	}

	public function app_secret_callback() {
		printf(
			'<input type="text" id="app_secret" name="fptc_option[app_secret]" value="%s" />',
			isset( $this->options['app_secret'] ) ? esc_attr( $this->options['app_secret'] ) : ''
		);
	}

	public function page_id_callback() {
		printf(
			'<input type="text" id="page_id" name="fptc_option[page_id]" value="%s" />',
			isset( $this->options['page_id'] ) ? esc_attr( $this->options['page_id'] ) : ''
		);
	}

	public function access_token_callback() {
		printf(
			'<input type="text" id="access_token" name="fptc_option[access_token]" value="%s" />',
			isset( $this->options['access_token'] ) ? esc_attr( $this->options['access_token'] ) : ''
		);
	}
}
