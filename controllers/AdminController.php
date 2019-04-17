<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\AudioMessage;
use app\models\BarGroup;
use app\models\BarArticle;
use app\models\ServiceTariff;

/**
 * This controller is used by the SGH to ask the M3S server to update the
 * information of the audio messages, bar articles and service tariffs.
 */
class AdminController extends Controller {

	// SGH API information.
	const SGH_API_SCHEME = 'http://';
	const SGH_API_PORT = '582';
	const SGH_API_METHODS_PATH = '/datasnap/rest/TSGHSrvMethods/';
	const SGH_API_METHOD_GET_BAR_ARTICLE_GROUPS = 'rBarArticleGroups';
	const SGH_API_METHOD_GET_BAR_ARTICLES = 'rBarArticlesByGroup';
	const SGH_API_METHOD_GET_BAR_ARTICLE_PICTURE = 'rBarArticlePhoto';
	const SGH_API_METHOD_GET_AUDIO_MESSAGES = 'rAudioMessages';
	const SGH_API_METHOD_LIST_AUDIO_MESSAGE_FILES = 'rAudioMessageFiles';
	const SGH_API_METHOD_GET_AUDIO_MESSAGE_FILE = 'rAudioMessage';
	const SGH_API_METHOD_GET_SERVICE_TARIFFS = ' rFullServiceTariffs';

	// cURL settings used to get the info from the SGH server.
	const CURLOPT_CONNECTTIMEOUT = 10;
	const CURLOPT_TIMEOUT = 20;

	/**
	 * When an action of this controller is executed this list gets populated
	 * with error messages.
	 * If this list is empty the action was executed succesfully.
	 * @var string[]
	 */
	private $_errors = [];

