<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "bar_article".
 * @property integer $id
 * @property integer $bar_group_id
 * @property integer $key
 * @property integer $id_number
 * @property string $name
 * @property string $description
 * @property string $picture_filename
 * @property string $price
 */
class BarArticle extends \yii\db\ActiveRecord {

    // The folder where the article pictures are stored.
    const BAR_IMAGES_FOLDER = 'images/bar';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'bar_article';
    }

    /**
     * {@inheritdoc}
     */
    public function fields() {
		// Add pictureUrl field.
        $fields = parent::fields();
        $fields['pictureUrl'] = function () {
            if ($this->picture_filename) {
                $encFilename = rawurlencode($this->picture_filename);
                return Url::to(
                    '@web/' . self::BAR_IMAGES_FOLDER . '/'. $encFilename,
					true
				);
            } else {
                return null;
            }
        };
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
			[['bar_group_id', 'key', 'name'], 'required'],
            [['bar_group_id', 'key', 'id_number'], 'integer'],
			[
				['bar_group_id'],
				'exist',
				'targetClass' => BarGroup::class,
				'targetAttribute' => 'id',
			],
			[['name', 'picture_filename'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['price'], 'number'],
        ];
    }

    /**
     * Save bar articles from a group of SGH bar article data objects.
     * @param object[] $articles
     * @param BarGroup $barGroup The bar group which the articles belong to.
     * @return BarArticle[] Saved bar articles.
     */
    public static function saveFromSGHData($articles, $barGroup) {
        // Save the articles.
        $return = [];
        foreach ($articles as $article) {
            $model = new self();
            $model->bar_group_id = $barGroup->id;
            $model->key = $article->ARTKEY;
            $model->id_number = $article->ARTIDNUM;
            $model->name = $article->ARTNAME;
            $model->description = $article->ARTDESC;
            $model->picture_filename = $article->ARTPHOTOFILENAME;
            $model->price = $article->ARTNEWPRICE;
            if ($model->save()) {
                $return[] = $model;
            } else {
                // If there was and old model that could not be updated
                // delete it.
                $model = self::find()->where(['' => $article->ARTKEY])->one();
                if ($model) {
                    $model->delete();
                }
            }
        }
        return $return;
    }
}
