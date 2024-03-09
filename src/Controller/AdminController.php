<?php

namespace App\Controller;

use App\Entity\Post;
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
        $posts = $postRepository -> findBy([],['id' => 'desc']);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'posts' => $posts
        ]);
    }

    #[Route('/admin/add', name: 'app_admin_add', methods: ['GET'])]
    public function show():Response {
        return $this -> render('admin/store.html.twig');
    }

    #[Route('admin/edit', name: 'app_admin_edit_view', methods: ['GET'])]
    public function editView():Response {
        return $this -> render('admin/edit');
    }

    #[Route('/admin/addpost', name: 'app_admin_store', methods: ['POST', 'GET'])]
    public function store(Request $request, EntityManagerInterface $em) {
        $post = new Post();

        $this -> addOrEdit($post, $request);

        $em -> persist($post);
        $em -> flush();

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/editpost/{id}', name: 'app_admin_edit', methods: ['PUT', 'PATCH', 'GET'])]
    public function edit(int $id,Request $request, EntityManagerInterface $em, PostRepository $pr) {
        $post = $pr -> find($id);

        if(!$post) {
            return $this -> redirectToRoute('/admin');
        }

        $this -> addOrEdit($post, $request);

        $em -> persist($post);
        $em -> flush();

        return $this->redirectToRoute('app_admin');
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

    /**
     * @param Post $post
     * @param Request $request
     * @return void
     */
    private function addOrEdit(Post &$post, Request &$request):void {
        $title = $request -> get('title');
        $description = $request -> get('description');

        $post -> setTitle($title);
        $post -> setDescription($description);

        $date = new \DateTime('now');

        $post -> setDate($date -> format('d M Y'));

        /**
         * Uploaded Image
         * @type UploadedFile
         */
        $image_file = $request -> files -> get('image_file');
        $path = $this -> getParameter('kernel.project_dir')."/public/build/images/";
        $name = uniqid().$image_file -> getClientOriginalName();

        $image_file -> move($path,$name);

        $post -> setImage("build/images/".$name);
    }
}
