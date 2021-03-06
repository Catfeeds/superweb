<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUploadUI;
use backend\models\UploadForm;
use \common\widgets\country\CountryWidget;
/* @var $this yii\web\View */
/* @var $model backend\models\OttEventTeam */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    div.required label:after {
        content: " *";
        color: red;
    }
</style>

<div class="ott-event-team-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'event_id')->hiddenInput()->label(false) ?>

    <div class="col-md-12">

        <?= CountryWidget::widget(['colClass' => 'col-md-12']); ?>
        <?= $form->field($model, 'team_country')->hiddenInput()->label(false); ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'team_name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'team_zh_name')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
        <?= $form->field($model, 'team_introduce')->textarea(['rows' => 6]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'team_icon')->textInput(['maxlength' => true]) ?>
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
                                 $("#otteventteam-team_icon").val(files.path);
                             }',
                'fileuploadfail' => 'function(e, data) {
                                 console.log(e);
                                 console.log(data);
                             }',
            ],
        ]);
        ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'team_icon_big')->textInput(['maxlength' => true]) ?>
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
                                 $("#otteventteam-team_icon_big").val(files.path);
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
        <?= $form->field($model, 'team_alias_name')->textInput(['maxlength' => true]) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('backend','Go Back'), ['ott-event-team/index', 'event_id' => $model->event_id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

$js=<<<JS
    //语言选择框
    $(".fastbannerform__country").on('select2:select', function (e) {
        $(this).val('');
        var data = e.params.data;
        $('#otteventteam-team_country').val(data.id);
    });
JS;

$this->registerJs($js);

?>



