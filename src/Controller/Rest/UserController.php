<?php


namespace App\Controller\Rest;

use App\Service\SimCardService;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Service\UserService;

/**
 * Class UserController
 *
 * @category User
 *
 * @package App\Controller\Rest
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class UserController extends AbstractFOSRestController
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
     *
     * @Rest\Get("/deleteUser")
     * @param Request $request
     * @return View
     */
    public function deleteUser(Request $request): View
    {
        $result = $this->userService->checkUserToken($request);
        if ($result['error']==true) {
            return View::create(['error'=>$result['error'],'message' => $result['errorMessage']], $result['responseCode']);
        }
        $user=$this->userService->getAdmin();
        if($user['token']==$request->get('token')) {
            $id = $request->get('userId');
            if (isset($id)) {
                $result = $this->userService->deleteUserById($id);
                return View::create($result, Response::HTTP_OK);
            }
        }
        return View::create(['result'=>false,'error'=>true,'message'=>'invalid parameters'], Response::HTTP_CREATED);
    }

    /**
    *
    * @Rest\Get("/viewUsers")
    * @param Request $request
    * @return View
    */
    public function viewUsers(Request $request): View
    {
        $result = $this->userService->checkUserToken($request);
        if ($result['error']==true) {
            return View::create(['error'=>$result['error'],'message' => $result['errorMessage']], $result['responseCode']);
        }
        $users = $this->userService->getAllUsers();

        return View::create(array('result'=>true,'users'=>$users), Response::HTTP_CREATED);
    }
}
