<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
     * @Route("/films", name="film_")
     */

class FilmController extends AbstractController
{

    private $filmRepository;
    private $serializer;

    public function __construct(FilmRepository $filmRepository, SerializerInterface $serializer)
    {
        $this->filmRepository = $filmRepository;
        $this->serializer = $serializer;
    }
    /**
     * @Route("", name="list", methods={"GET"})
     */
    public function getAll(): Response
    {
       
        $films = $this->filmRepository->findAll();
        $json = $this->serialize($films, 'json');
       
        return new Response($json,
         200,
        ['Content-Type'=> 'application/json']
        );
    }

     /**
     * @Route("/{film}", name="get_one", methods={"GET"})
     */
    public function getOne (Film $film){

        $film = $this->filmRepository->find($film);

        if(is_null($film)){
            $response = ['error' => 'Ce vehicul existe pas'];
            return new JsonResponse($response, 404,
            ['Content-Type'=> 'application/json'] );
        }

        return new Response($this->serializer->serialize($film, 'json'), 200,
        ['Content-Type'=> 'application/json'] );
      
    }

    /**
     * @Route("/{film}", name="delete", methods={"DELETE"})
     */
    public function delete (Film $film){
        try {
            $this->em->remove($film);
            $this->em->flush();
            return $this->json(['succes'=> true]);
        } catch (\Exception $e){
            return $this->json($e, 500);
        }
        
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */

    public function add(Request $request){
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(FilmType::class, new Film());
        $form->submit($data);
        if($form->isSubmitted() and $form->isValid()){
            $form = $form->getData();
            $this->em->persist($form);
            $this->em->flush();
            return $this->json($form, 201);
        } else {
            return $this->json($form->getErrors(true), 400);
        }
    }  
    
    /**
     * @Route("/{film}", name= "update", methods={"PUT"})
     */
    
    public function update(Film $marque, Request $request){
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(FilmType::class, $film);
        $form->submit($data);

        if($form->isSubmitted() and $form->isValid()){
            $form = $form->getData();
            $this->em->flush();
            return $this->json($form, 202);
        } else {
            return $this->json($form->getErrors(true), 400);
        }

    }
}
