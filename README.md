# libPromise

A minimalistic implementation of [Promises](https://en.wikipedia.org/wiki/Futures_and_promises) for PHP.

## Installation via DEVirion

Install the [DEVirion](https://poggit.pmmp.io/ci/poggit/devirion/DEVirion) plugin and start your server. This will create a `virions` folder in your server's root directory.

```
server_root
| -> plugins
|    --> DEVirion.phar
| -> virions
```

- Download pre-compiled `.phar` files can be downloaded from [poggit](https://poggit.pmmp.io/ci/cooldogedev/libPromise/libPromise).
- Place the pre-compiled `.phar` in the `virions` directory

## Running from source code

Clone the repository via git `git clone git@github.com:cooldogedev/libPromise.git`. This will create a `libPromise` folder in your directory.

```
your_plugin
| -> src
|    --> cooldogedev
|       --> libPromise
```

- Place the `cooldogedev\libPromise` folder in your `src` directory.

## Promise Examples

### Hello world

```php
$promise = new Promise(
    function (): string {
        return "Hello world";
    }
);

$promise
    ->then(fn(string $response) => var_dump($response))
    ->settle();
```

### JSON decoder

```php
$promise = new Promise(
    function (): string {
        $person = [
            "name" => "john",
            "lastName" => "smith",
            "age" => 40
        ];

        return json_encode($person);
    }
);

$promise
    ->then(fn(string $response): array => json_decode($response, true))
    ->then(fn(array $response) => var_dump($response))
    ->settle();
```

## ThreadedPromise Examples

### ThreadedPromise that returns a promise

```php
$pool = new PromisePool($this);

$promise = new ThreadedPromise(
    function (): ThreadedPromise {
        return new ThreadedPromise(fn(string $response) => var_dump("a nested promise"));
    },
    function (ThreadedPromise $promise): void {
        /**
         * You can convert the ThreadedPromise to a regular Promise via @link ThreadedPromise::asPromise()
         */
        $promise->settle();
    }
);

$pool->addPromise($promise);
```

### Coinbase API endpoint fetcher

```php
$pool = new PromisePool($this);

$promise = new ThreadedPromise(
    function (): string {
        $request = Internet::getURL("https://api.coinbase.com/v2/currencies");
        return $request->getBody();
    }
);

$promise
    ->then(fn(string $response): array => json_decode($response, true))
    ->then(fn(array $response) => var_dump($response))
    ->catch(fn(PromiseError $error) => var_dump($error->getMessage()));

$pool->addPromise($promise);
```

### Tell online players a random joke

```php
$pool = new PromisePool($this);

$promise = new ThreadedPromise(
    function (): string {
        $request = Internet::getURL("https://v2.jokeapi.dev/joke/Any");
        return $request->getBody();
    },
    function (array $response): void {
        foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $onlinePlayer->sendMessage($response["setup"]);
            $onlinePlayer->sendMessage($response["delivery"]);
        }
    }
);

$promise
    ->then(fn(string $response): array => json_decode($response, true))
    ->catch(fn(PromiseError $error) => var_dump($error->getMessage()));

$pool->addPromise($promise);
```

### Projects using libPromise
- [libSQL](https://github.com/cooldogedev/libSQL)
- [BedrockEconomy](https://github.com/cooldogedev/BedrockEconomy)
