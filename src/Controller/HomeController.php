<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PostsFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Board;
use App\Form\BoardFormType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Posts::class)->findAll();
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'post_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Posts();
        $form = $this->createForm(PostsFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('public_dir').'/img',
                    $newFilename
                );
                $post->setURL('img/' . $newFilename);
            }
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/boards', name: 'user_boards')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function boards(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $boards = $em->getRepository(Board::class)->findBy(['user' => $user]);
        return $this->render('board/index.html.twig', [
            'boards' => $boards,
        ]);
    }

    #[Route('/board/new', name: 'board_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function newBoard(Request $request, EntityManagerInterface $em): Response
    {
        $board = new Board();
        $form = $this->createForm(BoardFormType::class, $board);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $board->setUser($this->getUser());
            $em->persist($board);
            $em->flush();
            return $this->redirectToRoute('user_boards');
        }
        return $this->render('board/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
