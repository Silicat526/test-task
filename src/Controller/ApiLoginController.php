<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class ApiLoginController extends AbstractController
{
    public function __construct(protected readonly AccessTokenRepository $accessTokenRepository)
    {
    }

    #[Route('/v1/api/login', name: 'v1_api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if(!$user){
            return $this->json([
                'message' => 'Invalid credentials',
            ]);
        }


        $tokenString = bin2hex(random_bytes(32));
        $token = $this->accessTokenRepository->create($tokenString, $user);

        return $this->json([
            'token' => $token->getToken(),
        ]);
    }
}
