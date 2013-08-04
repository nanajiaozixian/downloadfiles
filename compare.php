<?php
/**
**作者： Doris
**作用： 对比两个文件
**参考文献：http://www.php.net/manual/zh/function.md5-file.php
**          
**/
define('READ_LEN', 4096);

function compare($file1, $file2){
	if(files_identical($file1, $file2))
    echo 'files identical';
else
    echo 'files not identical';
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