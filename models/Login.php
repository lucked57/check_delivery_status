<?php

    namespace app\models;

    use yii\db\ActiveRecord;

    class Login extends ActiveRecord{

        
        public static function tableName(){
        return 'user';
    }
        
        
        public function attributeLabels(){
        
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
        
    }
        
        
        public function rules(){
        return [
            [ ['username', 'password'], 'trim' ],
            [ ['username', 'password'], 'required' ], //, 'message' => 'Поле обязательно для заполнения'
            [ 'username', 'email' ],
           // [ 'name', 'string', 'min' => 2 ], //, 'tooShort' => 'wrong'
           // [ 'name', 'string', 'max' => 5], //tooLong
            [ 'username', 'string', 'length' => [2,100]],
           // [ 'username', 'myRule' ],
        ];
    }
        
        
         public function myRule($attr){ // самописные валидаторы работают только на сервере
            $cats = Login::find()->asArray()->where(['username' => $username])->all();
        if( empty($cats)){
            $this->addError($attr, 'Такого имени нету в массиве');
        }
        
    }
        
    }
