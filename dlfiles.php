<?php
/***
**���ߣ� Doris
**���ڣ� 2013-8-3
**���ã� ������ҳ
***/


/**�����**/
define('SAVEFILES', 'savefiles');//�����ļ����ļ�������
define('OTHERS', 'others');//���������ļ����ļ�������
 //DIRECTORY_SEPARATOR  ·��'/'  

/**ȫ�ֱ���**/
$others = SAVEFILES.DIRECTORY_SEPARATOR.OTHERS; 

$url = "http://www.adobe.com/cn/";//��ҳurl
$parts = parse_url($url);
$host = $parts['host'];//��ȡhostname
$main_file = basename($parts['path']);//��ȡpathname
$str_file = file_get_contents($url);
file_put_contents(SAVEFILES.DIRECTORY_SEPARATOR.$main_file, $str_file);
saveFiles($str_file);
//��savefiles�ļ���
if(!file_exists(SAVEFILES)){
	mkdir(SAVEFILES);
}

if(!file_exists($others)){
	mkdir($others);
}

/**
*�������� saveFiles
*���ã���str����ȡ���е�css��js��ͼƬ�ļ�·��������
*var: str  ���ҵ�Դ�ļ�
*return: �����ļ���·��
**/
function saveFiles($str){

	preg_match_all("/<link\s+.*href=\"([^\"]*)\".*>/",$str,$links, PREG_SET_ORDER);//link �ﱣ���˴�ҳ���ȡ������css�ļ���·��
	preg_match_all("/<script\s+.*src=\"([^\"]*)\".*>/",$str,$scripts, PREG_SET_ORDER);//script �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	preg_match_all("/<img\s+.*src=\"([^\"]*)\".*>/",$str,$images, PREG_SET_ORDER);//script �ﱣ���˴�ҳ���ȡ������js�ļ���·��


	//��������·����������ĳɾ���·��
	//linkarr �ǲ��ҵ�·�������飬 type ���ļ�����
	function addHostPath($linkarr){
		global $host;
		global $others;
		$count = 0;
		foreach($linkarr as $val){			
			if(strpos($val[1], "http:")!==0){
				
				$val[1] = $linkarr[$count][1] = "http://".$host.$val[1];
			}
	
			$parts_css = parse_url($val[1]);
			$css_file = basename($parts_css['path']);//��ȡpathname
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