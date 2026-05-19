-- Student Course Hub Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS coursehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coursehub;

-- Staff members (programme leaders and module leaders)
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    bio TEXT,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Programmes (undergraduate and postgraduate degrees)
CREATE TABLE IF NOT EXISTS programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    level ENUM('Undergraduate', 'Postgraduate') NOT NULL,
    description TEXT NOT NULL,
    duration_years TINYINT NOT NULL DEFAULT 3,
    image VARCHAR(255),
    published TINYINT(1) NOT NULL DEFAULT 0,
    programme_leader_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (programme_leader_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- Modules (shared across programmes)
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    credits TINYINT NOT NULL DEFAULT 20,
    image VARCHAR(255),
    module_leader_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_leader_id) REFERENCES staff(id) ON DELETE SET NULL
);

-- Links modules to programmes, specifying which year of study
CREATE TABLE IF NOT EXISTS programme_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programme_id INT NOT NULL,
    module_id INT NOT NULL,
    year_of_study TINYINT NOT NULL,
    UNIQUE KEY unique_programme_module (programme_id, module_id),
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

-- Student interest registrations
CREATE TABLE IF NOT EXISTS interest_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    programme_id INT NOT NULL,
    message TEXT,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_programme (email, programme_id),
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE
);

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- SAMPLE DATA
-- =====================

INSERT INTO staff (name, email, bio, photo) VALUES
('Dr. Sarah Mitchell', 'sarah.mitchell@university.ac.uk', 'Dr Mitchell specialises in software engineering and distributed systems with over 15 years of research experience.', NULL),
('Prof. James Carter', 'james.carter@university.ac.uk', 'Professor Carter leads the Cyber Security research group, focusing on network defence and ethical hacking.', NULL),
('Dr. Aisha Patel', 'aisha.patel@university.ac.uk', 'Dr Patel is an expert in machine learning and statistical modelling with a background in industry data science.', NULL),
('Dr. Tom Hughes', 'tom.hughes@university.ac.uk', 'Dr Hughes researches artificial intelligence and its applications in healthcare and autonomous systems.', NULL),
('Dr. Lisa Chen', 'lisa.chen@university.ac.uk', 'Dr Chen specialises in web technologies, human-computer interaction, and accessible design.', NULL),
('Prof. Mark Davies', 'mark.davies@university.ac.uk', 'Professor Davies is a leading researcher in cryptography and digital forensics.', NULL);

INSERT INTO programmes (title, level, description, duration_years, published, programme_leader_id) VALUES
('BSc Computer Science', 'Undergraduate', 'Develop a thorough grounding in the principles of computing, software engineering, and systems design. You will gain practical skills in programming, databases, algorithms, and web development, preparing you for a wide range of technology careers.', 3, 1, 1),
('MSc Cyber Security', 'Postgraduate', 'Gain advanced knowledge in protecting digital systems and networks. This programme covers ethical hacking, digital forensics, cryptography, and risk management—equipping you to tackle the most pressing security challenges in industry and government.', 1, 1, 2),
('BSc Data Science', 'Undergraduate', 'Explore the full data pipeline from collection and cleaning to analysis and visualisation. You will apply machine learning, statistical modelling, and big data tools to solve real-world problems across finance, healthcare, and technology sectors.', 3, 1, 3),
('BSc Artificial Intelligence', 'Undergraduate', 'Study the theory and practice of intelligent systems. Covering machine learning, neural networks, natural language processing, and robotics, this programme prepares you to build the next generation of AI-powered applications.', 3, 1, 4),
('MSc Data Analytics', 'Postgraduate', 'An intensive programme for those seeking to transform raw data into actionable business intelligence. You will master advanced analytics, data visualisation, and predictive modelling using industry-standard tools.', 1, 1, 3),
('BSc Software Engineering', 'Undergraduate', 'Learn to design, build, and maintain complex software systems. Emphasis is placed on agile methodologies, testing, DevOps, and collaborative development—skills that are in high demand across all sectors of the economy.', 3, 0, 1);

