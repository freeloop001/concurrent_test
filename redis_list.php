<?php
/**
 * @author cy
 * @since  2018-07-12
 * 使用redis队列控制奖品抢购、秒杀等活动的奖品库存，防止超发
 */

$redis = new redis();
$redis->connect('127.0.0.1', 6379);

// 比如需要抢购3个奖品

// 首先在redis队列添加3个奖品码 
$prize_code = ['aaa','bbb','ccc'];
foreach ($prize_code as $key => $value) {
	$redis->lpush( 'prize_code' , $value );
}

// 用户从这里进来

$code = $redis->lpop( 'prize_code' );
if ( empty($code) ) {
	//抢购活动已经结束，下次再来吧
}
else {
	// 添加code和用户信息到mysql存储
	// 恭喜你中奖了
}