<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'App Versions';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="app-version-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create App Version', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'version',
            'filename',
            ['attribute' => 'force_update', 'format' => 'boolean'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
