<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(Request $request)
    {
        $user = $this->getUser();
				
				if (!$this->isGranted('IS_AUTHENTICATED_FULLY'))
				{
					return $this->json([
						'error' => 'Usuario o contraseña incorrecta. Comprueba tus datos'		
					], 400);
				}

        return $this->json([
            'roles' => $user->getRoles(),
        ]);
    }
}
