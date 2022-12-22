<?php

namespace App\Controller;

// ini_set('memory_limit', '-1');

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;

    public function __construct(EntityManagerInterface $em, MovieRepository $movieRepository)
    {
        $this->em = $em;
        $this->movieRepository = $movieRepository;
    }

    #[Route("/movies/", name: 'movies')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();
        return $this->render('movies/index.html.twig', ['movies' => $movies]);
    }


    #[Route("/", name: 'root')]
    public function root(): Response
    {
        return $this->redirectToRoute('movies');
    }



    #[Route("/movies/create", name: 'create_movie')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();
            $imagePath = $form->get('imagePath')->getData();


            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(

                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $newMovie->setImagePath('/uploads/' . $newFileName);
            }
            $this->em->persist($newMovie);
            $this->em->flush();

            return $this->redirectToRoute('movies');
        }

        return $this->render('movies/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/movies/edit/{id}', name: 'edit_movie')]
    public function edit($id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $oldImgPath = $movie->getImagePath();

        //get project dir
        $projectDir = $this->getParameter('kernel.project_dir');
        $oldImgFullPath = $projectDir . '/public/' . $oldImgPath;

        //delete file from internal filesystem
        if ($movie->getImagePath() !== null) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($oldImgFullPath);
        }

        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);


        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {

                if ($movie->getImagePath() !== null) {
                    //if the same image is already present inside root dir
                    if (file_exists($this->getParameter('kernel.project_dir') . $movie->getImagePath())) {
                        $this->getParameter('kernel.project_dir') . $movie->getImagePath();
                    }
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                    try {
                        $imagePath->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newFileName);
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $oldMovieImgPath = $movie->getImagePath();
                    $newMovieImgPath = $imagePath = $form->get('imagePath')->getData();


                    $movie->setImagePath('/uploads/' . $newFileName);
                    $this->em->flush();
                    return $this->redirectToRoute('movies');
                }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());

                $this->em->flush();
                return $this->redirectToRoute('movies');
            }
        }
        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/delete/{id}', methods: ['GET', 'DELETE'], name: "delete_movie")]
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);
        //we can use em to delete row
        $this->em->remove($movie);

        //we still need to flush
        $this->em->flush();

        return $this->redirectToRoute('movies');
    }


    #[Route('/movies/{id}', methods: ['GET'], name: 'show_movie')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);

        return $this->render('movies/show.html.twig', [
            'movie' => $movie
        ]);
    }
    /**
     * oldMethod
     *
     * @Route("/old", name="old")
     */
    public function oldMethod(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your old method!',
            'path' => 'src/Controller/MoviesController.php',
        ]);
    }
}
