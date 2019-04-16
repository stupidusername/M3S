<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "app_version".
 * It stores the information of the updates for Android and Linux client
 * devices.
 * @property integer $id
 * @property string $version
 * @property string $filename
 * @property integer $force_update
 * @property string $path
 */
class AppVersion extends ActiveRecord {

	// Folder used to store client update files.
    const UPDATES_FOLDER = 'client_updates';

    // Updates types and extensions.
    const TYPE_APK = '.apk';
    const TYPE_TAR_GZ = '.tar.gz';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'app_version';
    }

    /**
     * {@inheritdoc}
     */
    public function fields() {
		// The field apkUrl contains the absolute URL to the update file.
		// The field md5 contains the md5 hash of the update file.
        $fields = parent::fields();
        $fields['apkUrl'] = function () {
            return Url::to(
				'@web/' . self::UPDATES_FOLDER . '/' . $this->filename,
				true
			);
        };
		$fields['md5'] = function () {
			return md5_file($this->path);
        };
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete() {
        // Delete the file after the record was deleted.
        unlink($this->path);
        parent::afterDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['version', 'filename'], 'required'],
            [['force_update'], 'integer'],
            [['version', 'filename'], 'string', 'max' => 255],
            [['version', 'filename'], 'unique'],
            [['version'], 'match', 'pattern' => '/^(\d+\.)*\d+$/'],
            [
				['filename'],
				function ($attribute, $params) {
					if (!file_exists($this->path)) {
						$this->addError(
							$attribute,
							"$this->path cannot be found."
						);
					}
				}
			],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'filename' => 'Filename',
            'force_update' => 'Force Update',
        ];
    }

	/**
	 * Get absolute path to the update file.
	 * @return string
	 */
	public function getPath() {
		return Yii::getAlias(
			'@webroot/' . self::UPDATES_FOLDER . '/' . $this->filename
		);
	}

    /**
     * Get the model from which the update information should be extracted.
     * @param string $type One of the update types.
     * @return AppVersion
     */
    public static function getLast($type) {
        $appVersion = null;
        $versions = self::find()->all();
        foreach ($versions as $v) {
			if (self::endsWith($v->filename, $type)) {
				if ($appVersion) {
					if ($v->force_update > $appVersion->force_update) {
						$appVersion = $v;
					} else if ($v->force_update >= $appVersion->force_update) {
                        $compareResult =
                            version_compare($v->version, $appVersion->version);
						if ($compareResult > 0) {
							$appVersion = $v;
						}
					}
				} else {
					$appVersion = $v;
				}
			}
        }
        return $appVersion;
    }

	/**
	 * Check if the string $haystack ends with string $needle.
	 * @param string $haystack
	 * @param string $needle
	 */
	private static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }
}
