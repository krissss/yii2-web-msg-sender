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
            'pushApiClientHost' => 'localhost',
            'logCategory' => 'webMsgSender',
        ],
    ],
];
```

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
