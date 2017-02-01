<?php

namespace yiiunit\extensions\queuemailer;

use Yii;
use PHPUnit_Framework_Constraint_IsType as PHPUnit_IsType;

/**
*
*/
class SenderTest extends TestCase
{
    public function testCreateBaseMailer()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
            ]
        ]);

        $mailer = $app->mailer->getInstance();

        $this->assertInstanceOf('yii\swiftmailer\Mailer', $mailer);
    }

    public function testCreateMessage()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ]
            ]
        ]);

        $message = $app->mailer->compose('test', []);

        $this->assertInstanceOf('yii\swiftmailer\Message', $message);
    }

    public function testSendingMessage()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
                'queue' => [
                    'class' => '\yiiunit\extensions\queuemailer\TestQueue'
                ]
            ]
        ]);

        $jobId = $app->mailer->compose('test', [])->setTo('test@mailinator.com')->send();

        $this->assertInternalType(PHPUnit_IsType::TYPE_INT, $jobId);
    }

    public function testRetrievingMessageFromQueue()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
                'queue' => [
                    'class' => '\yiiunit\extensions\queuemailer\TestQueue'
                ]
            ]
        ]);

        $app->mailer->compose('test', [])->setTo(['test@mailinator.com' => 'John Test'])->send();

        $job = $app->queue->pop('mailjob');
        $jobObject = call_user_func($job['body']['serializer'][1], $job['body']['object']);

        $this->assertInstanceOf('yii\swiftmailer\Message', $jobObject->message);
        $this->assertEquals(['test@mailinator.com' => 'John Test'], $jobObject->message->getTo());
    }
}
