<?php


namespace App\Controller\Rest;

use App\Service\CustomerApiService;
use App\Service\SimCardService;
use App\Service\SmsApiService;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Service\UserService;

/**
 * Class SimcardController
 *
 * @category Simcard
 *
 * @package App\Controller\Rest
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class SimcardController extends AbstractFOSRestController
{

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var SimCardService
     */
    private $simCardService;

    /**
     * @var SmsApiService
     */
    private $smsApiService;

    /**
     * SimcardController constructor.
     * @param UserService $userService
     * @param SimCardService $simCardService
     * @param SmsApiService $smsApiService
     */
    public function __construct(UserService $userService, SimCardService $simCardService, SmsApiService $smsApiService)
    {
        $this->userService = $userService;
        $this->simCardService = $simCardService;
        $this->smsApiService = $smsApiService;
    }

    /**
     *
     * @Rest\Get("/getBalance")
     * @param Request $request
     * @return View
     */
    public function getBalance(Request $request): View
    {
        $result = $this->userService->checkUserToken($request);
        if ($result['error'] === true) {
            return View::create(array('error'=>true, 'message' => 'User token required!'), Response::HTTP_BAD_REQUEST);
        }

        $inputData = array();
        $inputData['number'] = $request->get('number');

        $result = $this->simCardService->getBalance($this->smsApiService->getApiInfo(CustomerApiService::ACTION_TYPE_GET_BALANCE), $inputData);

        // $result['headerStatus']
        return View::create($result);
    }

    /**
     *
     * @Rest\Get("/addBalance")
     * @param Request $request
     * @return View
     */
    public function addBalance(Request $request): View
    {
        $result = $this->userService->checkUserToken($request);
        if ($result['error'] === true) {
            return View::create(array('error'=>true, 'message' => 'User token required!'), Response::HTTP_BAD_REQUEST);
        }

        $inputData = array();
        $inputData['number'] = $request->get('number');
        $inputData['currency'] = $request->get('currency');
        $inputData['amount'] = $request->get('amount');

        if ((int) $inputData['amount'] <= 0) {
            return View::create(array('error' => true, 'message' => 'You should enter valid amount.'), Response::HTTP_FORBIDDEN);
        }

        $result = $this->simCardService->addBalance($this->smsApiService->getApiInfo(CustomerApiService::ACTION_TYPE_ADD_BALANCE), $inputData);

//        $result['headerStatus']
        return View::create($result);
    }
}
