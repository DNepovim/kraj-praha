<?php

/**
 * Image with a link. The image is picked from the media library, so it is delivered in responsive sizes.
 */
class BannerWidget extends WP_Widget {

	function __construct() {
		parent::__construct('banner', 'Banner', ['description' => 'Obrázek s odkazem']);
		add_action('widgets_init', function() {
			register_widget('BannerWidget');
		});
		add_action('admin_enqueue_scripts', function($hook) {
			if ($hook === 'widgets.php') {
				wp_enqueue_media();
			}
		});
		add_action('customize_controls_enqueue_scripts', 'wp_enqueue_media');
		add_action('admin_print_footer_scripts', [$this, 'printScript']);
		add_action('customize_controls_print_footer_scripts', [$this, 'printScript']);
	}

	public function widget($args, $instance) {
		$id = (int) ($instance['image'] ?? 0);
		if (!$id) {
			return;
		}

		$attrs = [
			'class' => 'thumbnail__image',
			// rendered width of the sidebar: 91vw up to 500px, 70vw up to 992px, 291.33px above
			'sizes' => '(min-width: 992px) 291px, (min-width: 500px) 70vw, 91vw',
			'loading' => 'lazy',
		];
		if (!empty($instance['alt'])) {
			$attrs['alt'] = $instance['alt'];
		}
		$image = wp_get_attachment_image($id, 'banner_600', false, $attrs);
		if (!$image) {
			return;
		}

		echo $args['before_widget'];
		echo renderLatteToString(__DIR__ . '/../views/components/banner.latte', [
			'image' => $image,
			'link' => $instance['link'] ?? '',
			'blank' => $this->isExternal($instance['link'] ?? ''),
		]);
		echo $args['after_widget'];
	}

	/** Links to another host open in a new window. */
	private function isExternal($link) {
		$host = wp_parse_url($link, PHP_URL_HOST);
		return $host && strcasecmp($host, wp_parse_url(home_url(), PHP_URL_HOST)) !== 0;
	}

	public function form($instance) {
		$image = (int) ($instance['image'] ?? 0);
		$link = $instance['link'] ?? '';
		$alt = $instance['alt'] ?? '';
		$preview = $image ? wp_get_attachment_image_url($image, 'medium') : '';
		?>
		<div class="banner-widget">
			<p>
				<img class="banner-widget-preview" src="<?php echo esc_url($preview); ?>" style="max-width: 100%; height: auto;<?php echo $preview ? '' : ' display: none;'; ?>" alt="">
				<input class="banner-widget-id" type="hidden" id="<?php echo esc_attr($this->get_field_id('image')); ?>" name="<?php echo esc_attr($this->get_field_name('image')); ?>" value="<?php echo esc_attr($image); ?>">
				<button type="button" class="button banner-widget-pick">Vybrat obrázek</button>
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('link')); ?>">Odkaz:</label>
				<input class="widefat" id="<?php echo esc_attr($this->get_field_id('link')); ?>" name="<?php echo esc_attr($this->get_field_name('link')); ?>" type="url" value="<?php echo esc_attr($link); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('alt')); ?>">Popis obrázku (pro čtečky a vyhledávače):</label>
				<input class="widefat" id="<?php echo esc_attr($this->get_field_id('alt')); ?>" name="<?php echo esc_attr($this->get_field_name('alt')); ?>" type="text" value="<?php echo esc_attr($alt); ?>">
			</p>
		</div>
		<?php
	}

	public function update($new_instance, $old_instance) {
		return [
			'image' => absint($new_instance['image'] ?? 0),
			'link' => esc_url_raw($new_instance['link'] ?? ''),
			'alt' => sanitize_text_field($new_instance['alt'] ?? ''),
		];
	}

	public function printScript() {
		static $printed = false;
		if ($printed || !wp_script_is('media-editor', 'enqueued') && !did_action('wp_enqueue_media')) {
			return;
		}
		$printed = true;
		?>
		<script>
			jQuery(function ($) {
				$(document).on('click', '.banner-widget-pick', function (e) {
					e.preventDefault();
					var $widget = $(this).closest('.banner-widget');
					var frame = wp.media({title: 'Vyber obrázek', library: {type: 'image'}, multiple: false});
					frame.on('select', function () {
						var attachment = frame.state().get('selection').first().toJSON();
						var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
						$widget.find('.banner-widget-id').val(attachment.id).trigger('input').trigger('change');
						$widget.find('.banner-widget-preview').attr('src', url).show();
						var $alt = $widget.find('input[name$="[alt]"]');
						if (!$alt.val() && attachment.alt) {
							$alt.val(attachment.alt).trigger('input');
						}
					});
					frame.open();
				});
			});
		</script>
		<?php
	}

}

new BannerWidget();
