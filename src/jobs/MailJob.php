<?php

namespace mikk150\queuemailer\jobs;

use yii\queue\ActiveJob;
use yii\di\Instance;

/**
*
*/
class MailJob extends ActiveJob
{
    public $mailer;

    public $message;

    public function queueName()
    {
        return 'mailjob';
    }

    public function run()
    {
        $mailer = Instance::ensure($this->mailer, 'yii\mail\BaseMailer');
        $mailer->send($this->message);
    }
}
