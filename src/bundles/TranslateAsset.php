<?php

namespace dlds\translatemanager\bundles;

use yii\web\AssetBundle;

/**
 * Translation asset bundle
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class TranslateAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dlds/translatemanager/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'stylesheets/helpers.css',
        'stylesheets/translate.css',
    ];
}
