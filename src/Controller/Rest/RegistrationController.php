<?php

namespace App\Controller\Rest;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Service\UserService;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 *
 * @category Registration
 *
 * @package App\Controller\Rest
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class RegistrationController extends AbstractFOSRestController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * RegistrationController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Rest\Get("/createUser")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return View
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $passwordEncoder): View
    {
        $result = $this->userService->addUser($request->get('name'), $request->get('email'), $request->get('password'), $passwordEncoder);
        return View::create($result, Response::HTTP_CREATED);
    }
}
