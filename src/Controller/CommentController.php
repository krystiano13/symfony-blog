<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{

    #[Route('/sendComment', name: 'comment_send', methods: ['POST'])]
    public function store(Request $request, PostRepository $postRepository, EntityManagerInterface $em):Response {
        $text = $request -> get('text');
        $username = $request -> get('username');
        $post_id = $request -> get('post_id');

        $route = $request -> headers -> get('referrer');

        $comment = new Comment();
        $post = $postRepository -> find($post_id);

        $comment
            -> setText($text)
            -> setDate(new \DateTime('now'))
            -> setPost($post)
            -> setUsername($username);

        $em -> persist($comment);
        $em -> flush();

        return $this -> redirect('/blog/'.$post_id);
    }
}
