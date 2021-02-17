<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Repository\AbonneRepository;
use App\Repository\ActionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequetesController extends AbstractController
{
    /**
     * @Route("/requetes", name="requetes")
     */
    public function index(): Response
    {
        return $this->render('requetes/index.html.twig', [
            'controller_name' => 'RequetesController',
        ]);
    }

    /**
     * @Route("/dureeTotalReelleAppels", name="dureeTotalReelleAppels")
     * @param ActionRepository $actionRepository
     * @return Response
     */
    public function dureeTotalReelleAppels( ActionRepository $actionRepository): Response
    {
        $dateLimite = \DateTime::createFromFormat('d/m/Y' , '15/02/2012');
        $tempsReel = $actionRepository->findTotalReelleAppels($dateLimite);
        $tempsReelString=[];
        foreach ($tempsReel as $value){
            if (!empty($value['dureeVolumeReelEnHeure']) ){
                $tempsReelString[]= $value['dureeVolumeReelEnHeure']->format('H:i:s');
            }
        }

        $total = 0;
        foreach ($tempsReelString as $time) {
            list($heures, $minutes, $secondes) = explode(':', $time);
            $total += $heures * 3600 + $minutes * 60 + $secondes;
        }
        $total = floor($total / 3600) . ' Heures '  . ($total /60) % 60 . ' minutes ' . $total % 60 . ' secondes ';

        return $this->render('requetes/index.html.twig', [
            'tempsTotal' => $total,
        ]);
    }
    /**
     * @Route("/totalSms", name="totalSms")
     * @param ActionRepository $actionRepository
     * @return Response
     */
    public function totalSms( ActionRepository $actionRepository): Response
    {
       $actions = $actionRepository->findAll();
        $compteursSms =0;
       foreach ($actions as $action){
           if ($action->getType() != null){
               if (preg_match('/sms/' ,$action->getType()->getLibelle() )){
                   $compteursSms +=1 ;
               }
           }
       }
        return $this->render('requetes/index.html.twig', [
            'totalSms' => $compteursSms,
        ]);
    }

    /**
     * @Route("/topDataAbonne", name="topDataAbonne")
     * @param ActionRepository $actionRepository
     * @param AbonneRepository $abonneRepository
     * @return Response
     */
    public function topDataAbonne( ActionRepository $actionRepository , AbonneRepository $abonneRepository): Response
    {

        $abonnes = $abonneRepository->findAll();

        $debutTrancheHoraire = new \DateTime('08:00:00');
        $finTrancheHoraire = new \DateTime('18:00:00');
        foreach ($abonnes as $abonne){
            $c = 0;
            $actions = $actionRepository->findTop10Data($abonne,$debutTrancheHoraire,$finTrancheHoraire);
            foreach ($actions as $action){
                foreach ($action as $value){
                    $c +=1;
                    if (!empty($value)){
                        $datasAbonne[] = $value;
                        if ($c == 10 ){
                            $top10[] =['abonne'=>$abonne->getNumero(),'dataFacture'=>$datasAbonne];
                            $datasAbonne = [];
                        }
                    }
                }
            }
        }
        return $this->render('requetes/index.html.twig', [
            'TopDataAbonne' => $top10
        ]);
    }

}
