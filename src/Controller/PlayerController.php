<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Player;
use Doctrine\ORM\Query;
use Datetime;
use Symfony\Component\HttpFoundation\Request;

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
    public function show($id)
    {
       $player = $this->getDoctrine()
            ->getRepository(Player::class)
            ->find($id);

        if (!$player) {
            throw $this->createNotFoundException(
                'No product found for id '. $id
            );
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

        $player->setCreatedAt($datetime = new Datetime);
        $player->setCreatedAt($datetime);

        $entityManager->persist($player);

        $entityManager->flush();

        return $this->json([
            'result' => 'success',
            'data' => ['id' => $player->getId()],
        ]);
    }

    /**
     * @Route("/api/players", methods={"PUT"})
     */
    public function update()
    {

    }

    /**
     * @Route("/api/players/{id}", methods={"DELETE"})
     */
    public function delete()
    {

    }
}
