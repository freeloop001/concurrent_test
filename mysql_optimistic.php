<?php

/**
 * 使用Mysql乐观锁
 */

require 'pdoClass.php';

$sql     = 'select num from prize where id=1';
$res     = $pdo->FetchRow($sql);
$now_num = $res['errmsg']['num']; //当前剩余数量

$sql = 'update prize set num=num-1 where num=:num and num>0 ;';
$res = $pdo->PtmQuery($sql, array('num' => $now_num));

if (!empty($res['errmsg'])) {
    echo "中奖";
} else {
    echo "未中奖";
}
