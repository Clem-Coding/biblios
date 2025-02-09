<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Injection du service UserPasswordHasherInterface.
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * Définit les valeurs par défaut pour les utilisateurs.
     */
    protected function defaults(): array|callable
    {
        // Le même mot de passe sera utilisé pour tous les utilisateurs
        $plainPassword = 'password123'; // Mot de passe fixe pour tous les utilisateurs
        $hashedPassword = $this->passwordHasher->hashPassword(new User(), $plainPassword);

        return [
            'email' => self::faker()->unique()->email(),
            'firstname' => self::faker()->firstName,
            'lastname' => self::faker()->lastName,
            'password' => $hashedPassword,
            'roles' => [self::faker()->randomElement(['ROLE_AJOUT_DE_LIVRE', 'ROLE_EDITION_DE_LIVRE', 'ROLE_ADMIN'])],
        ];
    }

    /**
     * Initialisation de la factory (si nécessaire).
     */
    protected function initialize(): static
    {
        return $this;
    }
}
