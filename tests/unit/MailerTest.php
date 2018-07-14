<?php

namespace tests\unit;

use Codeception\Stub;
use Codeception\Stub\Expected;
use Exception;
use mikk150\queuemailer\jobs\MailJob;
use mikk150\queuemailer\Mailer;
use yii\base\InvalidConfigException;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\queue\file\Queue;

/**
 * Class MailerTest
 * @package tests\unit
 */
class MailerTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @throws Exception
     */
    public function ensureMailerDuringInit()
    {
        $this->expectException(InvalidConfigException::class);
        new Mailer([
            'mailer' => 'cannotcrossit',
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
    }

    /**
     * @test
     * @throws Exception
     */
    public function ensureQueueDuringInit()
    {
        $this->expectException(InvalidConfigException::class);
        new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'queue' => 'cannotcrossit',
        ]);
    }

    /**
     * @test
     * @throws Exception
     */
    public function successfulInstantiation()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
        $this->assertInstanceOf(MailerInterface::class, $mailer);
    }

    /**
     * @test
     * @throws Exception
     */
    public function sendMessage()
    {
        /** @var MessageInterface $message */
        $message = Stub::makeEmpty(MessageInterface::class);
        $mailer = Stub::makeEmpty(MailerInterface::class);

        $queueMailer = new Mailer([
            'mailer' => $mailer,
            'queue' => Stub::makeEmpty(Queue::class, [
                'push' => Expected::once(function($job) use ($message, $mailer) {
                    $this->assertInstanceOf(
                        MailJob::class,
                        $job,
                        'Pushed message should me instance of MailJob'
                    );
                    $this->assertEquals(
                        $job->message,
                        $message,
                        'Job `message` should be the equal to sent message'
                    );
                    $this->assertEquals(
                        $job->mailer,
                        $mailer,
                        'Job `mailer` should be the equal to configured mailer'
                    );
                }),
            ], $this),
        ]);
        $result = $queueMailer->send($message);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @throws Exception
     */
    public function compose()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class, [
                'compose' => Expected::once(function($view, $params) {
                    $this->assertEquals(
                        'reset-password',
                        $view,
                        'View should be passed to provided mailer'
                    );
                    $this->assertEquals(
                        ['password' => 'pAssWd'],
                        $params,
                        'Pasrams should be passed to provided mailer'
                    );
                    return Stub::makeEmpty(MessageInterface::class);
                }),
            ], $this),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);

        $message = $mailer->compose('reset-password', ['password' => 'pAssWd']);
        $this->assertInstanceOf(MessageInterface::class, $message);
        $this->assertEquals($message->mailer, $mailer);
    }
}
