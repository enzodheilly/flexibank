<?php

// src/Service/CardOrderStatusUpdater.php

namespace App\Service;

use App\Repository\CardOrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class CardOrderStatusUpdater
{
    private CardOrderRepository $cardOrderRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CardOrderRepository $cardOrderRepository, EntityManagerInterface $entityManager)
    {
        $this->cardOrderRepository = $cardOrderRepository;
        $this->entityManager = $entityManager;
    }

    public function updateOrderStatus(): void
    {
        $orders = $this->cardOrderRepository->findPendingOrders();

        foreach ($orders as $order) {
            switch ($order->getStatus()) {
                case 'pending':
                    $order->setStatus('processing');
                    break;
                case 'processing':
                    $order->setStatus('active'); // Pour carte virtuelle, statut final
                    break;
            }

            $this->entityManager->persist($order);
        }

        $this->entityManager->flush();
    }
}
