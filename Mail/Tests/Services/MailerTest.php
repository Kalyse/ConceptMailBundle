<?php

namespace App\Mail\Tests\Services;

use App\Mail\Entity\Mail;
use App\Mail\Services\Mailer;
use PHPUnit\Framework\TestCase;
use App\Mail\Services\MailBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use App\Mail\Provider\Mail\MailProviderInterface;

/**
 * Class MailerTest.
 */
class MailerTest extends TestCase
{
    /**
     * @var MailProviderInterface|MockObject
     */
    private $mailProviderInterface;
    /**
     * @var Mailer
     */
    private $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mailProviderInterface = $this->createMock(MailProviderInterface::class);

        $mailBuilder = new MailBuilder();
        $mailBuilder->setMailAddressFrom('mailAddressFrom@test.com');
        $mailBuilder->setMailAliasFrom('mailAliasFrom@test.com');
        $mailBuilder->setMailReplyTo('mailReplyTo@test.com');

        $this->mailer = new Mailer(
            $this->mailProviderInterface,
            $mailBuilder
        );
    }

    public function testCreateEmail()
    {
        $mail = $this->mailer->createEmail('subject', 'body', 'test@test.com');

        $this->assertEquals('subject', $mail->getSubject());
        $this->assertEquals('body', $mail->getBody());
        $this->assertEquals(['test@test.com'], $mail->getRecipient());
        $this->assertEquals('mailAddressFrom@test.com', $mail->getSenderEmail());
        $this->assertEquals('mailAliasFrom@test.com', $mail->getSenderAlias());
        $this->assertEquals('mailReplyTo@test.com', $mail->getReplyToEmail());
        $this->assertEquals([], $mail->getAttachments());

        $recipArray = $this->mailer->createEmail(
            'subject',
            'body',
            ['test@test.com', 'test2@test.com'],
            'attachement'
        );

        $this->assertEquals(['test@test.com', 'test2@test.com'], $recipArray->getRecipient());
        $this->assertEquals(['attachement'], $recipArray->getAttachments());
    }

    public function testSave()
    {
        $mailCollection = [
            new Mail('subject1', 'body1', ['recipient1@test.com']),
            new Mail('subject2', 'body2', ['recipient2@test.com']),
        ];

        $this->mailProviderInterface->expects($this->once())->method('save')->with($mailCollection);

        $this->mailer->save($mailCollection);
    }
}
