<?php

namespace App\Entity\Interfaces;

/**
 * Interface MailInterface.
 */
interface MailInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return \DateTime
     */
    public function getSentDate();

    /**
     * @param \DateTime $sentDate
     */
    public function setSentDate(\DateTime $sentDate = null);

    /**
     * @return string
     */
    public function getSenderAlias();

    /**
     * @param string $senderAlias
     */
    public function setSenderAlias($senderAlias);

    /**
     * @return string
     */
    public function getSenderEmail();

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail($senderEmail);

    /**
     * @return string
     */
    public function getReplyToEmail();

    /**
     * @param string $replyToEmail
     */
    public function setReplyToEmail($replyToEmail);

    /**
     * @return bool
     */
    public function isSentError();

    public function setSentError(bool $sentError = false);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return array
     */
    public function getRecipient();

    public function setRecipient(array $recipient);

    /**
     * @return array
     */
    public function getRecipientCopy();

    public function setRecipientCopy(array $recipientCopy);

    public function addRecipientCopy(string $recipientCopy);

    /**
     * @return array
     */
    public function getRecipientHiddenCopy();

    public function setRecipientHiddenCopy(array $recipientHiddenCopy);

    /**
     * @param string $recipientHiddenCopy
     */
    public function addRecipientHiddenCopy($recipientHiddenCopy);

    /**
     * @return array
     */
    public function getAttachments();

    public function setAttachments(array $attachement);

    public function updateSentDate();
}
