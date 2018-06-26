<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/5/29
 * Time: 9:27
 */

namespace console\models\event;

use backend\models\MajorEvent;
use backend\models\OttEvent;
use backend\models\OttEventTeam;
use common\components\BaiduTranslator;
use Yii;
use Symfony\Component\DomCrawler\Crawler;
use console\components\MySnnopy;
use console\models\parade\CommonParade;
use console\models\parade\collector;

//美国时间
class zhiboba extends CommonParade implements collector
{

    public $url = 'https://www.zhibo8.cc/';

    public function start()
    {
        $this->collect();
    }

    public function collect()
    {
        $events = $this->getPage();
        $this->FIFA($events);
    }

    public function getPage()
    {
        if (Yii::$app->cache->exists('zhiboba') == false) {
            $snnopy = MySnnopy::init();
            $snnopy->fetch($this->url);
            Yii::$app->cache->set('zhiboba', $snnopy->results);
            $data = $snnopy->results;
        } else {
            $data = Yii::$app->cache->get('zhiboba');
        }

        $dom = new Crawler();
        $dom->addHtmlContent($data);

        $events = [];
        $dom->filter(".schedule_container .box")->each(function(Crawler $box) use(&$events) {
            // 获取时间
            $time = $box->filter('.titlebar')->each(function(Crawler $h) {
                 $time = $h->text();
                 $time = explode(' ', trim($time));
                 return trim($time[0]);
            });

            $date = current($time);

            $box->filter('ul li')->each(function(Crawler $li) use(&$events, $date) {
                $events[] = [
                    'label' => $li->attr('label'),
                    'text' => $li->text(),
                    'date' => date('Y-') . str_replace(['月','日'],['-',''], $date)
                ];
            });
        });

        foreach ($events as &$event) {
            //找 cctv5 qq的位置
            $event['label'] = explode(',', $event['label']);
            if (preg_match('/等待更新|CCTV|QQ/', $event['text'])) {
                $event['text'] = preg_replace('/等待更新.*|CCTV.*|QQ.*/','', $event['text']);
            }

            $event['text'] = str_replace('-', '', $event['text']);
            $info = array_values(array_filter(explode(' ', $event['text'])));
            $event['info'] = $info;
        }

        return $events;
    }

    /**
     * 世界杯
     * @param $data
     */
    public function FIFA($data)
    {

        foreach ($data as $val) {
            if (in_array('世界杯', $val['label'])) {
                $raceName = $val['info'][1];
                $teams = [
                           'teamA' => $val['info'][2],
                           'teamB' => $val['info'][3]
                         ];
                $date = $val['date'] . ' ' . $val['info'][0] . ":00";
                $time = $this->convertTimeZone($date, 'timestamp', '8','8');

                $this->createMajorEvent("国际足联世界杯",$raceName, $time, $teams);

            }
        }

    }


    public function createMajorEvent($eventName, $raceName, $time, $teams)
    {
        // 查找赛事类别
        $event = OttEvent::find()->where(['event_name_zh' => $eventName])->one();
        if (is_null($event)) {
            echo "没有找到赛事:" . $eventName;
            return false;
        }

        // 查找队伍A信息
        $teamA = OttEventTeam::find()->where(['event_id' => $event->id, 'team_zh_name' => $teams['teamA']])->one();
        if (empty($teamA)) {
            echo "找不到队伍: " . $teams['teamA'] , PHP_EOL;
            return false;
        }

        $teamB = OttEventTeam::find()->where(['event_id' => $event->id, 'team_zh_name' => $teams['teamB']])->one();
        if (empty($teamB)) {
            echo "找不到队伍: " . $teams['teamB'], PHP_EOL;
            return false;
        }


        $live_match = [
            'title' => (new BaiduTranslator())->translate($raceName, 'zh', 'en'),
            'title_zh' => $raceName,
            'event_time' => $time,
            'event_info' => $event->event_name,
            'event_zh_info' => $event->event_name_zh,
            'event_icon' => $event->event_icon,
            'teams' => [
                    [
                        'team_name' => $teamA->team_name,
                        'team_zh_name' => $teamA->team_zh_name,
                        'team_icon' => $teamA->team_icon
                    ],
                    [
                        'team_name' => $teamB->team_name,
                        'team_zh_name' => $teamB->team_zh_name,
                        'team_icon' => $teamB->team_icon
                    ]
            ]
        ];

        $majorEvent = new MajorEvent();
        $majorEvent->live_match = json_encode($live_match);
        $majorEvent->title = $raceName;
        $majorEvent->time = $time;
        $majorEvent->base_time = $time;
        $majorEvent->save(false);

    }

    /**
     * 访问url数组
     * @return array
     */
    public function _getUrlGroup()
    {

    }

}