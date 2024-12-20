<?php

function get_if_set($key, $arr) {
	if (isset($arr[$key])) {
		return $arr[$key];
	}
	return null;
}
