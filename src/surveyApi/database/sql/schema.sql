CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    name_role VARCHAR(255) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    gmail VARCHAR(255) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES role(id_role) ON DELETE SET NULL
);

CREATE TABLE type_anwer (
    id_type INT AUTO_INCREMENT PRIMARY KEY,
    name_type VARCHAR(255) NOT NULL
);

CREATE TABLE survey (
    id_survey INT AUTO_INCREMENT PRIMARY KEY,
    name_survey VARCHAR(255) NOT NULL,
    user_id INT,
    status ENUM('draft', 'published', 'closed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE question (
    id_question INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT,
    text TEXT NOT NULL,
    type_id INT,
    FOREIGN KEY (survey_id) REFERENCES survey(id_survey) ON DELETE CASCADE,
    FOREIGN KEY (type_id) REFERENCES type_anwer(id_type)
);

CREATE TABLE question_options (
    id_option INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    option_text VARCHAR(255) NOT NULL,
    FOREIGN KEY (question_id) REFERENCES question(id_question) ON DELETE CASCADE
);

CREATE TABLE answer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_survey INT,
    user_id INT,
    question_id INT,
    option_id INT NULL,
    text TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_survey) REFERENCES survey(id_survey),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (question_id) REFERENCES question(id_question),
    FOREIGN KEY (option_id) REFERENCES question_options(id_option)
);