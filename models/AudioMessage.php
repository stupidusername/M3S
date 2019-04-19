<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "audio_message".
 * @property integer $id
 * @property integer $key Used as an ID by the SGH server.
 * @property string $name
 * @property string $name_spanish
 * @property string $filename
 * @property integer $kind General or particular.
 * @property integer $audio_output
 * @property integer $delay
 * @property integer $manual
 */
class AudioMessage extends \yii\db\ActiveRecord {

    /**
     * Room number or room name.
     * @var string
     */
    private $room;

	// Folder used to store the audio files.
    const AUDIO_MESSAGES_FOLDER = 'audio_messages';

	// Particular messages use a custom file for each room.
    const KIND_GENERAL = 0;
    const KIND_PARTICULAR = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'audio_message';
    }

    /**
     * {@inheritdoc}
     */
    public function fields() {
        // Add room and audioMessageUrl fields.
        $fields = parent::fields();
		$fields['room'] = function () {
            return $this->room;
        };
        $fields['audioMessageUrl'] = function () {
            $audioMessageUrl = null;
            // If the audio message is customized for each room we need to
            // build the filename using the room number.
            // In this scenario the filename has this format:
            // <filename>.<room>.<extension>
            $bits = [];
            $bits[] = pathinfo($this->filename, PATHINFO_FILENAME);
            if ($this->room && $this->kind == self::KIND_PARTICULAR) {
                $bits[] = $this->room;
            }
            $bits[] = pathinfo($this->filename, PATHINFO_EXTENSION);
            $filename = implode('.', $bits);
            $path = Yii::getAlias(
                '@webroot/' . self::AUDIO_MESSAGES_FOLDER . '/' . $filename
            );
            // If the file for the specified room is not found, fall back
            // to the original filename of the model.
            if (!file_exists($path)) {
                $filename = $this->filename;
            }
            // Build the file URL if the file exists.
            $path = Yii::getAlias(
                '@webroot/' . self::AUDIO_MESSAGES_FOLDER . '/' . $filename
            );
            if (file_exists($path)) {
                $encFilename = rawurlencode($filename);
                $audioMessageUrl = Url::to(
                    '@web/' . self::AUDIO_MESSAGES_FOLDER . '/' . $encFilename,
                    true
                );
            }
            return $audioMessageUrl;
        };
        return $fields;
    }

	/**
     * {@inheritdoc}
     */
	public function rules() {
		return [
			[['key', 'filename'], 'required'],
		];
	}

    /**
     * Save an audio message from one SGH audio message data object.
     * @param object $message
     * @return AudioMessage|null The saved model or null if an error occurred.
     */
    public static function saveFromSGHData($message) {
        $audioMessage = new self();
        $audioMessage->key = $message->AMKEY;
        $audioMessage->name = $message->AMNAME;
        $audioMessage->name_spanish = $message->AMSNAME;
        $audioMessage->filename = $message->AMFILENAME;
        $audioMessage->kind = $message->AMAUDIOKIND;
        $audioMessage->audio_output = $message->AMAUDIOOUTPUT;
        $audioMessage->delay = $message->AMDELAY;
        $audioMessage->manual = $message->AMMANUAL;
        if ($audioMessage->save()) {
            return $audioMessage;
        } else {
            return null;
        }
    }

    /**
     * Find an audio message by its key.
     * @param integer $key
     * @param string $room Used to find the audio file of particular messages.
     * @return AudioMessage|null Null if the model was not found.
     */
    public static function getAudioMessage($key, $room = null) {
        $model = self::find()->where(['key' => $key])->one();
        if ($model) {
            $model->room = $room;
        }
        return $model;
    }
}
