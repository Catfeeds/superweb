<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/9/12
 * Time: 11:01
 */

namespace console\jobs;


use backend\components\MyRedis;
use backend\models\Mac;

class SyncOnlineStateJob
{
    public static function start()
    {
        $redis = MyRedis::init(MyRedis::REDIS_DEVICE_STATUS);

        foreach (Mac::find()->select('MAC')->asArray()->each() as $mac) {
            $onLineState =  $redis->hget($mac['MAC'], 'token') ? true : false;
            if ($onLineState) {
                Mac::updateAll(['is_online' => 1], ['MAC' => $mac['MAC']]);
            }
        }

        echo "任务执行结束";
    }
}