<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\GasStation;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashController extends AbstractController
{
    /**
     * @Route ("/", name="app_dash_index")
     */
    public function index()
    {
        $vehicleRepository = $this->getDoctrine()->getRepository(Vehicle::class);
        $expenseRepository = $this->getDoctrine()->getRepository(Expense::class);
        $gasStationRepository = $this->getDoctrine()->getRepository(GasStation::class);

        return $this->render('pages/index.html.twig', [
            'sumHT'=>$expenseRepository->sumHT(),
            'sumTTC'=>$expenseRepository->sumTTC(),
            'sumHtByCategory'=>$expenseRepository->sumHtByCategory(),
            'sumTtcByCategory'=>$expenseRepository->sumTtcByCategory(),
        ]);
    }

    /**
     * @Route("/importcsv", name="app_dash_importcsv")
     */
    public function importPage()
    {
        return $this->render('pages/importcsv.html.twig');
    }

}