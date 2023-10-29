<?php

namespace app\models;

//use Yii;
use yii\db\ActiveRecord;


class HomePage extends ActiveRecord
{
    public static function tableName(){
        return 'HomePage';
    }
    
}