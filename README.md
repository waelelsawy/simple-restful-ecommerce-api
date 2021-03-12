# Simple RESTful Ecommerce API

## Installation

To start a server, run from root directory

```bash
$ cd public
$ php -S localhost:8000

// or
$ php artisan serve
```

Once the server is up and running, you can migrate tables and generate fake data by running

```bash
$ php artisan migrate --seed
```

### Creating a new user

```bash
$ curl --header "Accept: application/json" --header "Content-Type: application/json" --request POST --data '{"name": "John Doe", "email": "johndoe@email.com", "password": "1234", "password_confirmation": "1234"}' http://localhost:8000/api/users
```

And to log in

```bash
$ curl --header "Accept: application/json" --header "Content-Type: application/json" --request POST --data '{"email": "johndoe@email.com", "password": "1234"}' http://localhost:8000/api/login
```

For subsequent order requests, you must include the API Token generated from the login request

```json
{
    "token": "<ACCESS_TOKEN>"
}
```

### Creating a new product

```bash
$ curl --header "Accept: application/json" --header "Content-Type: application/json" --request POST --data '{"title": "New Product", "price": 2499, "count": 5}' http://localhost:8000/api/products
```

### Create a new Order

```bash
$ curl --header "Accept: application/json" --header "Content-Type: application/json" --header "Authorization: Bearer <ACCESS_TOKEN>" --request POST --data '{"items": [{"product_id": 1, "quantity": 1}]}' http://localhost:8000/api/orders
```

## License

Licensed under the `MIT license`.
