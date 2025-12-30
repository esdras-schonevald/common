# common
Common objects to general usage

## Installation

```bash
composer require phprise/common
```

## Usage

Creating a TransferObject
```php
use Phprise\Common\DTO\TransferObject;

class User extends TransferObject
{
    public string $name;
    public int $age;
}

$user = User::fromArray([
    'name' => 'John Doe',
    'age' => 30,
]);

echo $user->toJson();
```

Creating a TransferObjectCollection
```php
use Phprise\Common\DTO\TransferObjectCollection;

class UserCollection extends TransferObjectCollection
{
    protected function createFrom(array $elements): static
    {
        return new static($elements);
    }
}

$userCollection = UserCollection::fromArray([
    'name' => 'John Doe',
    'age' => 30,
]);

echo $userCollection->toJson();
```

Creating a Store Request
```php
use Phprise\Common\Request\StoreRequest;

// example extending StoreRequest class
class UserStoreRequest extends StoreRequest
{
    public function __construct(User $user)
    {
        parent::__construct(
            '/api/users',
            ['content-type' => 'application/json'],
            $user->toJson()
        );
    }
}
// creating a DTO user
$user = User::fromArray([
    'name' => 'John Doe',
    'age' => 30,
]);

// creating a StoreRequest with a DTO user
$userStoreRequest = new UserStoreRequest($user);
```