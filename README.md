# Common

Common objects for general usage in PHP projects, including Data Transfer Objects (DTOs), Value Objects, and HTTP Request wrappers.

## Installation

```bash
composer require phprise/common
```

## Usage

### TransferObject (DTO)

The `TransferObject` class helps you map arrays to objects with type safety, recursion, and convenient serialization methods.

#### Basic Usage

```php
use Phprise\Common\DTO\TransferObject;

class User extends TransferObject
{
    // Public properties are automatically mapped
    public string $name;
    public int $age;

    // You can also use DateTimeImmutable, it will be automatically converted from ISO string
    public \DateTimeImmutable $createdAt;
}

$user = User::fromArray([
    'name' => 'John Doe',
    'age' => 30,
    'created_at' => '2023-10-27T10:00:00+00:00', // Snake case keys are automatically converted to camelCase properties
]);

echo $user->name; // John Doe
echo $user->createdAt->format('Y-m-d'); // 2023-10-27
```

#### Serialization

```php
// Convert back to array
$array = $user->toArray();

// Convert to JSON
$json = $user->toJson();

// Convert to Snake Case array (useful for database or API responses)
$snakeArray = $user->toSnakeCaseArray(); // ['name' => 'John Doe', 'created_at' => ...]
```

#### Nested Objects

`TransferObject` supports nested DTOs.

```php
class Address extends TransferObject
{
    public string $city;
    public string $country;
}

class UserProfile extends TransferObject
{
    public string $username;
    public Address $address; // Type hint the nested DTO
}

$profile = UserProfile::fromArray([
    'username' => 'jdoe',
    'address' => [
        'city' => 'New York',
        'country' => 'USA'
    ]
]);

echo $profile->address->city; // New York
```

### TransferObjectCollection

A collection wrapper for `TransferObject`s, implementing `Doctrine\Common\Collections\Collection`.

```php
use Phprise\Common\DTO\TransferObjectCollection;

class UserCollection extends TransferObjectCollection
{
    // Optional: Override createFrom if you need custom instantiation logic
}

$collection = UserCollection::fromArray([
    new User(['name' => 'Alice']),
    new User(['name' => 'Bob']),
]);

// Or if you want to initialize from raw arrays, you might handle that in your collection or manually map
// Standard behavior expects elements to be passed to constructor

// Filtering
$filtered = $collection->filter(fn($user) => $user->name === 'Alice');
echo $filtered->count(); // 1
```

### Value Objects

#### StringObject

A wrapper for string manipulation.

```php
use Phprise\Common\ValueObject\StringObject;

$str = new StringObject('hello_world');

echo $str->toCamel();   // helloWorld
echo $str->toPascal();  // HelloWorld
echo $str->toKebab();   // hello-world
echo $str->toTitle();   // Hello World
echo $str->toUpper();   // HELLO_WORLD
```

#### ArrayObject

An extension of the native `ArrayObject` with extra utilities.

```php
use Phprise\Common\ValueObject\ArrayObject;

$arr = new ArrayObject(['old_key' => 'value']);

// Replace a key while keeping the value
$arr->replaceKey('old_key', 'new_key');

echo $arr['new_key']; // value
```

### Requests

Abstract classes to wrap Guzzle PSR-7 Requests for common HTTP methods. Useful for building API clients.

```php
use Phprise\Common\Request\StoreRequest;
use Phprise\Common\Request\UpdateRequest;

class CreateUserRequest extends StoreRequest
{
    public function __construct(array $userData)
    {
        parent::__construct(
            '/api/users',
            json_encode($userData),
            ['Content-Type' => 'application/json']
        );
    }
}

class UpdateUserRequest extends UpdateRequest
{
    public function __construct(int $id, array $userData)
    {
        parent::__construct(
            "/api/users/{$id}",
            json_encode($userData),
            ['Content-Type' => 'application/json']
        );
    }
}

// Usage
$request = new CreateUserRequest(['name' => 'John']);
// $request is now a PSR-7 Request object ready to be sent with a matching client
```

Available Request classes:
- `StoreRequest` (POST)
- `UpdateRequest` (PATCH)
- `ReplaceRequest` (PUT)
- `DestroyRequest` (DELETE)
- `ShowRequest` (GET)

---

# Otaku - The Manifesto of Fluid Structure

## Synthesis of the Philosophy
1. Write programs that communicate via clear contracts.
2. Compose them like Unix tools.
3. Be strict with yourself. Restrict your structure to guarantee readability.
4. Protect the heart of the business.
5. Speak the user's language.
6. Let infrastructure be just a silent detail.

## The Principle of Modularity (Unix & Clean Architecture)
**The Rule of Composition**: Just like in Unix, each component should do one thing and do it well. In software design, this translates to concentric layers where dependencies point only inwards.

**Data Pipes**: Treat the application flow as a pipeline. Input enters through an adapter (Controller), traverses the Use Case (Core), and is persisted or transformed, keeping business logic isolated from external side effects.

## The Purity of Form (Object Calisthenics)
**Restriction as Liberation**: Use Object Calisthenics rules (like only one level of indentation and small classes) to force domain decomposition.

**Total Encapsulation**: If an object needs a behavior, it must own it. Do not expose internal state; ask the object to perform the action.

## The Language as Foundation (DDD)
**Code is the Map**: The design must reflect the domain, not the database. Use Ubiquitous Language so that code is readable by both developers and business experts.

**Bounded Frontiers**: Define your Bounded Contexts clearly. What is a "User" in the Authentication context is not the same "User" in the Billing context.

## The Single Truth of the Contract (OpenAPI)
**Design-First**: The contract (OpenAPI) is the specification of truth. Before coding, define the interface. This allows frontend and backend development to happen in parallel, guided by an immutable technical promise.

**Living Documentation**: The specification is not a static document, but the functional skeleton of communication between systems.

## Invisible Persistence (Doctrine)
**Entities, not Tables**: Use Doctrine (or Data Mapper patterns) to treat persistence as an infrastructure detail. Your domain entities should be Plain Old PHP Objects (POPOs), ignorant of how they are saved.

**The Repository as a Collection**: Treat data access as an in-memory collection, abstracting SQL complexity to keep focus on domain logic.