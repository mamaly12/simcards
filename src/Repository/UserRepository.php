<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAdmin()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT u.id as userId, u.name as name, u.token as token 
            FROM user as u where roles REGEXP 'ROLE_ADMIN'";
        try {
            $stmt = $conn->prepare($sql);
        } catch (DBALException $e) {
        }
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param $token
     * @return User|null
     */
    public function getUserByToken($token)
    {
        return $this->findOneBy(array('token'=>$token));
    }
}
