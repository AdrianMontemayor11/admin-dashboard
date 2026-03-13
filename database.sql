-- database.sql
-- Import this into phpMyAdmin (school_db) to test the dashboard.

DROP TABLE IF EXISTS absences;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS teachers;
DROP TABLE IF EXISTS admins;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_no VARCHAR(30) NOT NULL UNIQUE,
  first_name VARCHAR(60) NOT NULL,
  last_name VARCHAR(60) NOT NULL,
  grade_level VARCHAR(20) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE teachers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_no VARCHAR(30) NOT NULL UNIQUE,
  first_name VARCHAR(60) NOT NULL,
  last_name VARCHAR(60) NOT NULL,
  department VARCHAR(60) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE absences (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT DEFAULT NULL,
  teacher_id INT DEFAULT NULL,
  absence_date DATE NOT NULL,
  reason VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_abs_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
  CONSTRAINT fk_abs_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data
INSERT INTO students (student_no, first_name, last_name, grade_level) VALUES
('STU-2025-0001','Aira','Santos','10'),
('STU-2025-0002','Miguel','Reyes','10'),
('STU-2025-0003','Jasmine','Cruz','11'),
('STU-2025-0004','Paolo','Dela Cruz','12');

INSERT INTO teachers (employee_no, first_name, last_name, department) VALUES
('TCH-1001','Maria','Garcia','Math'),
('TCH-1002','John','Lim','Science'),
('TCH-1003','Karla','Navarro','English');

INSERT INTO absences (student_id, teacher_id, absence_date, reason) VALUES
(1, 1, '2026-02-23', 'Sick'),
(2, 2, '2026-02-24', 'Family emergency'),
(3, 3, '2026-02-24', 'Late arrival (marked absent)');

-- Optional: create a secure admin (password: admin123)
-- NOTE: dashboard demo login uses hardcoded credentials in login.php,
-- but you can switch to DB-auth later if you want.
