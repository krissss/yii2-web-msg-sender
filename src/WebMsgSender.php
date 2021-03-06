<?php

namespace kriss\webMsgSender;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;

class WebMsgSender extends BaseObject
{
    const COMPONENT_NAME = 'web-msg-sender';

    /**
     * socket 端口
     * @see Service
     * @see getReceiveSocketUrl()
     * @var int
     */
    public $socketPort = 2120;
    /**
     * scoketIO 的 options 参数
     * 在配置 ssl 时有效
     * @var array
     */
    public $socketOpts = [];
    /**
     * 用于接收推送消息的端口
     * @see getPushApiServerUrl()
     * @see getPushApiClientUrl()
     * @var int
     */
    public $innerHttpPort = 2121;
    /**
     * 发送的url地址
     * @see getPushApiServerUrl()
     * @see getPushApiClientUrl()
     * @var int
     */
    public $pushApiUrl = 'http://{domain}:{port}/';
    /**
     * If ServerSender is used in console, this must be config, or config $pushApiUrl
     * default is $_SERVER['SERVER_NAME']
     * @see getPushApiClientUrl()
     * @var null|string
     */
    public $pushApiClientHost;
    /**
     * @see getPushApiServerUrl()
     * @var string
     */
    public $pushApiServerHost = '0.0.0.0';
    /**
     * 接收端的socket地址
     * @see getReceiveSocketUrl()
     * @var int
     */
    public $receiveSocketUrl = 'http://{domain}:{port}';
    /**
     * If sockectIO domain is not like web domain, this must be config, or config $receiveSocketUrl
     * @see getReceiveSocketUrl()
     * @var null|string
     */
    public $receiveClientHost;
    /**
     * @see ServerSend::formatMsg()
     * @var string
     */
    public $pushMsgTemplate = <<<HTML
<div class="alert alert-{type} alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  {time} {content}
</div>
HTML;
    /**
     * @see logger()
     * @var string
     */
    public $logCategory = 'app';
    /**
     * @see logger()
     * @var string
     */
    public $logType = 'info';

    /**
     * 发送端的url
     * @return string
     */
    public function getPushApiClientUrl()
    {
        return strtr($this->pushApiUrl, [
            '{domain}' => $this->pushApiClientHost ?: $_SERVER['SERVER_NAME'],
            '{port}' => $this->innerHttpPort,
        ]);
    }

    /**
     * 服务端绑定的地址
     * @return string
     */
    public function getPushApiServerUrl()
    {
        return strtr($this->pushApiUrl, [
            '{domain}' => $this->pushApiServerHost,
            '{port}' => $this->innerHttpPort,
        ]);
    }

    /**
     * @return string
     */
    public function getReceiveSocketUrl()
    {
        return strtr($this->receiveSocketUrl, [
            '{domain}' => $this->receiveClientHost ?: $_SERVER['SERVER_NAME'],
            '{port}' => $this->socketPort,
        ]);
    }

    /**
     * @param $msg
     */
    public function logger($msg)
    {
        Yii::{$this->logType}(Json::encode($msg), $this->logCategory);
    }

    /**
     * @return ServerSender
     */
    public function getSender()
    {
        return new ServerSender(static::getComponent());
    }

    /**
     * @return null|object|static
     * @throws \yii\base\InvalidConfigException
     */
    public static function getComponent()
    {
        return Yii::$app->get(static::COMPONENT_NAME);
    }
}
