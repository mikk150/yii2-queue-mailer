<?php

namespace mikk150\queuemailer\jobs;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\queue\JobInterface;

/**
 * Class MailJob
 * @package mikk150\queuemailer\jobs
 */
class MailJob extends BaseObject implements JobInterface
{
    /**
     * @var string|array|MailerInterface
     */
    public $mailer;

    /**
     * @var string|array|MessageInterface
     */
    public $message;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->mailer = Instance::ensure($this->mailer, MailerInterface::class);
        $this->message = Instance::ensure($this->message, MessageInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $this->mailer->send($this->message);
    }
}
