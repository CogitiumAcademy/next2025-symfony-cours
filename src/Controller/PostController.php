<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PostController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PostRepository $postRepository): Response
    {
        //$posts = $postRepository->findAll();
        $count = $postRepository->count();
        //dd($count);
        $posts = $postRepository->findLastPosts();
        $oldPosts = $postRepository->findOldPosts(3);
        //dd($oldPosts);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'oldPosts' => $oldPosts,
        ]);
    }

    //#[IsGranted('ROLE_USER')]
    #[Route('/post/add', name: 'post_add')]
    public function addPost(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());
            $post->setActive(false);
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();
            //dd($post);
            return $this->redirectToRoute('home');
        }

        return $this->render('post/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*
    #[Route('/post/{id}', name: 'post_view')]
    //#[Route('/post/{slug}', name: 'post_view')]
    public function view(Post $post): Response
    {
        //dd($post);
        return $this->render('post/post.html.twig', [
            'post' => $post
        ]);
    }
        */

    #[Route('/post/{slug}', name: 'post_view')]
    public function view(PostRepository $postRepository, $slug): Response
    {
        //dd($post);
        $post = $postRepository->findOneBy(['slug' => $slug]);
        return $this->render('post/post.html.twig', [
            'post' => $post
        ]);
    }

}
