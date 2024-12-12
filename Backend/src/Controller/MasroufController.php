<?php

namespace App\Controller;

use App\Entity\Masrouf;
use App\Repository\MasroufRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/masrouf', name: 'api_masrouf_')]
class MasroufController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(MasroufRepository $masroufRepository): JsonResponse
    {
        $masroufs = $masroufRepository->findAll();
        return $this->json($masroufs, 200);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Masrouf $masrouf): JsonResponse
    {
        return $this->json($masrouf, 200);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $masrouf = new Masrouf();
        $masrouf->setValue($data['value'] ?? null);
        $masrouf->setDate(isset($data['date']) ? new \DateTime($data['date']) : null);
        $masrouf->setType($data['type'] ?? null);

        // Validate the entity
        $errors = $validator->validate($masrouf);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => (string) $errors,
            ], 400);
        }

        $entityManager->persist($masrouf);
        $entityManager->flush();

        return $this->json($masrouf, 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        Request $request,
        Masrouf $masrouf,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (isset($data['value'])) {
            $masrouf->setValue($data['value']);
        }
        if (isset($data['date'])) {
            $masrouf->setDate(new \DateTime($data['date']));
        }
        if (isset($data['type'])) {
            $masrouf->setType($data['type']);
        }

        // Validate the updated entity
        $errors = $validator->validate($masrouf);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Validation failed',
                'errors' => (string) $errors,
            ], 400);
        }

        $entityManager->flush();

        return $this->json($masrouf, 200);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Masrouf $masrouf,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $entityManager->remove($masrouf);
        $entityManager->flush();

        return $this->json(['message' => 'Masrouf deleted successfully'], 200);
    }
}