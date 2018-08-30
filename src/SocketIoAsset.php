<?php

namespace kriss\webMsgSender;

use yii\web\AssetBundle;

class SocketIoAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../asset';
    public $css = [
    ];
    public $js = [
        'socket.io.js'
    ];
}
