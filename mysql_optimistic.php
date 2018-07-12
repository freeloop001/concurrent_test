<?php

/**
 * @author cy
 * @since  2018-07-11
 * 使用Mysql乐观锁
 * 优点: 乐观锁在不发生取锁失败的情况下开销比悲观锁小，程序实现，不会存在死锁等问题。
 * 缺点: 如果取锁失败需要回滚则开销较大
 */

require 'pdoClass.php';

$sql     = 'select num from prize where id=1';
$res     = $pdo->FetchRow($sql);
$now_num = $res['errmsg']['num']; //当前剩余数量

$sql = 'update prize set num=num-1 where num=:num and num>0 ;';
$res = $pdo->PtmQuery($sql, array('num' => $now_num));

if (!empty($res['errmsg'])) {
    echo "success";
} else {
    echo "fail";
}
