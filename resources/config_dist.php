<?php

return [
    'notification_settings' => [
        [
            /** @var int id (required) */
            'id' => 1,
            /** @var string notification name (required) */
            'name' => '',
            /** @var array (required) */
            'notification_templates' => [
                /** @var int template id.(required) */
                'id' => 1,
                /**
                 * @var string channel name.(required)
                 * @see \GitBalocco\LaravelNotificationTemplate\ValueObject\SupportedChannelList
                 */
                'channel' => '',
                /** @var string / required */
                'locale' => '',
                /** @var string view name that actually exists.(required) */
                'viewName' => '',
                /** @var string dto class name that actually exists.(optional default:stdClass) */
                'dtoClass' => '',

                /**
                 * @var string driver class name that actually exists
                 * optional default : decided by channel
                 * @see \GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel::defaultDriverClassName()
                 * driver has a fixed interface to be implemented for each channel.
                 * @see \GitBalocco\LaravelNotificationTemplate\ValueObject\NotificationChannel::driverInterfaceName()
                 */
                'driver' => '',

                /** @var string subject(optional) Valid only if the channel is "mail" */
                'subject' => '',
                /** @var array from(optional) Valid only if the channel is "mail" */
                'from' => [
                    /** @var string mail address.Only RFC-compliant email addresses can be specified. */
                    'address' => '',
                    /** @var string mail name. */
                    'name' => ''
                ],
            ],
        ]
    ]
];