<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "service".
 * @property integer $id
 * @property string $title
 */
class Service extends \yii\db\ActiveRecord {

	// The path under the images folder where the services image is stored.
	const IMAGE_PATH = 'images/services/hotel.jpg';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
			[['title'], 'required'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

	/**
	 * Return the path to the services image
	 * @return string
	 */
	public static function getImagePath() {
		return Yii::getAlias('@webroot/' . self::IMAGE_PATH);
	}

	/**
	 * Return the URL to the services image, if the file exists.
	 * @return string|null
	 */
	public static function getImageUrl() {
		if (file_exists(self::getImagePath())) {
			return Url::to([
				'@web/' . self::IMAGE_PATH,
				'v' => filemtime($path),
			]);
		} else {
			return null;
		}
	}
}
