<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bar_group"
 * @property integer $id
 * @property integer $key Used as an ID by the SGH server.
 * @property integer $id_number
 * @property string $name
 */
class BarGroup extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'bar_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['key', 'name'], 'required'],
            [['key', 'id_number'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * Save a bar group from one SGH bar group data object.
     * @param object $group
     * @return BarGroup|null If the bar group was not saved it returns null.
     */
    public static function saveFromSGHData($group) {
        $barGroup = self::findOne(['key' => $group->ARTGROUPKEY]);
        if (!$barGroup) {
            $barGroup = new self();
        }
        $barGroup->key = $group->ARTGROUPKEY;
        $barGroup->id_number = $group->ARTGROUPIDNUM;
        $barGroup->name = $group->ARTGROUPNAME;
        if ($barGroup->save()) {
            return $barGroup;
        } else {
            return null;
        }
    }
}
