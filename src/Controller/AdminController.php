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
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function show(Request $request):Response {
        $errors = $request -> query -> get('errors');
        return $this -> render('admin/store.html.twig',[
            'errors' => json_decode($errors) ?? []
        ]);
    }

    #[Route('admin/edit/{id}', name: 'app_admin_edit_view', methods: ['GET'])]
    public function editView($id, PostRepository $pr, Request $request):Response {
        $post = $pr -> find($id);
        $errors = $request -> query -> get('errors');

        if(!$post) {
            return $this -> redirectToRoute('app_admin');
        }

        return $this -> render('admin/edit.html.twig', [
            'id' => $id,
            'post' => $post,
            'errors' => json_decode($errors) ?? []
        ]);
    }

    #[Route('/admin/addpost', name: 'app_admin_store', methods: ['POST', 'GET'])]
    public function store(Request $request, EntityManagerInterface $em, ValidatorInterface $validator) {
        $post = new Post();

        $this -> addOrEdit($post, $request);

        $errors = $validator -> validate($post);
        $messages = array();

        foreach($errors as $error) {
            array_push($messages,$error -> getMessage());
        }

        if(count($errors)) {
            return $this->redirect('/admin/add/?errors='.json_encode($messages));
        }

        $em -> persist($post);
        $em -> flush();

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/editpost/{id}', name: 'app_admin_edit', methods: ['PUT', 'POST', 'GET'])]
    public function edit($id,Request $request, EntityManagerInterface $em, PostRepository $pr, ValidatorInterface $validator) {
        $post = $pr -> find($id);

        if(!$post) {
            return $this -> redirectToRoute('app_admin');
        }

        $this -> addOrEdit($post, $request);

        $errors = $validator -> validate($post);
        $messages = array();

        foreach($errors as $error) {
            array_push($messages,$error -> getMessage());
        }

        if(count($errors)) {
            return $this->redirect('/admin/edit/'.$id.'?errors='.json_encode($messages));
        }

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

        if(!$request -> files -> get('image_file')) {
            $post -> setImage(null);
            return;
        }

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
