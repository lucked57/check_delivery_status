<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/magnific-popup.css',
        //'css/bootstrap.min.css',
        'css/bootstrap5.min.css',
        //'https://use.fontawesome.com/releases/v5.6.3/css/all.css',
        //'css/materialdesignicons.min.css',
        //'css/Pe-icon-7-stroke.css',
        //'css/owl.carousel.css',
        //'css/owl.theme.css',
        //'css/owl.transitions.css',
        //'css/style.css',

    ];
    public $js = [
        //'https://code.jquery.com/jquery-3.3.1.min.js',
        //'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
        
        //'js/jquery.min.js',
        //'js/bootstrap.bundle.min.js',
        'js/bootstrap5.bundle.min.js',
        'web/npm/vue@2.6.14',
        'web/axios/dist/axios.min.js',
        'web/react/build/react.min.js',
        //'js/jquery.easing.min.js',
        //'js/scrollspy.min.js',
        //'js/jquery.magnific-popup.min.js',
        //'js/owl.carousel.min.js',
        //'js/contact.init.js',
        //'js/jquery.mb.YTPlayer.js',
        'js/app.js',
        'js/main.js',
        //'js/admin.js',
        //'js/nicEdit.js',
    ];
    
    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}