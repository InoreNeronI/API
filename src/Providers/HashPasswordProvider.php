<?php

namespace App\Providers;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class HashPasswordProvider
{
    public function __construct(private UserPasswordHasherInterface $encoder)
    {
    }

    public function hashPassword(string $plainPassword): string
    {
        return $this->encoder->hashPassword(new User(), $plainPassword);
    }

}
