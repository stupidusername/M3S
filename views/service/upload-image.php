<?php

/** @var yii\web\View $model */
/** @var app\modules\reports\models\ServicesImageForm $model */

use app\models\Service;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Upload Services Image';
$this->params['breadcrumbs'][] = ['label' => 'Services', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="service-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Service::getImageUrl()): ?>

        <div style="margin-bottom: 20px;">

            <h2>Current Image</h2>

            <p>
                <?= Html::img(Service::getImageUrl(), ['class'=>'img-rounded img-thumbnail']);?>
            </p>

            <?= Html::a('Delete', ['delete-image'], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>

        </div>

    <?php endif; ?>

    <div class="service-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'file')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissable">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissable">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
