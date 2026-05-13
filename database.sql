CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    phone VARCHAR(20) NOT NULL,
    service_type VARCHAR(255),
    message TEXT,
    file_path TEXT,
    estimated_price VARCHAR(50),
    source VARCHAR(50) DEFAULT 'site',
    source_ip VARCHAR(64) DEFAULT NULL,
    status ENUM('new', 'in_progress', 'completed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== ТАБЛИЦА ОТЗЫВОВ =====
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(255) NOT NULL,
    service_type VARCHAR(255) DEFAULT NULL,
    rating TINYINT NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== ТАБЛИЦА ГАЛЕРЕИ =====
CREATE TABLE IF NOT EXISTS gallery_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description VARCHAR(500) DEFAULT NULL,
    category ENUM('apartment','office','furniture','windows','renovation','other') NOT NULL DEFAULT 'other',
    before_image VARCHAR(500) NOT NULL,
    after_image VARCHAR(500) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== ТАБЛИЦА РАБОЧИХ =====
CREATE TABLE IF NOT EXISTS workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    telegram_chat_id VARCHAR(50) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== РАСШИРЕНИЕ ТАБЛИЦЫ ЗАЯВОК =====
ALTER TABLE requests
    ADD COLUMN IF NOT EXISTS worker_id INT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS area_sqm DECIMAL(7,1) DEFAULT NULL COMMENT 'Площадь м²',
    ADD COLUMN IF NOT EXISTS rooms INT DEFAULT NULL COMMENT 'Количество комнат',
    ADD COLUMN IF NOT EXISTS address VARCHAR(500) DEFAULT NULL COMMENT 'Адрес объекта',
    ADD COLUMN IF NOT EXISTS scheduled_at DATETIME DEFAULT NULL COMMENT 'Дата и время уборки',
    ADD COLUMN IF NOT EXISTS tracking_token VARCHAR(64) DEFAULT NULL COMMENT 'Токен для клиента',
    ADD FOREIGN KEY IF NOT EXISTS (worker_id) REFERENCES workers(id) ON DELETE SET NULL;

-- Индекс для быстрого поиска по телефону и токену
CREATE INDEX IF NOT EXISTS idx_requests_phone ON requests(phone);
CREATE INDEX IF NOT EXISTS idx_requests_token ON requests(tracking_token);
