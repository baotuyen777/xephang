<?php
function pagerLink($url, $ext, $page, $title) {
	$url = $url . '?page=' . $page;
	if ($ext != '') {
		$url = $url . '&' . $ext;
	}
	return '<a href="' . $url . '">' . $title . '</a>&nbsp;';
}

function pagerLink_1($url, $ext, $page, $title, $catid) {
	$url = $url . '?page=' . $page;
	if ($ext != '') {
		$url = $url . '&' . $ext;
	}
	return '<a href="javascript:void(0)" onclick="loadpage('.$catid.','.$page.')">' . $title . '</a>&nbsp;';
}