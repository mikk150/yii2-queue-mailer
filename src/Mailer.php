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

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        try {
            return parent::__call($name, $params);
        } catch (UnknownMethodException $e) {
            return call_user_func_array([$this->getInstance(), $name], $params);
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            return $this->getInstance()->{$name};
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
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
        $message = $this->getInstance()->compose($view, $params);
        $message->mailer = $this;
        return $message;
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function getInstance()
    {
        return Instance::ensure($this->mailer, 'yii\mail\BaseMailer');
    }
}
