<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository -> findAll();
        return $this->render('blog/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/blog/{id}', name: 'show_blog')]
    public function show(int $id, PostRepository $postRepository): Response {
        $post = $postRepository -> find($id);

        if(!isset($post)) {
            return $this->redirectToRoute('app_blog');
        }

        return $this -> render('blog/show.html.twig', [
            'post' => $post
        ]);
    }
}
