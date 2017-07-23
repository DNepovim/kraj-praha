<?php

// $ApiRequest = [ 'param1', 'param2', ... ]

switch($ApiRequest[0]) {
	case 'refresh-streams':
		rtc_load();
		fptc_load();
		$payload = ['status' => 'ok'];
		break;
	case 'editpost':
		$adminurl = admin_url('/post.php?post=' . $ApiRequest[1] . '&action=edit');
		if (is_user_logged_in()) {
			wp_redirect($adminurl);
			exit;
		} else {
			wp_redirect(add_query_arg('redirect_to', urlencode($adminurl), home_url(get_option('aio_wp_security_configs')['aiowps_login_page_slug'])));
			exit;
		}
		break;
}

sendPayload($payload);
