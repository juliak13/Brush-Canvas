create table ADMIN (
    'fname' varchar(30) NOT NULL,
    'lname' varchar(30) NOT NULL,
    'email' varchar(80) NOT NULL,
    'password' varchar(30) NOT NULL
    'aid' int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY
);

create table INSTRUCTOR (
    'fname' varchar(30) NOT NULL, 
    'lname' varchar(30) NOT NULL,
    'email' varchar(80) NOT NULL,
    'password' varchar(30) NOT NULL,
    'iid' int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY
);

create table STUDENT (
    'fname' varchar(30) NOT NULL,
    'lname' varchar(30) NOT NULL,
    'email' varchar(80) NOT NULL, 
    'password' varchar(30) NOT NULL,
    'cid' int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY
);
