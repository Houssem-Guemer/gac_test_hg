<?php

namespace App\Controller;

use App\Entity\Expense;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashController extends AbstractController
{
    /**
     * @Route ("/", name="app_dash_index")
     */
    public function index(Request $request)
    {
        $expenseRepository = $this->getDoctrine()->getRepository(Expense::class);

        $startDate = '2022-01-01 00:00:00';
        $endDate = '2022-02-28 23:59:59';

        if($request->query->get('startDate')) {
            $startDate = $request->query->get('startDate');
        }

        if($request->query->get('endDate')) {
            $endDate = $request->query->get('endDate');
        }

        return $this->render('pages/index.html.twig', [
            'sumHT'=>$expenseRepository->sumHT($startDate,$endDate),
            'sumTTC'=>$expenseRepository->sumTTC($startDate,$endDate),
            'sumHtByCategory'=>$expenseRepository->sumHtByCategory($startDate,$endDate),
            'sumTtcByCategory'=>$expenseRepository->sumTtcByCategory($startDate,$endDate),
            'sumHtByVehicle'=>$expenseRepository->sumHtByVehicle($startDate,$endDate),
            'sumTtcByVehicle'=>$expenseRepository->sumTtcByVehicle($startDate,$endDate),
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