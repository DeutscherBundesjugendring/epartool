SET @name = 'Name';
SET @email = 'email@email.com';
SET @password = '$2a$04$upGvMgYvjX6L6wSj3ePZWOOk5nh2A/UrLycATZ7XqW0PUlXidinhS'; -- password

INSERT INTO `users` (`name`, `email`, `password`, `lvl`)
VALUES
    (@name, @email, @password, 'adm');
