<?php

/**
 * @author cy
 * @since  2018-07-12
 * 使用Mysql乐观锁悲观锁
 * 通常所说的“一锁二查三更新”即指的是使用悲观锁。通常来讲在数据库上的悲观锁需要数据库本身提供支持，即通过常用的select … for update操作来实现悲观锁。
 * 当数据库执行select for update时会获取被select中的数据行的行锁，因此其他并发执行的select for update如果试图选中同一行则会发生排斥（需要等待行锁被释放），因此达到锁的效果。select for update获取的行锁会在当前事务结束时自动释放，因此必须在事务中使用。
 * 优点: 悲观锁是“先取锁再访问”的保守策略，为数据处理的安全提供了保证。
 * 缺点: 悲观锁是数据库层面加锁，会阻塞去等待锁,还会增加产生死锁的可能。
 */

require 'pdoClass.php';

$pdo->Begin();
$pdo->FetchRow('select num from prize where id=1 for update ');
$res = $pdo->PtmQuery('update prize set num=num-1 where id=1 and num>0 ');
if (!empty($res['errmsg'])) {
    $pdo->Commit();
    echo "success";
} else {
    $pdo->RollBack();
    echo "fail";
}