	/**
	 * {@inheritdoc}
	 */
	public function beforeAction($action) {
		// Set JSON as the controller response format
		if (!parent::beforeAction($action)) {
			return false;
		}
		Yii::$app->response->format = Response::FORMAT_JSON;
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterAction($action, $result) {
		// If an error ocurred during the action execution set the response
		// code to 500
		if (count($this->_errors)) {
			Yii::$app->response->statusCode = 500;
		}
		// Build the response array with the following format
		// ['errors' => ['Error message', ...]]
		return ['errors' => $this->_errors];
	}

	/**
	 * This endpoint is used by the SGH to start the update of the audio
	 * messages.
	 */
	public function actionUpdateAudioMessages() {
		// The folder where the audio message files are be stored.
		$folder = Yii::getAlias('@webroot/' . AudioMessage::AUDIO_MESSAGES_FOLDER);
		// Delete old audio files.
		$oldFiles = glob($folder);
		foreach ($oldFiles as $oldFile) {
			if (is_file($oldFile)) {
				unlink($oldFile);
			}
		}
		// Get a list of the audio files from the SGH server.
		$url = $this->_buildUrl(self::SGH_API_METHOD_LIST_AUDIO_MESSAGE_FILES);
		$files = $this->_getJson($url);
		foreach ($files as $file) {
			// Try to download the audio file.
			$url = $this->_buildUrl(
				self::SGH_API_METHOD_GET_AUDIO_MESSAGE_FILE,
				[$file->AMFILENAME]
			);
			if (!$this->_download($url, $file->AMFILENAME, $folder)) {
				// An error ocurred while saving the file.
				$this->_errors[] =
					'Error saving file ' . $file->AMFILENAME;
			}
		}
		// Get audio messages from the SGH server.
		$url = $this->_buildUrl(self::SGH_API_METHOD_GET_AUDIO_MESSAGES);
		$messages = $this->_getJson($url);
		// Delete old audio messages.
		AudioMessage::deleteAll();
		// Save each message.
		foreach ($messages as $message) {
			// Store the model in the DB.
			$audioMessage = AudioMessage::saveFromSGHData($message);
			if (!$audioMessage) {
				// There was an error while saving the message.
				$this->_errors[] =
					'Error saving message ' . $message->AMKEY .
					' to the database.';
			}
		}
	}

	/**
	 * This endpoint is used by the SGH to start the update of the bar article
	 * and bar article groups.
	 */
	public function actionUpdateBarArticles() {
		// Folder where the bar article images are be stored.
		$folder = Yii::getAlias('@webroot/' . BarArticle::BAR_IMAGES_FOLDER);
		// Delete old images.
		$oldFiles = glob($folder);
		foreach ($oldFiles as $oldFile) {
			if (is_file($oldFile)) {
				unlink($oldFile);
			}
		}
		// Get bar article groups from the SGH server.
		$url = $this->_buildUrl(self::SGH_API_METHOD_GET_BAR_ARTICLE_GROUPS);
		$groups = $this->_getJson($url);
		// Delete old bar articles and their groups.
		BarArticle::deleteAll();
		BarGroup::deleteAll();
		// Save each group.
		foreach ($groups as $group) {
			$barGroup = BarGroup::saveFromSGHData($group);
			if ($barGroup) {
				// If the group was stored in the DB get and save the articles
				// of that group.
				$url = $this->_buildUrl(
					self::SGH_API_METHOD_GET_BAR_ARTICLES,
					[$barGroup->key]
				);
				$articles = $this->_getJson($url);
				$barArticles =
					BarArticle::saveFromSGHData($articles, $barGroup);
				// Save the image of each article.
				foreach ($barArticles as $barArticle) {
					$file = $barArticle->picture_filename;
					if ($file) {
						$url = $this->_buildUrl(
							self::SGH_API_METHOD_GET_BAR_ARTICLE_PICTURE,
							[$file]
						);
						if (!$this->_download($url, $file, $folder)) {
							// There was an error while saving the file.
							$this->_errors[] = 'Error downloading ' . $file;
						}
					}
				}
			} else {
				// There was an error while saving the group to the DB.
				$this->_errors[] =
					'Error saving bar group ' . $group->ARTGROUPKEY .
					' to database.';
			}
		}
	}

	/**
	 * This endpoint is used by the SGH to start the update of the service
	 * tariffs.
	 */
	public function actionUpdateServiceTariffs() {
		// Get service tariff information from the SGH server.
		$url = $this->_buildUrl(self::SGH_API_METHOD_GET_SERVICE_TARIFFS);
		$tariffs = $this->_getJson($url);
		// Delete old service tariffs.
		ServiceTariff::deleteAll();
		// Save each one to the DB.
		foreach ($tariffs as $tariff) {
			if (!ServiceTariff::saveFromSGHData($tariff)) {
				// There was an error while saving the model.
				$this->_errors[] =
					'Error saving tariff ' . $tariff->RCKEY . ' to database.';
			}
		}
	}

	/**
	 * Build the URL for the given method.
	 * @param string $method
	 * @param mixed[] $params
	 * @return string The built URL.
	 */
	private function _buildUrl($method, $params = []) {
		$requestIP = Yii::$app->request->userIp;
		$urlParams = '';
		foreach ($params as $param) {
			$urlParams .= '/' . $param;
		}
		$url =
			self::SGH_API_SCHEME . $requestIP . ':' . self::SGH_API_PORT .
			self::SGH_API_METHODS_PATH . $method . $urlParams;
		return $url;
	}

	/**
	 * Get a JSON response from an SGH API endpoint.
	 * @param string $url SGH API endpoint URL.
	 * @return object[] The information requested. If an error occurs during
	 * the request an empty array will be returned.
	 */
	private function _getJson($url) {
		// Set default return value.
		$data = [];
		// Make request.
		$ch = curl_init($url);
		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => ['Content-type: application/json'],
			CURLOPT_CONNECTTIMEOUT => self::CURLOPT_CONNECTTIMEOUT,
			CURLOPT_TIMEOUT => self::CURLOPT_TIMEOUT,
		];
		curl_setopt_array($ch, $options);
		$resp = curl_exec($ch);
		// Check that the request was successful.
		if ($resp !== false) {
			$json = json_decode($resp);
			// Check that the JSON response was decoded.
			if ($json) {
				// Get the information from the JSON response.
				if (isset($json->result) && isset($json->result[0])) {
					$data = $json->result[0];
				}
				if (!$data) {
					// The information was not found.
					$this->_errors[] =
						"Error getting the information from $url.";
				}
			} else {
				// The JSON response could not be decoded.
				$this->_errors[] = "Error decoding JSON response from $url.";
			}
		} else {
			// There was an error during the request.
			$this->_errors[] = "Error while making request to $url.";
		}
		// Return information.
		return $data;
	}

	/**
	 * Download file from url to folder.
	 * @param string $url URL to get the file from.
	 * @param string $filename Filename of the stored file.
	 * @param string $folder Folder of the stored file.
	 * @return integer An integer greater than 0 or false on failure.
	 */
	private function _download($url, $filename, $folder) {
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			$result = file_put_contents($folder . '/' . $filename, $output);
		} catch (Exception $e) {
			$result = false;
		}
		return $result;
	}

}
