-- Таблица рабочих
CREATE TABLE IF NOT EXISTS workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    telegram_chat_id VARCHAR(50) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Новые поля в requests (без IF NOT EXISTS)
ALTER TABLE requests ADD COLUMN worker_id INT DEFAULT NULL;
ALTER TABLE requests ADD COLUMN area_sqm DECIMAL(7,1) DEFAULT NULL;
ALTER TABLE requests ADD COLUMN rooms INT DEFAULT NULL;
ALTER TABLE requests ADD COLUMN address VARCHAR(500) DEFAULT NULL;
ALTER TABLE requests ADD COLUMN scheduled_at DATETIME DEFAULT NULL;
ALTER TABLE requests ADD COLUMN tracking_token VARCHAR(64) DEFAULT NULL;

-- Индексы
CREATE INDEX idx_requests_phone ON requests(phone);
CREATE INDEX idx_requests_token ON requests(tracking_token);
