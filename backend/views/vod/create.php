<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Vod */

$this->title = 'Create Vod';
$this->params['breadcrumbs'][] = ['label' => 'Vods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vod-create">
     <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
