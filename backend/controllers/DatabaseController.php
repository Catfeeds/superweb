<?php
namespace backend\controllers;

use Yii;

class DatabaseController extends BaseController
{

    public function actionExport()
    {
        $infos = Yii::$app->db->createCommand('SHOW TABLE STATUS')->queryAll();
        $infos = array_map('array_change_key_case', $infos);

        return $this->render('export', [
            'infos' => $infos,
        ]);
    }

    public function actionImport()
    {
        return $this->render('import', [

        ]);
    }

    public function actionRepairOpt()
    {
        $operation = Yii::$app->request->get('operation', '');
        $tables = Yii::$app->request->get('tables', '');
        if($tables && in_array($operation, ['repair', 'optimize'])) {
            Yii::$app->db->createCommand($operation.' TABLE '.$tables);
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Success'));
            return $this->redirect(['export']);
        }
    }

}