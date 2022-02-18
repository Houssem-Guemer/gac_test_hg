<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashController extends AbstractController
{
    /**
     * @Route ("/", name="app_dash_index")
     */
    public function index()
    {
        return $this->render('pages/index.html.twig');
    }

    /**
     * @Route("/importcsv", name="app_dash_importcsv")
     */
    public function importPage()
    {
        return $this->render('pages/importcsv.html.twig');
    }

    /**
     * @Route("/importcsvfile", methods="POST", name="app_dash_importfile")
     */
    public function importCSV(){
        return $this->json(['success'=>true]);
    }

}