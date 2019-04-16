<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var boolean
     */
    public $rememberMe = true;

    /**
     * @var User|false
     */
    private $_user = false;


    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            // Username and password are both required.
            [['username', 'password'], 'required'],
            // RememberMe must be a boolean value.
            ['rememberMe', 'boolean'],
            // Password is validated by validatePassword().
            ['password', 'validatePassword'],
        ];
    }

    /**
     * This method serves as the inline validation for password.
     * @param string $attribute The attribute currently being validated.
     * @param array $params The additional name-value pairs given in the rule.
     */
    public function validatePassword($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Log in a user using the provided username and password.
     * @return boolean Whether the user is logged in successfully.
     */
    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? User::LOGIN_TIME : 0
            );
        } else {
            return false;
        }
    }

    /**
     * Find user by [[username]].
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
