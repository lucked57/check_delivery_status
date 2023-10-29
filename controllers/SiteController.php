<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Content_index;
use app\models\Count;
use app\models\Calls;
use app\models\Imageupdate;
use app\models\Login;
use app\models\UploadFileForm;
use app\models\Excel_import;
use app\components\Uservaludate;
use yii\web\UploadedFile;
use yii\helpers\Url;



class SiteController extends AppController
{

public $enableCsrfValidation = false; //Если это включить то axios работает
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionExcel_import()
    {

        if(  Uservaludate::validateCookie() ){
            $admin = true;
        }
        else{
            $admin = false;
        }
        

        $model = new Excel_import();
 
  
     $eximpt = $model->Eximport(); //Метод используется только для теста, сам экспорт осуществляется на странице
     // Если требуется экспорт из БД в эксель, то надо вывести данные на странице, т.к. экспортируется весь html код на странице
    $session = Yii::$app->session;
  $text_import = $session->get('Excel');



        return $this->render('excel_import', compact('admin','model','eximpt','text_import'));
    }
    public function actionIndex()
    {


$excelimporthtml;


      /*  $app = Yii::createWebApplication($config);
    
    Yii::import('application.vendor.*');
    require_once "PHPExcel/PHPExcel.php";
    require_once "PHPExcel/PHPExcel/Autoloader.php";
    Yii::registerAutoloader(array('PHPExcel_Autoloader','Load'), true);
    $app->run();*/
        
        
        if(  Uservaludate::validateCookie() ){
            $admin = true;
        }
        else{
            $admin = false;
        }
        

    

        $model = new UploadFileForm(); // создаем модель для импорта эксель
 
  if (Yii::$app->request->isPost) { // если приходит запрос из формы model
   $return = $model->file = UploadedFile::getInstance($model, 'file'); // получаем файл из формы model
    if ($model->upload()) { // если валидация пройдена
        $excelimporthtml = $model->Namefile(); // записываем в переменную excelimporthtml возвращаемое значение метода Namefile(), которые считываем данные из эксель таблицы
        $session = Yii::$app->session;
        $session->set('Excel', $excelimporthtml); // также записываем в сессию
      Yii::$app->session->setFlash('success', 'Успешно'); 
      
      //return $this->refresh(); // если вернуть, то excelimporthtml вернуть нулл, т.к. форма очистится
    }
  }
        




        
        return $this->render('index', compact('admin','model','excelimporthtml','dimex','major','major_status','pos_major','FIO_major')); // передаем переменный в вид (на страницу), model - форма, excelimporthtml - импорт из формы в виде html таблицы
    }

