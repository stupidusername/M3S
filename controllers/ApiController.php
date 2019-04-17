<?php

namespace app\controllers;

use app\models\AppVersion;
use app\models\AudioMessage;
use app\models\BarArticle;
use app\models\BarGroup;
use app\models\Channel;
use app\models\Music;
use app\models\Service;
use app\models\ServiceTariff;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * API endpoits for client devices.
 */
class ApiController extends \yii\web\Controller {

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action) {
        // Set JSON as the controller response format.
        if (!parent::beforeAction($action)) {
            return false;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return true;
    }

	/**
	 * Get the most recent app update for Android client devices.
	 * @return AppVersion
	 */
	public function actionGetUpdate() {
        $appVersion = AppVersion::getLast(AppVersion::TYPE_APK);
        return $appVersion;
    }

	/**
	 * Get the most recent app update for Linux client devices.
	 * @return AppVersion
	 */
    public function actionGetLastAppUpdate() {
        $appVersion = AppVersion::getLast(AppVersion::TYPE_TAR_GZ);
        return $appVersion;
    }

	/**
     * Get the audio message identified by $key. Some messages have a custom
     * version for each room.
     * @param integer $key Audio message key.
     * @param string $room (optional) Room name or room number.
	 * @return AudioMessage
     */
    public function actionGetAudioMessage($key, $room = null) {
        $audioMessage = AudioMessage::getAudioMessage($key, $room);
        return $audioMessage;
    }

    /**
	 * Get a list of the music radios.
	 * @return array
	 */
	public function actionGetRadios() {
        return Music::getRadios();
    }

    /**
	 * Get the songs from one radio.
     * @param integer $id The radio ID.
	 * @return array
	 */
	public function actionGetRadioSongs($id) {
        return Music::getSongs($id);
    }

    /**
     * Send the albumart data from a song.
     * @param string $songPath Path to the song file under the music folder.
     * @param string $albumartFilename Filename of the albumart. It's used to
     *  help the client determining the file extension.
     * @throws BadRequestException If $songPath is not valid.
     */
    public function actionGetSongAlbumart($songPath, $albumartFilename) {
        // Full path to the song.
        $path = Yii::getAlias('@webroot/' . Music::FOLDER . '/' . $songPath);
        // Get the albumart data and MIME type.
        $albumartData = Music::getSongAlbumartData($path);
        // If $albumartData is null the the $songPath is probably wrong.
        if (!$albumartData) {
            throw new BadRequestHttpException('The song path is not valid.');
        } else {
            Yii::$app->getResponse()->sendContentAsFile(
                $albumartData['data'],
                $albumartFilename,
                ['mimeType' => $albumartData['mimeType']]
            );
        }
    }

    /**
	 * Get TV channel categories.
	 * @return array
	 */
	public function actionGetChannelCategories() {
        return Channel::getCategories();
    }

    /**
	 * Get the TV channels from one category.
     * @param integer $categoryId The channel category ID.
	 * @return array
	 */
	public function actionGetChannels($categoryId) {
        return Channel::getChannels($categoryId);
    }

	/**
	 * Get bar groups.
	 * @return BarGroup[]
	 */
	public function actionGetBarGroups() {
        $barGroups = BarGroup::find()->all();
        return $barGroups;
    }

    /**
     * Get the articles of a bar group.
     * @param integer $id Nar group ID
     * @return BarArticle[]
     */
    public function actionGetBarArticles($id) {
        $barArticles =
            BarArticle::find()->andWhere(['bar_group_id' => $id])->all();
        return $barArticles;
    }

    /**
	 * Get a list of the service tariffs.
	 * @return ServiceTariff[].
	 */
    public function actionGetServiceTariffs() {
        $serviceTariffs =
			ServiceTariff::find()->orderBy(['key' => SORT_ASC])->all();
        return $serviceTariffs;
    }

	/**
	 * Get a list of the services provided.
	 * @return Service[]
	 */
    public function actionGetServices() {
        $services = Service::find()->all();
        return $services;
    }
}
