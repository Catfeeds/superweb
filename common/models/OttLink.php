<?php

namespace common\models;

use backend\models\LinkToScheme;
use backend\models\Scheme;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ott_link".
 *
 * @property int $id
 * @property int $channel_id 关联频道号
 * @property string $link 链接
 * @property string $source 来源
 * @property int $sort 排序
 * @property string $use_flag 可用
 * @property int $format
 * @property string $script_deal 脚本开关
 * @property string $definition 清晰度
 * @property string $method 本地算法
 * @property string $decode 硬软解
 * @property string $scheme_id 方案号
 */
class OttLink extends \yii\db\ActiveRecord
{

    public $use_flag_status = [
        '不可用',
        '可用',
    ];

    public $use_flag_text;
    public $schemeText;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ott_link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['channel_id', 'link', 'source', 'format'], 'required'],
            [['channel_id', 'sort', 'format'], 'integer'],
            [['link'], 'string'],
            [['source'], 'string', 'max' => 30],
            [['use_flag', 'script_deal', 'definition', 'decode'], 'string', 'max' => 1],
            [['method'], 'string', 'max' => 20],
            ['use_flag', 'default', 'value' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel_id' => '关联频道号',
            'link' => '链接',
            'source' => '来源',
            'sort' => '排序',
            'use_flag' => '可用',
            'format' => 'Format',
            'script_deal' => '脚本开关',
            'definition' => '清晰度',
            'method' => '本地算法',
            'decode' => '硬软解',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'channel_id',
            'link',
            'source' ,
            'sort',
            'use_flag' ,
            'format',
            'script_deal',
            'definition',
            'method',
            'decode',
            'schemeText',
            'use_flag_text',
            'scheme_id'
        ];
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->use_flag_text = $this->use_flag_status[$this->use_flag];

        if ($this->scheme_id == 'all') {
            $this->schemeText = '全部';
        } elseif (!empty($this->scheme_id)){
            $schemeName = Scheme::find()->select('schemeName')->where("id in ({$this->scheme_id})")->all();
            if (!empty($schemeName)) {
                $schemeName = ArrayHelper::getColumn($schemeName, 'schemeName');
                $this->schemeText = implode(',', $schemeName);
            }
        }
    }

    /**
     * 频道关联关系
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(OttChannel::className(), ['id' => 'channel_id']);
    }


}
