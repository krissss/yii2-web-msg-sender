<?php

namespace kriss\webMsgSender;

use PHPSocketIO\SocketIO;
use yii\base\Model;

class ClientSendModel extends Model
{
    public $type;

    public $to;

    public $content;

    /**
     * @var SocketIO
     */
    public $socketIo;
    /**
     * @var string
     */
    public $socketMsgType = 'new_msg';

    public function rules()
    {
        return [
            [['type', 'content'], 'required'],
            [['type', 'to', 'content'], 'string'],
        ];
    }

    public function send()
    {
        // 发送给客户端
        $socketIo = $this->socketIo;
        if ($this->to) {
            $socketIo->to($this->to);
        }
        $socketIo->emit($this->socketMsgType, $this->content);
    }
}
