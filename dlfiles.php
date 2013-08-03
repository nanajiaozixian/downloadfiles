<?php
/***
**作者： Doris
**日期： 2013-8-3
**作用： 下载网页
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
$main_file = basename($parts['path']);//获取pathname
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

	preg_match_all("/<link\s+.*href=\"([^\"]*)\".*>/",$str,$links, PREG_SET_ORDER);//link 里保存了从页面获取的所有css文件的路径
	preg_match_all("/<script\s+.*src=\"([^\"]*)\".*>/",$str,$scripts, PREG_SET_ORDER);//script 里保存了从页面获取的所有js文件的路径
	preg_match_all("/<img\s+.*src=\"([^\"]*)\".*>/",$str,$images, PREG_SET_ORDER);//script 里保存了从页面获取的所有js文件的路径


	//如果是相对路径，则把它改成绝对路径
	//linkarr 是查找的路径的数组， type 是文件类型
	function addHostPath($linkarr){
		global $host;
		global $others;
		$count = 0;
		foreach($linkarr as $val){			
			if(strpos($val[1], "http:")!==0){
				
				$val[1] = $linkarr[$count][1] = "http://".$host.$val[1];
			}
	
			$parts_css = parse_url($val[1]);
			$css_file = basename($parts_css['path']);//获取pathname
			$str_file_content = file_get_contents($val[1]);
			file_put_contents($others.DIRECTORY_SEPARATOR.$css_file, $str_file_content);
			$count++;
		}
	}

	addHostPath($links);
	addHostPath($scripts);
	addHostPath($images);
	
}

?>