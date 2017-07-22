<?php

class RTCSettingsPage {

	private $options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_plugin_page() {
		add_submenu_page(
			'edit.php?post_type=rss',
			'Nastavení RSS',
			'Nastavení',
			'manage_options',
			'options',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		$this->options = get_option( 'rtc_option' );
		?>
		<div class="wrap">
			<h1>Nastavení RSS</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'rtc_option_group' );
				do_settings_sections( 'rtc-setting-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function page_init() {

		register_setting(
			'rtc_option_group',
			'rtc_option',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'rtc_setting_section',
			'',
			'',
			'rtc-setting-admin'
		);

		add_settings_field(
			'rss_url',
			'URL',
			array( $this, 'rss_url_callback' ),
			'rtc-setting-admin',
			'rtc_setting_section'
		);
	}

	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['rss_url'] ) ) {
			$new_input['rss_url'] = absint( $input['rss_url'] );
		}
		return $input;
	}

	public function rss_url_callback() {
		printf(
			'<input type="text" id="rss_url" name="rtc_option[rss_url]" value="%s" />',
			isset( $this->options['rss_url'] ) ? esc_attr( $this->options['rss_url'] ) : ''
		);
	}
}
