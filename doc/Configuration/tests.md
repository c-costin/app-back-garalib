# Tests

## Installation

```bash
composer require --dev symfony/test-pack
```

## Configuration

Créer le `.env.test.local` et ajouter l'URL de votre BDD :

```properties
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=maraidb-10.3.*&charset=utf8mb4"
```

Création de la BDD :

```bash
php bin/console --env=test doctrine:database:create
```

Chargé les entités en BDD :

```bash
php bin/console --env=test doctrine:migrations:migrate -n
```

Vérification de la BDD : (Optionnel)

```bash
php bin/console --env=test doctrine:schema:create
```

Chargé des fixtures dans la BDD test : (Optionnel)

```bash
php bin/console --env=test doctrine:fixtures:load -n
```

## Création d'un test

Lancer la commande `php bin/console make:test` et suivait les demandes :

```bash
$ php bin/console make:test

 Which test type would you like?:
 > WebTestCase

 The name of the test class (e.g. BlogPostTest):
 > Controller\PostControllerTest
```

*Remarque :* Pour le nommage des fichiers, suivais le namespace déjà exsitant.

Exemple de test :

```php
// tests/Controller/PostControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', '/');

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
```

## Lancer des tests

```bash
php bin/phpunit
```
