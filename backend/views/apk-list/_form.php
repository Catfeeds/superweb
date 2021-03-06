<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\Jsblock;
use backend\models\Scheme;
use dosamigos\fileupload\FileUploadUI;
use backend\models\UploadForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ApkList */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    div.required label:after {
        content: " *";
        color: red;
    }
</style>
<div class="apk-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'typeName')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'sort')->textInput() ?>
    </div>

    <div class="col-md-12">



        <?= $form->field($model, 'img')->textInput(['maxlength' => true]) ?>

        <?= FileUploadUI::widget([
            'model' => new UploadForm(),
            'attribute' => 'image',
            'url' => ['upload/image-upload',],
            'gallery' => false,
            'fieldOptions' => ['accept' => 'image/*'],
            'clientOptions' => ['maxFileSize' => 2000000],
            'clientEvents' => [
                'fileuploaddone' => 'function(e, data) {
                                     var files = data.result.files[0];
                                     $("#apklist-img").val(files.path);
                                 }',
                'fileuploadfail' => 'function(e, data) {
                                     console.log(e);
                                     console.log(data);
                                 }',
            ],
        ]);
        ?>
    </div>

    <div class="col-md-12">

        <div class="form-group">
            <?= Html::submitButton(\Yii::t('backend','Save'), ['class' => 'btn btn-success']); ?>
            <?= Html::a(Yii::t('backend','Go Back'),Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>




