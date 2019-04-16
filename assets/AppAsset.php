<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle {

    /**
     * {@inheritdoc}
     */
    public $basePath = '@webroot';

    /**
     * {@inheritdoc}
     */
    public $baseUrl = '@web';

    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/site.css',
    ];

    /**
     * {@inheritdoc}
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
