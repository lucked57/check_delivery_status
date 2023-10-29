<?php

    namespace app\models;

    use yii\db\ActiveRecord;

    class Calls extends ActiveRecord{

        
        public static function tableName(){
        return 'calls';
    }
        
    }
