<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class SecurityController extends AbstractController
{
    private RoleHierarchyInterface $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    /**
     * @return JsonResponse
     */
    private function getRoles(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json([]);
        }
        $userRoles = $user->getRoles();
        $allRoles = $this->roleHierarchy->getReachableRoleNames($userRoles);
        return $this->json($allRoles);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid credentials.'
            ], 400);
        }

        return $this->json(['roles' => $this->getRoles()]);
    }
}
