<?php

use dosamigos\fileupload\FileUpload;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this*/
/** @var app\models\AppVersion $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="app-version-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'filename')->hiddenInput(['id' => 'filename'])->label(false) ?>

    <?= FileUpload::widget([
        // This name should not be changed. It is used by the upload handler.
        'name' => 'files',
        'url' => ['upload-chunk'],
        'clientOptions' => [
            // Enable chunked file uploads.
            // Chunk size 512KB.
            'maxChunkSize' => 512 * 1024,
        ],
        // Get the filename when the upload is finished.
        'clientEvents' => [
            'fileuploaddone' => 'function(e, data) {
                var response = data.response();
                var parsedresponse = $.parseJSON(response.result);
                var files = parsedresponse.files;
                $("#filename").val(files[0].name);
            }',
            'fileuploadprogressall' => 'function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $("#progress-bar").css("width", progress + "%");
            }',
        ],
    ]); ?>

    <div class="progress" style="margin-top: 20px;">
        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
    </div>

    <?= $form->field($model, 'force_update')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
