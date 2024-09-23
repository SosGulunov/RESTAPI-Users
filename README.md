***REST API Users***

БД: 
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `second_name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `date_create` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

В файле congig.php меняем данные на свои, устанавливаем зависимости


Возможности API:

1. Создание пользователя

- URL: /users
- Метод: POST
- Тело запроса (JSON):
{
  "first_name": "Иван",
  "second_name": "Иванов",
  "email": "test@gmail.com",
  "password": "qwerty"
}

При успешном запросе: 
{
  "message": "User created successfully"
}

2. Удаление пользователя
   
- URL: /users/{id}
- Метод: DELETE
- Заголовки:
- Authorization: Bearer {token}

При успешном запросе: 

{
  "message": "User deleted successfully"
}

3. Авторизация пользователя

- URL: /users/login
- Метод: POST
- Тело запроса (JSON):

{
  "email": "test@gmail.com",
  "password": "qwerty"
}

При успешном запросе: 

{
  "token": "Выдаст ваш токен"
}

4. Получение списка всех пользователей

- URL: /users
- Метод: GET
- Заголовки:
- Authorization: Bearer {token}

При успешном запросе: 

*Выдаст всех пользователей в JSON формате*

5. Получение информации о пользователе

- URL: /users/{id}
- Метод: GET
- Заголовки: Authorization: Bearer {token}

При успешном запросе: 

*Выдаст пользователя по id указаном в запросе(в JSON формате)*

6. Обновление информации пользователя

- URL: /users/{id}
- Метод: PUT
- Заголовки:
- Authorization: Bearer {token}
- Тело запроса (JSON):
  
{
  "first_name": "Иван",
  "second_name": "Петров",
  "email": "ivan.petrov@example.com",
  "password": "newsecurepassword"
}

При успешном запросе: 

{
  "message": "User updated successfully"
}

