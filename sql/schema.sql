
CREATE DATABASE IF NOT EXISTS care_project;
USE care_project;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','doctor','patient') NOT NULL,
  address VARCHAR(255),
  phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20),
  address VARCHAR(255),
  city VARCHAR(100),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE doctors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  specialization VARCHAR(100),
  experience INT,
  image VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT NOT NULL,
  appointment_time DATETIME NOT NULL,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
  FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

CREATE TABLE cities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

INSERT INTO users (username, email, password, role)
VALUES (
  'admin',
  'admin@example.com',
  '$2y$10$ZzjFf9V1Q8tOqLdsS9T8fuqF6JoIh3g0XhZzQYsd0y7zIZlWB7I9C', 
  'admin'
);
