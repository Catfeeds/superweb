<?php

namespace backend\controllers;

use common\models\MainClass;
use common\models\SubClass;
use Yii;
use common\models\OttChannel;
use common\models\search\OttChannelSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OttChannelController implements the CRUD actions for OttChannel model.
 */
class OttChannelController extends BaseController
{
    public $mainClass;
    public $subClass;

    public function beforeAction($action)
    {
        parent::beforeAction($action); // TODO: Change the autogenerated stub
        $sub_class_id = Yii::$app->request->get('sub-id');
        if ($sub_class_id) {
            $this->subClass = SubClass::find()->where(['id' => $sub_class_id])->one();
            $this->mainClass = $this->subClass->mainClass;

        }

        return true;

    }

    /**
     * Lists all OttChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new OttChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mainClass' => $this->mainClass,
            'subClass' => $this->subClass
        ]);
    }

    /**
     * Displays a single OttChannel model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new OttChannel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OttChannel();
        $model->sub_class_id = $this->subClass->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'mainClass' => $this->mainClass,
            'subClass' => $this->subClass
        ]);
    }

    /**
     * Updates an existing OttChannel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $field = Yii::$app->request->post('field');
            if (in_array($field, ['sort','zh_name', 'name', 'use_flag','keywords'])) {
                $model->$field = Yii::$app->request->post('value');
                $model->save(false);
            }
            return [
                'status' => 0
            ];
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->setFlash('info', '修改成功');
            return $this->redirect(Url::to(['ott-channel/index', 'sub-id' => $model->subClass->id]));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OttChannel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $this->setFlash('success', '操作成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionBatchDelete()
    {
        $id = Yii::$app->request->get('id');
        OttChannel::deleteAll(['in', 'id', $id]);

        $this->setFlash('info', "批量删除成功");

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the OttChannel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OttChannel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OttChannel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
