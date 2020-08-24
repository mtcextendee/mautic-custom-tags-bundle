<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomTagsBundle\Helper;

use Joomla\Http\Http;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Helper\PrimaryCompanyHelper;

/**
 * Class TokenHelper.
 */
class TokenHelper
{
    /**
     * @var Http ;
     */
    protected $connector;

    /**
     * @var PrimaryCompanyHelper
     */
    private $primaryCompanyHelper;

    /**
     * EmailSubscriber constructor.
     *
     * @param Http                 $connector
     * @param PrimaryCompanyHelper $primaryCompanyHelper
     */
    public function __construct(Http $connector, PrimaryCompanyHelper $primaryCompanyHelper)
    {
        $this->connector = $connector;
        $this->primaryCompanyHelper = $primaryCompanyHelper;
    }

    /**
     * @param string $content
     * @param mixed  $lead
     *
     * @return string
     */
    public function findTokens($content, $lead)
    {
        $tokens = [];

        // convert Lead entity to array
        if ($lead instanceof Lead) {
            $lead = $this->primaryCompanyHelper->getProfileFieldsWithPrimaryCompany($lead);
        }
        preg_match_all('/{getremoteurl=(.*?)}/', $content, $matches);
        if (count($matches[0])) {
            foreach ($matches[1] as $k => $id) {
                $token = $matches[0][$k];

                if (isset($tokens[$token])) {
                    continue;
                }
                try {
                    $token = str_replace(['[', ']'], ['{', '}'], $token);
                    $token = \Mautic\LeadBundle\Helper\TokenHelper::findLeadTokens($token, $lead, true);
                    $data = $this->connector->get(
                        $id,
                        [],
                        30
                    );
                    $tokens[$token] = $data->body;
                } catch (\Exception $e) {
                    $tokens[$token] = '';
                }
            }
        }
        preg_match_all('/{base64decode=(.*?)}/', $content, $matches);
        if (count($matches[0])) {
            foreach ($matches[1] as $k => $id) {
                $token = $matches[0][$k];

                if (isset($tokens[$token])) {
                    continue;
                }
                $tokens[$token] =  (!empty($lead[$id])) ? base64_decode($lead[$id]) : '';
            }
        }

        return str_replace(array_keys($tokens), $tokens, $content);
    }
}
