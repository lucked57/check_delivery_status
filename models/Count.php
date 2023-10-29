<?php

namespace app\models;

//use Yii;
use yii\db\ActiveRecord;


class Count extends ActiveRecord
{
    public static function tableName(){
        return 'Count';
    }

        
     
    public function attributeLabels()
    {
        return [
            'id'     => 'id',
            'Dimex'          => 'Счетчик ТК'   ,
            'Dimex_date'     => 'Дата обновления',
            'Major'          => 'Счетчик ТК'   ,
            'Major_date'     => 'Дата обновления',
            'DPD'          => 'Счетчик ТК'   ,
            'DPD_date'     => 'Дата обновления',
            'KCE'          => 'Счетчик ТК'   ,
            'KCE_date'     => 'Дата обновления',
        ];
    }
     
    public function attributes()
    {
        return [
            'id',
            'Dimex',
            'Dimex_date',
            'Major',
            'Major_date',
            'DPD',
            'DPD_date',
            'KCE',
            'KCE_date',
        ];
    }
    
}