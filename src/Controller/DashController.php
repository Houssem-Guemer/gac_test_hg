<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashController extends AbstractController
{
    /**
     * @Route ("/", name="app_dash_index")
     */
    public function index()
    {
        return $this->render('pages/index.html.twig', [
            'uploaded'=>'false'
        ]);
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
    public function importCSV(Request $request){
        $this->addFlash('uploaded','Votre fichier a été importer avec succès');
        return $this->redirect("/");
    }

}