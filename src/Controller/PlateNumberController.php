<?php

namespace App\Controller;

use App\Entity\Expense;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlateNumberController extends AbstractController
{
    /**
     * @Route("/platenumber", name="plate_number_search")
     */
    public function index(): Response
    {
        return $this->render('/pages/plate_number/index.html.twig');
    }

    /**
     * @Route("/platenumber/{plateNumber}", name="plate_number_show")
     */
    public function show($plateNumber): Response
    {
        $expenseRepository = $this->getDoctrine()->getRepository(Expense::class);
        return $this->render('/pages/plate_number/show.html.twig', [
            'plateNumber' => $plateNumber,
            'vehicleHistory' => $expenseRepository->vehicleHistory($plateNumber),
        ]);
    }
}
