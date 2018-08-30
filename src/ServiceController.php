<?php

namespace kriss\webMsgSender;

use yii\console\Controller;

class ServiceController extends Controller
{
    public function actionStart() {
        $service = new Service();
        $service->run();
    }
}
