<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentController extends AbstractController
{

    #[Route('/sendComment', name: 'comment_send', methods: ['POST'])]
    public function store(
        Request $request,
        PostRepository $postRepository,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
    ):Response
    {
        $text = $request -> get('text');
        $username = $request -> get('username');
        $post_id = $request -> get('post_id');

        $comment = new Comment();
        $post = $postRepository -> find($post_id);

        $comment
            -> setText($text)
            -> setDate(new \DateTime('now'))
            -> setPost($post)
            -> setUsername($username);

        $errors = $validator -> validate($comment);
        $messages = array();

        foreach($errors as $error) {
            array_push($messages,$error -> getMessage());
        }

        if(!count($errors)) {
            $em -> persist($comment);
            $em -> flush();
        }

        return $this -> redirect('/blog/'.$post_id.'?errors='.json_encode($messages));
    }
}
