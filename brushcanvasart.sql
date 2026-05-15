
create table CLASS (
    'cid' int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    'cname' varchar(30) NOT NULL, 
    'credit' int(10) NOT NULL,
    'dept' varchar(30) NOT NULL,
    'instructor' varchar(30) NOT NULL, 
    'description' varchar(200) NOT NULL, 
    'modality' varchar(30) NOT NULL, 
    'seat' int(10) NOT NULL
);
COMMIT;