<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AppVersion $model */

$this->title = 'Upload App Version';
$this->params['breadcrumbs'][] = ['label' => 'App Versions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="app-version-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
