<?php
/**
 * Created by PhpStorm.
 * User: lychee
 * Date: 2018/4/26
 * Time: 13:27
 */

namespace api\controllers;


use api\components\Formatter;
use common\components\Func;
use common\oss\Aliyunoss;
use yii\rest\ActiveController;


class ApkController extends  ActiveController
{
    public $modelClass = 'backend\models\ApkList';

    public function actionUpgrade($type,$ver)
    {
        $model = $this->modelClass;
        $apk = $model::find()->where(['apk_list.type'=>$type])->joinWith('newest')->asArray()->one();

        if ($apk && !empty($apk['newest'])) {
                foreach ($apk['newest'] as $field => $value) {
                    $apk[$field] = $value;
                }

                if ($apk['save_position'] == 'oss') {
                    $oss = new Aliyunoss();
                    $apk['url'] = $oss->getDownloadUrl($apk['url'], 1000);
                } else {
                    $apk['url'] = Func::getAccessUrl($apk['url'], 3600);
                }
        }

        if ($apk['newest']) {
            unset($apk['newest']);
            if ($ver < $apk['ver']) {
                 return $apk;
            }
        }

        return Formatter::format(null, Formatter::NO_NEED_UPDATE, '无新版本');
    }

}