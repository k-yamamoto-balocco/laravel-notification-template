<?php

return [
    /**
     * 通知設定:通知1種類につき、1つの配列を構成する。
     */
    'notification_settings' => [
        /** @var int key/通知番号。一意な値となるように設定を行うこと。 */
        1 => [
            /** @var string 通知名。*/
            'name' => 'sample',

            /**
             * @var array
             * 通知に対して紐付けるテンプレート。
             * channel , locale の組み合わせがユニークとなるよう設定すること。
             */
            'notification_templates' => [
                [
                    /** @var string viewテンプレート名。必須。ViewFinderが見つけられる名前を指定する。 */
                    'viewName' => 'notifications.1.ja.mail',

                    /**
                     * @var string チャンネル名。必須。
                     * サポートしているチャンネル名は、SupportedChannelList を参照すること。
                     * このリストに存在しないチャンネル名の指定は例外となる。
                     * @see \GitBalocco\LaravelNotificationTemplate\ValueObject\SupportedChannelList
                     */
                    'channel' => 'mail',

                    /** @var string Dtoクラス名。必須。 viewテンプレートにアサインされるオブジェクトの型を設定する。 */
                    'dtoClass' => \stdClass::class,

                    /**
                     * @var string locale名。必須。
                     */
                    'locale' => 'ja',

                    /**
                     * @var string ドライバ名。任意。通知内容のビルドを行うクラスのクラス名を指定する。
                     * 省略した場合、各チャンネルのデフォルトドライバが採用される。デフォルト値の決定方法は下記クラス参照。
                     * @see \GitBalocco\LaravelNotificationTemplate\ValueObject\DriverName
                     *
                     * 通知内容のビルドをカスタマイズしたい場合は、この項目に任意のクラス名を指定する。
                     */
                    'driver' => '',
                ],
                [
                    'viewName' => 'notifications.1.en.mail',
                    'channel' => 'mail',
                    'dtoClass' => \stdClass::class,
                    'driver' => '',
                    'locale' => 'en',
                ],

                [
                    'viewName' => 'notifications.1.ja.database',
                    'channel' => 'database',
                    'dtoClass' => \stdClass::class,
                    'driver' => '',
                    'locale' => 'ja',
                ],

                [
                    'viewName' => 'notifications.1.en.database',
                    'channel' => 'database',
                    'dtoClass' => \stdClass::class,
                    'driver' => '',
                    'locale' => 'en',
                ],
            ],
        ],
        2 => [
            'id' => 2,
            'name' => 'sample2',
            'notification_templates' => [
                [
                    'viewName' => 'notifications.2.ja.mail',
                    'channel' => 'mail',
                    'dtoClass' => \stdClass::class,
                    'driver' => '',
                    'locale' => 'ja',

                ],
                [
                    'viewName' => 'notifications.2.en.mail',
                    'channel' => 'mail',
                    'dtoClass' => \stdClass::class,
                    'driver' => '',
                    'locale' => 'en',

                ],
            ],
        ]
    ]
];