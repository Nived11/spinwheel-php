-- database/schema.sql

CREATE DATABASE IF NOT EXISTS empire_plaza_spin
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE empire_plaza_spin;

-- Users table (replaces MongoDB User model)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    dob_or_anniversary VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_uid (uid),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Spins table (replaces MongoDB Spin model)
CREATE TABLE spins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) UNIQUE NOT NULL,
    prize VARCHAR(100) NOT NULL,
    probability INT NOT NULL,
    spin_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    redeemed BOOLEAN DEFAULT FALSE,
    redemption_time TIMESTAMP NULL,
    INDEX idx_uid (uid),
    INDEX idx_redeemed (redeemed),
    FOREIGN KEY (uid) REFERENCES users(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prize configurations (for reference)
CREATE TABLE prize_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prize VARCHAR(100) NOT NULL,
    weight INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
