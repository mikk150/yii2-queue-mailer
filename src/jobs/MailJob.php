<?php

namespace mikk150\queuemailer\jobs;

use yii\queue\Job;
use yii\base\Object;
use yii\di\Instance;

/**
 *
 */
class MailJob extends Object implements Job
{
    public $mailer;

    public $message;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $mailer = Instance::ensure($this->mailer, 'yii\mail\BaseMailer');
        $mailer->send($this->message);
    }
}
