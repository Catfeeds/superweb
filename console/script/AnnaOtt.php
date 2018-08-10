<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/8/6
 * Time: 15:39
 */

namespace console\script;


use common\models\MainClass;
use common\models\OttChannel;
use common\models\OttLink;
use common\models\SubClass;
use yii\helpers\Console;

class AnnaOtt extends base
{
    protected static $data;

    public function __destruct()
    {
        $this->stdout('本次任务执行结束,若无输出则说明无新数据', Console::FG_GREEN);
    }

    /**
     * 处理直播数据
     */
    public function dealOTT()
    {
        $accounts = [
            '287994000090' => '287994000090',
            '287994000099' => '287994000099',
        ];

        foreach ($accounts as $account => $password) {
            self::$data = $this->download($account, $password);
            $data = $this->initData();

            foreach ($data as $value) {
                $mainClassID  = $this->_mainClass($value);
                $subClassID = $this->_subClass($value, $mainClassID);
                $channelID = $this->_channel($value, $subClassID);
                $this->_link($value, $channelID);
            }
        }
    }

    /**
     * 一级分类
     * @param $value
     * @return bool|int
     */
    private function _mainClass($value)
    {
        $className = explode('|',$value['group-title']);
        if (!isset($className[1])) {
            return false;
        }

        $mainClassName = $className[1];
        $mainClass = MainClass::findOne(['name' => $mainClassName]);

        if (is_null($mainClass)) {
            $mainClass = new MainClass();
            $mainClass->name = $mainClassName;
            $mainClass->zh_name = $mainClassName;
            $mainClass->save(false);
        }

        return $mainClass->id;
    }

    /**
     * 二级分类
     * @param $value
     * @param $mainClassID
     * @return bool|int
     */
    private function _subClass($value, $mainClassID)
    {
        $className = explode('|',$value['group-title']);
        if (!isset($className[1])) {
            return false;
        }

        $subClassName = $className[0];
        $subClass = SubClass::findOne(['name' => $subClassName]);

        if (is_null($subClass)) {
            $subClass = new SubClass();
            $subClass->name = $subClassName;
            $subClass->zh_name = $subClassName;
            $subClass->keyword = $subClassName;
            $subClass->main_class_id = $mainClassID;
            $subClass->save(false);
        }

        return $subClass->id;
    }

    /**
     * 增加频道号
     * @param $value
     * @param $subClassID
     * @return bool|int
     */
    private function _channel($value, $subClassID)
    {
        if ($subClassID == false)  return false;

        //查找频道
        $channel = OttChannel::findOne([
            "name" => $value['tvg-name'],
            'sub_class_id' => $subClassID
        ]);

        //新增频道
        if (empty($channel)) {
           $channel = new OttChannel();
           $channel->sub_class_id = $subClassID;
           $channel->name = $value['tvg-name'];
           $channel->zh_name = $value['tvg-name'];
           $channel->keywords = $value['tvg-name'];
           // 判断是否有HD 有的话去掉
           $alias = preg_replace('/\s*HD/', '', $value['tvg-name']);
           $channel->alias_name = $alias;

           $channel->save(false);

           $this->stdout("直播新增频道：" . $value['tvg-name'].PHP_EOL, Console::FG_BLUE);
        }

        return $channel->id;
    }

    /**
     * 新增链接
     * @param $value
     * @param $channelID
     * @return bool|int
     */
    private function _link($value, $channelID)
    {
        if ($channelID == false) {
            return false;
        }

        $Link = OttLink::findOne(['channel_id' => $channelID, 'link' => $value['ts']]);

        //新增链接
        if (is_null($Link)) {
            $Link = new OttLink();
            $Link->channel_id = $channelID;
            $Link->link = $value['ts'];
            $Link->source = 'file';
            $Link->use_flag = 1;
            $Link->method = 'null';
            $Link->decode = 1;
            $Link->save(false);
        }

        return $Link->id;
    }

    private static function get($data)
    {
        if (isset($data[0]) && !empty($data[0])) {
            return trim($data[0]);
        }

        return null;
    }

    private function download($account, $password)
    {
        $this->stdout("下载文件".PHP_EOL);
        $data = file_get_contents("http://www.hdboxtv.net:8000/get.php?username={$account}&password={$password}&type=m3u_plus&output=ts");
        $this->stdout("下载文件结束" . PHP_EOL);
        return $data;
    }

    /**
     * @param string $type 'ott|iptv'
     * @return array
     */
    private function initData($type = "ott")
    {
        $data = self::$data;
        $data = preg_split('/#EXTINF:-1/',$data);
        $array = [];

        foreach ($data as $item) {
            $preg = [];
            preg_match('/(?<=tvg-id\=")[^"]+/', $item, $tvg_id);
            preg_match('/(?<=tvg-name\=")[^"]+/', $item, $tvg_name);
            preg_match('/(?<=tvg-logo\=")[^"]+/', $item, $tvg_logo);
            preg_match('/(?<=group-title\=")[^"]+/i', $item, $group_title);
            preg_match('/\S+\.(ts|mp4|mkv|rmvb)/', $item, $ts);
            preg_match('/(?<=",)[^\r\n]+/', $item, $other);

            $preg['tvg-id'] = self::get($tvg_id);
            $preg['tvg-name'] = strpos( self::get($tvg_name), '|') ? strstr(self::get($tvg_name), '|', true) : self::get($tvg_name) ;
            $preg['tvg-logo'] = self::get($tvg_logo);
            $preg['group-title'] = self::get($group_title);
            $preg['ts'] = iconv("ASCII", "UTF-8", self::get($ts));
            $preg['other'] = self::get($other);
            $preg['type'] = strpos($preg['ts'], 'ts') !== false ? 'ott' : "iptv";

            if (!$preg['ts']) continue;

            $array[] = $preg;
        }

        foreach ($array as $key => $value) {
            if ($value['type'] != $type) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}