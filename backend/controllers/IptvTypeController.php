<?php

namespace backend\controllers;

use backend\models\IptvTypeItem;
use common\components\Func;
use common\models\Type;
use common\models\Vod;
use common\models\VodList;
use Yii;
use backend\models\IptvType;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * IptvTypeController implements the CRUD actions for IptvType model.
 */
class IptvTypeController extends BaseController
{
    public $list;

    public function actionIndex()
    {
        $vod_list_id = Yii::$app->request->get('list_id');

        if ($vod_list_id == false) {
            return $this->redirect(Url::to(['vod-list/index']));
        }

        $this->session()->set('vod_list_id', $vod_list_id);

        $this->list = VodList::findOne($vod_list_id);

        if (is_null($this->list)) {
            throw new NotFoundHttpException(Yii::t('backend', '404 Not Found'));
        }

        $dataProvider = new ActiveDataProvider([
            'query' => IptvType::find()->where(['vod_list_id' => $vod_list_id]),
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'list' => $this->list
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $this->rememberReferer();
        $model = new IptvType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Func::getLastPage());
        }

        $model->vod_list_id = Yii::$app->request->get('vod_list_id');

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->setFlash('success', Yii::t('backend', 'success'));
            if ($this->session()->has('vod_list_id')) {
                return $this->redirect(['iptv-type/index', 'list_id' => $this->session()->get('vod_list_id')]);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = IptvType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionSync()
    {
        $vod_list_id = Yii::$app->request->get('vod_list_id');
        $items = IptvType::find()->with('items')->where(['vod_list_id' => $vod_list_id])->asArray()->all();
        foreach ($items as $item) {
            $items = $item['items'];
            foreach ($items as $val) {
                if (strtolower($item['field']) == 'hot') $item['field'] = 'type';

                $count = Vod::find()->where(['like', 'vod_'.$item['field'], $val['name']])->count();
                IptvTypeItem::updateAll(['exist_num' => $count], ['id' => $val['id']]);
            }
        }


        $this->success();

        return $this->redirect($this->getReferer());
    }


}
