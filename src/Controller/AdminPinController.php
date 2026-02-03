<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/pins')]
#[IsGranted('ROLE_ADMIN')]
class AdminPinController extends AbstractController
{
    #[Route('/', name: 'admin_pins_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $pins = $em->getRepository(Posts::class)->findAll();
        return $this->render('admin/pins/index.html.twig', [
            'pins' => $pins,
        ]);
    }

    #[Route('/new', name: 'admin_pins_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Posts();
        $form = $this->createForm(PostsFormType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('public_dir').'/img',
                    $newFilename
                );
                $pin->setURL('img/' . $newFilename);
            }
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute('admin_pins_index');
        }
        return $this->render('admin/pins/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_pins_edit')]
    public function edit(Request $request, Posts $pin, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PostsFormType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('public_dir').'/img',
                    $newFilename
                );
                $pin->setURL('img/' . $newFilename);
            }
            $em->flush();
            return $this->redirectToRoute('admin_pins_index');
        }
        return $this->render('admin/pins/edit.html.twig', [
            'form' => $form->createView(),
            'pin' => $pin,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_pins_delete', methods: ['POST'])]
    public function delete(Request $request, Posts $pin, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pin->getId(), $request->request->get('_token'))) {
            $em->remove($pin);
            $em->flush();
        }
        return $this->redirectToRoute('admin_pins_index');
    }
} 