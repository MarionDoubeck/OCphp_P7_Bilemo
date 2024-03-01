# BileMo API Documentation

Welcome to the documentation of the BileMo API, a platform offering a selection of high-end mobile phones available exclusively through B2B (business to business) sales.

## Context

BileMo provides its partners with access to its catalog of mobile phones via an API (Application Programming Interface). Partners can use this API to view the list of products, product details, manage registered users, and perform other operations.

## Customer Requirements

The first client of BileMo has signed a partnership contract, requiring the implementation of the following APIs:

- View the list of BileMo products
- View the details of a BileMo product
- View the list of registered users linked to a client on the website
- View the details of a registered user linked to a client
- Add a new user linked to a client
- Delete a user added by a client.

## Data Presentation

The data of the BileMo API follows the rules of levels 1, 2, and 3 of the Richardson model. Responses are served in JSON format and are cached to optimize the performance of requests to the API.

### Authentication

Only referenced clients can access the APIs. API clients must be authenticated via JWT.

### Available Endpoints

#### List of Products

GET /api/products
Returns the list of BileMo products.

#### Product Details

GET /api/products/{id}
Returns the details of a specified BileMo product by its identifier.

#### List of Consumers

GET /api/partners/{partner_id}/consumers
Returns the list of registered consumers linked to a client.

#### Consumer Detail

GET /api/partners/{partner_id}/consumers/{id}
Returns the details of a registered consumer linked to a client by its identifier.

#### Add Consumer

POST /api/partners/{partner_id}/consumers
Allow adding a new consumer linked to a client.

#### Delete Consumer

DELETE /api/partners/{partner_id}/consumers/{id}
Allows deleting a consumer added by a client specified by its identifier.

### Usage Example

GET /api/products
Authorization: Bearer <your-jwt-token>

### Error Handling

In case of an error, appropriate HTTP status codes will be returned, accompanied by explanatory messages to help diagnose the problem.

### Notes

For more details on accepted parameters and possible responses for each endpoint, please refer to the provided technical documentation at the endpoint
/api/doc

## Project Installation

### Clone the Projcet

To obtain a local copy of the project, use the following command:

```
git clone https://github.com/MarionDoubeck/OCphp_P7_Bilemo
```
### Install Dependencies

In your terminal, run the following command to install the project dependencies using Composer:

```
composer install
```
### Environment Configuration

Ensure that your environment is properly configured, including the database. You'll need to create a env.local file for your local configuration. Here's an example of the content for this file:

```
DATABASE_URL=mysql://nom_utilisateur:mot_de_passe@localhost:3306/nom_de_la_base_de_donnees
APP_DEBUG=true
APP_SECRET=cle_secrete_unique_pour_votre_application
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=PASSWORD
```
Make sure to customize the values with your specific information.

### Migrations

To create the database tables, execute the migration using the following command:

```
php bin/console doctrine:migrations:migrate
```

### Loading Fixtures

To load data into the database, execute the following command:
```
php bin/console doctrine:fixtures:load
```


### Running the Application

To run the application, execute the following command:

```
symfony serve -d
```
