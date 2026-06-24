<?php

namespace App\DTO;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateRequest
{
    public function __construct
    (
        #[Assert\Type('int')]
        public ?int $id = null,
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 1,
            max: 8,
            minMessage: 'Login should be at least 1 character long',
            maxMessage: 'Login should be at most 8 characters long'
        )]
        public string $login,
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 1,
            max: 8,
            minMessage: 'Password should be at least 1 character long',
            maxMessage: 'Password should be at most 8 characters long'
        )]
        public string $password,
        #[Assert\Type('string')]
        #[Assert\Length(
            min: 1,
            max: 8,
            minMessage: 'Phone should be at least 1 character long',
            maxMessage: 'Phone should be at most 8 characters long'
        )]
        public string $phone,
    )
    {
    }
}
