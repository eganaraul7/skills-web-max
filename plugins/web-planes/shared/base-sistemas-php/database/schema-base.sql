-- =====================================================================
-- Esquema BASE del núcleo (usuarios, intentos de acceso, medios, ajustes, tasas, secuencias).
-- Copiar al inicio de database/schema.sql de cada proyecto y añadir debajo las tablas de sus módulos.
-- Convención: tablas en plural snake_case, columnas snake_case, montos DECIMAL.
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS sequences, exchange_rates, settings, media, login_attempts, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(120) NOT NULL,
  email          VARCHAR(190) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  role           ENUM('admin','viewer') NOT NULL DEFAULT 'viewer',
  is_active      TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at  DATETIME NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(190) NOT NULL,
  ip_address    VARCHAR(45) NOT NULL,
  attempted_at  DATETIME NOT NULL,
  KEY idx_login_attempts_email (email, attempted_at),
  KEY idx_login_attempts_ip (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  file_name      VARCHAR(80) NOT NULL,
  original_name  VARCHAR(255) NOT NULL,
  mime_type      VARCHAR(60) NOT NULL,
  size_bytes     INT UNSIGNED NOT NULL,
  width          SMALLINT UNSIGNED NULL,
  height         SMALLINT UNSIGNED NULL,
  alt_text       VARCHAR(255) NOT NULL DEFAULT '',
  attribution    VARCHAR(255) NULL,
  created_by     INT UNSIGNED NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_media_file (file_name),
  CONSTRAINT fk_media_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
  id                              TINYINT UNSIGNED PRIMARY KEY,
  business_name                   VARCHAR(120) NOT NULL,
  logo_media_id                   INT UNSIGNED NULL,
  base_currency                   ENUM('USD','VES') NOT NULL DEFAULT 'USD',
  exchange_rate                   DECIMAL(14,4) NOT NULL DEFAULT 1.0000,
  exchange_rate_updated_at        DATETIME NULL,
  phone                           VARCHAR(30) NULL,
  whatsapp                        VARCHAR(30) NULL,
  email                           VARCHAR(190) NULL,
  address                         VARCHAR(255) NULL,
  timezone                        VARCHAR(64) NOT NULL DEFAULT 'America/Caracas',
  allow_negative_stock            TINYINT(1) NOT NULL DEFAULT 0,
  updated_at                      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_settings_logo FOREIGN KEY (logo_media_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE exchange_rates (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rate        DECIMAL(14,4) NOT NULL,
  created_by  INT UNSIGNED NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_exchange_rates_created (created_at),
  CONSTRAINT fk_exchange_rates_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sequences (
  name        VARCHAR(40) PRIMARY KEY,
  last_value  INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

