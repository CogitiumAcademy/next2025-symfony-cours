<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/post', name: 'admin_post_')]
final class PostController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        //dd($categories);

        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/activate/{id}', name: 'activate')]
    public function activate(Post $post, ManagerRegistry $doctrine): Response
    {
        $post->setActive(($post->isActive()) ? false : true);
        $em = $doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute('admin_post_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Post $post, ManagerRegistry $doctrine, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('delete', $request->query->get('_csrf_token'));
        if ($csrfTokenManager->isTokenValid($token)) {
            $em = $doctrine->getManager();
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Article supprimé !');
        } else {
            $this->addFlash('danger', 'Token absent ou invalide !');
        }
        return $this->redirectToRoute('admin_post_index');
    }
}
