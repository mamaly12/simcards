<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class SimcardService
 *
 * @category Server
 *
 * @package App\Service
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class SimCardService
{
    /**
     * @var CustomerApiService
     */
    private $apiService;

    /**
     * @var SmsApiService
     */
    private $smsApiService;

    /**
     * @var emailApiService
     */
    private $emailApiService;

    /**
     * SimCardService constructor.
     * @param CustomerApiService $apiService
     * @param SmsApiService $smsApiService
     */
    public function __construct(CustomerApiService $apiService, SmsApiService $smsApiService, EmailApiService $emailApiService)
    {
        $this->apiService = $apiService;
        $this->smsApiService = $smsApiService;
        $this->emailApiService = $emailApiService;
    }

    /**
     * @param $apiInfo
     * @param $apiData
     * @return array
     */
    public function getBalance($apiInfo, $apiData = array())
    {
        return $this->apiService->processCustomerRequest($apiInfo, $apiData);
        $this->smsApiService = $smsApiService;
    }

    /**
     * @param $apiInfo
     * @param array $apiData
     * @return array
     */
    public function addBalance($apiInfo, $apiData = array())
    {
        $getBalanceInfo = $this->getBalance($this->smsApiService->getApiInfo(CustomerApiService::ACTION_TYPE_GET_BALANCE), $apiData);

        // Check ig getBalance has error
        if ($getBalanceInfo['type'] === 'ERROR') {
            $errorData = array('error' => true, 'text' => $getBalanceInfo['text'], 'httpStatus' => $getBalanceInfo['headerStatus']);
            return $errorData;
        }

        // Check if user number is blocked
        if ($getBalanceInfo['card']['blocked'] === true) {
            $errorData = array('error' => true, 'text' => 'Your number is blocked.', 'httpStatus' => Response::HTTP_FORBIDDEN);
            return $errorData;
        }

        // Check current currency type
        if ($getBalanceInfo['card']['curr'] !== $apiData['currency']) {
            $errorData = array('error' => true, 'text' => 'Your currency must be ' . $getBalanceInfo['data']['curr'], 'httpStatus' => Response::HTTP_FORBIDDEN);
            return $errorData;
        }

        $currentBalance = $getBalanceInfo['card']['balance'];

        // Check if current balance is greater than -5
        if ($currentBalance < -5) {
            $errorData = array('error' => true, 'text' => 'Your balance cannot be increased.', 'httpStatus' => Response::HTTP_FORBIDDEN);
            return $errorData;
        }

        // Correct negative balance
        if ($currentBalance < 0) {
            $apiData['amount'] = $apiData['amount'] + (-1 * $currentBalance);
        }

        // send request for addBalance
        $apiResult = $this->apiService->processCustomerRequest($apiInfo, $apiData);

        // process api result
        return $this->handleAddBalanceApiResult($apiResult, $apiData, $apiInfo);
    }

    public function handleAddBalanceApiResult($apiResult, $apiInputData, $apiInfo) {


        switch ($apiResult['error_code'])
        {
            case CustomerApiService::ERROR_CODE_GENERAL:
                return $this->addBalance($apiInfo, $apiInputData);
            case CustomerApiService::ERROR_CODE_TIMEOUT || CustomerApiService::ERROR_CODE_404 || CustomerApiService::ERROR_CODE_500:
                $this->notifyUser($apiInfo, $apiResult['text']);
                break;
            case '':
                $this->notifyUser($apiInfo, $apiResult['text']);
                break;
            default:
                break;
        }

        return $apiResult;
    }

    /***
     * Send sms and email notification to number
     * @param $apiInfo
     * @param $text
     */
    public function notifyUser($apiInfo, $text) {
        $this->emailApiService->sendEmail($apiInfo, $text);
        $this->smsApiService->sendSms(SmsApiService::getSendSmsUrl($apiInfo['base_sms_url'], $apiInfo['base_sms_number'], $text));
    }
}
