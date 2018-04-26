<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/4/22
 * Time: 12:14
 */

namespace api\controllers;

use common\models\Vod;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\rest\ActiveController;

class VodController extends ActiveController
{

    public $modelClass = 'common\models\Vod';

    public function actions()
    {
        $actions = parent::actions(); // TODO: Change the autogenerated stub
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionView($id)
    {
        $vod = Vod::findOne($id);
        if (empty($vod->vod_url)) {
            $vod->vod_url = 'http://img.ksbbs.com/asset/Mon_1703/05cacb4e02f9d9e.mp4';
        }
        return $vod;
    }

    public function actionIndex()
    {
        $request =  \Yii::$app->request;
        $cid = $request->get('cid');
        $per_page = $request->get('per_page', 12);
        $modelClass = $this->modelClass;

        $fields = Vod::getFields();
        unset($fields[array_search('vod_url', $fields)]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $cid ? $modelClass::find()->select($fields)->where(['vod_cid' => $cid]) : $modelClass::find()->select($fields),
            'pagination' => [
                'pageSize' => $per_page,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * 首页
     * @return ActiveDataProvider
     */
    public function actionHome()
    {
        $request =  \Yii::$app->request;

        $per_page = $request->get('per_page', 12);
        $modelClass = $this->modelClass;

        $fields = Vod::getFields();
        unset($fields[array_search('vod_url', $fields)]);

        $dataProvider = new ActiveDataProvider([
            'query' =>  $modelClass::find()->select($fields)->where(['vod_home' => 1]),
            'pagination' => [
                'pageSize' => $per_page,
            ],
        ]);

        return $dataProvider;
    }

}