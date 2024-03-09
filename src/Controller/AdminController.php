<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository -> findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'posts' => $posts
        ]);
    }

    #[Route('/admin/add', name: 'app_admin_add', methods: ['GET'])]
    public function show():Response {
        return $this -> render('admin/store.html.twig');
    }

    #[Route('/admin/addpost', name: 'app_admin_store', methods: ['POST', 'GET'])]
    public function store(Request $request) {
        $title = $request -> get('title');
        $description = $request -> get('description');

        /**
         * Uploaded Image
         * @type UploadedFile
         */
        $image_file = $request -> files -> get('image_file');

        $image_file -> move(
            $this -> getParameter('kernel.project_dir')."/public/build/images/",
            uniqid().$image_file -> getClientOriginalName()
        );

        dd($image_file);
    }

    #[Route('/admin/post/delete/{id}', name: 'app_admin_post_delete', methods: ['DELETE', 'GET'])]
    public function destroy(int $id, PostRepository $pr, EntityManagerInterface $em, CommentRepository $cr):Response {
        $post = $pr -> find($id);

        if($post -> getId() !== null) {
            $comments = $cr -> findBy(['post' => $post]);

            foreach($comments as $comment) {
                $post -> removeComment($comment);
            }

            $em -> remove($post);
            $em -> flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}
