
CREATE TABLE IF NOT EXISTS _prefix_events (
  event_id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  start_time TIMESTAMP,
  end_time TIMESTAMP,
  reservation_start_time TIMESTAMP,
  reservation_end_time TIMESTAMP,
  active BOOLEAN DEFAULT 0,
  front_info TEXT,
  room_type_info TEXT,
  transport_info TEXT,
  regulations MEDIUMTEXT,
  add_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  edit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS _prefix_room_types (
  room_type_id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  space_count INT NOT NULL,
  bathroom BOOLEAN NOT NULL,
  room_count INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  event_id MEDIUMINT(9) NOT NULL,
  add_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  edit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(event_id) REFERENCES _prefix_events(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS _prefix_rooms (
  room_id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  room_type_id MEDIUMINT(9) NOT NULL,
  sex ENUM('male', 'female'),
  family_person_id MEDIUMINT(9),
  -- status ENUM('free', 'full'),
  add_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  edit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(room_type_id) REFERENCES _prefix_room_types(room_type_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS _prefix_transports (
  transport_id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  event_id MEDIUMINT(9) NOT NULL,
  add_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  edit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(event_id) REFERENCES _prefix_events(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS _prefix_persons (
  person_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  family_person_id MEDIUMINT(9),
  room_type_id MEDIUMINT(9) NOT NULL,
  room_id MEDIUMINT(9) NOT NULL,
  transport_id MEDIUMINT(9) NOT NULL,
  first_name VARCHAR(255) NOT NULL,
  last_name VARCHAR(255) NOT NULL,
  birth_date DATE NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(255) NOT NULL,
  city VARCHAR(255),
  disabled BOOLEAN NOT NULL DEFAULT 0,
  disabled_guardian BOOLEAN NOT NULL DEFAULT 0,
  disability_type VARCHAR(255),
  stairs_accessibility BOOLEAN NOT NULL DEFAULT 1,
  guardian_person_name VARCHAR(255),
  disabled_person_name VARCHAR(255),
  underaged ENUM('no', 'alone', 'with_guardian') NOT NULL,
  underaged_guardian BOOLEAN NOT NULL DEFAULT 0,
  underaged_person_name VARCHAR(255),
  family_guardian BOOLEAN NOT NULL DEFAULT 0,
  sex ENUM('male', 'female') NOT NULL,
  comments TEXT,
  add_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  edit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY(room_type_id) REFERENCES _prefix_room_types(room_type_id),
  FOREIGN KEY(room_id) REFERENCES _prefix_rooms(room_id),
  FOREIGN KEY(transport_id) REFERENCES _prefix_transports(transport_id),
  FOREIGN KEY(family_person_id) REFERENCES _prefix_persons(person_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS _prefix_audit_logs (
  audit_log_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  object_id MEDIUMINT(9),
  log TEXT,
  user TEXT,
  add_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  edit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);