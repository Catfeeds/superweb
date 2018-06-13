<?php

namespace backend\models;

use common\components\BaiduTranslator;
use Yii;
use common\models\OttChannel;
use yii\helpers\Json;

/**
 * This is the model class for table "ott_major_event".
 *
 * @property int $id
 * @property int $time 世界时间
 * @property string $title 标题
 * @property string $live_match 对阵信息
 * @property int $base_time 比赛时间
 * @property string $match_data 匹配预告列表
 * @property string $sort 排序
 */
class MajorEvent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ott_major_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'title', 'time', 'base_time'], 'required'],
            [['match_data', 'live_match'], 'safe'],
            [['title'], 'string'],
            ['sort', 'default' , 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time' => '日期',
            'title' => '赛事轮次信息',
            'live_match' => '赛事信息',
            'base_time' => '比赛时间',
            'match_data' => '匹配预告列表',
            'sort' => '排序',
        ];
    }

    /**
     *
     */
    public function beforeValidate()
    {
       parent::beforeValidate();
       $this->time = strtotime($this->time);
       return true;
    }


    public function initData($post)
    {

        $date = $post['MajorEvent']['time'];
        $this->base_time = strtotime($date . ' ' . trim(strstr($post['MajorEvent']['base_time'], '-', true)));

        $majorEvent = OttEvent::findOne(['event_name_zh' => $post['event_info']]);
        $teamA = OttEventTeam::findOne(['team_zh_name' => $post['teamA']]);
        $teamB = OttEventTeam::findOne(['team_zh_name' => $post['teamB']]);

        //赛事信息
        $event_data = [
            'title' => (new BaiduTranslator())->translate($post['MajorEvent']['title'], 'zh', 'en'),
            'title_zh' => $post['MajorEvent']['title'],
            'event_time' => $this->base_time,
            'event_info' => $majorEvent->event_name,
            'event_zh_info' => $majorEvent->event_name_zh,
            'event_icon' => $majorEvent->event_icon,
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
            ],

        ];

        $this->live_match = Json::encode($event_data);

        //频道
        $match_data = [];
        foreach ($post['channel_id'] as $key => $channel_id) {
            $channelObject = OttChannel::findOne($channel_id);
            $match_data[] = [
                'channel_language' => $post['language'][$key],
                'main_class' => $post['main_class'][$key],
                'channel_name' => $channelObject->name,
                'channel_id' => $channelObject->channel_number,
                'channel_icon' => $channelObject->image
            ];
        }

        if (empty($match_data)) {
            return false;
        }

        $this->match_data = Json::encode($match_data);

        return true;
    }

}