<?php

namespace App\Controller;

use App\Entity\Slider;
use App\Form\SliderForm;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class SliderController extends AbstractController
{
    /**
     * @Route("/slider", name="create_slider")
     */
    public function create(Request $request)
    {
        $slider = new Slider;

        $form = $this->createForm(SliderForm::class, $slider);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sliderFile = $form->get('image')->getData();

            if ($sliderFile) {
                $newFilename = 'img-'.uniqid().'.'.$sliderFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $sliderFile->move(
                        $this->getParameter('slides_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $slider->setImage($newFilename);
            }
            $em = $this->getDoctrine()->getManager();

            $userId = $this->get('security.token_storage')->getToken()->getUser()->getId() ?? null;

            $slider->setCreatedBy($em->getRepository('App:User')->find($userId));
            
            // Save
            $em->persist($slider);
            $em->flush();
            
            return $this->redirectToRoute('list');
        }

        return $this->render('slider/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/slider-image", name="get_slider_image")
     */
    public function getSlider(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('App:Slider')->findOneBy(['image' => $request->get('image')]);

        if (!$slider) {
          $slider = $em->getRepository('App:Slider')->find(0);
        }

        $filepath = $this->getParameter('slides_dir') . $slider->getImage();

        if (!is_file($filepath)) {
            $filepath = $this->getParameter('slides_dir') . 'no-image.png';
        }

        $imageParts = explode('.', $slider->getImage());
        $extension = end($imageParts);

        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $slider->getImage());
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/' . $extension);
        $response->setContent(file_get_contents($filepath));

        return $response;
    }

    /**
     * @Route("/delete-slider-image", name="delete_slider_image")
     */
    public function deleteSlider(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('App:Slider')->findOneBy(['id' => $request->get('id')]);

        if (!$slider) {
          throw $this->createNotFoundException('The slide does not exist');
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($currentUser->getId() == $slider->getCreatedBy()->getId() || $currentUser->hasRole('ROLE_ADMIN')) {
          $filepath = $this->getParameter('slides_dir') . $slider->getImage();

          if (is_file($filepath)) {
            unlink($filepath);
          }

          $em = $this->getDoctrine()->getManager();
          $em->remove($slider);
          $em->flush();

          return $this->redirectToRoute('list');
        } else {
          throw $this->createAccessDeniedException('You have not access to delete current slide');
        }
    }
}
