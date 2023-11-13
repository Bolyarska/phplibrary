<?php

namespace frontend\models;

use Yii;
use yii\helpers\Html;
use yii\base\Model;
use common\models\User;
use yii\helpers\VarDumper;


class SignupForm extends Model
{   
    public $name_surname;
    public $username;
    public $email;
    public $phone_number;
    public $address;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['name_surname', 'username', 'email', 'phone_number', 'address', 'password', 'password_repeat'], 'required'],
            ['username', 'string', 'min' => 4, 'max' => 64],
            ['username', 'validateUsername'],
            ['email', 'email'],
            ['phone_number', 'string', 'length' => 10],
            ['address', 'string', 'max' => 128],
            [['password', 'password_repeat'], 'string', 'min' => 8, 'tooShort' => 'Password must be at least 8 characters.'], //=> Yii::$app->params['user.passwordMinLength']
            [['password_repeat'], 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function validateUsername($attribute, $params)
    {
        $user = User::findOne(['username' => $this->username]);
        if ($user !== null) {
            $this->addError($attribute, 'This username already exists.');
        }
    }

    /*public function validateEmail($attribute, $params)
    {
        $email = $this->email;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError($attribute, 'Invalid email');
        }
    }*/


    public function signup()
    {   

        $user = new User();
        $user->name_surname = $this->name_surname;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->password = \Yii::$app->security->generatePasswordHash($this->password);
        $user->phone_number = $this->phone_number;
        $user->address = $this->address;

        //$user->auth_key = \Yii::$app->security->generateRandomString();
        //$user->access_token = \Yii::$app->security->generateRandomString();


        if ($user->save()){
            return true;
        }

        \Yii::error("User was not saved: ".VarDumper::dumpAsString($user->errors));
        return false;
    }

}