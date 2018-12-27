<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\Query;
use App\Entity\Order;
use App\Entity\User;
use DateTime;

class OrderController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function index()
    {
        return $this->render('orders/index.html.twig', ['error' => false]);
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function store(Request $request)
    {
        if (!$request->request->get('product_id')) {
            return $this->json([
                'result' => 'error',
                'message' => 'product_id_not_provided',
            ], 400);
        }

        $feature = $request->request->get('feature');

        $userId = $request->request->get('user_id');

        $function = 'get' . $feature . 'Limit';

        $date = new DateTime();
        $date->modify('-24 hour');

        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($userId);

        $orderLimit = (int) $user->$function();

        $count = (int) $this->getDoctrine()
            ->getRepository(Order::class)
            ->createQueryBuilder('u')
            ->andWhere('u.user_id = :user_id')
            ->setParameter('user_id', $userId)
            ->andWhere('u.created_at > :date')
            ->setParameter(':date', $date)
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($count >= $orderLimit) {
            return $this->render('orders/index.html.twig', [
                'error' => true,
                'message' => 'This user has a ' . $feature . ' limit of ' . $orderLimit . ' in the last 24 hours',
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $order = new Order;

        $order->setUserId($userId);

        $order->setProductId(
            $request->request->get('feature')
        );

        $order->setAmount(1);

        $order->setCreatedAt(new DateTime);

        $entityManager->persist($order);

        $entityManager->flush();

        return $this->render('orders/index.html.twig', [
            'error' => false,
            'message' => 'Order Created',
        ]);
    }

}
