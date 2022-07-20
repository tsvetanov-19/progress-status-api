## Progress status evaluation and estimation API

### Requirements

`PHP 8.1` and `Symfony 6.1`

### Initial setup

`composer install`

### Unit testing
Both controller and business logic classes have been covered with unit tests. To run them
use command

` php console/phpunit tests `

### Development Server

For dev deployment, testing, etc. I used the built-in development server:

`symfony server:start`

### Project Structure

Pretty simple. Consists of controller (`src/Controller/ProgressController`) which has only one route, defined in annotation.
GET method only.

Path is `/statuses/{duration}/{currentProgress}/{dateCreated}/{dueDate}`

Controller method takes care of input validation, all other logic is passed to `src/CoreLogic/Calculator.php`.