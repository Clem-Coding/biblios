<?php

// Déclare l'espace de nom du fichier, ici on déclare que notre listener fait partie du namespace 'App\EventListener'.
namespace App\EventListener;

// Importation de la classe 'AsEventListener' pour pouvoir l'utiliser comme un attribut de l'EventListener.
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

// Importation de la classe 'RequestEvent' qui représente l'événement du kernel qui contient des informations sur la requête HTTP.
use Symfony\Component\HttpKernel\Event\RequestEvent;

// Importation de la classe 'KernelEvents' qui contient les événements que Symfony génère pendant le traitement de la requête (ici, l'événement 'REQUEST').
use Symfony\Component\HttpKernel\KernelEvents;

// Importation de la classe 'Response' qui représente une réponse HTTP envoyée au client (ici utilisée pour renvoyer la page de maintenance).
use Symfony\Component\HttpFoundation\Response;

// Importation de la classe 'Environment' qui représente l'environnement Twig utilisé pour rendre des templates.
use Twig\Environment;

final class MaintenanceListener
{
    // Déclaration d'une constante qui va définir si le site est en mode maintenance ou non. 'false' veut dire que le site n'est pas en maintenance.
    public const IS_MAINTENANCE = false;

    // Le constructeur injecte une instance de 'Environment' (Twig) dans la classe pour pouvoir rendre des templates.
    public function __construct(private readonly Environment $twig) {}  // 'private readonly' assure que la propriété $twig est disponible uniquement dans la classe.

    // L'attribut 'AsEventListener' indique que cette méthode écoute l'événement 'REQUEST' du kernel avec une priorité de 2000.
    // Cela signifie que ce listener s'exécutera lors du traitement de la requête, avant d'autres événements avec une priorité inférieure.
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 2000)]
    public function onKernelRequest(RequestEvent $event): void
    {
        // Vérifie si le site est en mode maintenance. Si 'IS_MAINTENANCE' est 'true', alors on continue avec la logique de maintenance.
        if (self::IS_MAINTENANCE) {

            // Si le site est en maintenance, on crée une réponse HTTP contenant le rendu du template Twig 'maintenance.html.twig'.
            // Cette réponse sera envoyée au client à la place de la page normalement demandée.
            $response = new Response($this->twig->render('maintenance.html.twig'));

            // L'événement 'RequestEvent' permet de définir la réponse qui sera envoyée. Ici, on remplace la réponse par la page de maintenance.
            $event->setResponse($response);
        }
    }
}
