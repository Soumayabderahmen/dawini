<?php

namespace App\Security;

use App\Entity\Medecin;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Medecin) {
            return;
        }

        if(!$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas actif.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof Medecin) {
            return;
        }
    }
}