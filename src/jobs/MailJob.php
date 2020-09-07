<?php

namespace mikk150\queuemailer\jobs;

use yii\base\BaseObject;
use yii\di\Instance;
use yii\queue\Job;

/**
 *
 */
class MailJob extends BaseObject implements Job
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
