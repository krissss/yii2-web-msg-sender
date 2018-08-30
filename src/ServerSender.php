<?php

namespace kriss\webMsgSender;

/**
 * @method sendSuccess($msg, $to = '')
 * @method sendDanger($msg, $to = '')
 * @method sendInfo($msg, $to = '')
 * @method sendWarning($msg, $to = '')
 */
class ServerSender
{
    public $webMsgSender;

    public function __construct(WebMsgSender $webMsgSender)
    {
        $this->webMsgSender = $webMsgSender;
    }

    public function __call($name, $params)
    {
        if (substr($name, 0, 4) == 'send') {
            call_user_func([$this, 'send'], ...array_merge($params, [strtolower(substr($name, 4))]));
        }
    }

    protected function send($msg, $type, $to = '')
    {
        $this->curl($this->formatMsg($msg, $type), $to);
    }

    protected function formatMsg($msg, $type)
    {
        return strtr($this->webMsgSender->pushMsgTemplate, [
            '{type}' => $type,
            '{time}' => date('Y-m-d H:i:s'),
            '{content}' => $msg,
        ]);
    }

    protected function curl($msg, $to)
    {
        $pushApiUrl = $this->webMsgSender->getPushApiClientUrl();
        $data = [
            'type' => 'publish',
            'content' => $msg,
            'to' => $to,
        ];
        $this->webMsgSender->logger(['pushApiUrl' => $pushApiUrl, 'data' => $data]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pushApiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        $this->webMsgSender->logger(['result' => $result]);
    }
}
