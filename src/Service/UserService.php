<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return array
     */
    public function list(): array
    {
        $users = $this->doctrine
            ->getRepository(User::class)
            ->findAll();

        $data = [];

        foreach ($users as $user) {
            $data[] = $this->convertUserToArray($user);
        }

        return [
            'errors' => [],
            'status' => 'success',
            'data' => $data
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function show(int $id): array
    {
        $user = $this->doctrine
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            return [
                'errors' => [],
                'status' => 'error',
                'data' => []
            ];
        }

        return [
            'errors' => [],
            'status' => 'success',
            'data' => $this->convertUserToArray($user)
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function create(Request $request)
    {
        $entityManager = $this->doctrine->getManager();

        $user = new User();
        $user->setEmail($request->query->get('email'));
        $user->setName($request->query->get('name'));
        $user->setAge($request->query->get('age'));
        $user->setSex($request->query->get('sex'));
        $user->setBirthday($this->convertBirthday($request->query->get('birthday')));
        $user->setPhone($request->query->get('phone'));
        $user->setCreatedAt(new \DateTimeImmutable("now"));
        $user->setUpdatedAt(new \DateTimeImmutable("now"));
        $entityManager->persist($user);
        $entityManager->flush();

        return [
            'errors' => [],
            'status' => 'success',
            'data' => $this->convertUserToArray($user)
        ];
    }

    /**
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function update(Request $request, int $id): array
    {
        $entityManager = $this->doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return [
                'errors' => [],
                'status' => 'error',
                'data' => []
            ];
        }

        foreach ($request->query as $key=>$value) {
            switch ($key) {
                case 'email':
                    $user->setEmail($request->query->get('email'));
                    break;
                case 'name':
                    $user->setName($request->query->get('name'));
                    break;
                case 'age':
                    $user->setAge($request->query->get('age'));
                    break;
                case 'sex':
                    $user->setSex($request->query->get('sex'));
                    break;
                case 'birthday':
                    $user->setBirthday($this->convertBirthday($request->query->get('birthday')));
                    break;
                case 'phone':
                    $user->setSex($request->query->get('phone'));
                    break;
            }
        }
        $user->setUpdatedAt(new \DateTimeImmutable("now"));

        $entityManager->flush();

        return [
            'errors' => [],
            'status' => 'success',
            'data' => $this->convertUserToArray($user)
        ];
    }

    public function delete(int $id): array
    {
        $entityManager = $this->doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return [
                'errors' => [],
                'status' => 'error',
                'data' => []
            ];
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return [
            'errors' => [],
            'status' => 'success',
            'data' => $this->convertUserToArray($user)
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    protected function convertUserToArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'age' => $user->getAge(),
            'sex' => (int)$user->getSex(),
            'birthday' => $user->getBirthday(),
            'phone' => $user->getPhone(),
            'date_created' => $user->getCreatedAt(),
            'date_updated' => $user->getUpdatedAt(),
        ];
    }

    /**
     * @param string $birthday
     * @return \DateTimeInterface
     */
    protected function convertBirthday(string $birthday): \DateTimeInterface
    {
        $bday = explode('-', $birthday);
        $result = new DateTime();
        $result
            ->setDate($bday[0], $bday[1], $bday[2])
            ->format('Y-m-d');

        return $result;
    }
}
