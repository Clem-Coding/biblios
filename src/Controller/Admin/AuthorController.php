<?php


//Un espace de noms est utilisé en PHP pour organiser le code et éviter les conflits entre des classes portant 
//le même nom dans différents endroits du projet.
namespace App\Controller\Admin;

// Pourquoi use?
// PHP utilise l'autoloading, grâce au standard PSR-4 (le plus couramment utilisé dans Symfony). 
//Cela signifie que les fichiers de classes sont automatiquement inclus lorsque vous utilisez leurs namespaces. 
//On n'a donc pas besoin de manuellement inclure un fichier avec require ou include.


//utilise l'entité Author
use App\Entity\Author;

//utilise l'entité AuthorType qui correspond au formulaire associé
use App\Form\AuthorType;

// Import de l'interface EntityManagerInterface de Doctrine pour gérer les entités et les opérations sur la base de données.
use Doctrine\ORM\EntityManagerInterface;


//Elle permet d'utiliser la classe AbstractController de Symfony dans votre fichier. AbstractController est une classe 
//de base fournie par Symfony qui simplifie la création de contrôleurs. 
//Elle contient des méthodes et fonctionnalités préconfigurées qu'on peut utiliser dans notre controller'.
// exemples : render(),redirectToRoute(),createForm()...
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



// Elle permet d'importer la classe Request de Symfony, qui représente la requête HTTP envoyée au serveur. 
// Cette classe contient toutes les informations relatives à la requête, telles que :
//     Les données envoyées via un formulaire (POST).
//     Les paramètres d'URL (GET).
//     Les en-têtes HTTP (headers).
//     Les cookies, fichiers, et bien plus.

use Symfony\Component\HttpFoundation\Request;


// Elle permet d'importer la classe Response de Symfony, qui représente la réponse HTTP que le serveur retourne au client (navigateur, API, etc.).
// Une Response contient :
//     Le contenu (HTML, JSON, texte brut, etc.) à envoyer au client.
//      Le code de statut HTTP (par exemple, 200 OK, 404 Not Found, etc.).
//      Les en-têtes HTTP (par exemple, Content-Type, Cache-Control, etc.).
use Symfony\Component\HttpFoundation\Response;




// Permet d'importer la classe Route de Symfony, qui est utilisée pour définir des routes directement 
//au-dessus des méthodes de votre contrôleur via des annotations (ou plus précisément des attributs PHP)
use Symfony\Component\Routing\Attribute\Route;



// Importe la classe AuthorRepository pour accéder aux données des auteurs
use App\Repository\AuthorRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//déclare une route pour le contrôleur AuthorController dans Symfony.
//Cette route spécifie que les requêtes adressées à /admin/author (comme dans un navigateur ou via un appel API) seront gérées par cette classe.
#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED')]
    //déclare la route pour la méthode index() qui va render le template index.html.twig
    #[Route('', name: 'app_admin_author_index', methods: ['GET'])]
    public function index(Request $request, AuthorRepository $repository): Response
    {

        $dates = [];

        if ($request->query->has('start')) {

            $dates['start'] = $request->query->get('start');
        }



        if ($request->query->has('end')) {

            $dates['end'] = $request->query->get('end');
        }





        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->findByDateOfBirth()),
            $request->query->get('page', default: 1),
            maxPerPage: 10
        );

        return $this->render('admin/author/index.html.twig', [

            //'controller_name' => 'AuthorController' : Ici, la clé 'controller_name' est associée à la valeur 'AuthorController'.
            // Cela signifie que dans la vue Twig index.html.twig, on peut accéder à une variable appelée controller_name contenant la chaîne 'AuthorController'.
            //permet par exemple de :
            //  Afficher dynamiquement le nom du contrôleur dans le template.
            //   Adapter l'affichage en fonction du contrôleur (par exemple, modifier le titre de la page ou afficher des informations spécifiques).
            //  Avoir un outil de débogage pour savoir quel contrôleur est responsable du rendu.
            //   Personnaliser le comportement ou l'affichage de la page selon le contrôleur actif.
            'controller_name' => 'AuthorController',
            'authors' => $authors,
        ]);
    }

    #[IsGranted('ROLE_AJOUT_DE_LIVRE')]
    //déclare la route pour la méthode new() en lui appliquant les méthodes GET et POST(il s'agit du template de formulaire)
    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]



    // Déclare la méthode new qui gère la création d'un nouvel auteur. Elle prend l'objet Request pour récupérer les données du formulaire.
    public function new(?Author $author, Request $request, EntityManagerInterface $manager): Response
    {


        if ($author) {
            $this->denyAccessUnlessGranted('ROLE_EDITION_DE_LIVRE');
        }
        //Crée une nouvelle instance de l'entité Author pour représenter l'auteur qui sera ajouté.
        $author = new Author();  // Crée un nouvel auteur

        //Crée le formulaire à partir de la classe AuthorType et l'associe à l'objet $author (createForm() vient de l'AbstractController)
        $form = $this->createForm(AuthorType::class, $author);

        //Gère la requête HTTP pour remplir le formulaire avec les données envoyées par l'utilisateur.
        //Lorsque qu'on utilise la méthode createForm(), Symfony crée un objet de type Form, et c'est sur cet objet qu'on appelle handleRequest().
        $form->handleRequest($request);

        //Vérifie si le formulaire a été soumis et est valide.
        //méthodes isSubmitted() et isValid() appartiennent à Form
        if ($form->isSubmitted() && $form->isValid()) {





            // Les méthodes persist() et flush() viennent de l'instance du gestionnaire d'entités (EntityManager) de Doctrine, 
            // Prépare l'objet $author pour être sauvegardé dans la base de données.
            $manager->persist($author);

            //$manager->flush(); : Effectue la sauvegarde réelle des objets persistés dans la base de données.
            $manager->flush();

            return $this->redirectToRoute(route: 'app_admin_author_index');
        }
        return $this->render('admin/author/new.html.twig', [
            'form' => $form->createView(),  // Passe le formulaire à la vue
        ]);
    }


    //ici on indique dans Route que le chemin contiendra l'id passsé en paramètre comme une variable
    //le name:Nom de la route, utilisé pour les générateurs d'URL.
    //requirements : on indique que le pramètre à passer en id doit être un ou plusieurs chiffres (cf regex)
    //methods : GET parce qu'il s'agit uniquement de l'affichage
    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {

        return $this->render('admin/author/show.html.twig', [

            'author' => $author,

        ]);
    }


    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Author $author, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AuthorType::class, $author);  // Crée le formulaire avec les données existantes de l'auteur
        $form->handleRequest($request);  // Traite la soumission du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, effectue la mise à jour de l'auteur
            $manager->flush();

            // Redirige vers la page d'affichage de l'auteur modifié
            return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
        }

        return $this->render('admin/author/edit.html.twig', [
            'form' => $form->createView(),  // Passe le formulaire à la vue
            'author' => $author,  // Passe l'auteur à la vue pour l'édition
        ]);
    }
}
