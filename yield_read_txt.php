<?php
/**
 * User: cy
 * Date: 2018/7/19
 * Time: 16:50
 * 用yield迭代器读取大文件
 * 使用生成器读取文件，每次被加载到内存中的文字只有一行，大大的减小了内存的使用。
 */

header("content-type:text/html;charset=utf-8");
function readTxt()
{
    # code...
    $handle = fopen("./test.txt", 'rb');

    while (feof($handle) === false) {
        # code...
        yield fgets($handle);
    }

    fclose($handle);
}

foreach (readTxt() as $key => $value) {
    # code...
    echo $value . '<br />';
}