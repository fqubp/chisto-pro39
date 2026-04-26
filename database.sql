-- Создание базы данных и таблицы для заявок
-- Выполнить в phpMyAdmin или через консоль

CREATE DATABASE IF NOT EXISTS chisto_pro39_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE chisto_pro39_db;

CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    phone VARCHAR(20) NOT NULL,
    service_type VARCHAR(255),
    message TEXT,
    file_path TEXT, -- Теперь TEXT для хранения JSON массива файлов
    estimated_price VARCHAR(50),
    source VARCHAR(50) DEFAULT 'site',
    status ENUM('new', 'in_progress', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание пользователя (если нужно)
-- GRANT ALL PRIVILEGES ON chisto_pro39_db.* TO 'chisto_user'@'localhost' IDENTIFIED BY 'your_password_here';
-- FLUSH PRIVILEGES;