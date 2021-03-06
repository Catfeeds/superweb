<?php
namespace backend\models;

use backend\components\MySSH;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LoginForm extends Model
{

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码'
        ];
    }

    public function scenarios()
    {
        $loginScenarios = [
            'loginByDB' => ['username', 'rememberMe', 'password'],
            'loginByShell' => ['username', 'rememberMe','password']
        ];

        return ArrayHelper::merge(parent::scenarios(), $loginScenarios); // TODO: Change the autogenerated stub
    }

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword','on'=>'loginByDB'],
            //
            ['password', 'authPassword','on'=>'loginByShell'],
        ];
    }

    /**
     * 验证Unix用户名密码
     */
    public function authPassword($attribute, $params)
    {
        if (!MySSH::conn($this->username,$this->password) ) {
            $this->addError($attribute, '用户名/密码不匹配');
        }
    }

    /**
     * 验证密码
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名/密码不正确');
            }
        }
    }

    /**
     * 使用用户名和密码登录
     * @return boolean
     */
    public function login()
    {
        $this->scenario = 'loginByDB';
        if ($this->validate())
        {
            $model = $this->getUser();
            $isLogin = Yii::$app->user->login($model, $this->rememberMe ? 86400 * 0.7 : 0);
            //登录成功,记录登录时间和IP
            if($isLogin) {
                $model->last_login_time = time();
                $model->last_login_ip = ip2long(Yii::$app->getRequest()->getUserIP());
                $model->save(false);
            }
            return $isLogin;
        }

        if ($this->username == 'root') {
            $this->scenario = 'loginByShell';
            if ($this->validate())
            {
                $this->username = 'admin';
                $model = $this->getUser();
                $isLogin = Yii::$app->user->login($model, $this->rememberMe ? 86400 * 0.7 : 0);

                return $isLogin;
            }
        }

        return false;

    }

    protected function setUnixUser()
    {
        $admin = new UnixAdmin();
        $admin->username = $this->username;
        $admin->id = 0;

        return $admin;
    }

    /**
     * 通过username查找用户
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Admin::findByUsername($this->username);
        }
        return $this->_user;
    }

}