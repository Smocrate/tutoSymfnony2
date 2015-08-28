<?php
// src/SMO/PlatformBundle/Controller/AdvertController.php

namespace SMO\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
#use Symfony\Component\HttpFoundation\Response;
#use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
#use SMO\PlatformBundle\Entity\Advert;
#use SMO\PlatformBundle\Entity\Image;
#use SMO\PlatformBundle\Entity\Application;
#use SMO\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
  /**
   * Affichage de la page principal des annonces 
   * @param integer $page
   */
  public function indexAction($page)
  {
    if ($page < 1)
    {
      throw $this->createNotFoundHttpException("La page ".$page." n'existe pas.");
    }
    
    // Récupération de la liste de toutes les annonces
    $listAdverts = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('SMOPlatformBundle:Advert')
        ->getAdverts()
    ;
    
    // On affiche les résultats
    return $this->render('SMOPlatformBundle:Advert:index.html.twig', array(
        'listAdverts' => $listAdverts
    ));
  }
  
  
  /**
   * Affichage d'une annonce grace à son id
   * @param integer $id
  */
  public function viewAction($id)
  {
    // Recuperation du manager
    $em = $this
        ->getDoctrine()
        ->getManager()
    ;
    
    // Recuperation de l'annonce grace à son id
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->myFind($id)
    ;
    
    if(null === $advert)
    {
        throw $this->createNotFoundHttpException("L'annonce d'id '$id' n'existe pas");
    }
    
    // Récupération de la liste des skills
    $listSkills = $em
        ->getRepository('SMOPlatformBundle:AdvertSkill')
        ->findByAdvert($advert)
    ;
    
    // Afficher la page de l'annonce
    return $this->render('SMOPlatformBundle:Advert:view.html.twig', array(
      'advert' => $advert,
      'listAdvertSkills' => $listSkills
    ));
  }
    
  /**
  * Ajout d'une annonce
  * @param request $request
  */
  public function addAction(Request $request)
  {
    // Si on arrive avec un envoi de formulaire
    if($request->isMethod('POST'))
    {
        $request
            ->getSession()
            ->getFlashBag()
            ->add('info', 'Annonce bien enregistrée.')
        ;
        
        return $this->redirect($this->generateUrl('smo_platform_view', array(
            'id' => 1
        )));
    }
    
    // Si on arrive pas avec POST on affiche le formulaire
    return $this->render('SMOPlatformBundle:Advert:edit.html.twig');    
  }
  
  /**
   * Edition d'une annonce
   * @param integer $id
   * @param request $request
   */
  public function editAction($id, Request $request)
  {
    // Récupération du manager
    $em = $this->getDoctrine()->getManager();
    
    // Récupération de l'entité de l'annonce
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->find($id)
    ;
    
    if(null === $advert)
    {
        throw $this->createNotFoundHttpException("L'annonce id = '$id' n'existe pas.");
    }    
    
    return $this->render('SMOPlatformBundle:Advert:edit.html.twig', array(
        'advert' => $advert
    ));
  }
  
  /**
   * Suppression d'une annonce
   * @param integer $id
   */
  public function deleteAction($id)
  {
    // Récupération du manager
    $em = $this->getDoctrine()->getManager();
    
    // Récupération de l'entité de l'annonce
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->find($id)
    ;
    
    if(null === $advert)
    {
        throw $this->createNotFoundHttpException("Annonce id = '$id' n'existe pas.");
    }
    
    // Si on entre par la méthod post on supprime l'annonce
    if($request->isMethod('POST'))
    {
        $request
            ->getSession()
            ->getFlashBag()
            ->add('info', 'Annonce bien supprimée.')
        ;
        
        return $this->redirect($this->generateUrl('smo_platform_home'));
    }
    
    // Si la requete est en get on affiche une page de confirmation avant delete
    return $this->render('SMOPlatformBundle:Advert:delete.html.twig', array(
        'advert' => $advert
    ));
  }
  
  /**
   * Retourne un certain nombre d'annonce pour le menu
   * @param integer $limit
   */
  public function menuAction($limit = 3)
  {
    // Récupération de la liste
    $listAdverts = $this
        ->getDoctrine()
        ->getRepository('SMOPlatformBundle:Advert')
        ->findBy(
            array(),                    // Pas de critère
            array('date' => 'DESC'),    // On tri par ordre décroissant
            $limit,                     // On retourne les $limit premier résultats
            0                           // A partir du 1er résultat
    );
    
    return $this->render("SMOPlatformBundle:Advert:menu.html.twig", array(
        'listAdverts' => $listAdverts
        )
    );
  }
}