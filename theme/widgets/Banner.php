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

		$meta = wp_get_attachment_metadata($id);
		$ratio = !empty($meta['width']) && !empty($meta['height']) ? round($meta['height'] / $meta['width'], 4) : 0.5;

		$link = $instance['link'] ?? '';
		$page = (int) ($instance['page'] ?? 0);
		if ($page && get_post_status($page) === 'publish') {
			$link = get_permalink($page);
		}

		echo $args['before_widget'];
		echo renderLatteToString(__DIR__ . '/../views/components/banner.latte', [
			'image' => $image,
			'link' => $link,
			'blank' => $this->isExternal($link),
			'ratio' => $ratio,
			// the link needs a name even when the image has no description
			'label' => empty($instance['alt']) ? ($page ? get_the_title($page) : null) : null,
			'rounded' => ($instance['style'] ?? 'shape') === 'rounded',
		]);
		echo $args['after_widget'];
	}

	/** Links to another host open in a new window. */
	private function isExternal($link) {
		$host = wp_parse_url($link, PHP_URL_HOST);
		$normalize = function($host) {
			return preg_replace('~^www\.~i', '', (string) $host);
		};
		return $host && strcasecmp($normalize($host), $normalize(wp_parse_url(home_url(), PHP_URL_HOST))) !== 0;
	}

	public function form($instance) {
		$image = (int) ($instance['image'] ?? 0);
		$link = $instance['link'] ?? '';
		$page = (int) ($instance['page'] ?? 0);
		$groups = [
			'Stránky' => get_pages(['sort_column' => 'post_title']),
			'Příspěvky' => get_posts(['numberposts' => 200, 'post_status' => 'publish']),
		];
		// keep the chosen item selectable even when it is older than the listed posts
		if ($page && !in_array($page, array_merge(...array_values(array_map(function($items) {
			return wp_list_pluck($items, 'ID');
		}, $groups))))) {
			$selected = get_post($page);
			if ($selected) {
				$groups[$selected->post_type === 'page' ? 'Stránky' : 'Příspěvky'][] = $selected;
			}
		}
		$alt = $instance['alt'] ?? '';
		$style = $instance['style'] ?? 'shape';
		$preview = $image ? wp_get_attachment_image_url($image, 'medium') : '';
		?>
		<div class="banner-widget">
			<p>
				<img class="banner-widget-preview" src="<?php echo esc_url($preview); ?>" style="max-width: 100%; height: auto;<?php echo $preview ? '' : ' display: none;'; ?>" alt="">
				<input class="banner-widget-id" type="hidden" id="<?php echo esc_attr($this->get_field_id('image')); ?>" name="<?php echo esc_attr($this->get_field_name('image')); ?>" value="<?php echo esc_attr($image); ?>">
				<button type="button" class="button banner-widget-pick">Vybrat obrázek</button>
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('page')); ?>">Odkaz na:</label>
				<select class="widefat banner-widget-page" id="<?php echo esc_attr($this->get_field_id('page')); ?>" name="<?php echo esc_attr($this->get_field_name('page')); ?>">
					<option value="0">Vlastní adresa</option>
					<?php foreach ($groups as $label => $items): ?>
						<optgroup label="<?php echo esc_attr($label); ?>">
							<?php foreach ($items as $item): ?>
								<option value="<?php echo esc_attr($item->ID); ?>"<?php selected($page, $item->ID); ?>><?php echo esc_html($item->post_title); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</p>
			<p class="banner-widget-link"<?php echo $page ? ' style="display: none;"' : ''; ?>>
				<label for="<?php echo esc_attr($this->get_field_id('link')); ?>">Adresa:</label>
				<input class="widefat" id="<?php echo esc_attr($this->get_field_id('link')); ?>" name="<?php echo esc_attr($this->get_field_name('link')); ?>" type="url" value="<?php echo esc_attr($link); ?>">
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('style')); ?>">Ohraničení:</label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
					<option value="shape"<?php selected($style, 'shape'); ?>>Tvar rámečku (SVG)</option>
					<option value="rounded"<?php selected($style, 'rounded'); ?>>Malé zaoblené ohraničení</option>
				</select>
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
			'page' => absint($new_instance['page'] ?? 0),
			'link' => esc_url_raw($new_instance['link'] ?? ''),
			'alt' => sanitize_text_field($new_instance['alt'] ?? ''),
			'style' => ($new_instance['style'] ?? '') === 'rounded' ? 'rounded' : 'shape',
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
			document.addEventListener('change', function (e) {
				if (!e.target.classList.contains('banner-widget-page')) {
					return;
				}
				var link = e.target.closest('.banner-widget').querySelector('.banner-widget-link');
				link.style.display = e.target.value === '0' ? '' : 'none';
			});
			document.addEventListener('click', function (e) {
				var button = e.target.closest('.banner-widget-pick');
				if (!button) {
					return;
				}
				e.preventDefault();
				var widget = button.closest('.banner-widget');
				var frame = wp.media({title: 'Vyber obrázek', library: {type: 'image'}, multiple: false});
				var notify = function (input) {
					input.dispatchEvent(new Event('input', {bubbles: true}));
					input.dispatchEvent(new Event('change', {bubbles: true}));
				};
				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
					var id = widget.querySelector('.banner-widget-id');
					var preview = widget.querySelector('.banner-widget-preview');
					var alt = widget.querySelector('input[name$="[alt]"]');
					id.value = attachment.id;
					notify(id);
					preview.src = url;
					preview.style.display = '';
					if (!alt.value && attachment.alt) {
						alt.value = attachment.alt;
						alt.dispatchEvent(new Event('input', {bubbles: true}));
					}
				});
				frame.open();
			});
		</script>
		<?php
	}

}

new BannerWidget();
