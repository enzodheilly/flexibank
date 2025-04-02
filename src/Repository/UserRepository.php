<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouver les utilisateurs par rôle
     *
     * @param string $role Le rôle à chercher (ex: 'ROLE_ADMIN')
     * @return User[] Retourne un tableau d'utilisateurs avec ce rôle
     */
    public function findByRole(string $role): array
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $sql = "SELECT * FROM user WHERE JSON_CONTAINS(roles, :role)";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute(['role' => json_encode($role)]);
    
        return $stmt->fetchAllAssociative();
    }
    
    public function findByEmail(string $email)
{
    return $this->createQueryBuilder('u')
        ->where('u.email LIKE :email')
        ->setParameter('email', '%' . $email . '%')
        ->getQuery()
        ->getResult();
}

}
