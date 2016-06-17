<?php

namespace mikk150\queuemailer;

use mikk150\queuemailer\jobs\MailJob;
use yii\di\Instance;
use yii\mail\BaseMailer;

/**
*
*/
class Mailer extends BaseMailer
{

    /**
     * mailer config or component to send mail out in the end
     */
    public $mailer;

    protected function sendMessage($message)
    {
        $job=new MailJob([
            'message' => $message,
            'mailer' => $this->mailer
        ]);
        return $job->push();
    }

    public function compose($view = null, array $params = [])
    {
        $mailer = Instance::ensure($this->mailer, 'yii\mail\BaseMailer');
        return $mailer->compose($view, $params);
    }
}
