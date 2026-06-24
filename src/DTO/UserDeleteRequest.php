<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
class UserDeleteRequest
{
    public function __construct(
        #[Assert\Type('integer')]
        public int $id,
    )
    {
    }
}
