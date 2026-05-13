-- Запускать ТОЛЬКО если БД уже существует и таблицы созданы ранее
-- Добавляем таблицу рабочих
CREATE TABLE IF NOT EXISTS workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    telegram_chat_id VARCHAR(50) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем новые поля в таблицу заявок
ALTER TABLE requests
    ADD COLUMN IF NOT EXISTS worker_id INT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS area_sqm DECIMAL(7,1) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS rooms INT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS address VARCHAR(500) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS scheduled_at DATETIME DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS tracking_token VARCHAR(64) DEFAULT NULL;

-- Индексы
CREATE INDEX IF NOT EXISTS idx_requests_phone ON requests(phone);
CREATE INDEX IF NOT EXISTS idx_requests_token ON requests(tracking_token);
