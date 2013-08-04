<?php
/***
**作者： Doris
**日期： 2013-8-3
**作用： 下载网页
**       下载下来的主页存在savefiles文件夹里，XX.html是主页面，其他css、js、img文件存在others文件夹里。
**       XX_local.html是XX.html的修改版，文件里的所有路径都改成指向本地的文件的相对路径
***/


/**宏变量**/
define('SAVEFILES', 'savefiles');//保存文件的文件夹名字
define('OTHERS', 'others');//保存其它文件的文件夹名字
 //DIRECTORY_SEPARATOR  路径'/'  

/**全局变量**/
$others = SAVEFILES.DIRECTORY_SEPARATOR.OTHERS; 

$url = "http://www.adobe.com/cn/";//网页url
$parts = parse_url($url);
$host = $parts['host'];//获取hostname
$main_file_init = basename($parts['path']);//获取pathname
$main_file = $main_file_init;
if(substr($main_file, -5)!=".html"){
	$main_file = $main_file.".html";
}
$local_file = $main_file_init."_local.html";
$str_file = file_get_contents($url);
file_put_contents(SAVEFILES.DIRECTORY_SEPARATOR.$main_file, $str_file);
saveFiles($str_file);
//建savefiles文件夹
if(!file_exists(SAVEFILES)){
	mkdir(SAVEFILES);
}

if(!file_exists($others)){
	mkdir($others);
}

/**
*函数名： saveFiles
*作用：从str中提取所有的css，js，图片文件路径并下载
*var: str  查找的源文件
*return: 所有文件的路径
**/
function saveFiles($str){
	$str_new = $str;
	preg_match_all("/<link\s+.*href=\"([^\"]*)\".*>/",$str,$links, PREG_SET_ORDER);//links 里保存了从页面获取的所有css文件的路径
	preg_match_all("/<script\s+.*src=\"([^\"]*)\".*>/",$str,$scripts, PREG_SET_ORDER);//scripts 里保存了从页面获取的所有js文件的路径
	preg_match_all("/<img\s+.*src=\"([^\"]*)\".*>/",$str,$images, PREG_SET_ORDER);//images 里保存了从页面获取的所有js文件的路径
	
	$arr_link_css = array(); //保存css 文件完整link
	$arr_filename_css = array(); //保存css 文件的名字
	$arr_localpath_css = array();//保存css 文件本地存储路径
	$arr_link_js = array(); //保存js 文件完整link
	$arr_filename_js = array(); //保存js 文件的名字
	$arr_localpath_js = array();//保存js 文件本地存储路径
	$arr_link_img = array(); //保存img 文件完整link
	$arr_filename_img = array(); //保存img 文件的名字
	$arr_localpath_img = array();//保存img 文件本地存储路径
	
	

	global $host;
	global $others;
	$localpath = OTHERS.DIRECTORY_SEPARATOR;

	//存储css文件原来的地址、文件名和下载在本地的路径
	$count = 0;	
	foreach($links as $val){	
		$arr_link_css[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $links[$count][1] = "http://".$host.$val[1];
		}	
		$parts_css = parse_url($val[1]);
		$filname_css = basename($parts_css['path']);//获取pathname
		$arr_filename_css[$count] = $filname_css;
		$str_file_content = file_get_contents($val[1]);
		$arr_localpath_css[$count] = $localpath.$filname_css;
		file_put_contents(SAVEFILES.DIRECTORY_SEPARATOR.$arr_localpath_css[$count], $str_file_content);
		$count++;
	}


	//存储js文件原来的地址、文件名和下载在本地的路径
	$count = 0;	
	foreach($scripts as $val){	
		$arr_link_js[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $scripts[$count][1] = "http://".$host.$val[1];
		}	
		$parts_js = parse_url($val[1]);
		$filname_js = basename($parts_js['path']);//获取pathname
		$arr_filename_js[$count] = $filname_js;
		$str_file_content = file_get_contents($val[1]);
		$arr_localpath_js[$count] = $localpath.$filname_js;
		file_put_contents(SAVEFILES.DIRECTORY_SEPARATOR.$arr_localpath_js[$count], $str_file_content);
		$count++;
	}

	//存储img文件原来的地址、文件名和下载在本地的路径
	$count = 0;	
	foreach($images as $val){	
		$arr_link_img[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $images[$count][1] = "http://".$host.$val[1];
		}	
		$parts_img = parse_url($val[1]);
		$filname_img = basename($parts_img['path']);//获取pathname
		$arr_filename_img[$count] = $filname_img;
		$str_file_content = file_get_contents($val[1]);
		$arr_localpath_img[$count] = $localpath.$filname_img;
		file_put_contents(SAVEFILES.DIRECTORY_SEPARATOR.$arr_localpath_img[$count], $str_file_content);
		$count++;
	}


	//把html文件里的css、js、img路径更改指向保存的路径
	global $local_file;
	$str_new = str_replace($arr_link_css, $arr_localpath_css, $str_new);
	$str_new = str_replace($arr_link_js, $arr_localpath_js, $str_new);
	$str_new = str_replace($arr_link_img, $arr_localpath_img, $str_new);
	file_put_contents(SAVEFILES.DIRECTORY_SEPARATOR.$local_file, $str_new);
}


?>