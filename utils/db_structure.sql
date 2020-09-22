-- SQL sentences to set up all the DB tables

CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(id),
  sub VARCHAR(255) NOT NULL UNIQUE,
  email VARCHAR(320)
);

CREATE TABLE subjects (
  id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(id),
  friendly_name VARCHAR(100) NOT NULL UNIQUE, -- Nom que es mostra al web
  calendar_name VARCHAR(100) NOT NULL -- Nom al calendari de la FME
);

CREATE TABLE user_subjects (
  id INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY(id),
  user_id INT NOT NULL,
  subject_id INT NOT NULL
);

-- @TODO: Add form completion log table
