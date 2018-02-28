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

/**
 * Class TokenHelper.
 */
class TokenHelper
{
    /**
     * @var Http $connector ;
     */
    protected $connector;

    /**
     * EmailSubscriber constructor.
     *
     * @param Http $connector
     */
    public function __construct(Http $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param string $content
     * @param mixed $lead
     * @return string
     */
    public function findFormTokens($content, $lead)
    {
        $tokens = [];

        // convert Lead entity to array
        if($lead instanceof Lead){
            $lead = $lead->getProfileFields();
        }

        preg_match_all('/{getremoteurl=(.*?)}/', $content, $matches);
        if (count($matches[0])) {
            foreach ($matches[1] as $k => $id) {
                $token = $matches[0][$k];

                if (isset($tokens[$token])) {
                    continue;
                }
                try {
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
        $content = str_replace(array_keys($tokens), $tokens, $content);

        return $content;
    }
}
