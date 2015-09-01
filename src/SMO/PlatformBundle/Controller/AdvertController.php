<?php
// src/SMO/PlatformBundle/Controller/AdvertController.php

namespace SMO\PlatformBundle\Controller;

use SMO\PlatformBundle\Bigbrother\BigbrotherEvents;
use SMO\PlatformBundle\Bigbrother\MessagePostEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use SMO\PlatformBundle\Entity\Advert;
use SMO\PlatformBundle\Form\AdvertType;
use SMO\PlatformBundle\Form\AdvertEditType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#
use Symfony\Component\HttpFoundation\Response;
#use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
#use SMO\PlatformBundle\Entity\Image;
#use SMO\PlatformBundle\Entity\Application;
#use SMO\PlatformBundle\Entity\AdvertSkill;

class AdvertController extends Controller
{
  /**
   * Affichage de la page principal des annonces 
   * @param integer $page
   * @param integer $nbPerPage
   */
  public function indexAction($page = 1)
  {
    if ($page < 1)
    {
      throw $this->createNotFoundHttpException("La page '$page' n'existe pas.");
    }
    
    $nbPerPage = 3;
    
    // Récupération de la liste de toutes les annonces
    $listAdverts = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('SMOPlatformBundle:Advert')
        ->getAdverts($page, $nbPerPage)
    ;
    
    $nbAdverts = count($listAdverts);
    $nbPages   = ceil($nbAdverts / $nbPerPage);
    
    // Retourne une erreur si page demandé est > $nbPagesTotal
    if($page > $nbPages)
    {
        if($nbAdverts == 0)
        {
            return $this->createNotFoundHttpException("Aucune annonce n'a été enregistrée");
        }
        return $this->createNotFoundHttpException("La page '$page' n'existe pas.");
    }
    
    // On affiche les résultats
    return $this->render('SMOPlatformBundle:Advert:index.html.twig', array(
        'listAdverts' => $listAdverts,
        'nbAdverts' => $nbAdverts,
        'page' => $page,
        'nbPages' => $nbPages
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
        ->find($id)
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
  * @Security("has_role('ROLE_USER')")
  */
  public function addAction(Request $request)
  {
    $advert = new Advert();
    $advert->setUser($this->getUser());
    $advert->setAuthor($this->getUser()->getUsername());
    $form = $this->get('form.factory')->create(new AdvertType(), $advert);
    
    // Vérifier la validité des données entré
    if($form->handleRequest($request)->isValid())
    {
        $event = new MessagePostEvent($advert->getContent(), $advert->getUser());

        $this
            ->get('event_dispatcher')
            ->dispatch(BigbrotherEvents::onMessagePost, $event)
        ;

        $advert->setContent($event->getMessage());

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush();
        
        $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');
        
        return $this->redirect($this->generateUrl('smo_platform_view', array(
            'id' => $advert->getId(),
        )));
    }
    
    // Affichage du formulaire si pas d'envon, ou formulaire invalide
    return $this->render('SMOPlatformBundle:Advert:add.html.twig', array(
        'form' => $form->createView(),
    ));    
  }
  
  /**
   * Edition d'une annonce
   * @param integer $id
   * @param request $request
   */
  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->find($id)
    ;
    
    if(null === $advert)
    {
        throw $this->createNotFoundHttpException("L'annonce id = '$id' n'existe pas.");
    }    
    
    $form = $this->get('form.factory')->create(new AdvertEditType(), $advert);
    
    if($form->handleRequest($request)->isValid())
    {
        // Le formulaire vient d'être envoyé et il est valide
        $em->flush();
        
        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
        
        return $this->redirect($this->generateUrl('smo_platform_view', array(
            'id' => $id,
        )));
    }
    
    return $this->render('SMOPlatformBundle:Advert:edit.html.twig', array(
        'advert'    => $advert,
        'form'      => $form->createView(),
    ));
  }
  
  /**
   * Suppression d'une annonce
   * @param integer $id
   */
  public function deleteAction($id, Request $request)
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
    
    $form = $this->createFormBuilder()->getForm();
    
    if($form->handleRequest($request)->isValid())
    {
        $em->remove($advert);
        $em->flush();
        
        $request->getSession()->getFlashBag()->add('info', 'Annonce bien suppriumée.');
        
        return $this->redirect($this->generateUrl('smo_platform_home'));
    }
    
    // Si la requete est en get on affiche une page de confirmation avant delete
    return $this->render('SMOPlatformBundle:Advert:delete.html.twig', array(
        'advert'    => $advert,
        'form'      => $form->createView(),
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
  
  /**
   * createNotFoundHttpException
   * @param string $message
   */
  private function createNotFoundHttpException($message)
  {
    return $this->render('SMOPlatformBundle::error404.html.twig', array(
        'message' => $message
    ));
  }
  
  /**
   * Afficher la page de test avec la jquery
   */
  public function jqueryAction()
  {
      return $this->render('SMOPlatformBundle:Advert:jquery.html.twig');
  }
}   