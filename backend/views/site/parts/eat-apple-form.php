<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $model \common\models\Apple */

$formModel = new \backend\models\EatAppleForm();

?>
<?php $form = ActiveForm::begin([
    'action' => ['/site/eat-apple'],
    'options' => ['method' => 'POST'],
    'id' => 'form-eat',
]); ?>
<div class="row">
    <div class="col-xs-4" style="padding-right: 0px">
        <?= $form->field($formModel, 'size')
            ->label(false)
            ->input('number', [
                'min' => 0,
                'max' => $model->size * 100,
                'value' => 0,
                'disabled' => !$model->canEat()
            ]); ?>
        <?= $form->field($formModel, 'id')->hiddenInput(['value' => $model->id])->label(false); ?>
    </div>
    <div class="col-xs-2" style="padding-left: .2em;">
        <?= Html::submitButton(
            'Eat',
            [
                'class' => 'btn ' . ($model->canEat() ? 'btn-success' : 'btn-secondary disabled'),
                'name' => 'eat-button'
            ]
        ) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>
