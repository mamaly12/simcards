<?php


namespace App\Controller\Web;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class UserController
 *
 * @category User
 *
 * @package App\Controller\Web
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class UserController extends AbstractController
{

    /**
     * @var UserService
     */
    private $userService;
    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * @Security("has_role('ROLE_USER')")
     * @return Response
     * @Route("/user/list", name="home_url", methods={"GET"})
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $adminUser=$this->userService->getAdmin();
        $adminData=array();
        if (isset($adminUser)) {
            $adminData['id']=(int)$adminUser['userId'];
            $adminData['token']=(int)$adminUser['token'];
        }
        return $this->render('users/index.html.twig', array('users'=>$users,'adminData'=>$adminData));
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/user/delete", name="delete_user", methods={"DELETE"})
     */
    public function deleteUserAjax(Request $request)
    {
        $id = $request->get('id');
        $result=$this->userService->deleteUserById($id);
        exit(json_encode($result));
    }
}
