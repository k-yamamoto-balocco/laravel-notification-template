<?php


namespace GitBalocco\LaravelNotificationTemplate\Entity\NotificationTemplate;


use GitBalocco\LaravelNotificationTemplate\ValueObject\MailFrom;
use Illuminate\Support\Facades\App;

class MailSetting extends DefaultSetting
{
    /** @var MailFrom $from */
    private $from;
    /** @var string $subject */
    private $subject;

    public function __construct(
        $id,
        string $viewName,
        string $channel,
        string $locale,
        string $dtoClass = '',
        string $driver = '',
        array $from = [],
        string $subject = ''
    ) {
        parent::__construct($id, $viewName, $channel, $locale, $dtoClass, $driver);

        $this->subject = $subject;
        $this->from = App::make(MailFrom::class, $from);
    }

    /**
     * @return MailFrom
     */
    public function getFrom(): MailFrom
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }


}