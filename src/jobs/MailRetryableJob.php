<?php

namespace mikk150\queuemailer\jobs;

use RuntimeException;
use yii\queue\RetryableJobInterface;

/**
 * Class MailRetryableJob
 * @package mikk150\queuemailer\jobs
 */
class MailRetryableJob extends MailJob implements RetryableJobInterface
{
    /**
     * Number of attempts to send mail
     * @var int
     */
    public $attempts = 1;

    /**
     * Number of seconds between retries
     * @var int
     */
    public $timeToRetry = 60;

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return $this->timeToRetry;
    }

    /**
     * @param int $attempt
     * @param \Exception|\Throwable $error
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < $this->attempts;
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        if ($this->mailer->send($this->message) !== true) {
            throw new RuntimeException('Can`t send email');
        }
    }
}

