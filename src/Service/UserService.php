<?php

namespace App\Service;

use \App\Repository\UserRepository;
use \App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserService
 *
 * @category User
 *
 * @package App\Service
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface  $entityManager, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param string $name
     * @return User|null
     */
    public function getUserByName(string $name): ?User
    {
        return $this->userRepository->findOneBy(array('name'=>$name));
    }

    /**
     * @return array|null
     */
    public function getAllUsers(): ?array
    {
        return $this->userRepository->findAll();
    }

    public function getAdmin()
    {
        return $this->userRepository->getAdmin();
    }

    /**
     * @param $token
     * @return User|null
     */
    public function getUserByToken($token)
    {
        return $this->userRepository->getUserByToken($token);
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return array|null
     */
    public function addUser(string $name, string $email, string $password, UserPasswordEncoderInterface $passwordEncoder): ?array
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setToken(uniqid());
        $users = $this->userRepository->findAll();
        if (sizeof($users)>0) {
            $user->setRoles(array('ROLE_USER'));
        } else {
            $user->setRoles(array('ROLE_ADMIN','ROLE_USER'));
        }
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return array('user'=>$user,'error'=>false, 'message'=>'User registered successfully');
    }

    /**
     * @param $id
     * @return array|null
     */
    public function deleteUserById($id): ?array
    {
        $user = $this->userRepository->find($id);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return array('result'=>true,'error'=>false, 'message'=>'User deleted successfully');
        }
        return array('result'=>false,'error'=>true, 'message'=>'User cannot be deleted');
    }

    /**
     * @param $request
     * @return array
     */
    public function checkUserToken(Request $request)
    {
        $error=false;
        $errorMessage='';
        $responseCode='';
        if (!$request->get('token')) {
            $error=true;
            $errorMessage = 'user token is needed from user table as a get token parameter';
            $responseCode=Response::HTTP_BAD_REQUEST;
        } else {
            $token = $request->get('token');
            $user = $this->getUserByToken($token);
            if (!isset($user)) {
                $error = true;
                $errorMessage = 'invalid token, user token is needed from user table as a get token parameter';
                $responseCode = Response::HTTP_UNAUTHORIZED;
            }
        }
        return array('error'=>$error,'errorMessage'=>$errorMessage,'responseCode'=>$responseCode);
    }
}
