<?php
/***
**���ߣ� Doris
**���ڣ� 2013-8-3
**���ã� ������ҳ
**		 ������ҳ���ݶ��ᱣ����pages�ļ����
**       ������������ҳ���ڸ�����ҳ�ļ��е�versions�ļ����XX.html����ҳ�棬����css��js��img�ļ�����others�ļ����
**       XX_local.html��XX.html���޸İ棬�ļ��������·�����ĳ�ָ�򱾵ص��ļ������·��
**		 Ŀǰ���ļ��в㼶�ṹ�ǣ�pages/com.adobe.com/versions/v0/others/
**		 �����Ҫ�޸ı���·���Ĳ㼶�ṹ, ���Կ����޸�VERSIONS�� OTHERS�� V�� $foler_name ��$version�� $others����
***/


/******************************************************************��Ҫ����*******************************************************************************************/

//�����ⲿ�ļ�
include 'compare.php';


/**�����**/
define('VERSIONS', 'versions');//�������а��ļ����ļ�������
define('OTHERS', 'others');//���������ļ����ļ�������
define('V', 'v');//���浥һ�汾�ļ����ļ�������
 //DIRECTORY_SEPARATOR  ·��'/'  

/**ȫ�ֱ���**/

$url = "http://www.adobe.com/cn/";//��ҳurl
$parts = parse_url($url);//����url
$host = $parts['host'];//��ȡhostname
$main_file_init = basename($parts['path']);//��ȡpathname
$foler_name = preg_replace("/(\w+)\.(\w+)\.(\w+)/i", "$3.$2.$1", $host);
$foler_name = $main_file_init.".".$foler_name;//��ҳ�����ļ������֣������������壬��www.adobe.com/cn,���ļ�������Ϊcn.com.adobe.com
$version = "pages".DIRECTORY_SEPARATOR.$foler_name.DIRECTORY_SEPARATOR.VERSIONS.DIRECTORY_SEPARATOR.V."0"; //version·��: versions\v0 
$others = $version.DIRECTORY_SEPARATOR.OTHERS; //others·��: versions\v0\others

//��version�ļ���
createFolder($others);
echo $version;
echo $others;

$main_file = $main_file_init;
if(substr($main_file, -5)!=".html"){
	$main_file = $main_file.".html";
}
$local_file = $main_file_init."_local.html";
$str_file = file_get_contents($url);
file_put_contents($version.DIRECTORY_SEPARATOR.$main_file, $str_file);

saveFiles($str_file);

/********************************************************************���ֺ���************************************************************************************/
/**
*�������� createFolder
*���ã��������·��
**/
function createFolder($path)
{
   if (!file_exists($path))
   {
    createFolder(dirname($path));

    mkdir($path, 0777);
   }
}



/**
*�������� saveFiles
*���ã���str����ȡ���е�css��js��ͼƬ�ļ�·��������
*var: str  ���ҵ�Դ�ļ�
*return: �����ļ���·��
**/
function saveFiles($str){
	$str_new = $str;
	preg_match_all("/<link\s+.*href=\"([^\"]*)\".*>/",$str,$links, PREG_SET_ORDER);//links �ﱣ���˴�ҳ���ȡ������css�ļ���·��
	preg_match_all("/<script\s+.*src=\"([^\"]*)\".*>/",$str,$scripts, PREG_SET_ORDER);//scripts �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	preg_match_all("/<img\s+.*src=\"([^\"]*)\".*>/",$str,$images, PREG_SET_ORDER);//images �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	
	$arr_link_css = array(); //����css �ļ�����link
	$arr_filename_css = array(); //����css �ļ�������
	$arr_localpath_css = array();//����css �ļ����ش洢·��
	$arr_link_js = array(); //����js �ļ�����link
	$arr_filename_js = array(); //����js �ļ�������
	$arr_localpath_js = array();//����js �ļ����ش洢·��
	$arr_link_img = array(); //����img �ļ�����link
	$arr_filename_img = array(); //����img �ļ�������
	$arr_localpath_img = array();//����img �ļ����ش洢·��
	
	

	global $host;
	global $others;
	global $version;
	$localpath = OTHERS.DIRECTORY_SEPARATOR;

	//�洢css�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
	$count = 0;	
	foreach($links as $val){	
		$arr_link_css[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $links[$count][1] = "http://".$host.$val[1];
		}	
		$parts_css = parse_url($val[1]);
		$filname_css = basename($parts_css['path']);//��ȡpathname
		$arr_filename_css[$count] = $filname_css;
		$str_file_content = file_get_contents($val[1]);
		$arr_localpath_css[$count] = $localpath.$filname_css;
		file_put_contents($version.DIRECTORY_SEPARATOR.$arr_localpath_css[$count], $str_file_content);
		$count++;
	}


	//�洢js�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
	$count = 0;	
	foreach($scripts as $val){	
		$arr_link_js[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $scripts[$count][1] = "http://".$host.$val[1];
		}	
		$parts_js = parse_url($val[1]);
		$filname_js = basename($parts_js['path']);//��ȡpathname
		$arr_filename_js[$count] = $filname_js;
		$str_file_content = file_get_contents($val[1]);
		$arr_localpath_js[$count] = $localpath.$filname_js;
		file_put_contents($version.DIRECTORY_SEPARATOR.$arr_localpath_js[$count], $str_file_content);
		$count++;
	}

	//�洢img�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
	$count = 0;	
	foreach($images as $val){	
		$arr_link_img[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $images[$count][1] = "http://".$host.$val[1];
		}	
		$parts_img = parse_url($val[1]);
		$filname_img = basename($parts_img['path']);//��ȡpathname
		$arr_filename_img[$count] = $filname_img;
		$str_file_content = file_get_contents($val[1]);
		$arr_localpath_img[$count] = $localpath.$filname_img;
		file_put_contents($version.DIRECTORY_SEPARATOR.$arr_localpath_img[$count], $str_file_content);
		$count++;
	}


	//��html�ļ����css��js��img·������ָ�򱣴��·��
	global $local_file;
	$str_new = str_replace($arr_link_css, $arr_localpath_css, $str_new);
	$str_new = str_replace($arr_link_js, $arr_localpath_js, $str_new);
	$str_new = str_replace($arr_link_img, $arr_localpath_img, $str_new);
	file_put_contents($version.DIRECTORY_SEPARATOR.$local_file, $str_new);
}


?>