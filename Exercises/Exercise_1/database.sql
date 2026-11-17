CREATE DATABASE thesis;

USE thesis;

CREATE TABLE graduate_theses (
    id INT NOT NULL AUTO_INCREMENT,
    work_name TEXT NOT NULL,
    work_text LONGTEXT,
    work_link TEXT NOT NULL,
    identification_number BIGINT NOT NULL,
    CONSTRAINT graduate_theses_pk PRIMARY KEY (id),
    CONSTRAINT graduate_theses_identification_number_unique
        UNIQUE (identification_number)
);
