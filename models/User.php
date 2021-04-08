<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property boolean $deleted
 */
class User extends ActiveRecord implements IdentityInterface {

    // How long the login is remembered. 2592000 seconds = 1 month.
    const LOGIN_TIME = 2592000;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public static function find() {
        return new class(get_called_class()) extends ActiveQuery {
            // Add active() method to user queries to find non-deleted users.
            public function active() {
                return $this->andWhere(
                    ['or', ['deleted' => 0], ['deleted' => null]]
                );
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function delete() {
        $this->deleted = true;
        $this->save(false);
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['username'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Username',
        ];
    }

    /**
     * Get the active user.
     * @return User
     */
    public static function getActiveUser() {
        $userId = Yii::$app->user->id;
        return self::find()->where(['id' => $userId])->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return self::find()->where(['id' => $id])->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        // This functionality is not implemented.
        return null;
    }

    /**
     * Find user by username.
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return self::find()
            ->active()->andWhere(['username' => $username])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }

    /**
     * Validate .
     * @param string $password Password to validate.
     * @return boolean If password provided is valid for current user.
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword(
            $password,
            $this->password
        );
    }
}
