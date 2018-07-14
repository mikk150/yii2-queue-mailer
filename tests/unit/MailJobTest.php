<?php

namespace tests\unit;

use Codeception\Stub;
use Codeception\Stub\Expected;
use Exception;
use mikk150\queuemailer\jobs\MailJob;
use yii\base\InvalidConfigException;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\queue\file\Queue;
use yii\queue\JobInterface;

/**
 * Class MailJobTest
 * @package tests\unit
 */
class MailJobTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @throws Exception
     */
    public function ensureMailerDuringInit()
    {
        $this->expectException(InvalidConfigException::class);
        new MailJob([
            'mailer' => 'cannotcrossit',
            'message' => Stub::makeEmpty(MessageInterface::class),
        ]);
    }

    /**
     * @test
     * @throws Exception
     */
    public function ensureMessageDuringInit()
    {
        $this->expectException(InvalidConfigException::class);
        new MailJob([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'message' => 'cannotcrossit',
        ]);
    }

    /**
     * @test
     * @throws Exception
     */
    public function successfulInstantiation()
    {
        $job = new MailJob([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'message' => Stub::makeEmpty(MessageInterface::class),
        ]);
        $this->assertInstanceOf(JobInterface::class, $job);
    }

    /**
     * @test
     * @throws Exception
     */
    public function messageSended()
    {
        $mailer = Stub::makeEmpty(MailerInterface::class, [
            'send' => Expected::once(function($message) {
                $this->assertInstanceOf(MessageInterface::class, $message);
            }),
        ], $this);
        $job = new MailJob([
            'mailer' => $mailer,
            'message' => Stub::makeEmpty(MessageInterface::class)
        ]);
        $job->execute(Stub::makeEmpty(Queue::class));
    }
}
