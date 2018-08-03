<?php
/**
 * environmental check
 */
function env_check(&$envTtems) {
    $envTtems[] = array('name' => '操作系统', 'min' => '无限制', 'good' => 'linux', 'cur'=>PHP_OS, 'status' => 1);
    $envTtems[] = array('name' => 'PHP版本', 'min' => '5.4', 'good' => '5.4', 'cur' => PHP_VERSION, 'status'=>(PHP_VERSION < 5.4 ? 0:1));
	$tmp = function_exists('gd_info') ? gd_info() : array();
	preg_match("/[\d.]+/", $tmp['GD Version'],$match);
	unset($tmp);
    $envTtems[] = array('name' => 'GD库', 'min' => '2.0', 'good' => '2.0', 'cur' => $match[0], 'status' => ($match[0] < 2 ? 0:1));
    $envTtems[] = array('name' => '附件上传', 'min' => '未限制', 'good' => '2M','cur' => ini_get('upload_max_filesize'), 'status' => 1);
	$disk_place = function_exists('disk_free_space') ? floor(disk_free_space(CALFBB) / (1024*1024)) : 0;
    $envTtems[] = array('name' => '磁盘空间', 'min' => '100M', 'good' => '>100M','cur' => empty($disk_place) ? '未知' : $disk_place.'M', 'status' => $disk_place < 100 ? 0:1);
}
/**
 * file check
 */
function dirfile_check(&$dirfileTtems) {
	foreach($dirfileTtems as $key => $item) {
        $item_path = '/'.$item['path'];
		if($item['type'] == 'dir') {
			if(!dir_writeable(CALFBB.$item_path)) {
				if(is_dir(CALFBB.$item_path)) {
                    $dirfileTtems[$key]['status'] = 0;
                    $dirfileTtems[$key]['current'] = '+r';
				} else {
                    $dirfileTtems[$key]['status'] = -1;
                    $dirfileTtems[$key]['current'] = 'nodir';
				}
			} else {
                $dirfileTtems[$key]['status'] = 1;
                $dirfileTtems[$key]['current'] = '+r+w';
			}
		} else {
			if(file_exists(ROOT_PATH.$item_path)) {
				if(is_writable(ROOT_PATH.$item_path)) {
                    $dirfileTtems[$key]['status'] = 1;
                    $dirfileTtems[$key]['current'] = '+r+w';
				} else {
                    $dirfileTtems[$key]['status'] = 0;
                    $dirfileTtems[$key]['current'] = '+r';
				}
			} else {
				if ($fp = @fopen(ROOT_PATH.$item_path,'wb+')){
                    $dirfileTtems[$key]['status'] = 1;
                    $dirfileTtems[$key]['current'] = '+r+w';
					@fclose($fp);
					@unlink(ROOT_PATH.$item_path);
				}else {
                    $dirfileTtems[$key]['status'] = -1;
                    $dirfileTtems[$key]['current'] = 'nofile';
				}
			}
		}
	}
}
/**
 * dir is writeable
 * @return number
 */
function dir_writeable($dir) {
	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0755);
	}else {
		@chmod($dir,0755);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.txt");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}
/**
 * function is exist
 */
function function_check(&$funcItems) {
	$func = array();
	foreach($funcItems as $key => $item) {
        $funcItems[$key]['status'] = function_exists($item['name']) ? 1 : 0;
	}
}


function extension_check(&$extensionItems){

    foreach ($extensionItems as $key=>$item){
        $extensionItems[$key]['status'] = extension_loaded($item['name']) ? 1 : 0;
    }
}
function show_msg($msg){
	global $html_title,$html_header,$html_footer;
	include 'step_msg.php';
	exit();
}


