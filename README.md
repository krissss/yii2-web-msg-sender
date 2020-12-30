Yii2 Web Msg Sender
===================
Yii2 web msg sender with workerman

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist kriss/yii2-web-msg-sender "*"
```

or add

```
"kriss/yii2-web-msg-sender": "*"
```

to the require section of your `composer.json` file.

Usage
-----

### 1. Config

in `web.php` or `main-local.php`

```php
<?php
use kriss\webMsgSender\WebMsgSender;

return [
    'components' => [
        // others
        WebMsgSender::COMPONENT_NAME => [
            'class' => WebMsgSender::class,
            // config see WebMsgSender public attributes
            // change like this
            //'socketPort' => 2123
        ],
    ],
];
```

> Don't forget to open the port on the server, default port is 2120 for socketIO client and 2121 for PushApi from system. 2120 must open for client connect, 2121 must open for current system (or other system) to curl to push message.

### 2. Console Server

config in `console/config`

```php
<?php
return [
    'controllerMap' => [
        'web-msg-sender-service' => [
            'class' => \kriss\webMsgSender\ServiceController::class,
        ],
    ],
];
```

then start server by

```bash
php yii web-msg-sender-service/start
```

### 3. View Client

in `views/layout` or other view file

```php
<?= \kriss\webMsgSender\ClientReceiveWidget::widget(['uid' => Yii::$app->user->id]) ?>
```

open browser and open the view

### 4. Send One Msg For Test

```php
<?php
use kriss\webMsgSender\WebMsgSender;
use yii\helpers\Html;

WebMsgSender::getComponent()->getSender()->sendInfo('You Have A Message,' . Html::a('[clickMe]', 'http://www.baidu.com'));
```

### 5. Preview

![preview](https://github.com/krissss/yii2-web-msg-sender/raw/master/screenshots/preview.png)


FAQ
-----

### How to use https or wss with socket (config ssl)

two way:

1. SocketIO origin ssl config

add config

```php
use kriss\webMsgSender\WebMsgSender;

return [
    'components' => [
        // others
        WebMsgSender::COMPONENT_NAME => [
            'class' => WebMsgSender::class,
            // others
            'socketOpts' => [
                'ssl' => [
                    'local_cert'  => __DIR__ . '/local-ssl.test.pem', // absolute path
                    'local_pk'    => __DIR__ . '/local-ssl.test.key',
                    'verify_peer' => false,
                ],
            ],
            'receiveSocketUrl' => 'https://local-ssl.test:{port}', // domain must match ssl cert
        ],
    ],
];
```

2. Use Nginx to proxy forward ssl

```conf
server {
  listen 443;

  ssl on;
  ssl_certificate /etc/ssl/local-ssl.test.pem;
  ssl_certificate_key /etc/ssl/local-ssl.test.key;
  ssl_session_timeout 5m;
  ssl_session_cache shared:SSL:50m;
  ssl_protocols SSLv3 SSLv2 TLSv1 TLSv1.1 TLSv1.2;
  ssl_ciphers ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP;

  location /socket.io
  {
    proxy_pass http://127.0.0.1:2120;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header X-Real-IP $remote_addr;
  }
}
```