<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $nbUsers = $userRepository->nbUsers();
        $counts = $postRepository->nbAllSubjects();
        //dd($counts);
        return $this->render('admin/index.html.twig', [
            'counts' => $counts[0],
        ]);
    }    

}
