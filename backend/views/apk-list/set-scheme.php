<?php


use backend\models\Scheme;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use common\widgets\Jsblock;
/* @var $this yii\web\View */
/* @var $model backend\models\ApkList */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="col-md-12">
<table class="table table-bordered" style="font-size: 13px; width: 100%;">

    <tbody>
    <tr>
        <td>
            <?= $form->field($model, "scheme_id")->checkboxList(\yii\helpers\ArrayHelper::map(Scheme::getAll(),'id', 'schemeName')); ?>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            <a class="btn btn-default btn-xs check"  href="#" style="margin-right: 10px"><?= Yii::t('backend', 'All') ?></a>
            <a class="btn btn-default btn-xs check"  href="#" style="margin-right: 10px"><?= Yii::t('backend', 'Cancel') ?></a>
        </td>
    </tr>
    </tbody>

</table>
</div>
<div class="col-md-12">

    <div class="form-group">
        <?= Html::submitButton(\Yii::t('backend','Save'), ['class' => 'btn btn-success']); ?>
        <?= Html::a(Yii::t('backend','Go Back'),Yii::$app->request->referrer, ['class' => 'btn btn-default']); ?>
    </div>
</div>
<?php $form = ActiveForm::end(); ?>



<?php JsBlock::begin(); ?>
<script>
  $('.check').click(function(){
    var flag = !$(this).index();
    $('input[type=checkbox]').prop('checked', flag);
    return false;
  });
</script>
<?php JsBlock::end(); ?>
