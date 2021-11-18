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

use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PageBundle\PageEvents;
use MauticPlugin\MauticCustomTagsBundle\Helper\TokenHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PageSubscriber.
 */
class PageSubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenHelper ;
     */
    protected $tokenHelper;

    private ContactTracker $contactTracker;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * EmailSubscriber constructor.
     */
    public function __construct(TokenHelper $tokenHelper, ContactTracker $contactTracker, CorePermissions $security)
    {
        $this->tokenHelper = $tokenHelper;
        $this->security    = $security;
        $this->contactTracker = $contactTracker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PAGE_ON_DISPLAY => ['onPageDisplay', 0],
        ];
    }

    public function onPageDisplay(PageDisplayEvent $event)
    {
        $content = $event->getContent();
        $lead    = ($this->security->isAnonymous()) ? $this->contactTracker->getContact() : null;
        if ($lead && $lead->getId()) {
            $content = $this->tokenHelper->findTokens($content, $lead);
        }
        $event->setContent($content);
    }
}
