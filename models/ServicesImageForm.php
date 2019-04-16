<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * This is the model class for the services image form.
 */
class ServicesImageForm extends Model {

	/**
	 * @var UploadedFile
	 */
	public $file;

	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['file'], 'required'],
			// The uploaded image must be a PNG or JPEG and smaller than 1MB.
			[
				['file'],
				'file',
				'extensions' => ['jpeg', 'jpg', 'png'],
				'maxSize' => 1024 * 1024
			],
		];
	}

	/**
	 * Validate the form and upload the image.
	 * @return boolean True if the operation was successful.
	 */
	public function upload() {
		// Get the uploaded file.
        $this->file = UploadedFile::getInstance($this, 'file');
        // Validate the model.
        if (!$this->validate()) {
            return false;
        }
		// Save the image.
		$path = Service::getImagePath();
		return (bool)
			Image::getImagine()->open($this->file->tempName)->save($path);
	}
}
