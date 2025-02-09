<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class LastConnectionListener
{
    private LoggerInterface $logger;

    // Injection du logger via le constructeur
    public function __construct(private readonly EntityManagerInterface $manager, LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[AsEventListener(event: 'security.interactive_login')]
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // Récupération de l'utilisateur connecté
        $user = $event->getAuthenticationToken()->getUser();

        // Vérifie si l'utilisateur est bien une instance de User
        if ($user instanceof User) {
            // Log de la connexion de l'utilisateur
            $this->logger->info('Utilisateur connecté : ' . $user->getUsername());

            // Mise à jour de la dernière connexion
            $user->setLastConnectedAt(new \DateTimeImmutable());
            $this->manager->flush();

            // Log de la mise à jour de la date de dernière connexion
            $this->logger->info('Dernière connexion mise à jour pour l\'utilisateur : ' . $user->getUsername());
        } else {
            // Log si l'utilisateur n'est pas une instance de User
            $this->logger->warning('Tentative de connexion d\'un utilisateur non valide.');
        }
    }
}
