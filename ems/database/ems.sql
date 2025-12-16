-- ==============================
-- EMPLOYEE MANAGEMENT SYSTEM
-- DATABASE: ems
-- ==============================

DROP DATABASE IF EXISTS ems;
CREATE DATABASE ems
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE ems;

-- ==============================
-- USERS TABLE
-- ==============================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','employee') NOT NULL
) ENGINE=InnoDB;

-- ==============================
-- DEPARTMENTS TABLE
-- ==============================
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB;

-- ==============================
-- EMPLOYEES TABLE
-- ==============================
CREATE TABLE employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  department_id INT NOT NULL,
  position VARCHAR(100),
  join_date DATE,
  salary DECIMAL(10,2),
  increment_rate INT DEFAULT 5,
  last_increment DATE,

  CONSTRAINT fk_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE,

  CONSTRAINT fk_department FOREIGN KEY (department_id)
    REFERENCES departments(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ==============================
-- ATTENDANCE TABLE
-- ==============================
CREATE TABLE attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  date DATE NOT NULL,
  status ENUM('Present','Absent','Leave') NOT NULL,

  CONSTRAINT fk_attendance_emp FOREIGN KEY (employee_id)
    REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- SALARY HISTORY TABLE
-- ==============================
CREATE TABLE salary_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  salary DECIMAL(10,2),
  increment_date DATE,

  CONSTRAINT fk_salary_emp FOREIGN KEY (employee_id)
    REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- QUALIFICATIONS TABLE
-- ==============================
CREATE TABLE qualifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  qualification VARCHAR(150),
  institute VARCHAR(150),
  year YEAR,

  CONSTRAINT fk_qualification_emp FOREIGN KEY (employee_id)
    REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==============================
-- DEFAULT USERS
-- ==============================

-- ADMIN
-- Email: admin@gmail.com
-- Password: admin123
INSERT INTO users (name, email, password, role)
VALUES (
  'Admin',
  'admin@gmail.com',
  '$2y$10$BsoLb99N8RbGgr6GksYbzeSA1PxFcC0t2LzTaJSO9qgVqfZmVAaEq',
  'admin'
);

-- EMPLOYEE
-- Email: employee@gmail.com
-- Password: emp123
INSERT INTO users (name, email, password, role)
VALUES (
  'Employee',
  'employee@gmail.com',
  '$2y$10$8WZ5QKk5mO8Sx7i9n0zQ9e4h1tY4hHn2Qf9cXGZJqP6M5Zk0q',
  'employee'
);

-- ==============================
-- DEFAULT DEPARTMENT
-- ==============================
INSERT INTO departments (name) VALUES ('IT');

-- ==============================
-- EMPLOYEE RECORD
-- ==============================
INSERT INTO employees
(user_id, department_id, position, join_date, salary)
VALUES
(2, 1, 'Staff', '2023-01-01', 30000);
