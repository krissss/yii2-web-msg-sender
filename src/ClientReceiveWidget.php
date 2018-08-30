<?php

namespace kriss\webMsgSender;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Html;

class ClientReceiveWidget extends Widget
{
    public $uid;

    public static $isRendered = false;

    public function init()
    {
        if (!$this->uid) {
            throw new InvalidConfigException('uid 必须');
        }
        // 有且仅能有一个的判断。防止多个socket的建立
        if (static::$isRendered) {
            throw new InvalidConfigException('该组件已被调用，请勿重复调用');
        }
        static::$isRendered = true;
    }

    public function run()
    {
        echo Html::tag('div', '', ['id' => $this->id]);
        $webMsgSender = WebMsgSender::getComponent();

        SocketIoAsset::register($this->view);
        $js = <<<JS
        var uid = '{$this->uid}';
        var containerEl = document.getElementById('{$this->id}');
        var socket = io('{$webMsgSender->getReceiveSocketUrl()}');
        socket.on('connect', function(){
          socket.emit('login', uid);
        });
        socket.on('new_msg', function(msg) {
          containerEl.innerHTML += msg;
        });
JS;
        $this->view->registerJs($js);
    }
}
