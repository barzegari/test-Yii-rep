<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\web\UrlManager;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string  $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $mobile
 * @property integer $status
 */
class User extends ActiveRecord implements IdentityInterface
{
    
	public $old_password;
    public $new_password;
    public $repeat_password;
	
	const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['mobile', 'phoneNumber', 'skipOnEmpty' => true],
            ['repeat_password',  'required', 'on'=>['changePass', 'create']],
            ['old_password',  'required', 'on'=>'changePass'],
            ['new_password',  'required', 'on'=>['changePass', 'create']],
            ['old_password', 'checkPass', 'on'=>'changePass'],
            ['repeat_password', 'compare', 'compareAttribute'=>'new_password', 'on'=>['changePass', 'create']],
            ['email', 'email','message'=>"Not a valid email"],
            ['email', 'required'],
            ['username', 'required'],
            ['username', 'unique'],
			['avatar', 'image','skipOnEmpty' => true],
            [['image'], 'file','skipOnEmpty' => true,  'extensions'=>'jpg,png'],
			
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
	
    /**
     * Updates the current avatar.
     * @param $avatar_name string the name that the file will have
     * @return bool returns true if it managed to save correctly the new avatar.
     */
    public function upload($avatar_name)
    {
		try{
		
			if ($this->validate() ){
				
				// resize the image
				$size = getimagesize($this->avatar->tempName);
				$width = $size[0];
				$height = $size[1];
				$ratio = max([$width, $height]) / 200.0;
				$width /= $ratio;
				$height /= $ratio;
				$new_image =  imagecreatetruecolor($width, $height);
				
				$current_image =($this->avatar->extension=="png")?
									imagecreatefrompng($this->avatar->tempName):
									imagecreatefromjpeg($this->avatar->tempName);
				
				imagecopyresized($new_image, $current_image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
				
				// save the image
				$imageName = Yii::$app->security->generateRandomString().".".$this->avatar->extension;
				switch($this->avatar->extension)
				{
					case "png":{
							imagepng($new_image, Yii::$app->basePath. '/web/images/' . $imageName);
						break;
					}
					case "jpg":{
							imagejpeg($new_image, Yii::$app->basePath. '/web/images/' . $imageName);
						break;
					}
				}
				return $imageName;
			}
		}
		catch(Exception $e){
			$this->addError($attribute,'Error In uploading file!' );
			return false;
		}
    }
    /**
     * Checks if the old password is correct
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function checkPass($attribute, $params)
    {
        if (!$this->validatePassword($this->old_password))
            $this->addError($attribute, 'Old password is incorrect.');
    }
    /**
     * Validates the mobile .
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function phoneNumber($attribute, $params)
    {
        if(preg_match("@\+?([0-9\s]{6,26})@",$this->$attribute) === 0)
        {
            $this->addError($attribute,
                'Incorrect Phone number, use the following characters: "0123456789+"' );
        }
    }
    /**
     * fetch stored image file name with complete path 
     * @return string
     */
    public function getImageFile() 
    {
        return isset($this->avatar) ? Yii::$app->params['uploadPath'] . $this->avatar : null;
    }

    /**
     * fetch stored image url
     * @return string
     */
    public function getImageUrl() 
    {
        // return a default image placeholder if your source avatar is not found
		$avatar = isset($this->avatar) ? $this->avatar : 'default_user.jpg';
        return  '/images/' . $avatar;
		
    }

    /**
    * Process upload of image
    *
    * @return mixed the uploaded image instance
    */
    public function uploadImage() {
        // get the uploaded file instance. for multiple file uploads
        // the following data will return an array (you may need to use
        // getInstances method)
        $image = UploadedFile::getInstance($this, 'image');

        // if no image was uploaded abort the upload
        if (empty($image)) {
            return false;
        }

        // store the source file name
        //$this->filename = $image->name;
        $ext = end((explode(".", $image->name)));

        // generate a unique file name
        $this->avatar = Yii::$app->security->generateRandomString().".{$ext}";

        // the uploaded image instance
        return $image;
    }

    /**
    * Process deletion of image
    *
    * @return boolean the status of deletion
    */
    public function deleteImage() {
        $file = $this->getImageFile();

        // check if file exists on server
        if (empty($file) || !file_exists($file)) {
            return true;
        }

        // check if uploaded file can be deleted on server
        if (!unlink($file)) {
            return false;
        }

        // if deletion successful, reset your file attributes
        $this->avatar = null;
        //$this->filename = null;

        return true;
    }	
}
