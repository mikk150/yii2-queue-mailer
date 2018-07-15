<?php

namespace tests;

use yii\mail\MailerInterface;

/**
 * Interface DummyMailerInterface
 * @package tests
 */
interface DummyMailerInterface extends MailerInterface
{
    /**
     * @param integer $value
     * @return string
     */
    public function dummy($value);
}
