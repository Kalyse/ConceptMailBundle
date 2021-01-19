<?php

namespace App\Mail\EventSubscriber;

    use Datetime;
    use App\Entity\ChannelBrand;
    use App\Entity\MailTemplate;
    use App\Mail\Mime\TemplatedEmail;
    use Symfony\Component\Mime\Address;
    use App\Event\Reservation\ReservationCreatedEvent;
    use Symfony\Component\Validator\Constraints\Email;
    use App\Event\Reservation\AbstractReservationEvent;
    use Lycan\MailerBundle\EventSubscriber\Traits\ReservationTrait;

    /**
     * Class ReservationSubscriber.
     */
    class ReservationStakeholderNotificationsSubscriber extends AbstractMailerSubscriber
    {
        use ReservationTrait;

        /**
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents(): array
        {
            return [
                ReservationCreatedEvent::class => 'doNotifyStakeholdersOfCreatedReservation',
            ];
        }

        public function doNotifyStakeholdersOfCreatedReservation(AbstractReservationEvent $event): void
        {
            // We send should notifications to people....
            // 1. The owner of the property
            // 2. The collection owners if it's not the same as the property owner?
            $reservation = $event->getReservation();
            $variables = [
                'reservation' => $reservation,
            ];
            $recipients = [];
            $bcc = [];
            if ($reservation->getListing() && $reservation->getListing()->getChannel()) {
                /** @var ChannelBrand $channel */
                $channel = $reservation->getListing()->getChannel();
                $notificationRules = $channel->getMailNotificationRules();
                $template = $channel->getMailTemplateStakeholderConfirmation();
                $listing = $reservation->getListing();
                if (! $template || ! $listing) {
                    return;
                }

                if (isset($notificationRules['propertyOwner']) && $notificationRules['propertyOwner'] && $reservation->getProperty()) {
                    $recipient = $reservation->getProperty()->getOwner();
                    if ($recipient) {
                        $recipients[] = new Address($recipient->getEmail(), $recipient->getBrandCompanyName() ?? $recipient->getFullname());
                    }
                }

                if (isset($notificationRules['collectionOwner']) && $notificationRules['collectionOwner'] && $reservation->getListing()->getChannel()->getBrand()) {
                    $recipient = $reservation->getListing()->getChannel()->getBrand()->getOwner();
                    if ($recipient) {
                        $recipients[] = new Address($recipient->getEmail(), $recipient->getBrandCompanyName() ?? $recipient->getFullname());
                    }
                }

                if (isset($notificationRules['customRecipients']) && ! empty($notificationRules['customRecipients'])) {
                    // Must validate email..

                    foreach ($notificationRules['customRecipients'] as $email) {
                        $emailConstraint = new Email();
                        $errors = $this->validator->validate(
                            $email,
                            $emailConstraint
                        );
                        if (0 === $errors->count()) {
                            $bcc[] = new Address($email);
                        }
                    }
                }

                if ($reservation->getListing()->getChannel()->getBrand()->getOwner()) {
                    $defaultFrom = new Address($reservation->getListing()->getChannel()->getBrand()->getOwner()->getEmail(), $reservation->getListing()->getChannel()->getDescriptiveName());
                }

                $mail = (new TemplatedEmail())
                    ->subject(sprintf('%s - New Reservation for %s at %s', $channel->getDescriptiveName(), $listing->getDescriptiveName(), (new Datetime())->format('H:i:sa')))
                    // ->replyTo($replyToAddress)
                    ->to(...$recipients)
                    ->bcc(...$bcc)
                    ->from($this->getFrom($reservation) ?? $defaultFrom ?? null);

                $message = $this->mailer->createTemplatedEmail(
                    $template->getCode(),
                    $mail,
                    $variables
                );

                $this->mailer->send($message);
            } elseif ($reservation->getProperty()->getOwner()) {
                $recipient = $reservation->getProperty()->getOwner();
                if ($recipient) {
                    $recipients[] = new Address($recipient->getEmail(), $recipient->getBrandCompanyName() ?? $recipient->getFullname());
                }
                $mail = (new TemplatedEmail())
                    ->subject(sprintf('New Reservation for %s', $reservation->getProperty()->getDescriptiveName()))
                    // ->replyTo($replyToAddress)
                    ->to(...$recipients)
                    ->bcc(...$bcc)
                    ->from($this->getFrom($reservation));

                $message = $this->mailer->createTemplatedEmail(
                    MailTemplate::TYPE_RESERVATION_RECEIVED_NOTIFY_STAKEHOLDER,
                    $mail,
                    $variables
                );

                $this->mailer->send($message);
            }
        }
    }
