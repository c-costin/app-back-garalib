# Suppression en cascade des entités avec `Doctrine`

## Activation de la contrainte `ON DELETE` en BDD sur les `Foreign keys`

Sur chaque relation entre entité mettre l'option `onDelete="CASCADE"` sur le côté du `Owining side`

```php
@ORM\JoinColumn(onDelete="CASCADE")
```

## Les Méthodes de suppressions

### `cascade={"remove"}`

### `orphanRemoval=true`

## Méthode alternative

### Suppresion en cascade depuis le contrôleur

Exemple de suppression d'un utilisateur de son adresse :

```php
// Check if user has an address
if ($user->getAddress() !== null) {
    // Find User Address
    $userAddress = $addressRepository->find($user->getAddress());
    // Remove User Adress into database
    $addressRepository->remove($userAddress, true);
}

// Remove User into database
$userRepository->remove($user, true);
```
