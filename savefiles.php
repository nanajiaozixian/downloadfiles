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


/**�����**/
define('VERSIONS', 'versions');//�������а��ļ����ļ�������
define('OTHERS', 'others');//���������ļ����ļ�������
define('V', 'v');//���浥һ�汾�ļ����ļ�������
define('TEMP', 'temporary');//������ʱ�ļ�
 //DIRECTORY_SEPARATOR  ·��'/'  

/**ȫ�ֱ���**/

$v = 3;//�汾�� 
$url = "http://www.adobe.com/cn/";//��ҳurl ��������������������ע�⣬�����ϴ���ʱ���������Ӧ���Ǵ�ǰ�˴����ġ�
$parts = parse_url($url);//����url
$host = $parts['host'];//��ȡhostname
$main_file_init = basename($parts['path']);//��ȡpathname
$foler_name = preg_replace("/(\w+)\.(\w+)\.(\w+)/i", "$3.$2.$1", $host);
$foler_name = $main_file_init.".".$foler_name;//��ҳ�����ļ������֣������������壬��www.adobe.com/cn,���ļ�������Ϊcn.com.adobe.com
$version_template = "pages".DIRECTORY_SEPARATOR.$foler_name.DIRECTORY_SEPARATOR.VERSIONS.DIRECTORY_SEPARATOR.V;
$version = $version_template.$v; //version·��: versions\v0 
$others = $version.DIRECTORY_SEPARATOR.OTHERS; //others·��: versions\v0\others

//��version�ļ���
createFolder($others);
createFolder(TEMP);

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
*�������� saveFiles
*���ã���str����ȡ���е�css��js��ͼƬ�ļ�·��������
*var: str  ���ҵ�Դ�ļ�
*return: �����ļ���·��
**/
function saveFiles($str){
	$str_new = saveCSSFiles($str);
	$str_new = saveJSFiles($str_new);
	$str_new = saveIMGFiles($str_new);
	global $local_file;
	global $version;
	file_put_contents($version.DIRECTORY_SEPARATOR.$local_file, $str_new);
	recursive_delete(TEMP.DIRECTORY_SEPARATOR);//ɾ����ʱ�ļ�������ļ�
}

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
**��������isFileExist
**���Ҿɰ汾��ĳ�ļ��Ƿ����
**var $filename: �ļ���
**����ֵ  �����򷵻ؾɰ汾�ţ������ڷ���false;
**/
function isFileExist($filename){
	global $version_template;
	global $v;
	$old_v = $v-1;
	$filepath;
	for(;$old_v>=0; $old_v--){
		$temppath = $version_template.$old_v.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filename;
		
		if(file_exists($temppath)){
			echo "temppath: $temppath<br/>";
			return $old_v;
		}
	}
	return false;
}


/**
**��������saveCSSFiles
**�洢css�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
**var $str: �ļ��ı�
**����ֵ  �������޸Ĺ�·�����ı���
**/
function saveCSSFiles($str){
	global $host;
	global $others;
	global $version;
	global $version_template;
	$localpath = OTHERS.DIRECTORY_SEPARATOR;
	$arr_link_css = array(); //����css �ļ�����link
	$arr_filename_css = array(); //����css �ļ�������
	$arr_localpath_css = array();//����css �ļ����ش洢·��
	preg_match_all("/<link\s+.*href=\"([^\"]*)\".*>/",$str,$links, PREG_SET_ORDER);//links �ﱣ���˴�ҳ���ȡ������css�ļ���·��
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
		$newfilepath = $version.DIRECTORY_SEPARATOR.$localpath.$filname_css;
		$arr_localpath_css[$count] = $localpath.$filname_css;
		
		//����ɰ汾�в����ڸ��ļ�����ֱ�����ظ��ļ�
		$old_version = isFileExist($filname_css);
		$oldfilepath = "";
		if($old_version === false){
			file_put_contents($newfilepath, $str_file_content);
		}else{
			$oldfilepath = $version_template.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_css;
			$tempfilepath = TEMP.DIRECTORY_SEPARATOR.$filname_css;
			file_put_contents($tempfilepath, $str_file_content);
			echo "oldfilepath: $oldfilepath<br/>tempfilepath: $tempfilepath<br/>";
			if(!compare($oldfilepath, $tempfilepath)){
				file_put_contents($newfilepath, $str_file_content);
			}else{
				$arr_localpath_css[$count] = "..".DIRECTORY_SEPARATOR.V.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_css;
				
			}
		}
		$count++;
	}
	
	//��html�ļ����css·������ָ�򱣴��·��
	$str_new = $str;
	$str_new = str_replace($arr_link_css, $arr_localpath_css, $str_new);
	return $str_new;
}

