<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the register form
 */
class RegisterForm extends Model {

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $passwordRepeat;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            // Username and password are both required.
            [['username', 'password', 'passwordRepeat'], 'required'],
            // Password is validated by validatePassword().
            ['passwordRepeat', 'validatePassword'],
        ];
    }

    /**
     * Validates the password. This method serves as the inline validation for
     * password.
     * @param string $attribute The attribute currently being validated.
     * @param array $params The additional name-value pairs given in the rule.
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->passwordRepeat != $this->password) {
                $this->addError($attribute, 'Passwords must match.');
            }
        }
    }

    /**
     * Registers and logs in a user using the provided username and password.
     * @return boolean Whether the user is logged in successfully.
     */
    public function register() {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            // Store the hash generated from the entered password.
            $user->password =
                    Yii::$app->security->generatePasswordHash($this->password);
            if ($user->save()) {
                return Yii::$app->user->login($user, User::LOGIN_TIME);
            }
        } else {
            return false;
        }
    }
}
