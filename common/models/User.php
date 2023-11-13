<?php

namespace common\models;

use Yii;
use \yii\web;
use yii\base\Security;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name_surname
 * @property string $username
 * @property string $email
 * @property string $phone_number
 * @property string $address
 * @property string $password
 * @property string|null $note
 * @property string|null $type
 *
 * @property Returnedbooks[] $returnedbooks
 * @property Savedbooks[] $savedbooks
 * @property Takenbooks[] $takenbooks
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_surname', 'username', 'email', 'phone_number', 'address', 'password'], 'required'],
            [['note', 'type'], 'string'],
            [['name_surname', 'username', 'email', 'password'], 'string', 'max' => 64],
            [['phone_number'], 'string', 'max' => 10],
            [['address'], 'string', 'max' => 128],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['is_active'], 'safe'],
            [['two_factor_enabled'], 'safe'],

            
        ];
    }

        /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_surname' => 'Name Surname',
            'username' => 'Username',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'password' => 'Password',
            'note' => 'Note',
            'type' => 'Type',
            'is_active' => 'is_active',
            'two_factor_enabled' => 'Two Factor Authentication'
        ];
    }

    public static function findIdentity($id)
    {

        //return self::find()->where(['id' => $id])->one();

        $cacheKey = ['user_identity', $id];
        $user = Yii::$app->cache->get($cacheKey);
    
        if ($user === false) {
            $user = self::find()
                ->where(['id' => $id,])
                ->one();
    
            if ($user !== null) {
                Yii::$app->cache->set($cacheKey, $user);

            }
        }
    
        return $user;
    }

    public function getPermissions()
    {
    $cacheKey = ['user_permissions', $this->id];
    $permissions = Yii::$app->cache->get($cacheKey);

    if ($permissions === false) {
        // Fetch the user's permissions from the RBAC system
        // For example, using Yii's RBAC manager or any other RBAC implementation
        // $permissions = Yii::$app->authManager->getPermissionsByUser($this->id);

        // For the sake of this example, let's just return an empty array (no permissions)
        $permissions = [];

        // Cache the permissions for future use
        Yii::$app->cache->set($cacheKey, $permissions);
    }

    return $permissions;
    }

    public static function findByUsername($username)
    {
        return self::find()->where(['username' => $username])->one();
    }

    // Change password functions SetPassword
    public function setPassword($password)
    {
        $security = new Security();
        $this->password = $security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        
    }

    public function validateAuthKey($authKey)
    {
        
    }

    /**
     * Gets query for [[Returnedbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReturnedbooks()
    {
        return $this->hasMany(Returnedbooks::class, ['reader_id' => 'id']);
    }

    /**
     * Gets query for [[Savedbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSavedbooks()
    {
        //return $this->hasMany(Savedbooks::class, ['reader_id' => 'id']);

        $user = Yii::$app->user->identity;
    
        if ($user !== null) {
            return Savedbooks::find()
                ->where(['reader_id' => $user->id])
                ->orderBy(['date_saved' => SORT_DESC])
                ->all();
        }
        
        return [];
    }

    /**
     * Gets query for [[Takenbooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTakenbooks()
    {
        return $this->hasMany(Takenbooks::class, ['reader_id' => 'id']);
    }

    public function validate2FACode($code)
    {
        $_g2fa = new Google2FA();
        $valid = $_g2fa->verifyKey($this->secret_key, $code); // true/false
        
       return $valid;
    }
}
