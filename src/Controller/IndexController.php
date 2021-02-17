<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Entity\Action;
use App\Entity\CompteFacture;
use App\Entity\Facture;
use App\Entity\FactureAbonne;
use App\Entity\TypeAction;
use App\Form\CsvReaderType;
use App\Repository\AbonneRepository;
use App\Repository\ActionRepository;
use App\Repository\CompteFactureRepository;
use App\Repository\FactureAbonneRepository;
use App\Repository\FactureRepository;
use App\Repository\TypeActionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', []);
    }

    /**
     * @return mixed
     */
    protected function ouvrirCsv(){
        //on choisi le nom du fichier csv à charger
        $nom_fichier = "./fichiersCsv/tickets_appels_201202.csv";
        //on choisi le separateur des lignes du csv
        $separateur = ";";
        $row = 0;
        $donnee = array();
        if (($f = fopen($nom_fichier, "r")) !== FALSE) {
            $taille = filesize($nom_fichier) + 1;
            while ($donnee = fgetcsv($f, $taille, $separateur)) {
                $result[$row] = $donnee;
                $row++;
            }
            fclose($f);
        }
        return $result;
    }


    /**
     * @Route("/chargementCsvPartieCompteEtFacture", name="chargementCsvPartieCompteEtFacture")
     * @param CompteFactureRepository $compteFactureRepository
     * @param FactureRepository $factureRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieCompteEtFacture(CompteFactureRepository $compteFactureRepository, FactureRepository $factureRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les comptes deja enregistré en BD
        $compteBdd = $compteFactureRepository->findAll();
        if (!empty($compteBdd)) {
            foreach ($compteBdd as $value) {
                $compteExiste[] = $value->getNumero();
            }
        } else {
            $compteExiste = [];
        }
        //on recupere les factures deja enregistré en BDD
        $factureBdd = $factureRepository->findAll();
        if (!empty($factureBdd)) {
            foreach ($factureBdd as $value) {
                $factureExiste[] = $value->getNumero();
            }
        } else {
            $factureExiste = [];
        }

        //on parcours les lignes du csv récuperé
        for ($i = 3; $i < count($result); $i++) {
            for ($c = 0; $c < count($result[$i]); $c++) {
                switch ($c) {
                    //si $c = 0 alors on scanne les comptes
                    //si un compte n'est pas deja en BDD on l'enregistre
                    case 0:
                        if (!in_array($result[$i][0], $compteExiste)) {
                            $compteExiste[] = $result[$i][0];
                            $nouveauCompteFacture = new CompteFacture();
                            $nouveauCompteFacture->setNumero($result[$i][0]);
                            $entityManager->persist($nouveauCompteFacture);
                        }
                    //si $c = 1 alors on scanne les factures
                    //si une facture n'est pas deja en BDD on l'enregistre
                    case 1:
                        if (!in_array($result[$i][1], $factureExiste)) {
                            $factureExiste[] = $result[$i][1];
                            $nouvelleFacture = new Facture();
                            $nouvelleFacture->setNumero($result[$i][1]);
                            $entityManager->persist($nouvelleFacture);
                        }
                }
            }
        }
        $entityManager->flush();

        //on redirige vers la suite de l'enregistrement du csv
        return $this->redirectToRoute('chargementCsvPartieAbonne');
    }

    /**
     * @Route("/chargementCsvPartieAbonne", name="chargementCsvPartieAbonne")
     * @param CompteFactureRepository $compteFactureRepository
     * @param FactureRepository $factureRepository
     * @param AbonneRepository $abonneRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAbonne(CompteFactureRepository $compteFactureRepository, FactureRepository $factureRepository, AbonneRepository $abonneRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les comptes deja enregistré en BDD
        $compteBdd = $compteFactureRepository->findAll();
        if (!empty($compteBdd)) {
            foreach ($compteBdd as $value) {
                $compteExiste[] = $value->getNumero();
            }
        } else {
            $compteExiste = [];
        }
        //on recupere les factures deja enregistré en BDD
        $factureBdd = $factureRepository->findAll();
        if (!empty($factureBdd)) {
            foreach ($factureBdd as $value) {
                $factureExiste[] = $value->getNumero();
            }
        } else {
            $factureExiste = [];
        }
        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();
        if (!empty($abonneBdd)) {
            foreach ($abonneBdd as $value) {
                $abonneExiste[] = $value->getNumero();
            }
        } else {
            $abonneExiste = [];
        }

        for ($i = 3; $i < count($result); $i++) {
            for ($c = 0; $c < count($result[$i]); $c++) {
                switch ($c) {
                    //on garde dans une variable $compte le compte de la ligne scanner pour l'enregistrer avec l'abonne
                    case 0:
                        foreach ($compteBdd as $value) {
                            if ($result[$i][0] == $value->getNumero()) {
                                $compte = $value;
                            }
                        }
                    case 2:
                        //si l'abonne n'est pas deja en BDD
                        if (!in_array($result[$i][2], $abonneExiste)) {
                            $abonneExiste[] = $result[$i][2];
                        //on creer un nouvel objet abonne
                            $nouvelAbonne = new Abonne();
                            // on lui passe le n° d'abonne de la ligne et le n° de compte
                            $nouvelAbonne->setNumero($result[$i][2]);
                            $nouvelAbonne->setCompte($compte);
                            $entityManager->persist($nouvelAbonne);
                            $abonneBdd[] = $nouvelAbonne;
                        }
                }
            }
        }
        $entityManager->flush();
        return $this->redirectToRoute('chargementCsvPartieFactureAbonne');

    }

    /**
     * @Route("/chargementCsvPartieFactureAbonne", name="chargementCsvPartieFactureAbonne")
     * @param FactureRepository $factureRepository
     * @param AbonneRepository $abonneRepository
     * @param FactureAbonneRepository $factureAbonneRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieFactureAbonne(FactureRepository $factureRepository, AbonneRepository $abonneRepository, FactureAbonneRepository $factureAbonneRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les factures deja enregistré en BDD
        $factureBdd = $factureRepository->findAll();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();

        //on recupere toutes les liaisons FactureAbonne deja enregistré en BDD
        $factureAbonneBdd = $factureAbonneRepository->findAll();
        if (!empty($factureAbonneBdd)){
            foreach ($factureAbonneBdd as $value){
                $abonneAvecFacture[] = $value->getAbonne()->getNumero();
            }
            foreach ($factureAbonneBdd as $value){
                $factureAvecAbonne[] = $value->getFacture()->getNumero();
            }
        }else{
            $abonneAvecFacture = [];
            $factureAvecAbonne=[];
        }

        for ($i = 3; $i < count($result); $i++) {
            for ($c = 0; $c < count($result[$i]); $c++) {
                //si il n'y a pas de liaison facture - abonne en BDD
                if (empty($factureAbonneBdd)){
                    //on creer un nouvel objet FactureAbonne
                    $nouvelleFactureAbonne = new FactureAbonne();
                    foreach ($factureBdd as $value){
                        //on recupere l'abonne de la ligne et on le passe dans l'objet
                        if ($value->getNumero() == $result[$i][1]){
                            $nouvelleFactureAbonne->setFacture($value);
                            $factureAvecAbonne[] = $result[$i][1] ;
                        }
                    }
                    //on recupere la facture de la ligne et on la passe dans l'objet
                    foreach ($abonneBdd as $value){
                        if ($value->getNumero() == $result[$i][2]){
                            $nouvelleFactureAbonne->setAbonne($value);
                            $abonneAvecFacture[] = $result[$i][2];
                        }
                    }
                    $entityManager->persist($nouvelleFactureAbonne);
                    $factureAbonneBdd[] = $nouvelleFactureAbonne;
                    $entityManager->flush();
                }
                else{
                    //si le n° d'abonne de la ligne n'est pas egale a un n° abonnee ayant deja une relation
                    if (!in_array($result[$i][2], $abonneAvecFacture)){
                        //nouvel objet abonne
                        $nouvelleFactureAbonne = new FactureAbonne();
                        $abonneAvecFacture[]= $result[$i][2];
                        //on recupere la facture de la ligne et on la passe dans l'objet
                        foreach ($factureBdd as $value){
                            if ($value->getNumero() == $result[$i][1]){
                                $nouvelleFactureAbonne->setFacture($value);
                            }
                        }
                        foreach ($abonneBdd as $value){
                            //on recupere l'abonne de la ligne et on le passe dans l'objet
                            if ($value->getNumero() == $result[$i][2]){
                                $nouvelleFactureAbonne->setAbonne($value);
                            }
                        }
                        $factureAbonneBdd[] = $nouvelleFactureAbonne;
                        $entityManager->persist($nouvelleFactureAbonne);
                    }else{
                        //si le n° de facture de la ligne n'est pas egale a un n° de facture ayant deja une relation
                        if (!in_array($result[$i][1], $factureAvecAbonne)) {
                            $nouvelleFactureAbonne = new FactureAbonne();
                            $factureAvecAbonne[]=$result[$i][1];
                            //on recupere la facture de la ligne et on la passe dans l'objet
                            foreach ($factureBdd as $value) {
                                if ($value->getNumero() == $result[$i][1]) {
                                    $nouvelleFactureAbonne->setFacture($value);
                                }
                            }
                            //on recupere l'abonne de la ligne et on le passe dans l'objet
                            foreach ($abonneBdd as $value) {
                                if ($value->getNumero() == $result[$i][2]) {
                                    $nouvelleFactureAbonne->setAbonne($value);
                                }
                            }
                            $factureAbonneBdd[] = $nouvelleFactureAbonne;
                            $entityManager->persist($nouvelleFactureAbonne);
                        }
                    }
                }
            }
        }
        $entityManager->flush();
        return $this->redirectToRoute('chargementCsvPartieTypeAction');
    }

    /**
     * @Route("/chargementCsvPartieTypeAction", name="chargementCsvPartieTypeAction")
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieTypeAction(TypeActionRepository $typeActionRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();
        if (!empty($typeActionsBdd)) {
            foreach ($typeActionsBdd as $value) {
                $typeActionExiste[] = $value->getLibelle();
            }
        } else {
            $typeActionExiste = [];
        }
        for ($i = 3; $i < count($result) ; $i++) {
            for ($c = 0; $c < count($result[$i]); $c++) {
                if ($c == 7) {
                    //si le type d'action n'est pas en BDD on creer un nouvel objet TypeAction que l'on enregistre dans la BDD
                    if (!in_array($result[$i][7], $typeActionExiste)) {
                        $typeActionExiste[] = $result[$i][7];
                        $nouveauTypeAction = new TypeAction();
                        $nouveauTypeAction->setLibelle(utf8_encode($result[$i][7]));
                        $typeActionsBdd[] = $nouveauTypeAction;
                        $entityManager->persist($nouveauTypeAction);
                    }
                }
            }
        }
        $entityManager->flush();
        return$this->redirectToRoute('chargementCsvPartieAction1');
    }


    //function qui sera appelé pour enregistrer les nouvel action
    /**
     * @param $result
     * @param $i
     * @param $abonneBdd
     * @param $typeActionsBdd
     * @return Action
     */
    public function nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd){
        $nouvelleAction = new Action();
        for ($c = 0; $c < count($result[$i]); $c++) {
            //on recupere l'objet abonne de la ligne et on le set dans l'objet
            foreach ($abonneBdd as $value) {
                if ($result[$i][2] == $value->getNumero()) {
                    $abonne = $value;
                }
            }
            $nouvelleAction->setAbonne($abonne);
            //on recupere la date de l'action qui est de type string que l'on passe en type DateTime
            $nouvelleAction->setDate(\DateTime::createFromFormat('d/m/Y', $result[$i][3]));
            //on verifie si une heure existe
            if (preg_match("/:/", $result[$i][4]) && isset($result[$i][4])) {
                //on recupere l'heure' de l'action qui est de type string que l'on passe en type DateTime
                $nouvelleAction->setHeure(\DateTime::createFromFormat('H:i:s', $result[$i][4]));
            }
            
            // on verifie grace au ':' si la valeur duree volume reel en heure est une heure ou des datas
            if (preg_match("/:/", $result[$i][5]) ) {
                //si oui on recupere la duree volume reel en heure de l'action qui est de type string que l'on passe en type DateTime
                $dureeVolumeReelEnHeure = \DateTime::createFromFormat("H:i:s", $result[$i][5]);
                $nouvelleAction->setDureeVolumeReelData(null);
                $nouvelleAction->setDureeVolumeReelEnHeure($dureeVolumeReelEnHeure);
            } elseif (preg_match("/./", $result[$i][5])) {
                //sinon on l'enregistre en type int
                $nouvelleAction->setDureeVolumeReelData((int)$result[$i][5]);
                $nouvelleAction->setDureeVolumeReelEnHeure(null);
            } else {
                //sinon on n'enregistre rien
                $nouvelleAction->setDureeVolumeReelData(null);
                $nouvelleAction->setDureeVolumeReelEnHeure(null);
            }

            // on verifie grace au ':' si la valeur duree volume facture en heure est une heure ou des datas
            if (preg_match("/:/", $result[$i][6])) {
                //si oui on recupere la duree volume facture en heure de l'action qui est de type string que l'on passe en type DateTime
                $dureeVolumeFactureEnHeure = \DateTime::createFromFormat("H:i:s", $result[$i][6]);
                $nouvelleAction->setDureeVolumeFactureData(null);
                $nouvelleAction->setDureeVolumeFactureEnHeure($dureeVolumeFactureEnHeure);
            } elseif (preg_match("/./", $result[$i][6])) {
                //sinon on l'enregistre en type int
                $nouvelleAction->setDureeVolumeFactureData((int)$result[$i][6]);
                $nouvelleAction->setDureeVolumeFactureEnHeure(null);
            } else {
                //sinon on n'enregistre rien
                $nouvelleAction->setDureeVolumeFactureData(null);
                $nouvelleAction->setDureeVolumeFactureEnHeure(null);
            }

            if ($c == 7){
                //on recupere le type d'action deja enregistrer en BDD et on le passe à l'objet nouvelleAction
                foreach ($typeActionsBdd as $value) {
                    if ($result[$i][7] == $value->getLibelle()) {
                        $nouvelleAction->setType($value);
                    }
                }
            }

        }

        return $nouvelleAction;
    }

    //function réitérer plusieur fois pour eviter saturatione de la memoire pour enregistrer les actions
    /**
     * @Route("/chargementCsvPartieAction1", name="chargementCsvPartieAction1")
     * @param AbonneRepository $abonneRepository
     * @param ActionRepository $actionRepository
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAction1(AbonneRepository $abonneRepository, ActionRepository $actionRepository, TypeActionRepository $typeActionRepository)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();

        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();

        for ($i = 3 ; $i < round(count($result)/6, -1 ); $i++) {
            $nouvelleAction = $this->nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd);
            $entityManager->persist($nouvelleAction);
        }
        $entityManager->flush();

        return$this->redirectToRoute('chargementCsvPartieAction2');
    }

    /**
     * @Route("/chargementCsvPartieAction2", name="chargementCsvPartieAction2")
     * @param AbonneRepository $abonneRepository
     * @param ActionRepository $actionRepository
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAction2(AbonneRepository $abonneRepository, ActionRepository $actionRepository, TypeActionRepository $typeActionRepository)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();


        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();


        for ($i = round(count($result)/6, -1 ) ; $i < round(count($result)/3, -1 ); $i++) {
            $nouvelleAction = $this->nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd);
            $entityManager->persist($nouvelleAction);
        }
        $entityManager->flush();
        return$this->redirectToRoute('chargementCsvPartieAction3');

    }
    /**
     * @Route("/chargementCsvPartieAction3", name="chargementCsvPartieAction3")
     * @param AbonneRepository $abonneRepository
     * @param ActionRepository $actionRepository
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAction3(AbonneRepository $abonneRepository, ActionRepository $actionRepository, TypeActionRepository $typeActionRepository)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();


        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();


        for ($i = round(count($result)/3, -1 ) ; $i < round(count($result)/2, -1 ); $i++) {
            $nouvelleAction = $this->nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd);
            $entityManager->persist($nouvelleAction);
        }
        $entityManager->flush();
        return$this->redirectToRoute('chargementCsvPartieAction4');

    }
    /**
     * @Route("/chargementCsvPartieAction4", name="chargementCsvPartieAction4")
     * @param AbonneRepository $abonneRepository
     * @param ActionRepository $actionRepository
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAction4(AbonneRepository $abonneRepository, ActionRepository $actionRepository, TypeActionRepository $typeActionRepository)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();


        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();


        for ($i = round(count($result)/2, -1 ) ; $i < round((count($result)*2)/3, -1 ); $i++) {
            $nouvelleAction = $this->nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd);
            $entityManager->persist($nouvelleAction);
        }
        $entityManager->flush();
        return$this->redirectToRoute('chargementCsvPartieAction5');

    }
    /**
     * @Route("/chargementCsvPartieAction5", name="chargementCsvPartieAction5")
     * @param AbonneRepository $abonneRepository
     * @param ActionRepository $actionRepository
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAction5(AbonneRepository $abonneRepository, ActionRepository $actionRepository, TypeActionRepository $typeActionRepository)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();


        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();


        for ($i = round((count($result)*2)/3, -1 ) ; $i < round((count($result)*5)/6, -1 ); $i++) {
            $nouvelleAction = $this->nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd);
            $entityManager->persist($nouvelleAction);
        }
        $entityManager->flush();
        return$this->redirectToRoute('chargementCsvPartieAction6');

    }

    /**
     * @Route("/chargementCsvPartieAction6", name="chargementCsvPartieAction6")
     * @param AbonneRepository $abonneRepository
     * @param ActionRepository $actionRepository
     * @param TypeActionRepository $typeActionRepository
     * @return RedirectResponse
     */
    public function chargementCsvPartieAction6(AbonneRepository $abonneRepository, ActionRepository $actionRepository, TypeActionRepository $typeActionRepository)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $result = $this->ouvrirCsv();

        //on recupere les abonne deja enregistré en BDD
        $abonneBdd = $abonneRepository->findAll();


        //on recupere les tout les types d'action deja enregistré en BDD
        $typeActionsBdd = $typeActionRepository->findAll();


        for ($i = round((count($result)*5)/6, -1 ) ; $i < count($result); $i++) {
            $nouvelleAction = $this->nouvelleAction($result,$i,$abonneBdd,$typeActionsBdd);
            $entityManager->persist($nouvelleAction);
        }
        $entityManager->flush();
        return $this->render('requetes/index.html.twig', []);


    }

}
