<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Service\UserService;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/users', name: 'user_index', methods:['get'] )]
    public function index(UserService $userService): JsonResponse
    {
        $result = $userService->list();

        return $this->json($result);
    }

    #[Route('/user/{id}', name: 'user_show', methods:['get'] )]
    public function show(UserService $userService, int $id): JsonResponse
    {
        $result = $userService->show($id);

        return $this->json($result);
    }

    #[Route('/user', name: 'user_create', methods:['post'])]
    public function create(UserService $userService, Request $request): JsonResponse
    {
        $result = $userService->create($request);

        if ($result['status'] == 'success') {
            return $this->json('Created a user successfully');
        } else {
            return $this->json('Created a user error');
        }
    }

    #[Route('/user/{id}', name: 'user_update', methods:['put', 'patch'] )]
    public function update(UserService $userService, Request $request, int $id): JsonResponse
    {
        $result = $userService->update($request, $id);

        if ($result['status'] == 'success') {
            return $this->json('Updated a user successfully with id ' . $id);
        } else {
            return $this->json('Updated a user error with id ' . $id);
        }
    }

    #[Route('/user/{id}', name: 'user_delete', methods:['delete'] )]
    public function delete(UserService $userService, int $id): JsonResponse
    {
        $result = $userService->delete($id);

        if ($result['status'] == 'success') {
            return $this->json('Deleted a user successfully with id ' . $id);
        } else {
            return $this->json('Deleted a user error with id ' . $id);
        }
    }

    private function convertBirthday(string $birthday): \DateTimeInterface
    {
        $bday = explode('-', $birthday);
        $result = new DateTime();
        $result
            ->setDate($bday[0], $bday[1], $bday[2])
            ->format('Y-m-d');

        return $result;
    }
}
