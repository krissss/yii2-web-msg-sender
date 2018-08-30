<?php

namespace kriss\webMsgSender;

use PHPSocketIO\Socket;
use PHPSocketIO\SocketIO;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Service
{
    /**
     * 用于存储全局变量，可以在回调中使用
     * @var array
     */
    public $globalData = [];
    /**
     * @var SocketIO
     */
    public $socketIo;
    /**
     * @var WebMsgSender
     */
    public $webMsgSender;

    public function __construct()
    {
        $this->webMsgSender = WebMsgSender::getComponent();

        $this->socketIo = new SocketIO($this->webMsgSender->socketPort);
        $this->globalData['uidConnectMap'] = [];
        // 客户端发起连接事件时，设置连接socket的各种事件回调
        $this->socketIo->on('connection', function (Socket $socket) {
            // 当客户端发来登录事件时触发
            $socket->on('login', function ($uid) use ($socket) {
                $uid = (string)$uid;
                // 已经登录过了
                if (isset($socket->uid)) {
                    return;
                }
                // 加入全局变量用于后面使用
                if (!isset($this->globalData['uidConnectMap'][$uid])) {
                    $this->globalData['uidConnectMap'][$uid] = $uid;
                }
                // 将这个连接加入到uid分组，方便针对uid推送数据
                $socket->join($uid);
                $socket->uid = $uid;
            });
            // 当客户端断开连接是触发（一般是关闭网页或者跳转刷新导致）
            $socket->on('disconnect', function () use ($socket) {
                if (!isset($socket->uid)) {
                    return;
                }
            });
        });
        // 当$socketIo启动后监听一个http端口，通过这个端口可以给任意uid或者所有uid推送数据
        $this->socketIo->on('workerStart', function () {
            // 监听一个http端口
            $innerHttpWorker = new Worker($this->webMsgSender->getPushApiServerUrl());
            // 当http客户端发来数据时触发
            $innerHttpWorker->onMessage = function (TcpConnection $httpConnection) {
                $uidConnectionMap = $this->globalData['uidConnectMap'];
                $requestParams = $_POST ?: $_GET;
                // 推送数据的url格式 type=publish&to=uid&content=xxxx
                $model = new ClientSendModel(['socketIo' => $this->socketIo]);
                if ($model->load($requestParams, '') && $model->validate()) {
                    if ($model->to && !isset($uidConnectionMap[$model->to])) {
                        return $httpConnection->send('offline');
                    }
                    $model->send();
                    return $httpConnection->send('ok');
                }
                return $httpConnection->send('fail');
            };
            // 执行监听
            $innerHttpWorker->listen();
        });
    }

    public function run()
    {
        Worker::runAll();
    }
}
