<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/4/26
 * Time: 16:28
 */

namespace api\controllers;


use common\models\Vod;
use yii\db\Query;
use yii\helpers\Url;
use yii\rest\ActiveController;

class RecommendController extends ActiveController
{
    public $modelClass = 'common\models\Vod';

    public function actions()
    {
       $actions = parent::actions(); // TODO: Change the autogenerated stub
       unset($actions['view']);
       return $actions;
    }

    /**
     * 关联的资源
     * @param $id
     * @return array|Vod[]|\yii\db\ActiveRecord[]
     */
    public function actionView($id)
    {
        $num = \Yii::$app->request->get('num', 6);

        $range =  [];
        for ($i = 1; $i <= $num; $i++) {
            $range[] = $id + $i;
        }

        $vod = Vod::find()->select(Vod::getFields())->where(['in', 'vod_id', $range])
            ->orderBy('vod_addtime desc')
            ->asArray()
            ->all();

        if ($vod) {
            array_walk($vod, function(&$v, $k) {
                $v['_links'] = [
                    'self' => [
                        'href' => Url::to(['vod/view', 'id' => $v['vod_id']], true),
                    ],
                    'recommend' => [
                        'href' => Url::to(['recommend/view', 'id' => $v['vod_id']], true),
                    ]
                ];
            });
        }

        return $vod;
    }

}