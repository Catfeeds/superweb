<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Firmware Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="firmware-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Firmware Detail', ['create', 'firmware_id' => $firmware_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                    'label' => '名称',
                    'value' => 'firmware.name'
            ],
            'ver',
            'md5',
            'url:ntext',
            //'content:ntext',
            //'sort',
            //'force_update',
            //'type',
            //'is_use',

            [
                    'class' => 'common\grid\MyActionColumn',
                    'size' => 'btn-sm'
            ],
        ],
    ]); ?>
</div>
