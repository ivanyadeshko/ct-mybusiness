<?php

use \common\models\Apple;
use \yii\helpers\Html;
use \yii\web\View;

/* @var $this View */
/* @var $activeDataProvider yii\data\ActiveDataProvider */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <?= Html::beginForm(['/site/grow-apples'], 'POST'); ?>
                <?= Html::submitButton('Grow Apples', ['class' => 'btn btn-success', 'style' => 'margin: 1em;']); ?>
                <?= Html::endForm(); ?>
            </div>
        </div>



        <?= \yii\grid\GridView::widget([
            'dataProvider' => $activeDataProvider,
            'columns' => [
                'id',
                [
                    'attribute' => 'color',
                    'format' => 'raw',
                    'value' => function (Apple $model) {
                        return "<div style='opacity: 70%; width: 20px; height: 20px; background-color: $model->color'></div>";

                    },
                ],
                [
                    'attribute' => 'fall_at',
                    'value' => function (Apple $model) {
                        if ($model->isOnTree()) {
                            return 'On the tree';
                        }
                        return (new DateTime())->setTimestamp($model->fall_at)->format('Y-m-d H:i');
                    },
                ],
                [
                    'attribute' => 'size',
                    'format' => 'raw',
                    'value' => function(Apple $model) {
                        return $model->size*100 .'%';
                    }
                ],
                [
                    'header' => 'State',
                    'format' => 'raw',
                    'value' => function(Apple $model) {
                        return $model->isFresh() ? 'Fresh' : 'Addled';
                    }
                ],
                [
                    'header' => 'Eat',
                    'content' => function(Apple $model) {
                        /* @var $this View */
                        return $this->render('parts/eat-apple-form', [
                            'model' => $model
                        ]);
                    }
                ],
                [
                    'header' => 'Fall To Ground',
                    'content' => function(Apple $model) {
                        return Html::a(
                            'Throw off',
                            \yii\helpers\Url::toRoute(['site/fall-to-ground-apple', 'id' => $model->id]),
                            [
                                'class' => "btn " . ($model->isOnTree() ? 'btn-success' : 'btn-secondary disabled'),
                                'data' => [
                                    'method' => 'post',
                                    'params' => [
                                        'id' => $model->id
                                    ]
                                ]
                            ]
                        );
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function (Apple $model) {
                        return (new DateTime())->setTimestamp($model->created_at)->format('Y-m-d H:i');
                    },
                ],

            ],

        ]) ?>


    </div>
</div>
