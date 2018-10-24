<?php
use yii\grid\GridView;
use common\models\Vodlink;
use yii\helpers\Html;
use yii\helpers\Url;

/**@var $model \backend\models\PlayGroup **/
$group_id = Yii::$app->request->get('group_id', false);
$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => Vodlink::find()->where(['group_id' => $model->id]),
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>



<div class="panel panel-default">
    <div class="panel-heading">
        <span class="text-dark">播放来源：<b><?= $model->group_name ?></b></span>
        <span style="float: right"><?= Html::a('编辑', Url::to(['play-group/update', 'id' => $model->id])) ?></span>
    </div>
    <div class="panel-body">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            "options" => ["class" => "grid-view","style"=>"overflow:auto", "id" => "grid"],
            'layout' => '{items}{pager}',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    "class" => "yii\grid\CheckboxColumn",
                    "name" => "id",
                ],
                [
                    'attribute' => 'save_type',
                    'value' => function($model) {
                        return Vodlink::$saveTypeMap[$model->save_type];
                    }
                ],
                [
                    'attribute' => 'url',
                    'format' => 'raw',
                    'value' => function($mod) use ($model) {
                        if ($mod->save_type == Vodlink::FILE_LINK) {
                            if (strtolower($model->group_name) == 'youtube') {

                                //$href = "https://www.youtube.com/watch?v=" . $mod->url;
                                $href = "http://192.200.112.162/play/" . $mod->url. '?resolve=youtube&noauth=true';
                            } else {
                                $href = $mod->url;
                            }
                            $text = $href;
                        } else if ($mod->save_type == Vodlink::FILE_SERVER) {
                            $href = \common\components\Func::getAccessUrl($mod->url, 1800);
                            $text = $mod->url;
                        }

                        return Html::a($text, $href, [
                                'class' => 'btn btn-link',
                                'target' => '_blank'
                        ]);
                    },
                    'headerOptions' => [
                        'class' => 'col-md-8',
                    ]
                ],
                'episode',

                [
                    'class' => 'common\grid\MyActionColumn',
                    'template' => '{update} {delete}',
                    'headerOptions' => [
                        'class' => 'col-md-2'
                    ],
                    'buttons' => [
                        'view' => function($url, $model) {
                            return Html::a(Yii::t('backend', 'View'),Url::to(['link/view','vod_id'=>$model->video_id, 'id' => $model->id]),[
                                'class' => 'btn btn-success btn-xs'
                            ]);
                        },
                        'update' => function($url, $model) {
                            return Html::a(Yii::t('backend', 'Edit'),Url::to(['link/update','vod_id'=>$model->video_id, 'id' => $model->id]),[
                                'class' => 'btn btn-info btn-xs'
                            ]);
                        },
                        'delete' => function($url, $model) {
                            return Html::a(Yii::t('backend', 'Delete'),Url::to(['link/delete','vod_id' => $model->video_id, 'id' => $model->id]),[
                                'class' => 'btn btn-danger btn-xs',
                                'data-confirm' => Yii::t('backend', 'Are you sure?')
                            ]);
                        },

                    ]
                ],
            ],
        ]); ?>

    </div>
    <div class="panel-footer text-right">
        <?= \yii\helpers\Html::a('增加链接', \yii\helpers\Url::to(['link/create', 'group_id' => $model->id]), ['class' => 'btn btn-success btn-sm']) ?>
        <?= \yii\helpers\Html::a('删除此组', \yii\helpers\Url::to(['play-group/delete', 'id' => $model->id]), ['class' => 'btn btn-danger btn-sm', 'data-confirm' => Yii::t('backend', 'Are you sure?')]) ?>
    </div>
</div>


