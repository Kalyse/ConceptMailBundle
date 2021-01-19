<?php

namespace App\Entity\Interfaces;

    interface RecipientInterface
    {
        public const NOTIFICATION_MODE_NEVER = 0;
        public const NOTIFICATION_MODE_DAYLY = 1;
        public const NOTIFICATION_MODE_HOURLY = 2;
        public const NOTIFICATION_MODE_IMMEDIATELY = 3;

        /**
         * Get the unique id of this entity (e.g.e the userId).
         *
         * @return int
         */
        public function getId();

        /**
         * Get the recipients email address.
         *
         * @return string email address
         */
        public function getEmail();

        /**
         * Get the recipients Name (e.g. "Mr. John Doe").
         *
         * @return string
         */
        public function getDisplayName();

        /**
         * Get the interval for notifications.
         *
         * @return int
         */
        public function getNotificationMode();

        /**
         * Whether the recipient likes to get the newsletter or not.
         *
         * @return bool
         */
        public function getNewsletter();

        /**
         * Get the recipients prefered locale for the emails.
         *
         * @return string
         */
        public function getPreferredLocale();

        public function toRecipientParamsArray();

        public function getApplication();

        public function getNotificationPreferences(): array;
    }
