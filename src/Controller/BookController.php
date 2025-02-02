<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

final class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book_index', methods: ['GET'])]
    public function index(Request $request, BookRepository $repository): Response
    {

        $books = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findAllSortedByTitle()),
            $request->query->get('page', default: 1),
            maxPerPage: 1
        );
        // Rendu de la vue avec les livres paginés
        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }


    #[Route('/{id}', name: 'app_book_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Book $book): Response
    {

        return $this->render('book/show.html.twig', [

            'book' => $book,

        ]);
    }
}
