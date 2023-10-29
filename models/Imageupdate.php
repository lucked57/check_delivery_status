<?php

    namespace app\models;

    use yii\db\ActiveRecord;
    use yii\web\UploadedFile;
    use app\components\Uservaludate;
    use app\models\Content_index;

    class Imageupdate extends ActiveRecord{

        public $id;
        public $image;

        
        public static function tableName(){
        return 'content_index';
    }
        
        
        public function attributeLabels(){
        
        return [
            'id' => 'Нажмите на форму "выберите файл" или перетащите туда изображение',
            'image' => 'Изображение',
        ];
        
    }
        
        
        public function rules(){
        return [ 
            [ ['id'], 'trim' ],
            [['image'], 'file', 'extensions' => 'png, jpg', 'maxSize' => 1*(1024*1024),],
             //[ 'image', 'myRule' ],
        ];
    }
        
        public function myRule($attr){
            $imageinfo = getimagesize($this->image->tempName);
            if($imageinfo['mime'] != 'image/png' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/jpg'){
               $this->addError($attr, 'Есть пустые значения'); 
            }
        }
        
        public function upload($id, $image){
            if(!empty($id)){
                    $errors;

                    $imageinfo = getimagesize($image->tempName);
                    if($imageinfo['mime'] != 'image/png' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/jpg'){
                       $errors = 'Данный файл формата '.$imageinfo['mime'].' необходимо загружать файлы данных форматов: jpg, jpeg или png';
                    }

                    if(!($image->extension == 'jpg' || $image->extension == 'jpeg' || $image->extension == 'png')){
                        $errors = 'Данный файл формата '.$image->extension.' необходимо загружать файлы данных форматов: jpg, jpeg или png';
                    }

                    

                    if(empty($image)){
                        $errors = 'Файл не найден';
                    }

                    if(empty($errors)){
                        $imageName = time();
                        
                        $image->saveAs($_SERVER['DOCUMENT_ROOT'].'/web/images/'.$imageName.'.'.$image->extension);

                        $file_success = $imageName.'.'.$image->extension;

                        $id = Uservaludate::validateInput($id);
                        $img_full_name = 'web/images/'.$imageName.'.'.$image->extension;
                        $model = Content_index::find()->where(['id' => $id])->one();

                        unlink($_SERVER['DOCUMENT_ROOT'].'/'.$model['img_full']);
                        $model->img_full = $img_full_name;
                        $model->save();


                        if(filesize($_SERVER['DOCUMENT_ROOT'].'/'.$img_full_name) > 200000){
                        $file_size = filesize($_SERVER['DOCUMENT_ROOT'].'/'.$img_full_name)/1000000;
                        $file_size = round($file_size, 2);
                            $return = 'Размер вашего файла '.$file_size.' мб, не рекемондуется загружать файл размером более 200кбайт (0.2 мб), посколько это отрицательно скажется на скорости загрузки';
                        }
                        else{
                            $return = 'Изображение '.$file_success.' успешно загружено на сервер';
                        }



                        
                    }
                    else{
                    $return = $errors;
                }
                    
                }
                else{
                    $return = $errors;
                }
                return $return;

    }
        
        public function delete_post($id){
            
            /*$table = Uploadpost::find()->asArray()->where(['id' => $id])->one();
            
            if(!empty($table['image'])){
                unlink('./'.$table['image']);
            }
            if(!empty($table['image_min'])){
                unlink('./'.$table['image_min']);
            }
            
            
            
            
            
            $table = Uploadpost::findone($id);
            
            $table->delete();*/
            
            
            
            return true;
        }
        
        
    }
