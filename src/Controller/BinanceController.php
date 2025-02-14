<?php

// src/Controller/BinanceController.php
namespace App\Controller;

use App\Service\BinanceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BinanceController extends AbstractController
{
    #[Route('/api/binance', name: 'binance_api')]
public function getBinanceData(BinanceService $binanceService)
{
    $data = $binanceService->getAccountInfo();
    
    return $this->json($data);
}

}
