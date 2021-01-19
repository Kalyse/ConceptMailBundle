<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\UserBundle\Entity\User;
use App\Entity\Interfaces\MailTemplateInterface;

/**
 * Class MailTemplate.
 *
 * @ORM\Entity()
 * @ORM\Table(name="mail_template")
 */
class MailTemplate implements MailTemplateInterface
{
    public const TYPE_RESERVATION_UPSERT_FAILURE = 'warn-upsert-reservation-failure';
    public const TYPE_RESERVATION_RECEIVED_NOTIFY_STAKEHOLDER = 'reservation-stakeholder';
    public const TYPE_RESERVATION_RECEIVED_ACKNOWLEDGE_TO_LEAD_GUEST = 'reservation-lead-guest';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private DateTime $createdAt;
    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt = null;
    /**
     * @var string
     * @ORM\Column(name="mail_subject", type="string", length=255)
     */
    protected $mailSubject = '';
    /**
     * @var string
     * @ORM\Column(name="mail_body", type="text")
     */
    protected $mailBody = '';
    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    protected $code = '';

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Application\Sonata\UserBundle\Entity\User",
     *     cascade={"persist"},
     *     inversedBy="properties",
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private ?User $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ChannelBrand",  cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private ?ChannelBrand $channel;

    /**
     * MailTemplate constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getMailSubject()
    {
        return $this->mailSubject;
    }

    /**
     * @param string $mailSubject
     */
    public function setMailSubject($mailSubject = '')
    {
        $this->mailSubject = $mailSubject;
    }

    /**
     * @return string
     */
    public function getMailBody()
    {
        return $this->mailBody;
    }

    /**
     * @param string $mailBody
     */
    public function setMailBody($mailBody = '')
    {
        $this->mailBody = $mailBody;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }

    public function setOwner(?User $owner): MailTemplate
    {
        $this->owner = $owner;

        return $this;
    }

    public function getChannel(): ?ChannelBrand
    {
        return $this->channel;
    }

    public function setChannel(?ChannelBrand $channel): MailTemplate
    {
        $this->channel = $channel;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }
}
