<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, protected readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
    }

    public function create(string $login, string $password, string $phone): User
    {
        $user = new User();
        $user = $this->setUserFields($user, $login, $password, $phone);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    public function update(string $login, string $password, string $phone, int $id): ?User
    {
        $user = $this->find($id);
        if(!$user){
            return null;
        }
        $user = $this->setUserFields($user, $login, $password, $phone);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    public function delete(int $id): bool
    {
        $user = $this->find($id);
        if($user){
            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();
            return true;
        }
        return false;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    protected function setUserFields($user, $login, $password, $phone): User
    {
        $user->setLogin($login);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setPhone($phone);
        return $user;
    }
}
