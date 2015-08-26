<?php
// src/SMO/PlatformBundle/Controller/AdvertController.php

namespace SMO\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SMO\PlatformBundle\Entity\Advert;
use SMO\PlatformBundle\Entity\Image;
use SMO\PlatformBundle\Entity\Application;
use SMO\PlatformBundle\Entity\AdvertSkill;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
  public function indexAction($page)
  {
    if ($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }
    
    $antispam = $this->container->get('smo_platform.antispam');
    $message = "J'écris un message de moins de 50 caractères !!!!!!!!! ou pas ^^";
    if( $antispam->isSpam($message) )
    {
        throw new \Exception('Votre message contient moins de 50 caractères');
    }
    
    return new Response($message);
    
    $mailer = $this->container->get('mailer');
    
    $listAdverts = $this->getTableauAnnonces();
    return $this->render('SMOPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
  
  
  /**
  *
  *
  */
  public function viewAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->myFindId($id)
    ;
    
    if( null === $advert )
    {
        throw new NotFoundHttpException("L'annonce d'id '$id' n'existe pas");
    }
    
    // On récupère la liste des candidatures
    $listApplications = $em
        ->getRepository('SMOPlatformBundle:Application')
        ->findBy(array('advert'=>$advert))
    ;
    
    //On récupère la liste des skill
    $listSkills = $em
        ->getRepository('SMOPlatformBundle:AdvertSkill')
        ->findBy(array(
            'advert' => $advert
        ))
    ;
    
    return $this->render('SMOPlatformBundle:Advert:view.html.twig', array(
      'advert' => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listSkills
    ));
  }
    
  /**
  *
  *
  */
  public function addAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    
    // Création d'une nouvelle annonce
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony2.');
    $advert->setAuthor('Guillaume E.');
    $advert->setContent('Nous cherchons un développeur Symfony2 débutant sur Grenoble.');
    $em->persist($advert);
    
    
    
    // Création d'une nouvelle image
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de rêve');
    // Liaison de image à annonce
    $advert->setImage($image);
    // Ajout de candidature 1
    $application1 = new Application();
    $application1->setAuthor('Martine');
    $application1->setContent("Je suis très motivé !");
    $application1->setAdvert($advert);
    $em->persist($application1);
    // Ajout de candidature 2
    $application2 = new Application();
    $application2->setAuthor('Didier');
    $application2->setContent("Je m'en fou :x");
    $application2->setAdvert($advert);
    $em->persist($application2);
    
    // Création des skill
    $listSkills = $em
        ->getRepository('SMOPlatformBundle:Skill')
        ->findAll()
    ;
    // Pour chaque compétences
    foreach($listSkills as $skill)
    {
        $advertSkill = new AdvertSkill();
        $advertSkill->setAdvert($advert);
        $advertSkill->setSkill($skill);
        $advertSkill->setLevel('Expert');
        $em->persist($advertSkill);
    }
    
    
    // Enregistrement de cette nouvelle annonce
    $em->flush();
    
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirect($this->generateUrl('smo_platform_view', array('id' => $advert->getId())));
    }

    return $this->render('SMOPlatformBundle:Advert:add.html.twig');
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    
    // id de l'annonce
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->find($id)
    ;
    
    if( null === $advert )
    {
        throw new NotFoundHttpException("L'annonce id = '$id' n'existe pas.");
    }
    
    // FindAll() pour renvoyer toutes les catégories de la base de données
    $listCategories = $em
        ->getRepository('SMOPlatformBundle:Category')
        ->findAll()
    ;
    
    foreach($listCategories as $category)
    {
        $advert->addCategory($category);
    }
    
    $em->flush();
    
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirect($this->generateUrl('smo_platform_view', array('id' => 5)));
    }
    
    
    return $this->render('SMOPlatformBundle:Advert:edit.html.twig', array(
        'advert' => $advert
    ));
  }

  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    
    $advert = $em
        ->getRepository('SMOPlatformBundle:Advert')
        ->find($id)
    ;
    
    if(null === $advert)
    {
        throw new NotFoundHttpException("Annonce id = '$id' n'existe pas.");
    }
    
    foreach($advert->getCategories() as $category)
    {
        $advert->removeCategory($category);
    }
    
    $em->flush();
    
    return $this->render('SMOPlatformBundle:Advert:delete.html.twig');
  }
  
  public function menuAction($limit)
  {
    $listAdverts = array(
        array('id'=>2, 'title'=>'Recherche développeur symfony2'),
        array('id'=>3, 'title'=>'Mission de webmaster'),
        array('id'=>4, 'title'=>'Offre de stage webdesigner')
    );
    
    return $this->render("SMOPlatformBundle:Advert:menu.html.twig", array(
        'listAdverts'=>$listAdverts
        )
    );
  }
  
  private function getTableauAnnonces()
  {
    return array(
      array(
        'title'   => 'Recherche développpeur Symfony2',
        'id'      => 1,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );
  }
}