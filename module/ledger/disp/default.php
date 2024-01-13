<?php
if(!defined('__AFOX__')) exit();

function proc($data) {

	$type = is_numeric($data['ledger']) ? $data['ledger'] : '';
	$category = empty($data['category']) ? '' : $data['category'];
	$search = empty($data['search']) ? '' : $data['search'];
	$page = empty($data['page']) ? '' : $data['page'];

	$result = [];
	$result['tpl'] = 'list';
	$result['_DOCUMENT_LIST_'] = getDocumentList($page, $type, $category, $search);

	return $result;
}
