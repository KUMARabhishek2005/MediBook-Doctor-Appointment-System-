CREATE DATABASE IF NOT EXISTS doctor_appointment;
USE doctor_appointment;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','doctor','patient') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE specializations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    specialization_id INT NOT NULL,
    qualification VARCHAR(150) NOT NULL,
    experience INT DEFAULT 0,
    fee DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason VARCHAR(255),
    status ENUM('Pending','Approved','Rejected','Completed','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_slot (doctor_id, appointment_date, appointment_time)
);

INSERT INTO specializations (name) VALUES
('General Physician'), ('Cardiologist'), ('Dermatologist'),
('Dentist'), ('Orthopedic');

-- Demo passwords for all demo accounts are: password
-- These hashes were generated with PHP password_hash('password', PASSWORD_DEFAULT).
INSERT INTO users (name,email,password,role) VALUES
('Admin','admin@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Dr. Amit Sharma','amit@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','doctor'),
('Rahul Kumar','rahul@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','patient');

INSERT INTO doctors (user_id,specialization_id,qualification,experience,fee)
SELECT u.id, s.id, 'MBBS, MD', 5, 500
FROM users u JOIN specializations s ON s.name='General Physician'
WHERE u.email='amit@example.com';

INSERT INTO users (name,email,password,role) VALUES
('Dr. Priya Nair','priya@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','doctor');
INSERT INTO doctors (user_id,specialization_id,qualification,experience,fee)
SELECT u.id, s.id, 'MBBS, DM Cardiology', 9, 900 FROM users u JOIN specializations s ON s.name='Cardiologist' WHERE u.email='priya@example.com';
