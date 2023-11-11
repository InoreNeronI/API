<?php

namespace App\Controller;

use Safe\Exceptions\InfoException;
use Symfony\Component\Security\Core\User\UserInterface;
use function Safe\phpinfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class SecurityController extends AbstractController
{
    public function __construct(private readonly RoleHierarchyInterface $roleHierarchy)
    {
    }

    /**
     * @return JsonResponse
     */
    private function getRoles(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return $this->json([]);
        }
        $userRoles = $user->getRoles();
        $allRoles = $this->roleHierarchy->getReachableRoleNames($userRoles);
        return $this->json($allRoles);
    }

    /**
     * @return Response
     * @throws InfoException
     */
    #[Route(path: '/', name: 'info', methods: ['GET'])]
    public function info(): Response
    {
        return new Response(phpinfo());
    }

    /**
     * @return JsonResponse
     */
    #[Route(path: '/login', name: 'login', methods: ['POST'])]
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
