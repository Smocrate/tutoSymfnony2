<?php



namespace SMO\PlatformBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

Class ByebyeController extends Controller
{
	public function indexAction()
	{
		$content = $this->get("templating")->render('SMOPlatformBundle:Byebye:index.html.twig');
		return new Response($content);
	}
}