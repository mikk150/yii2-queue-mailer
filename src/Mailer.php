<?php

namespace mikk150\queuemailer;

use mikk150\queuemailer\jobs\MailJob;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;
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

    public function __call($name, $params)
    {
        try {
            return parent::__call($name, $params);
        } catch (UnknownMethodException $e) {
            return call_user_func_array([$this->getInstance(), $name], $params);
        }
    }

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            return $this->getInstance()->{$name};
        }
    }

    public function __set($name, $value)
    {
        try {
            return parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            return $this->getInstance()->{$name} = $value;
        }
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $job=new MailJob([
            'message' => $message,
            'mailer' => $this->mailer
        ]);
        return $job->push();
    }

    /**
     * @inheritdoc
     */
    public function compose($view = null, array $params = [])
    {
        return $this->getInstance()->compose($view, $params);
    }

    public function getInstance()
    {
        return Instance::ensure($this->mailer, 'yii\mail\BaseMailer');
    }
}
