ALTER TABLE appointments
  ADD COLUMN service ENUM('ตรวจฟัน','ขูดหินปูน','อุดฟัน') NOT NULL DEFAULT 'ตรวจฟัน'
  AFTER time_slot;