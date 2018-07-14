<?php

namespace tests\unit;

use Codeception\Stub;
use Exception;
use mikk150\queuemailer\jobs\MailRetryableJob;
use RuntimeException;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\queue\file\Queue;

/**
 * Class MailRetryableJobTest
 * @package tests\unit
 */
class MailRetryableJobTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @throws Exception
     */
    public function timeToRetry()
    {
        $job = new MailRetryableJob([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'message' => Stub::makeEmpty(MessageInterface::class),
            'timeToRetry' => 123,
        ]);
        $this->assertEquals(123, $job->getTtr());
    }

    /**
     * @test
     * @throws Exception
     */
    public function canRetry() {
        $job = new MailRetryableJob([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'message' => Stub::makeEmpty(MessageInterface::class),
            'attempts' => 30,
        ]);
        $this->assertTrue($job->canRetry(29, null));
        $this->assertFalse($job->canRetry(30, null));
    }

    /**
     * @test
     * @throws Exception
     */
    public function executeWithoutError()
    {
        $job = new MailRetryableJob([
            'mailer' => Stub::makeEmpty(MailerInterface::class, [
                'send' => true,
            ]),
            'message' => Stub::makeEmpty(MessageInterface::class),
        ]);
        $job->execute(Stub::makeEmpty(Queue::class));
    }

    /**
     * @test
     * @throws Exception
     */
    public function executeWithError()
    {
        $job = new MailRetryableJob([
            'mailer' => Stub::makeEmpty(MailerInterface::class, [
                'send' => false,
            ]),
            'message' => Stub::makeEmpty(MessageInterface::class),
        ]);
        $this->expectException(RuntimeException::class);
        $job->execute(Stub::makeEmpty(Queue::class));
    }
}
