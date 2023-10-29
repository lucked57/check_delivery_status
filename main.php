<?php

use app\assets\AppAsset;

use yii\helpers\Html;
use app\models\Login;
use app\components\Uservaludate;
use app\models\Content_index;


$login_model = new Login();

$isAdmin = false;

$cookies = Yii::$app->request->cookies;

$content_footer = Content_index::find()->asArray()->where(['type' => 'footer'])->one();

$content_index = Content_index::find()->orderBy(['id' => SORT_ASC])->asArray()->limit(1000)->all();



$url_rel      = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];



if (($cookie = $cookies->get('admin')) !== null) {
    $email = $cookie->value;
    $pr_admin = Login::find()->asArray()->where(['username' => $email])->one();
}
if (($cookie = $cookies->get('auth_key')) !== null) {
    $auth_key = $cookie->value;
}




if(!empty($pr_admin)){
    if(strcasecmp($pr_admin['auth_key'], $auth_key) == 0){
    $isAdmin = true;

    $this->registerJsFile(
    '@web/js/admin.js',
    ['depends' => 'yii\web\YiiAsset']
    );
}
}


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="yandex-verification" content="e50055ae84491665" />
    <meta name="google-site-verification" content="YZhu-5lY6d8u-B1t59zj-V1RZug50_MHqvIWTMrYHwM" />
    <title><?= Html::encode($this->title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
   <link rel="shortcut icon" href="../web/favicon.ico" type="image/x-icon">
       <link rel="canonical" href="http://buhprogrecc.ru/">
   
</head>
<body class="">
   <?php $this->beginBody() ?>

     <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(76976161, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/76976161" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

        <div class="modal-menu"></div>
        
        
      <nav class="navbar navbar-expand-lg fixed-top navbar-custom sticky sticky-dark">
        <div class="container">
            <!-- LOGO -->
            <a class="navbar-brand logo text-uppercase" href="index-1.html">
                <img src="images/logo.png" class="logo-light" alt="" height="23">
                <img src="images/logo.png" class="logo-dark" alt="" height="23">
                <a style="color: #06d19c !important;">БухПрогресс</a>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <i class="mdi mdi-menu"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto navbar-center" id="mySidenav">
                    <li class="nav-item active">
                        <a href="#home" class="nav-link">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a href="#services" class="nav-link">Услуги</a>
                    </li>
                    <li class="nav-item">
                        <a href="#features" class="nav-link">Преимущества</a>
                    </li>
                    <li class="nav-item">
                        <a href="#client" class="nav-link">Отзывы</a>
                    </li>
                    <li class="nav-item">
                        <a href="#pricing" class="nav-link">Цены</a>
                    </li>
                    <li class="nav-item">
                        <a href="#faq" class="nav-link">FAQ</a>
                    </li>
                   <!--<li class="nav-item">
                        <a href="#blog" class="nav-link">Команда</a>
                    </li>-->
                    <li class="nav-item">
                        <a href="#contact" class="nav-link">Контакты</a>
                    </li>
                </ul>
                <?php if($isAdmin): ?>


         <!-- <a target="_blank" href="/site/meta" class="nav-link" style="cursor: pointer; padding: 0px;">
    <button style="cursor: pointer; padding: 0px;" class="btn pull-xs-righ" type="submit" style="border-radius: 50em;">Meta tags<i class="fa fa-sign-out ml-2" aria-hidden="true"></i></button>
    </a>-->

          <a target="_blank" href="https://search.google.com/search-console?resource_id=http%3A%2F%2Fbuhprogrecc.ru%2F" class="nav-link" style="cursor: pointer;">
    <button style="cursor: pointer; padding: 0px;" class="btn pull-xs-righ" type="submit" style="border-radius: 50em;">Search Console<i class="fa fa-sign-out ml-2" aria-hidden="true"></i></button>
    </a>

    <a target="_blank" href="https://metrika.yandex.ru/dashboard?group=day&period=week&id=76976161" class="nav-link" style="cursor: pointer;">
    <button style="cursor: pointer; padding: 0px;" class="btn pull-xs-righ" type="submit" style="border-radius: 50em;">Я_Аналитика<i class="fa fa-sign-out ml-2" aria-hidden="true"></i></button>
    </a>


         <a href="/site/logexit" class="nav-link">
    <button class="btn form-inline pull-xs-righ" type="submit" style="border-radius: 50em; padding: 0px;">Выйти<i class="fa fa-sign-out ml-2" aria-hidden="true"></i></button>
    </a>
     <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->
             
                  <?= $content ?>
          
                    <?php
                     $now = new DateTime();
                    $current_year = substr($now->format('Y-m-d H:i:s'), 0, 4);
                    ?>

                    <!-- START FOOTER -->
    <section class="bg-footer">
        <div class="container">
          
            <div class="row">
                <div class="col-lg-6">
                    <div class="footer-logo">
                        <h2 class="text-white">БухПрогресс</h2>
                    </div>
                    <a href="tel:+79269647624" class="text-muted mt-2"> +7 (926) 964-76-24</a> 
                    <p class="text-muted mt-4">buhprogrecc@mail.ru</p>
                    
                </div>

                <!--<div class="col-lg-3">
                    <div class="footer-contant footer-spacing">
                        <h6 class="footer-title">Services</h6>
                        <ul class="list-unstyled footer-link mt-4">
                            <li><a href="">Web Design</a></li>
                            <li><a href="">Development</a></li>
                            <li><a href="">Wordpress</a></li>
                            <li><a href="">Online Maeketing</a></li>
                        </ul>
                    </div>
                </div>-->

                <!--<div class="col-lg-3">
                    <div class="footer-contant footer-spacing">
                        <h6 class="footer-title">About us</h6>
                        <ul class="list-unstyled footer-link mt-4">
                            <li><a href="">About us</a></li>
                            <li><a href="">Work portfolio</a></li>
                            <li><a href="">Team</a></li>
                            <li><a href="">Plan & Pricing</a></li>
                        </ul>
                    </div>

                </div>-->

                <div class="col-lg-6">
                    <div class="footer-contant">
                        <h6 class="footer-title">Наш адрес</h6>
                        <p class="text-muted mt-4">Московская область, г. Королёв, ул. Циолковского, д. 27, оф. №2</p>

                        <div class="mt-4">
                            <!--<ul class="list-inline footer-social">
                                <li class="list-inline-item">
                                    <a href="#" class="rounded-circle">
                                        <i class="fab fa-vk"></i>
                                    </a>
                                </li>

                                <li class="list-inline-item">
                                    <a href="#" class="rounded-circle">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>

                                <li class="list-inline-item">
                                    <a href="#" class="rounded-circle">
                                        <i class="fab fa-yandex"></i>
                                    </a>
                                </li>
                            </ul>-->
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <p class="footer-alt mb-0">© <?=$current_year?> Все права защищены</p>

            </div>
        </div>
    </section>
    
      
      
    
    
    <?php if($isAdmin == true): ?>
     <script type="text/javascript" src="http://buhprogrecc.loc/web/js/nicEdit.js"></script>
          <script type="text/javascript">
            bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
          </script>



<div class="container-fluid popup">
          <div class="row justify-content-lg-center li-service">
            <div class="col-lg-12 col-xl-10">
              <button type="button" style="font-size: 35px;" class="close popup-close">
          <span aria-hidden="true">&times;</span>
        </button>
                    <h4>Текст</h4>
                    <textarea style="width: 100%; height: 300px;" id="textarea_ru" name="area2" cols="40"></textarea>

                    <button id="change_text" type="submit" class="btn btn-primary mt-5 mb-5">Изменить</button>


</div>
          </div>
        </div>
        



        



    <?php endif; ?> 

      <!--<link rel="stylesheet" href="css/bootstrap.min.css">
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
      <link rel="stylesheet" href="css/materialdesignicons.min.css">
      <link rel="stylesheet" href="css/icon-pe.css">
      <link rel="stylesheet" href="css/owl.carousel.css">
      <link rel="stylesheet" href="css/owl.theme.css">
      <link rel="stylesheet" href="css/style.css">-->
       </main>
    <?php $this->endBody() ?>
  

  
</body>
</html>
<?php $this->endPage() ?>
