CREATE DATABASE IF NOT EXISTS iaec_certificate_verification
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE iaec_certificate_verification;

CREATE TABLE IF NOT EXISTS graduates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    department VARCHAR(150) NOT NULL,
    program ENUM('bachelor', 'master', 'phd') NOT NULL,
    grade VARCHAR(100) NULL,
    year_of_graduation YEAR NOT NULL,
    current_status VARCHAR(150) NOT NULL,
    matric_number VARCHAR(100) NOT NULL UNIQUE,
    ref_number VARCHAR(100) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Run this on existing installations created before ref_number support:
-- ALTER TABLE graduates ADD ref_number VARCHAR(100) UNIQUE;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'superadmin') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(150) NOT NULL,
    primary_color VARCHAR(20) NOT NULL,
    secondary_color VARCHAR(20) NOT NULL,
    logo_path VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (id, school_name, primary_color, secondary_color, logo_path)
VALUES (1, 'IAEC University Togo', '#102a43', '#c69214', NULL)
ON DUPLICATE KEY UPDATE id = id;

-- Default superadmin login:
-- Email: admin@iaec-university.tg
-- Password: ChangeMe123!
-- Change this password immediately after first login.
INSERT INTO admins (email, password, role)
VALUES (
    'admin@iaec-university.tg',
    '$2y$10$cfWvavG08hXEQ2XZeYrCtO2RUhBDdM4KZoU89o4lOGiTCwy0MvrWa',
    'superadmin'
)
ON DUPLICATE KEY UPDATE email = email;
