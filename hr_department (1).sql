-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Гру 03 2025 р., 20:36
-- Версія сервера: 10.4.32-MariaDB
-- Версія PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `hr_department`
--

-- --------------------------------------------------------

--
-- Структура таблиці `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `vacancy_id` int(11) DEFAULT NULL,
  `status` enum('new','viewed','interview','rejected','offer') DEFAULT 'new',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `interview_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `vacancy_id`, `status`, `applied_at`, `interview_date`) VALUES
(13, 6, 16, 'offer', '2025-12-03 19:11:51', '2025-12-04 13:15:00'),
(14, 6, 17, 'offer', '2025-12-03 19:33:26', '2025-12-11 23:35:00');

-- --------------------------------------------------------

--
-- Структура таблиці `employee_profiles`
--

CREATE TABLE `employee_profiles` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `about_text` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `employee_profiles`
--

INSERT INTO `employee_profiles` (`user_id`, `full_name`, `position`, `about_text`, `photo`, `linkedin`) VALUES
(7, 'Степан', 'HR', 'HR з великим досвідом. Провів понад 1000 співбесід', 'emp_7_1764788556.jpg', NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `summary` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `resumes`
--

INSERT INTO `resumes` (`id`, `user_id`, `summary`, `skills`, `file_path`, `updated_at`) VALUES
(3, 6, 'Витривалий будівельник з великим досвідом', 'Будівництво', 'resume_6_1764789108.docx', '2025-12-03 19:11:48');

-- --------------------------------------------------------

--
-- Структура таблиці `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `author_name` varchar(50) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `vacancy_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `reviews`
--

INSERT INTO `reviews` (`id`, `author_name`, `review_text`, `rating`, `created_at`, `user_id`, `vacancy_id`) VALUES
(10, 'Іван', 'Команда професіоналів', 5, '2025-12-03 19:13:51', 6, 16),
(11, 'Іван', 'fdtfdcytdc', 5, '2025-12-03 19:34:56', 6, 17);

-- --------------------------------------------------------

--
-- Структура таблиці `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `stats_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `stats_count`) VALUES
(1, 'Рекрутинг', 'Пошук та підбір персоналу під ключ.', 150),
(2, 'Кадровий аудит', 'Перевірка документації та процесів.', 45),
(3, 'Перевірка працівників', 'Регулярне тестування працівників на відповідність вимогам', 30);

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','candidate') NOT NULL DEFAULT 'candidate',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Малейко Ігор Олегович', 'admin11@gmail.com', '$2y$10$v8.nM53CHh1BZpvtcO6F5.ExuiqLb5N8SFOnW03we4xKrjvSLn7u.', 'admin', '2025-12-02 15:12:49'),
(6, 'Іван', 'ivan@gmail.com', '$2y$10$ANHC0KKwHmizzNh.SJzkTe8dIIP0uvrr7LLMh2h/tNTMra9B4YGJC', 'candidate', '2025-12-03 18:59:58'),
(7, 'Степан', 'stepan@gmail.com', '$2y$10$x31RxWiGk6TUkHy/Qs/FU.ZpeVrCm37QvD1lq7fvYb7Mj/MG1bL3.', 'employee', '2025-12-03 19:00:32');

-- --------------------------------------------------------

--
-- Структура таблиці `vacancies`
--

CREATE TABLE `vacancies` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','closed') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `vacancies`
--

INSERT INTO `vacancies` (`id`, `title`, `salary`, `description`, `status`, `created_by`) VALUES
(16, 'Будівельник', '17000', 'Робота будівельника на об\'єкті', 'closed', 7),
(17, 'Електрик', '20000', 'Ремонтує розетки', 'closed', 7);

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vacancy_id` (`vacancy_id`);

--
-- Індекси таблиці `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Індекси таблиці `resumes`
--
ALTER TABLE `resumes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Індекси таблиці `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблиці `resumes`
--
ALTER TABLE `resumes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблиці `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблиці `vacancies`
--
ALTER TABLE `vacancies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD CONSTRAINT `employee_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `resumes`
--
ALTER TABLE `resumes`
  ADD CONSTRAINT `resumes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
