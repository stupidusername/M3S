<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\AppVersion $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'App Versions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="app-version-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'version',
            'filename',
            ['attribute' => 'force_update', 'format' => 'boolean'],
        ],
    ]) ?>

</div>
