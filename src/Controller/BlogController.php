<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository -> findBy([], ['id' => 'desc']);
        return $this->render('blog/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/blog/{id}', name: 'show_blog')]
    public function show(int $id, PostRepository $postRepository, Request $request): Response {
        $post = $postRepository -> find($id);
        $errors = $request -> query -> get('errors');
        
        if(!isset($post)) {
            return $this->redirectToRoute('app_blog');
        }

        $comments = $post -> getComments();

        return $this -> render('blog/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'errors' => json_decode($errors) ?? []
        ]);
    }
}
