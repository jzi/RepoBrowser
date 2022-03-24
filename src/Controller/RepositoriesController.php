<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\CodeRepo;
use App\Repository\CodeRepoRepository;

class RepositoriesController extends AbstractController
{
    #[Route('/repositories', name: 'app_repositories')]
    public function index(CodeRepoRepository $ormRepository): Response
    {
        
        $repositories = $ormRepository->findAll();

        return $this->render('repositories/index.html.twig', [
            'controller_name' => 'RepositoriesController',
            'repositories' => $repositories,
        ]);
    }
}
