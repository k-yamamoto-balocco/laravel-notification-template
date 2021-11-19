<?php

namespace GitBalocco\LaravelNotificationTemplate\Drivers\Mail\Contracts;

use GitBalocco\LaravelNotificationTemplate\Drivers\Contracts\ChannelDriver;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Mail\Mailable;

/**
 * Interface MailChannelDriver
 * @package GitBalocco\LaravelNotificationTemplate\Drivers\Contracts\Mail
 */
interface MailChannelDriver extends ChannelDriver, MailableContract, Renderable
{
    /**
     * @return Mailable
     */
    public function build(): Mailable;
}
