<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class SmsApiService
 *
 * @category SmsApi
 *
 * @package App\Service
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class SmsApiService
{

    private $params;

    /**
     * SmsApiService constructor.
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public static function getSendSmsUrl($baseUrl, $number, $text) {
        return $baseUrl . '?number=' . $number . '&text=' . $text;
    }

    /**
     * @param $url
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     */
    public function sendSms($url)
    {
        try {
            $client = HttpClient::create();
            $client->request('GET', $url);
        } catch (\Exception $e) {
        }
    }

    public function getApiInfo($actionType) {
        return array(
            'base_url' => $this->params->get('api.base_url'),
            'username' => $this->params->get('api.username'),
            'password' => $this->params->get('api.password'),
            'base_sms_url' => $this->params->get('sms.url'),
            'base_sms_number' => $this->params->get('sms.number'),
            'smtp_support_emails' => $this->params->get('smtp.support_emails'),
            'smtp_username' => $this->params->get('smtp.username'),
            'smtp_password' => $this->params->get('smtp.password'),
            'smtp_host' => $this->params->get('smtp.host'),
            'smtp_port' => $this->params->get('smtp.port'),
            'smtp_from' => $this->params->get('smtp.from'),
            'action_type' => $actionType,
        );
    }
}
