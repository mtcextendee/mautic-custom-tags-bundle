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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PageBundle\PageEvents;
use MauticPlugin\MauticCustomTagsBundle\Helper\TokenHelper;

/**
 * Class PageSubscriber.
 */
class PageSubscriber extends CommonSubscriber
{
    /**
     * @var TokenHelper $tokenHelper ;
     */
    protected $tokenHelper;


    /**
     * @var LeadModel $leadModel
     */
    protected $leadModel;


    /**
     * EmailSubscriber constructor.
     *
     * @param TokenHelper $tokenHelper
     * @param LeadModel $leadModel
     */
    public function __construct(TokenHelper $tokenHelper, LeadModel $leadModel)
    {
        $this->tokenHelper = $tokenHelper;
        $this->leadModel = $leadModel;
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

    /**
     * @param PageDisplayEvent $event
     */
    public function onPageDisplay(PageDisplayEvent $event)
    {

        $content = $event->getContent();
        $lead    = ($this->security->isAnonymous()) ? $this->leadModel->getCurrentLead() : null;
        if($lead && $lead->getId()){
            $content = $this->tokenHelper->findFormTokens($content,  $lead);
        }
        $event->setContent($content);
    }
}