/**
**��������saveJSFiles
**�洢js�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
**var $str: �ļ��ı�
**����ֵ  �������޸Ĺ�·�����ı���
**/
function saveJSFiles($str){
	global $host;
	global $others;
	global $version;
	global $version_template;
	$localpath = OTHERS.DIRECTORY_SEPARATOR;
	$arr_link_js = array(); //����js �ļ�����link
	$arr_filename_js = array(); //����js �ļ�������
	$arr_localpath_js = array();//����js �ļ����ش洢·��
	$count = 0;	


	preg_match_all("/<script\s+.*src=\"([^\"]*)\".*>/",$str,$scripts, PREG_SET_ORDER);//scripts �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	//�洢js�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
	
	foreach($scripts as $val){	
		$arr_link_js[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){
			
			$val[1] = $scripts[$count][1] = "http://".$host.$val[1];
		}	
		$parts_js = parse_url($val[1]);
		$filname_js = basename($parts_js['path']);//��ȡpathname
		$arr_filename_js[$count] = $filname_js;
		$str_file_content = file_get_contents($val[1]);
		$newfilepath = $version.DIRECTORY_SEPARATOR.$localpath.$filname_js;
		$arr_localpath_js[$count] = $localpath.$filname_js;

		//����ɰ汾�в����ڸ��ļ�����ֱ�����ظ��ļ�
		$old_version = isFileExist($filname_js);	
		$oldfilepath = "";
		if($old_version === false){
			file_put_contents($newfilepath, $str_file_content);
		}else{
			$oldfilepath = $version_template.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_js;
			$tempfilepath = TEMP.DIRECTORY_SEPARATOR.$filname_js;
			file_put_contents($tempfilepath, $str_file_content);
			if(!compare($oldfilepath, $tempfilepath)){
				file_put_contents($newfilepath, $str_file_content);
			}else{
				$arr_localpath_js[$count] = "..".DIRECTORY_SEPARATOR.V.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_js;
				
			}
		}
		$count++;
	}

	//��html�ļ����js·������ָ�򱣴��·��
	$str_new = $str;
	$str_new = str_replace($arr_link_js, $arr_localpath_js, $str_new);
	return $str_new;
}


/**
**��������saveIMGFiles
**�洢img�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
**var $str: �ļ��ı�
**����ֵ  �������޸Ĺ�·�����ı���
**/
function saveIMGFiles($str){
	global $host;
	global $others;
	global $version;
	global $version_template;
	$localpath = OTHERS.DIRECTORY_SEPARATOR;
	$arr_link_img = array(); //����img �ļ�����link
	$arr_filename_img = array(); //����img �ļ�������
	$arr_localpath_img = array();//����img �ļ����ش洢·��
	$count = 0;	

	preg_match_all("/<img\s+.*src=\"([^\"]*)\".*>/",$str,$images, PREG_SET_ORDER);//images �ﱣ���˴�ҳ���ȡ������js�ļ���·��
	//�洢img�ļ�ԭ���ĵ�ַ���ļ����������ڱ��ص�·��
	
	foreach($images as $val){	
		$arr_link_img[$count] = $val[1];
		if(strpos($val[1], "http:")!==0){		
			$val[1] = $images[$count][1] = "http://".$host.$val[1];
		}	
		$parts_img = parse_url($val[1]);
		$filname_img = basename($parts_img['path']);//��ȡpathname
		$arr_filename_img[$count] = $filname_img;
		$str_file_content = file_get_contents($val[1]);
		$newfilepath = $version.DIRECTORY_SEPARATOR.$localpath.$filname_img;
		$arr_localpath_img[$count] = $localpath.$filname_img;

		//����ɰ汾�в����ڸ��ļ�����ֱ�����ظ��ļ�
		$old_version = isFileExist($filname_img);
		$oldfilepath = "";
		if($old_version === false){
			file_put_contents($newfilepath, $str_file_content);
		}else{	
			$oldfilepath = $version_template.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_img;
			$tempfilepath = TEMP.DIRECTORY_SEPARATOR.$filname_img;
			file_put_contents($tempfilepath, $str_file_content);
			if(!compare($oldfilepath, $tempfilepath)){
				file_put_contents($newfilepath, $str_file_content);
			}else{
				$arr_localpath_img[$count] = "..".DIRECTORY_SEPARATOR.V.$old_version.DIRECTORY_SEPARATOR.OTHERS.DIRECTORY_SEPARATOR.$filname_img;
				
			}
		}
		//file_put_contents($version.DIRECTORY_SEPARATOR.$arr_localpath_img[$count], $str_file_content);
		$count++;
	}

	//��html�ļ����img·������ָ�򱣴��·��
	$str_new = $str;
	$str_new = str_replace($arr_link_img, $arr_localpath_img, $str_new);
	return $str_new;
}

/**
**��������recursive_delete
**ɾ���ļ��������е��ļ�
**var $dir: �ļ���·��
**/
function recursive_delete($dir)
{
	if(is_dir($dir)){
	   if($dh = opendir($dir)){
		   while(($file = readdir($dh)) !== false ){
				if($file != "." && $file != "..")
				{
					if(is_dir($dir.$file))
					{                               
					  recursive_delete($dir.$file."/"); 
					  rmdir($dir.$file );
					}
					else
					{
					  unlink( $dir.$file);
					}
				}
		   }
		   closedir($dh);
	   }
	}
}

/**
**�������� compare
**���ã� �Ա������ļ�
**var file1:�ļ�1��·��  file2: �ļ�2��·��
**�ο����ף�http://www.php.net/manual/zh/function.md5-file.php
**          
**/
define('READ_LEN', 4096);

function compare($file1, $file2){
	return files_identical($file1, $file2);
}

function files_identical($fn1, $fn2) {
    if(filetype($fn1) !== filetype($fn2))
        return FALSE;

    if(filesize($fn1) !== filesize($fn2))
        return FALSE;

    if(!$fp1 = fopen($fn1, 'rb'))
        return FALSE;

    if(!$fp2 = fopen($fn2, 'rb')) {
        fclose($fp1);
        return FALSE;
    }

    $same = TRUE;
    while (!feof($fp1) and !feof($fp2))
        if(fread($fp1, READ_LEN) !== fread($fp2, READ_LEN)) {
            $same = FALSE;
            break;
        }

    if(feof($fp1) !== feof($fp2))
        $same = FALSE;

    fclose($fp1);
    fclose($fp2);

    return $same;
}
?>