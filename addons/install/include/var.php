<?php
$envTtems = array();
$dirfileTtems = array(
		array('type' => 'dir', 'path' => 'data'),
        array('type' => 'dir', 'path' => 'data/cache'),
        array('type' => 'dir', 'path' => 'attachment'),
        array('type' => 'dir', 'path' => 'attachment/images'),
);

$funcItems = array(
		array('name' => 'mysqli_connect'),
		array('name' => 'fsockopen'),
		array('name' => 'gethostbyname'),
		array('name' => 'file_get_contents'),
		array('name' => 'mb_convert_encoding'),
		array('name' => 'json_encode'),
		array('name' => 'curl_init'),
);

$extensionItems = array(
    array('name' => 'pdo'),
    array('name' => 'curl'),
    array('name' => 'mysqli'),
    array('name' => 'pdo_mysql'),
);