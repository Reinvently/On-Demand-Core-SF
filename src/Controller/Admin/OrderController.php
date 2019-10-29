<?php
/**
 * Created by PhpStorm.
 * User: timur
 * Date: 10.06.19
 * Time: 22:57
 */

namespace App\Controller\Admin;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class OrderController extends AbstractController
{
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @Route("/admin/order/change-state", methods={"GET"})
     */
    public function changeState(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Order::class);
        $entityManager = $this->getDoctrine()->getManager();

        $order = $repository->find($request->query->get('id'));

        $workflow = $this->registry->get($order);
        $workflow->apply($order, $request->query->get('transition'));
        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirect('/admin/?entity=Order&action=list');
    }
}