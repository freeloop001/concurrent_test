<?php
/**
 * User: cy
 * Date: 2018/7/19
 * Time: 15:23
 */

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$arr = range(1, 100000);
//如果要把arr数组添加到Redis队列

//第一种方案 :
echo getMillisecond() . '<br/>';    //1531987750998
$redis->delete('test_list');
foreach ($arr as $val) {
    $redis->lPush('test_list', $val);
}
echo getMillisecond() . '<br/>';    //1531987756114 用时5116ms

//第二种方案
echo getMillisecond() . '<br/>';    //1531987678013
$redis->delete('test_list');
call_user_func_array([$redis, 'LPUSH'], $arr);
echo getMillisecond() . '<br/>';    //1531987678121 用时108ms


// 毫秒级时间戳
function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}