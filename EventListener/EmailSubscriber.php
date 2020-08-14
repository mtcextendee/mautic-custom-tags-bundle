<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomTagsBundle\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event as Events;
use MauticPlugin\MauticCustomTagsBundle\Helper\TokenHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EmailSubscriber.
 */
class EmailSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenHelper ;
     */
    protected $tokenHelper;

    /**
     * EmailSubscriber constructor.
     */
    public function __construct(TokenHelper $tokenHelper)
    {
        $this->tokenHelper = $tokenHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND    => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 0],
        ];
    }

    /**
     * Search and replace tokens with content.
     *
     * @param EmailSendEvent $event
     */
    public function onEmailGenerate(Events\EmailSendEvent $event)
    {
        $content = $event->getContent();
        $content = $this->tokenHelper->findFormTokens($content, $event->getLead());
        $event->setContent($content);
    }
}
