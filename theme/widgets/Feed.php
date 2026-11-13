<?php

class FeedWidget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'facebook',
			'Feed'
		);
		add_action( 'widgets_init', function() {
			register_widget('FeedWidget');
		});
	}

	public $args = array(
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
		'before_widget' => '<div class="widget-wrap">',
		'after_widget'  => '</div></div>'
	);


	private function getFacebookPosts($limit = 5) {
		$options = get_option('fptc_option');

		if (!($options['app_id'] && $options['app_secret'] && $options['page_id'] && $options['access_token'])) {
			return false;
		}

		$fb = new Facebook\Facebook( [
			'app_id'                => $options['app_id'],
			'app_secret'            => $options['app_secret'],
			'default_graph_version' => 'v2.8',
		] );

		try {
			$response = $fb->get( '/' . $options['page_id'] . '/posts?locale=cs_CZ&fields=id,message,story&limit=' . $limit, $options['access_token'] );
			$data = $response->getDecodedBody()['data'];
			$posts = array_map(function ($post) {
				return [
					"link" => "https://www.facebook.com/" . $post['id'],
					"content" => empty($post["story"]) ? $post["message"] : $post["message"] . " — " . $post["story"]
				];
			}, $data);
			return $posts;
		} catch ( Facebook\Exceptions\FacebookResponseException $e ) {
			// TODO: Error handling
			return false;
		} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
			// TODO: Error handling
			return false;
		}
	}

	private function getRssPosts($limit = 5) {
		$cacheKey = 'feed_widget_rss_posts';
		$cached = get_transient($cacheKey);
		if ($cached !== false) {
			return array_slice($cached, 0, $limit);
		}

		$result = [];
		$response = wp_remote_get('https://zpravodajstvi.skaut.cz/feed', ['timeout' => 5]);
		if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
			$previous = libxml_use_internal_errors(true);
			$rss = simplexml_load_string(wp_remote_retrieve_body($response));
			libxml_clear_errors();
			libxml_use_internal_errors($previous);
		} else {
			$rss = false;
		}

		if (empty($rss)) {
			// Cache the failure briefly so a down feed isn't hit on every request
			set_transient($cacheKey, [], 5 * MINUTE_IN_SECONDS);
			return [];
		}

		$index = 0;
		foreach ($rss->channel->item as $item) {
			$result[$index] = [
				"content" => (string) $item->title,
				"link" => (string) $item->link
			];
			$index++;
			if ($index == $limit) {
				break;
			}
		}

		set_transient($cacheKey, $result, 15 * MINUTE_IN_SECONDS);

		return $result;
	}


	public function widget($args, $instance) {
		if (!empty($instance["source"])) {
			$posts = $instance["source"] == "fb" ? $this->getFacebookPosts() : $this->getRssPosts();
			echo renderLatteToString(__DIR__ . '/../views/components/articlesBox.latte', [
				"posts" => $posts,
				"title" => $instance["title"],
				"link" => $instance["link"],
				"theme" => $instance["theme"]
			]);
		}
	}

	public function form($instance) {

		$title = !empty($instance['title'] ) ? $instance['title'] : esc_html__('', 'text_domain');
		$link = !empty($instance['link'] ) ? $instance['link'] : esc_html__('', 'text_domain');
		$theme = !empty($instance['theme'] ) ? $instance['theme'] : "green";
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Nadpis:', 'text_domain'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('link')); ?>"><?php echo esc_html__('Odkaz:', 'text_domain'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('link')); ?>" name="<?php echo esc_attr($this->get_field_name('link')); ?>" type="text" value="<?php echo esc_attr($link); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('theme')); ?>"><?php echo esc_html__('Téma:', 'text_domain'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('theme')); ?>" name="<?php echo esc_attr($this->get_field_name('theme')); ?>"  value="<?php echo esc_attr($link); ?>">
				<option value="green">Zelené</option>
				<option value="blue">Modré</option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('source')); ?>"><?php echo esc_html__('Zdroj:', 'text_domain'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('source')); ?>" name="<?php echo esc_attr($this->get_field_name('source')); ?>"  value="<?php echo esc_attr($source); ?>">
				<option value="fb">Facebook</option>
				<option value="rss">Křižovatke</option>
			</select>
		</p>
		<?php

	}

	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = (!empty( $new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
		$instance['link'] = (!empty( $new_instance['link'] )) ? $new_instance['link'] : '';
		$instance['theme'] = (!empty( $new_instance['theme'] )) ? $new_instance['theme'] : 'green';
		$instance['source'] = (!empty( $new_instance['source'] )) ? $new_instance['source'] : 'fb';

		return $instance;
	}

}
$facebookWidget = new FeedWidget();
