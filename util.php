<?php

function get_if_set($key, $arr, $default = null) {
	if (isset($arr[$key])) {
		return $arr[$key];
	}
	return $default;
}