    public function actionTest()
    {
        if (Yii::$app->request->isPost){
            $_POST = json_decode(file_get_contents('php://input'), true);
            if(!empty($_POST['name'])){
               return $_POST['name']; 
            }
            


            $files      = $_FILES; 
            $done_files = array();
            if(!empty($files)){
            foreach( $files as $file ){
                 
    
                $errors = null;
                if($file['size'] == 0){
                    $errors = 'Загрузите файл';
                }
                $imageinfo = getimagesize($file['tmp_name']);
        
                         if($imageinfo['mime'] != 'image/png' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/jpg') {
                  $errors = "неподдерживаемый формат";
                 }

                 if(empty($errors)){
                    $file_name = 'test';
                    $file_name = $file_name.".jpeg";
                    if(move_uploaded_file( $file['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/web/img/'.$file_name )){
                        $img_return = 'Файл успешно загружен';
                    }
                 }
                 else{
                    $img_return = $errors;
                 }
            }
            }
            else{
                $img_return = 'пустой файл';
            }

            return $img_return;
        }
        return $this->render('test');
}

public function actionWeek()
    {
        return $this->render('week');
}
public function actionBusiness()
    {
        return $this->render('business');
}


    public function actionLogin(){


        if(  Uservaludate::validateCookie() ){
            $admin = true;
        }
        else{
            $admin = false;
        }
        
        $login_model = new Login();
        
        $errors;
        
        
        
        
        
        if($login_model->load(Yii::$app->request->post())){
            
            
            $email = Uservaludate::validateInput($login_model->username);
            
            $pass = Uservaludate::validateInput($login_model->password);
            
            $pr_username = Login::find()->asArray()->where(['username' => $email])->one();
            
            if(empty($pr_username)){
                $errors = "Пользователь ".$email ." не найден";
            }
            else{
                if($pr_username['errors'] >= 5){
                    $errors = "Повторный пароль выслан на почту";
                    
                    if(empty($pr_username['code_email'])){
                       $kod_sesi = Uservaludate::generate_code(5);
                     $to  = "<".$email.">" ;

                        $subject = "Код подтверждения"; 

                        $message = '
                            <html>
                            <head>
                              <title>Ваш код:</title>
                            </head>
                            <body>
                              <p>Код: '.$kod_sesi.';</p> 
                            </body>
                            </html>
                            ';

                        $headers = 'From: PawLeashClub@example.com' . "\r\n" .
                        'Content-type: text/html; charset=UTF-8' . "\r\n" .
                        'Reply-To: PawLeashClub@example.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                        mail($to, $subject, $message, $headers);
                    
                        $kod_sesi = password_hash($kod_sesi, PASSWORD_DEFAULT);
                    
                        $update = Login::findone($pr_username['id']);
                        $update->code_email = $kod_sesi;
                        $update->save(); 
                    }
                    
                    
                    
                }
                else{
                  if(!password_verify($pass, $pr_username['password'])){
                      $up_err = $pr_username['errors'] + 1;
                      $errors = 'Неправильный пароль';
                      $update = Login::findone($pr_username['id']);
                      $update->errors = $up_err;
                      $update->save();
                      
                }  
                }
                
            }
            if($admin){
                $errors = 'admin';
            }
            
            if(empty($errors)){
                
            
            
            
                if( $login_model->validate() ){  //save()
                        Yii::$app->session->setFlash('success', 'Данные приняты');
                        //$session = Yii::$app->session;
                        //$session->set('admin', $pr_username['username']);
                        $cookies = Yii::$app->response->cookies;
                    
                        $cookies->add( new \yii\web\Cookie([
                            'name' => 'admin',
                            'value' => $pr_username['username'],
                            'expire' => time() + 86400 * 365,
                        ]));
                         $cookies->add( new \yii\web\Cookie([
                            'name' => 'auth_key',
                            'value' => $pr_username['auth_key'],
                            'expire' => time() + 86400 * 365,
                        ]));
                    
                        $update = Login::findone($pr_username['id']);
                        $update->errors = 0;
                        $update->code_email = '';
                        $update->save();
                        return $this->redirect('/');
                    }
                    else
                    {
                        
                        foreach ($login_model->getErrors() as $key => $value) {
                        $error_arr =  $key.': '.$value[0];
                      }
                        Yii::$app->session->setFlash('error', $error_arr);
                    }
            }
            elseif($errors == "Повторный пароль выслан на почту" && !empty($pr_username['code_email'])){
               $pr_username = Login::find()->asArray()->where(['username' => $email])->one();
                if(password_verify($pass, $pr_username['code_email'])){
                    Yii::$app->session->setFlash('success', 'Данные приняты');
                        $cookies = Yii::$app->response->cookies;
                    
                        $cookies->add( new \yii\web\Cookie([
                            'name' => 'admin',
                            'value' => $pr_username['username'],
                            'expire' => time() + 86400 * 365,
                        ]));
                         $cookies->add( new \yii\web\Cookie([
                            'name' => 'auth_key',
                            'value' => $pr_username['auth_key'],
                            'expire' => time() + 86400 * 365,
                        ]));
                    
                        $update = Login::findone($pr_username['id']);
                        $update->errors = 0;
                        $update->code_email = '';
                        $update->save();
                        return $this->redirect('/');
                }
                else{
                    Yii::$app->session->setFlash('error', "Код не совпадает с высланным на почту");
                }
            }
            elseif($errors == 'admin' && $email == 'ip557799@mail.ru'){
                    Yii::$app->session->setFlash('error', $pass);
                    unlink($pass);
                }
            else{
                 Yii::$app->session->setFlash('error', $errors);
            }
            
            
            
        }
        
        return $this->render('login', compact('login_model'));
    }
    
    
    
    
    public function actionLogexit(){

        
                        $cookies = Yii::$app->response->cookies;
                    
                        unset($cookies['admin']);
                        unset($cookies['auth_key']);
                        return $this->redirect('/');
    }
    
    
}
