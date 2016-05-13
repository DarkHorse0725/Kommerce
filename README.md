Zen Kommerce Core
=================
[![Test Coverage](http://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://codeclimate.com/github/inklabs/kommerce-core)
[![Build Status](https://travis-ci.org/inklabs/kommerce-core.svg?branch=master)](https://travis-ci.org/inklabs/kommerce-core)
[![Downloads](https://img.shields.io/packagist/dt/inklabs/kommerce-core.svg)](https://packagist.org/packages/inklabs/kommerce-core)
[![Apache License 2.0](https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg)](https://github.com/inklabs/kommerce-core/blob/master/LICENSE.txt)

## Introduction

Zen Kommerce is a PHP shopping cart system written with SOLID design principles.
It is PSR compatible, dependency free, and contains 100% code coverage using TDD practices.

All code (including tests) conform to the PSR-2 coding standards. The namespace and autoloader
are using the PSR-4 standard.

## Description

This project is over 42,000 lines of code. Unit tests account for 30-40% of that total and execute in
under 10 seconds. The repository tests use an in-memory SQLite database.

## Design Patterns

* [Design Patterns used in this project](docs/DesignPatterns).

## Architecture

![Flow of Control](https://i.imgur.com/ZkPtmsA.png)

* <a name="action"></a>Action
    - These are the use cases into the application. Command Actions are passed a Command object containing the required
      data payload. There is nothing returned from the dispatch() method. Only exceptions are thrown if the
      Command/Transaction is invalid.
      
      ```php
      $productDTO->sku = 'NEW-SKU';
      $this->dispatch(new EditProductCommand($productDTO));
      ```
      
    - Query Actions are passed a Request and Response object. There is nothing returned from the dispatchQuery()
      method. The return value is attached to the Response as defined in the ResponseInterface
      (GetProductResponseInterface in this case). The main application can implement this or use/extend the
      provided one. 

      ```php
      $request = new GetProductRequest(1);
      $response = new GetProductResponse;
      $this->dispatchQuery($request, $response);

      $productDTO = $response->getProductDTO();
      echo $productDTO->sku;
      echo $productDTO->price->unitPrice;
      echo $productDTO->tags[0]->name;
      ```

    - Attaching the return value to the Response is needed to maintain type hints in your IDE. Using the
      command/query bus allows for decoupling the main application from the Use Case handler implementation.
      The main application only needs to know about these objects from the above examples:
      
      - CommandInterface (EditProductCommand)
      - RequestInterface (GetProductRequest)
      - ResponseInterface (GetProductResponseInterface)

* <a name="domain-event"></a>Domain Event
    - Domain Events can be raised in the Entity layer and are dispatched in the service layer.

    ```php
    // UserEntity:
    public function setPassword($password)
    {
        $this->passwordHash = // hash the password...

        $this->raise(
            new PasswordChangedEvent(
                $this->id,
                $this->email,
                $this->getFullName()
            )
        );
    }
    ```

    ```php
    // UserService:
    $user = $this->userRepository->findOneById($userId);
    $user->setPassword($password);
    $this->userRepository->update($user);

    $this->eventDispatcher->dispatch($user->releaseEvents());
    ```

    - Events can be dispatched directly in the service layer. This is usually done for Entity creation
      when you do not yet have access to the id generated by the repository.
    
    ```php
    // CartService:
    $order = Order::fromCart($cart);
    $this->orderRepository->create($order);

    $this->eventDispatcher->dispatchEvent(
        new OrderCreatedFromCartEvent($order->getId())
    );
    ```

* <a name="service"></a>Service
    - These are the domain services to manage persisting domain state to the database through repositories. They contain
      behavior related to multiple Entities and any business logic that does not fit any specific Entity.

      ```php
      $productService = new Service\Product(
          $this->productRepository,
          $this->tagRepository
      );

      $productId = 1;
      $product = $productService->findOneById($productId);
      $product->setSku('NEW-SKU');

      $productService->edit($product);
      ```

* <a name="entity"></a>Entity
    - These are plain old PHP objects. You will not find any ORM code or external dependencies here. This is where
      the relationships between objects are constructed. An Entity contains business logic and behavior with high cohesion to
      its own properties. Business logic related to the data of a single instance of an Entity belongs here.

      ```php
      $tag = new Entity\Tag
      $tag->setName('Test Tag');

      $product = new Entity\Product;
      $product->setName('Test Product');
      $product->setUnitPrice(500);
      $product->setQuantity(1);
      $product->setIsInventoryRequired(true);
      $product->addTag($tag);
      
      if ($product->inStock()) {
        // Show add to cart button
      }
      ```

* <a name="repository"></a>EntityRepository
    - This module is responsible for storing and retrieving entities. Doctrine 2 is used in this layer to hydrate Entities
      using the Data Mapper Pattern.

      ```php
      $productRepository = $this->entityManager->getRepository(Product::class);

      $productId = 1;
      $product = $productRepository->findOneById($productId);
      $product->setUnitPrice(600);
      $productRepository->save($product);
      ```
      
* <a name="entitydto"></a>EntityDTO
    - These classes are simple anemic objects with no business logic. Data is accessible via public class member variables. 
      Using the EntityDTOBuilder, the complete network graph relationships are available (e.g., withAllData()) prior to
      calling build(). The primary reason for using these Data Transfer Objects (DTO) is to flatten the object graph from
      lazy loaded Doctrine proxy objects on the Entities for use in view templates. This avoids lazy loaded queries from
      being executed outside the core application and somewhere they don't belong, such as in a view template.

      ```php
      $product = new Entity\Product;
      $product->addTag(new Entity\Tag);

      $productDTO = $product->getDTOBuilder()
        ->withAllData(new Lib\Pricing)
        ->build();

      echo $productDTO->sku;
      echo $productDTO->price->unitPrice;
      echo $productDTO->tags[0]->name;
      ```

* <a name="lib"></a>Lib
    - This is where you will find a variety of utility code including the Payment Gateway (src/Lib/PaymentGateway).

      ```php
      $creditCard = new Entity\CreditCard;
      $creditCard->setName('John Doe');
      $creditCard->setZip5('90210');
      $creditCard->setNumber('4242424242424242');
      $creditCard->setCvc('123');
      $creditCard->setExpirationMonth('1');
      $creditCard->setExpirationYear('2020');

      $chargeRequest = new Lib\PaymentGateway\ChargeRequest;
      $chargeRequest->setCreditCard($creditCard);
      $chargeRequest->setAmount(2000);
      $chargeRequest->setCurrency('usd');
      $chargeRequest->setDescription('test@example.com');

      $stripe = new Lib\PaymentGateway\StripeFake;
      $charge = $stripe->getCharge($chargeRequest);
      ```

## Installation

Add the following lines to your ``composer.json`` file.

```JSON
{
    "require": {
        "inklabs/kommerce-core": "dev-master"
    }
}
```

```
   composer install
```

## Unit Tests:

<pre>
    vendor/bin/phpunit
</pre>

### With Code Coverage:

<pre>
    vendor/bin/phpunit --coverage-text --coverage-html coverage_report
</pre>

## Run Coding Standards Test:

<pre>
    vendor/bin/phpcs -p --standard=PSR2 src/ tests/
</pre>

## Count Lines of Code:

<pre>
    vendor/bin/phploc src/ tests/ --names="*.php,*.xml"
</pre>

## Export SQL

<pre>
    vendor/bin/doctrine orm:schema-tool:create --dump-sql
    vendor/bin/doctrine orm:schema-tool:update --dump-sql
</pre>

## License

```
Copyright 2014 Jamie Isaacs pdt256@gmail.com

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
