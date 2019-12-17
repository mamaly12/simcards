<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RegistrationController
 *
 * @category Registration
 *
 * @package App\Controller\Web
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class RegistrationController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * RegistrationController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @Route("/register", name="app_register")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        if ($this->getUser()!=null) {
            return new RedirectResponse($this->generateUrl('home_url'));
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $users=$this->userService->getAllUsers();
            $result = $this->userService->addUser($form->get('name')->getData(), $form->get('email')->getData(), $form->get('plainPassword')->getData(), $passwordEncoder);
            // do anything else you need here, like send an email
            if (isset($result['user'])) {
                $user = $result['user'];
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            } else {
                return new RedirectResponse($this->generateUrl('home_url'));
            }
        }
        return $this->render
        ('registration/register.html.twig',
            [
            'registrationForm' => $form->createView(),
            ]
        );
    }
}
