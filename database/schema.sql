CREATE DATABASE IF NOT EXISTS dental_clinic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dental_clinic;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  citizen_id VARCHAR(13) NOT NULL,
  phone VARCHAR(10) NOT NULL,
  date DATE NOT NULL,
  time_slot VARCHAR(20) NOT NULL,
  service ENUM('ตรวจฟัน','ขูดหินปูน','อุดฟัน') NOT NULL DEFAULT 'ตรวจฟัน',
  coverage ENUM('สิทธิหลักประกันสุขภาพถ้วนหน้า (บัตรทอง/ 30 บาท รักษาทุกโรค)','สิทธิเบิกได้กรมบัญชีกลาง (เบิกได้ข้าราชการ/บุคคลในครอบครัว)','สิทธิข้าราชการท้องถิ่น (อปท.)') NOT NULL,
  ref_token VARCHAR(64) UNIQUE NOT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(citizen_id), INDEX(date), INDEX(time_slot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- admin default (user: admin / pass: admin123)
INSERT IGNORE INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$y3L1DbsW7HqAxkO4ZVybBeT0mA3sj8G0gY1HcC5Hh4v1T1QyO1H5i');
