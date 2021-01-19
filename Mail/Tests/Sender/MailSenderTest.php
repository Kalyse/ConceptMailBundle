<?php

namespace App\Mail\Tests\Sender;

use App\Entity\Mail;
use App\Mail\Sender\MailSender;
use PHPUnit\Framework\TestCase;
use App\Mail\Exception\MailSenderException;
use PHPUnit\Framework\MockObject\MockObject;
use App\Mail\Provider\Mail\MailProviderInterface;
use App\Mail\Exception\MailerSenderEmptyException;

class MailSenderTest extends TestCase
{
    /**
     * @var \Swift_Mailer|MockObject
     */
    private $mailer;
    /**
     * @var MailProviderInterface|MockObject
     */
    private $mailEntityProvider;
    /**
     * @var MailSender|MockObject
     */
    private $mailSender;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailer = $this->createMock(\Swift_Mailer::class);
        $this->mailEntityProvider = $this->createMock(MailProviderInterface::class);
        $this->mailSender = new SwiftMailSender($this->mailer, $this->mailEntityProvider);
    }

    public function testInitSwiftMessage()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);
        $mail->setSenderEmail('sender@test.com');
        $mail->setSenderAlias('senderAlias');
        $mail->setAttachments(['test.pdf']);

        $message = $this->mailSender->initSwiftMessage($mail);

        $this->assertInstanceOf(\Swift_Message::class, $message);
        $this->assertEquals('subject', $message->getSubject());
        $this->assertEquals('body', $message->getBody());
        $this->assertEquals(['recipient@test.com' => null], $message->getTo());
        $this->assertEquals(['sender@test.com' => 'senderAlias'], $message->getFrom());
    }

    public function testSendWithSuccess()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);
        $mail->setSenderEmail('sender@test.com');
        $mail->setSenderAlias('senderAlias');

        $this->mailer->expects($this->once())->method('send')->willReturn(1);

        $sent = $this->mailSender->send($mail);
        $this->assertEquals(1, $sent);
    }

    public function testSendWithoutSenderEmail()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);
        $this->expectException(MailerSenderEmptyException::class);
        $this->mailSender->send($mail);
    }

    public function testSendWithException()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);
        $mail->setSenderEmail('sender@test.com');
        $mail->setSenderAlias('senderAlias');

        $this->mailer->expects($this->once())->method('send')
            ->willReturnCallback(function ($message, &$failed) {
                $failed = ['test@test.com'];
            });

        $this->expectException(MailSenderException::class);

        $this->mailSender->send($mail);
    }
}