INSERT INTO modules (title, description, credits, module_leader_id) VALUES
('Introduction to Programming', 'Fundamentals of programming using Python, covering variables, control flow, functions, and object-oriented concepts.', 20, 1),
('Web Development', 'Build modern, responsive web applications using HTML, CSS, JavaScript, and PHP with a focus on accessibility and usability.', 20, 5),
('Database Systems', 'Design and query relational databases using SQL; explore normalisation, transactions, and performance optimisation.', 20, 1),
('Algorithms and Data Structures', 'Study fundamental algorithms and data structures, complexity analysis, sorting, searching, and graph traversal.', 20, 4),
('Networks and Security', 'Introduction to networking protocols, TCP/IP, firewalls, and foundational cyber security concepts.', 20, 2),
('Software Engineering Principles', 'Agile methodologies, requirements engineering, UML, testing strategies, and version control best practices.', 20, 1),
('Machine Learning', 'Supervised and unsupervised learning, regression, classification, clustering, and neural network fundamentals.', 20, 3),
('Data Visualisation', 'Techniques for exploring and communicating data insights using tools such as Python, R, and Tableau.', 20, 3),
('Ethical Hacking', 'Penetration testing methodologies, vulnerability assessment, and responsible disclosure practices.', 20, 2),
('Digital Forensics', 'Principles of forensic investigation, evidence acquisition, analysis of file systems, and legal considerations.', 20, 6),
('Cryptography', 'Mathematical foundations of encryption, public-key infrastructure, digital signatures, and secure protocols.', 20, 6),
('Artificial Intelligence', 'Search algorithms, knowledge representation, planning, and an introduction to modern AI applications.', 20, 4),
('Natural Language Processing', 'Text processing, sentiment analysis, language models, and practical NLP with Python libraries.', 20, 4),
('Big Data Technologies', 'Distributed computing with Hadoop and Spark; processing and analysing large-scale datasets.', 20, 3),
('DevOps and Cloud Computing', 'CI/CD pipelines, containerisation with Docker, cloud platforms, and infrastructure as code.', 20, 1),
('Research Methods', 'Academic research skills, statistical analysis, hypothesis testing, and dissertation preparation.', 20, 3),
('Computer Systems Architecture', 'CPU design, memory hierarchy, assembly language, and operating system fundamentals.', 20, 4),
('Human-Computer Interaction', 'UX design principles, usability testing, accessibility standards, and prototyping methods.', 20, 5);

-- BSc Computer Science modules
INSERT INTO programme_modules (programme_id, module_id, year_of_study) VALUES
(1, 1, 1), (1, 2, 1), (1, 3, 1),
(1, 4, 2), (1, 5, 2), (1, 6, 2),
(1, 12, 3), (1, 15, 3), (1, 16, 3);

-- MSc Cyber Security modules
INSERT INTO programme_modules (programme_id, module_id, year_of_study) VALUES
(2, 5, 1), (2, 9, 1), (2, 10, 1), (2, 11, 1), (2, 16, 1);

-- BSc Data Science modules
INSERT INTO programme_modules (programme_id, module_id, year_of_study) VALUES
(3, 1, 1), (3, 3, 1), (3, 8, 1),
(3, 7, 2), (3, 14, 2), (3, 16, 2),
(3, 13, 3), (3, 15, 3);

-- BSc Artificial Intelligence modules
INSERT INTO programme_modules (programme_id, module_id, year_of_study) VALUES
(4, 1, 1), (4, 4, 1), (4, 17, 1),
(4, 7, 2), (4, 12, 2), (4, 18, 2),
(4, 13, 3), (4, 16, 3);

-- MSc Data Analytics modules
INSERT INTO programme_modules (programme_id, module_id, year_of_study) VALUES
(5, 7, 1), (5, 8, 1), (5, 14, 1), (5, 16, 1);

-- BSc Software Engineering modules (unpublished)
INSERT INTO programme_modules (programme_id, module_id, year_of_study) VALUES
(6, 1, 1), (6, 6, 1), (6, 2, 1),
(6, 4, 2), (6, 15, 2), (6, 3, 2),
(6, 12, 3), (6, 16, 3);

-- Default admin user: username=admin, password=Admin@1234
INSERT INTO admin_users (username, email, password_hash) VALUES
('admin', 'admin@university.ac.uk', '$2y$12$Ps/Jd2juviv4XYpZMkOOVOxcRw4YI5pVhuh1MPoEEHlB7S9Li8yNu');
