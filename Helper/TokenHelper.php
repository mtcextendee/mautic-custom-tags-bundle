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

use GuzzleHttp\Client;
use Joomla\Http\Http;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Helper\PrimaryCompanyHelper;

/**
 * Class TokenHelper.
 */
class TokenHelper
{
    /**
     * @var Client ;
     */
    protected $connector;

    /**
     * @var PrimaryCompanyHelper
     */
    private $primaryCompanyHelper;

    /**
     * EmailSubscriber constructor.
     */
    public function __construct(Client $connector, PrimaryCompanyHelper $primaryCompanyHelper)
    {
        $this->connector            = $connector;
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
            foreach ($matches[1] as $k => $url) {
                $token = $matches[0][$k];

                if (isset($tokens[$token])) {
                    continue;
                }
                try {
                    $url  = \Mautic\LeadBundle\Helper\TokenHelper::findLeadTokens($url = str_replace(['[', ']'], ['{', '}'], $url), $lead, true);
                    $data = $this->connector->get(
                        $url,
                        []
                    );
                    $tokens[$token] = $data->getBody()->getContents();
                } catch (\Exception $e) {
                    die(print_r($e->getMessage()));
                    $tokens[$token] = '';
                }
            }
        }
        preg_match_all('/{base64decode=(.*?)}/', $content, $matches);
        if (count($matches[0])) {
            foreach ($matches[1] as $k => $url) {
                $token = $matches[0][$k];

                if (isset($tokens[$token])) {
                    continue;
                }
                $tokens[$token] =  (!empty($lead[$url])) ? base64_decode($lead[$url]) : '';
            }
        }

        return str_replace(array_keys($tokens), $tokens, $content);
    }
}
