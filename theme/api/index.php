<?php

// $ApiRequest = [ 'param1', 'param2', ... ]

switch($ApiRequest[0]) {
	case 'refresh-streams':
		rtc_load();
		fptc_load();
		$payload = ['status' => 'ok'];
		break;
	case 'echo':
		$payload = [
			'status' => 'ok',
			'data' => $ApiRequest
		];
		break;
}

sendPayload($payload);
