<?php

namespace App\Controller;

use App\DTO\UserCreateRequest;
use App\DTO\UserDeleteRequest;
use App\DTO\UserUpdateRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/v1/api', name: 'v1_api_')]
#[IsGranted('ROLE_USER')]
final class UserController extends AbstractController
{
    public function __construct(
        protected readonly UserRepository $userRepository,
    ) {

    }

    #[Route('/users', name: 'users', methods: ['GET'])]
    public function index(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        if($this->isGranted('ROLE_ROOT')) {
            $userId = $request->query->get('id');
            if(!empty($userId)){
                $user = $this->userRepository->find($userId);
                if(!$user){
                    return $this->json([
                        'message' => 'No user found by id ' . $userId
                    ]);
                }
            }
        }

        return $this->json([
            'login' => $user->getLogin(),
            'phone' => $user->getPhone(),
        ]);
    }

    #[Route('/users', name: 'users_create', methods: ['PUT'])]
    #[IsGranted('ROLE_ROOT')]
    public function create(#[MapRequestPayload] UserCreateRequest $request): JsonResponse
    {
        $user = $this->userRepository->create(
            $request->login,
            $request->password,
            $request?->phone
        );
        return $this->json([
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'phone' => $user->getPhone()
        ]);
    }

    #[Route('/users', name: 'users_update', methods: ['POST'])]
    public function update(#[CurrentUser] User $user, #[MapRequestPayload] UserUpdateRequest $request): JsonResponse
    {
        if($this->isGranted('ROLE_ROOT') && $request->id) {
            $userId = $request->id;
        } else {
            $userId = $user->getId();
        }

        $user = $this->userRepository->update(
            $request->login,
            $request->password,
            $request->phone,
            $userId
        );

        if(!$user){
            return $this->json([
                'message' => 'No user found by id ' . $userId
            ]);
        }

        return $this->json([
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'phone' => $user->getPhone()
        ]);
    }

    #[Route('/users', name: 'users_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ROOT')]
    public function delete(#[MapRequestPayload] UserDeleteRequest $request)
    {
        $result = $this->userRepository->delete($request->id);
        $message = $result ? 'User deleted successfully' : 'No user found by id ' . $request->id;
        return $this->json([
            'message' => $message
        ]);
    }
}
