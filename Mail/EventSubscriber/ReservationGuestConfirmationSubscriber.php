<?php

namespace App\Mail\EventSubscriber;

    use App\Entity\Listing;
    use App\Utils\StringUtil;
    use App\Entity\ChannelBrand;
    use App\Entity\Embedded\Guest;
    use App\Mail\Mime\TemplatedEmail;
    use Symfony\Component\Mime\Address;
    use App\Entity\ChannelMemberSettings;
    use App\Entity\ChannelMemberSettingsRegistry;
    use App\Event\Reservation\ReservationCreatedEvent;
    use Symfony\Component\Validator\Constraints\Email;
    use App\Event\Reservation\AbstractReservationEvent;
    use Lycan\MailerBundle\EventSubscriber\Traits\ReservationTrait;

    /**
     * Class ReservationSubscriber.
     */
    class ReservationGuestConfirmationSubscriber extends AbstractMailerSubscriber
    {
        use ReservationTrait;

        /**
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents(): array
        {
            return [
                ReservationCreatedEvent::class => 'notifyGuestsOfConfirmationReservation',
            ];
        }

        public function notifyGuestsOfConfirmationReservation(AbstractReservationEvent $event): void
        {
            // We send should notifications to people....
            // 1. The owner of the property
            // 2. The collection owners if it's not the same as the property owner?
            $reservation = $event->getReservation();
            // We might also want to notify OTHER people.. For example:
            // The collection might want to notify the collection owner..
            // The collection might have additional emails set up...
            if ($reservation->getListing() && $reservation->getListing()->getChannel()) {
                /** @var ChannelBrand $channel */
                $channel = $reservation->getListing()->getChannel();
                $notificationRules = $channel->getMailNotificationRules();
                $template = $channel->getMailTemplateGuestConfirmation();
                if (isset($notificationRules['leadGuest']) && $notificationRules['leadGuest'] && $template) {
                    // Must validate email..
                    /** @var Guest $guest */
                    $guest = $reservation->getGuest();
                    $emailConstraint = new Email();
                    $errors = $this->validator->validate(
                        $guest->getEmail(),
                        $emailConstraint
                    );
                    if (0 === $errors->count()) {
                        // Do we have a Member Settings -- If so we want to set the reply-to for Guest emails to be the owner email notification address.
                        $registry = $this->entityManager->getRepository(Listing::class)
                            ->getListingChannelMembershipRegistry($reservation->getListing());
                        $replyToAddress = $this->getReplyTo($notificationRules);
                        if ($registry instanceof ChannelMemberSettingsRegistry && $registry->getSettings()) {
                            /** @var ChannelMemberSettings $settings */
                            $settings = $registry->getSettings();
                            if ($settings->getEmails()) {
                                $replyToEmail = current($settings->getEmails());
                                $replyToAddress = new Address($replyToEmail);
                            }
                        }

                        $subject = sprintf(
                            '%s Confirmation for %s %s sent at %s',
                            $reservation->getReservationTypeString(),
                            (string) StringUtil::trim($reservation->getProperty(), 22),
                            $reservation->getListing()->getChannel()->getBrand(),
                            (new \Datetime())->format(
                                'H:i:sa'
                            )
                        );
                        $toAddresses = [
                            new Address($guest->getEmail(), $guest->getFullName()),
                        ];
                        $mail = (new TemplatedEmail())
                            ->subject($subject)
                            ->replyTo($replyToAddress)
                            ->to(...$toAddresses)
                            ->from($this->getFrom($reservation));

                        $variables = [
                            'reservation' => $reservation,
                        ];

                        $message = $this->mailer->createTemplatedEmail(
                            $template->getCode(),
                            $mail,
                            $variables
                        );

                        $this->mailer->send($message);
                    }
                }
            }
        }
    }
