<?php
/**
 * User: cy
 * Date: 2018/7/19
 * Time: 16:15
 * 快速读取CSV文件的方案（只适用Linux系统）
 */

$fileName = 'test.csv';
getCsvData( $fileName );

/**
 * @param $file csv文件地址
 * @param int $row 每次读取行数
 */
function getCsvData($file, $row = 2000)
{
    $allRow = `cat $file|wc -l`;    //用linux cat命令获取文件行数
    if (empty($allRow)) {
        echo '读取文件失败';
        exit;
    }
    for ($i = 0, $count = floor($allRow / $row); $i <= $count; $i++) {
        $startRow   = $i == 0 ? $i * $row + 2 : $i * $row + 1;
        $endRow     = $i < $count ? $i * $row + $row : $i * $row + $allRow % $row;
        $dataArr    = readFromCsv($file, $startRow, $endRow);
        if (!empty($dataArr)) {
            foreach ($dataArr as &$value) {
                if (!empty($value)) {
                    $value = explode(',', trim($value));
                    //在这里就可以对数据进行处理
                    var_dump($value);
                }
            }
        }
        $dataArr = null;
    }
}

/**
 * 读取指定行数的数据
 * @param  string $file 文件路径
 * @param  int $startRow 起始行
 * @param  int $endRow 终止行
 * @return array         数据数组
 */
function readFromCsv($file, $startRow, $endRow)
{
    $file       = escapeshellarg($file); // 对命令行参数进行安全转义
    $tempData   = `sed -n $startRow,$endRow'p' $file`;    //读取文件指定行
    $dataArr    = explode("\n", trim(str_replace('"', '', iconv('EUC-CN', 'UTF-8', $tempData))));
    return $dataArr;
}