<?php


namespace App\Controller\Web;
use App\Form\TopUpForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SimCardService;
use App\Service\SmsApiService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Service\CustomerApiService;

/**
 * Class SimCardController
 *
 * @category SimCard
 *
 * @package App\Controller\Web
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class SimCardController extends AbstractController
{

    /**
     * @var SimCardService
     */
    private $simCardService;

    /**
     * @var SmsApiService
     */
    private $smsApiService;

    /**
     * SimCardController constructor.
     * @param SimCardService $simCardService
     */
    public function __construct(SimCardService $simCardService, SmsApiService $smsApiService)
    {
        $this->simCardService = $simCardService;
        $this->smsApiService = $smsApiService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("simcard/top_up", name="top_up", methods={"POST","GET"})
     * @Security("has_role('ROLE_USER')")
     */

    public function topUp(Request $request)
    {
        $form = $this->createForm(TopUpForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $number = $form->get('number')->getData();
            $amount = $form->get('amount')->getData();
            $currency = $form->get('currency')->getData();

            $inputData = array(
                'number' => $number,
                'currency' => $currency,
                'amount' => $amount,
            );

            $response = $this->simCardService->addBalance($this->smsApiService->getApiInfo(CustomerApiService::ACTION_TYPE_ADD_BALANCE), $inputData);
            $this->get('session')->getFlashBag()->add(
                'notice',
                $response['text']
            );
            return new RedirectResponse($this->generateUrl('top_up'));
        }

        return $this->render(
            'simcard/top_up.html.twig',
            array(
                'form'=>$form->createView(),
            )
        );
    }

}
