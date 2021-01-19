<?php

namespace App\Mail\EventSubscriber\Traits;

    use App\Entity\Reservation;
    use Symfony\Component\Validator\Constraints\Email;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    /**
     * Mail Templates associated with a reservation..
     */
    trait ReservationTrait
    {
        protected ValidatorInterface $validator;

        public function getFrom(Reservation $reservation): array
        {
            $fromName = $from = null;
            if ($reservation->getListing() && $reservation->getListing()->getChannel()) {
                $fromName = $reservation->getListing()->getChannel()->getDescriptiveName().' Events';

                $notificationRules = $reservation->getListing()->getChannel()->getMailNotificationRules();
                if (isset($notificationRules['fromEmail'])) {
                    $from = $notificationRules['fromEmail'];
                }

                if (isset($notificationRules['fromSenderName'])) {
                    $fromName = $notificationRules['fromSenderName'];
                }
            }

            return [$fromName, $from];
        }

        public function getReplyTo(array $notificationRules): array
        {
            if (isset($notificationRules['replyToEmail'], $notificationRules['replyToName'])) {
                $emailConstraint = new Email();
                $errors = $this->validator->validate(
                    $notificationRules['replyToEmail'],
                    $emailConstraint
                );
                if (0 === $errors->count()) {
                    return [$notificationRules['replyToName'], $notificationRules['replyToEmail']];
                }
            }

            return [null, null];
        }
    }
