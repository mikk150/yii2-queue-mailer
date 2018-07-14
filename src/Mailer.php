<?php

namespace mikk150\queuemailer;

use mikk150\queuemailer\jobs\MailJob;
use yii\base\InvalidConfigException;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;
use yii\di\Instance;
use yii\mail\BaseMailer;
use yii\mail\MailerInterface;
use yii\queue\Queue;

/**
 * Class Mailer
 * @package mikk150\queuemailer
 */
class Mailer extends BaseMailer
{
    /**
     * @var string|array|MailerInterface Mailer config or component to send mail out in the end
     */
    public $mailer;

    /**
     * @var string|array|Queue
     */
    public $queue;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->mailer = Instance::ensure($this->mailer, MailerInterface::class);
        $this->queue = Instance::ensure($this->queue, Queue::class);
    }

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
            return call_user_func_array([$this->mailer, $name], $params);
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
            return $this->mailer->{$name};
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            $this->mailer->{$name} = $value;
        }
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $this->queue->push(new MailJob([
            'message' => $message,
            'mailer' => $this->mailer,
        ]));
        return true;
    }

    /**
     * @inheritdoc
     */
    public function compose($view = null, array $params = [])
    {
        $message = $this->mailer->compose($view, $params);
        $message->mailer = $this;
        return $message;
    }
}
