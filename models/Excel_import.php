<?php

    namespace app\models;

    use yii\db\ActiveRecord;
    use yii\web\UploadedFile;
    include("PHPExcel.php");
    include("PHPExcel/Writer/Excel5.php");

    class Excel_import extends ActiveRecord{



        
        public static function tableName(){
        return 'Excel_import';
    }

    public function Eximport()
  {
        return "test";
  }
        
        
        
        
        
    }
