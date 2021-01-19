<?php

namespace App\Mail\Tests\Sender;

use App\Entity\Mail;
use App\Mail\Sender\Sender;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use App\Mail\Sender\MailSenderInterface;
use App\Mail\Exception\MailSenderException;
use PHPUnit\Framework\MockObject\MockObject;
use App\Mail\Provider\Mail\MailProviderInterface;

/**
 * Class SenderTest.
 */
class SenderTest extends TestCase
{
    private Sender $sender;
    /**
     * @var MailSenderInterface|MockObject
     */
    private $mailSenderInterface;
    /**
     * @var MailProviderInterface|MockObject
     */
    private $mailProviderInterface;
    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerInterface;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailSenderInterface = $this->createMock(MailSenderInterface::class);
        $this->mailProviderInterface = $this->createMock(MailProviderInterface::class);
        $this->loggerInterface = $this->createMock(LoggerInterface::class);
        $this->sender = new Sender($this->mailSenderInterface, $this->mailProviderInterface, $this->loggerInterface);
    }

    public function testSendAll()
    {
        $mailCollection = [
            new Mail('subject1', 'body1', ['recipient1@test.com']),
            new Mail('subject2', 'body2', ['recipient2@test.com']),
        ];

        $this->mailProviderInterface->expects($this->once())->method('findAllMail')->willReturn($mailCollection);

        $this->mailProviderInterface->expects($this->once())->method('save')->with($mailCollection);

        $this->sender->sendAll();
    }

    public function testSendAllWithException()
    {
        $this->mailProviderInterface
            ->expects($this->once())
            ->method('findAllMail')
            ->will($this->throwException(new \Exception()));
        $this->loggerInterface->expects($this->once())->method('critical');
        $this->sender->sendAll();
    }

    public function testSendOneWithSuccess()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);

        $this->mailSenderInterface->expects($this->once())->method('send')->with($mail);
        $this->loggerInterface->expects($this->once())->method('info');

        $this->sender->sendOne($mail);
    }

    public function testSendOneWithException()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);

        $this->mailSenderInterface
            ->expects($this->once())
            ->method('send')
            ->will($this->throwException(new MailSenderException()));

        $this->loggerInterface->expects($this->once())->method('error');

        $this->sender->sendOne($mail);
        $this->assertEquals(true, $mail->isSentError());
    }

    public function testGetMailLog()
    {
        $mail = new Mail('subject', 'body', ['recipient@test.com']);
        $log = $this->sender->getLogData($mail);
        $this->assertInternalType('array', $log);
        $this->assertCount(5, $log);
        $this->assertArrayHasKey('recipients', $log);
        $this->assertArrayHasKey('recipientsCopy', $log);
        $this->assertArrayHasKey('recipentsHiddenCopy', $log);
        $this->assertArrayHasKey('subject', $log);
        $this->assertArrayHasKey('id', $log);
    }
}
