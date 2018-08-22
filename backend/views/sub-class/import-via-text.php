<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\SubClass */

$this->title = '批量导入';
$this->params['breadcrumbs'][] = ['label' => '返回', 'url' => Url::to(['main-class/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-class-create">
    <div class="sub-class-form">

        <div class="list-group">
            <a href="#" class="list-group-item active">
                <h4 class="list-group-item-heading">
                    模式一 (一级分类,二级分类,频道名称,频道图标,排序,链接,[算法,可用标志,硬软解标志,链接排序])
                </h4>
            </a>
            <a href="#" class="list-group-item">
                <p class="list-group-item-heading">
                    1) 不带方案号默认支持全部<br>
                    vn,vtv,vtv1,http://topthinker.oss-cn-hongkong.aliyuncs.com/channel/5b25dd9c859a2.png,123,http://ott.realplaytv.net:12388/?header=hplus&name=vtv1&cdn=1,null,1,1,2<br>
                    vn,vtv,vtv1,http://topthinker.oss-cn-hongkong.aliyuncs.com/channel/5b25dd9c859a2.png,123,https://tvplay.vn/truyen-hinh-70.htm,local_tvplay,1,1,2
                </p>
                <br/>
                <p class="list-group-item-heading">
                    2) 指定支持哪些方案号<br>
                    vn,vtv,vtv1,http://topthinker.oss-cn-hongkong.aliyuncs.com/channel/5b25dd9c859a2.png,123,http://ott.realplaytv.net:12388/?header=hplus&name=vtv1&cdn=1,null,rk323|rk324|dvb|6605s,1,1
                </p>

             </a>
            <a href="#" class="list-group-item active">
                <h4 class="list-group-item-heading">
                    模式二  (关键字,二级分类,频道名称,链接,算法,可用标志,硬软解标志)
                </h4>
            </a>
            <a href="#" class="list-group-item">

                <p class="list-group-item-text">
                    综合,综合频道,http://ott.realplaytv.net:12388/?header=hplus&name=vtv1&cdn=1,null
                </p>
            </a>
        </div>



        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'mode')->dropDownList(\backend\models\importTextForm::getMode()) ?>

        <?= $form->field($model, 'text')->textarea([
            'rows' =>18
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('backend','Go Back'), ['main-class/index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
