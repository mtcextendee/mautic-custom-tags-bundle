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
use Mautic\LeadBundle\Model\LeadModel;
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

    /**
     * @var LeadModel
     */
    protected $leadModel;

    /**
     * @var CorePermissions
     */
    private $security;

    /**
     * EmailSubscriber constructor.
     */
    public function __construct(TokenHelper $tokenHelper, LeadModel $leadModel, CorePermissions $security)
    {
        $this->tokenHelper = $tokenHelper;
        $this->leadModel   = $leadModel;
        $this->security    = $security;
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
        $lead    = ($this->security->isAnonymous()) ? $this->leadModel->getCurrentLead() : null;
        if ($lead && $lead->getId()) {
            $content = $this->tokenHelper->findFormTokens($content, $lead);
        }
        $event->setContent($content);
    }
}
