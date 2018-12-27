<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\Query;
use App\Entity\Player;
use App\Validators\DateValidator;
use DateTime;

class PlayerController extends AbstractController
{
    /**
     * @Route("/api/players", methods={"GET"})
     */
    public function index()
    {
        $query = $this->getDoctrine()
            ->getRepository(Player::class)
            ->createQueryBuilder('c')
            ->getQuery();

        $players = $query->getResult(Query::HYDRATE_ARRAY);

        return $this->json([
            'result' => 'success',
            'data' => $players,
        ]);
    }

    /**
     * @Route("/api/players/{id}", methods={"GET"})
     */
    public function show(int $id)
    {
        if ($id < 1) {
            return $this->json([
                'result' => 'error',
                'message' => 'id_must_be_positive',
            ], 400);
        }

        $player = $this->getDoctrine()
            ->getRepository(Player::class)
            ->find($id);

        $orderLimit = $user->getOrderLimit();

        dd($orderLimit);

        if (!$player) {
            return $this->json([
                'result' => 'error',
                'message' => 'entity_not_found',
            ], 404);
        }

        $player = [
            'id' => $player->getId(),
            'first_name' => $player->getFirstName(),
            'last_name' => $player->getLastName(),
            'dob' => $player->getDob(),
            'created_at' => $player->getCreatedAt(),
            'updated_at' => $player->getUpdatedAt(),
        ];

        return $this->json([
            'result' => 'success',
            'data' => $player,
        ]);
    }

    /**
     * @Route("/api/players", methods={"POST"})
     */
    public function create(Request $request)
    {
        if (!$request->query->get('first_name') || !$request->query->get('last_name')) {
            return $this->json([
                'result' => 'error',
                'message' => 'must_provide_first_name_and_last_name',
            ], 400);
        }

        $validator = new DateValidator;

        if (!$validator->isValidDate($request->query->get('dob'))) {
            return $this->json([
                'result' => 'error',
                'message' => 'dob_not_a_valid_date',
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $player = new Player;

        $player->setFirstName(
            $request->query->get('first_name')
        );

        $player->setLastName(
            $request->query->get('last_name')
        );

        $dob = DateTime::createFromFormat('Y-m-d', $request->query->get('dob'));

        $player->setDob($dob);

        $player->setCreatedAt($datetime = new DateTime);
        $player->setCreatedAt($datetime);

        $entityManager->persist($player);

        $entityManager->flush();

        return $this->json([
            'result' => 'success',
            'data' => ['id' => $player->getId()],
        ]);
    }

    /**
     * @Route("/api/players/{id}", methods={"PUT"})
     */
    public function update($id, Request $request)
    {
        if ($id < 1) {
            return $this->json([
                'result' => 'error',
                'message' => 'id_must_be_positive',
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $player = $entityManager->getRepository(Player::class)->find($id);

        if (!$player) {
            return $this->json([
                'result' => 'error',
                'message' => 'entity_not_found',
            ], 404);
        }

        $validator = new DateValidator;

        if (!$validator->isValidDate($request->query->get('dob'))) {
            return $this->json([
                'result' => 'error',
                'message' => 'dob_not_a_valid_date',
            ], 400);
        }

        $dob = DateTime::createFromFormat('Y-m-d', $request->query->get('dob'));

        $player->setFirstName(
            $request->query->get('first_name')
        );

        $player->setLastName(
            $request->query->get('last_name')
        );

        $player->setDob($dob);

        $entityManager->flush();

        return $this->json([
            'result' => 'success',
            'data' => ['id' => $player->getId()],
        ]);
    }

    /**
     * @Route("/api/players/{id}", methods={"DELETE"})
     */
    public function delete($id)
    {
        if ($id < 1) {
            return $this->json([
                'result' => 'error',
                'message' => 'id_must_be_positive',
            ], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $player = $entityManager->getRepository(Player::class)->find($id);

        if (!$player) {
            return $this->json([
                'result' => 'error',
                'message' => 'entity_not_found',
            ], 404);
        }

        $playerId = $player->getId();

        $entityManager->remove($player);
        $entityManager->flush();

        return $this->json([
            'result' => 'success',
            'data' => ['deleted_id' => $playerId],
        ]);
    }
}
