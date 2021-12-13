<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('App:Category')->findAll();

        if (count($categories) && !$request->get('category-id')) {
            return $this->redirect($this->generateUrl('list', array('category-id' => $categories[0]->getId())));
        }

        $sliders = $em->getRepository('App:Slider')->findActive($request->get('category-id'));

        return $this->render('list/index.html.twig', [
            'categoryId' => $request->get('category-id'),
            'sliders' => $sliders,
            'categories' => $categories,
            'currentUserId' => $this->get('security.token_storage')->getToken()->getUser()->getId()
        ]);
    }
}
