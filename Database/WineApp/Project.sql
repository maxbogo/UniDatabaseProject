CREATE TABLE member (
mem_id int,
nickname varchar2(15) NOT NULL,
country varchar2(50),
PRIMARY KEY(mem_id)
);

CREATE TABLE grape (
grape_name varchar(100),
food_paring varchar(50),
acidity varchar(50) DEFAULT 'medium',
PRIMARY KEY(grape_name)
);

CREATE TABLE winery (
winery_name varchar(100),
founder varchar(100),
liters_of_wine integer,
PRIMARY KEY(winery_name)
);

CREATE TABLE country (
country_name varchar(500),
vineyard_area integer,
population integer,
PRIMARY KEY(country_name)
);

CREATE TABLE wine (
wine_id int,
color varchar(100),
vintage integer,
winery_name varchar(100),
grape_name varchar(100),
PRIMARY KEY(wine_id),
FOREIGN KEY(winery_name) REFERENCES winery(winery_name ),
FOREIGN KEY(grape_name) REFERENCES grape(grape_name)
);


CREATE TABLE review (
review_id integer,
points integer NOT NULL,
date_rev date NOT NULL,
member_id integer,
wine_id integer,
PRIMARY KEY(review_id),
FOREIGN KEY(member_id) REFERENCES member(mem_id),
FOREIGN KEY(wine_id) REFERENCES wine(wine_id)
                  ON DELETE CASCADE ,
CHECK (points <= 100 and points > 0)
);


CREATE TABLE region (
r_index integer,
m2 integer,
climate varchar(100),
country_name varchar(500),
PRIMARY KEY(r_index, country_name),
FOREIGN KEY(country_name) REFERENCES country(country_name)
);

CREATE TABLE autochthon (
tons integer,
export_tons integer,
grape_name varchar(100),
country_name varchar(500),
PRIMARY KEY(grape_name),
FOREIGN KEY(grape_name)  REFERENCES grape(grape_name),
FOREIGN KEY(country_name) REFERENCES country(country_name)
);

CREATE TABLE worldwide (
tons integer,
export_tons integer,
grape_name varchar(100),
PRIMARY KEY(grape_name),
FOREIGN KEY(grape_name)  REFERENCES grape(grape_name)
);


CREATE TABLE follow (
member_id1 integer,
member_id2 integer,
PRIMARY KEY(member_id1,member_id2),
FOREIGN KEY(member_id1)  REFERENCES member(mem_id),
FOREIGN KEY(member_id2)  REFERENCES member(mem_id)
);

CREATE TABLE want_try (
member_id integer,
wine_id integer,
PRIMARY KEY(member_id, wine_id),
FOREIGN KEY(member_id)  REFERENCES member(mem_id),
FOREIGN KEY(wine_id)  REFERENCES wine(wine_id)
);

CREATE TABLE from_c (
wine_id integer,
region_index integer,
country_name varchar(500),
PRIMARY KEY(wine_id, region_index, country_name),
FOREIGN KEY(wine_id)  REFERENCES wine(wine_id),
    FOREIGN KEY(country_name, region_index)  REFERENCES region(country_name, r_index)
);


CREATE TABLE have (
region_index integer,
winery_name varchar(100),
grape_name varchar(100),
country_name varchar(500),
PRIMARY KEY(region_index, winery_name, grape_name, country_name),
FOREIGN KEY(country_name, region_index)  REFERENCES region(country_name, r_index),
FOREIGN KEY(grape_name)  REFERENCES grape(grape_name),
FOREIGN KEY(winery_name)  REFERENCES winery(winery_name)
);

CREATE TABLE grow (
country_name varchar(500),
grape_name varchar(100),
PRIMARY KEY(country_name, grape_name),
FOREIGN KEY(country_name) REFERENCES country(country_name),
FOREIGN KEY(grape_name) REFERENCES worldwide(grape_name)
);




/* auto-increment */
 
 /* memeber */
CREATE  SEQUENCE seq_mem_id
    START WITH 100
    MINVALUE 100
    INCREMENT BY 1
    CACHE 100;
  
CREATE OR REPLACE TRIGGER tri_mem_id
    BEFORE INSERT  ON member
    FOR EACH ROW  
    BEGIN
        SELECT seq_mem_id.nextval
        INTO :new.mem_id
        FROM dual;
    END;
/



 /* review */
CREATE  SEQUENCE seq_review_id
    START WITH 1
    MINVALUE 1
    INCREMENT BY 1
    CACHE 100;
    
CREATE OR REPLACE TRIGGER tri_review_id
    BEFORE INSERT  ON review
    FOR EACH ROW  
    BEGIN
        SELECT seq_review_id.nextval
        INTO :new.review_id
        FROM dual;
    END;
/      


 /* wine */
CREATE  SEQUENCE seq_wine_id
    START WITH 1
    MINVALUE 1
    INCREMENT BY 1
    CACHE 100;
    
CREATE OR REPLACE TRIGGER tri_wine_id
    BEFORE INSERT  ON wine
    FOR EACH ROW  
    BEGIN
        SELECT seq_wine_id.nextval
        INTO :new.wine_id
        FROM dual;
    END;
/    


/* delete */
  /* member */
 CREATE OR REPLACE PROCEDURE p_delete_member(
    p_member_id  IN  member.mem_id%TYPE,
    p_error_code OUT NUMBER)
    AS
        BEGIN
            DELETE
            FROM member
            WHERE p_member_id =  member.mem_id;
            p_error_code := SQL%ROWCOUNT;
            IF (p_error_code = 1)  THEN  
                COMMIT;
            ELSE
                ROLLBACK;
            END IF;
            EXCEPTION
            WHEN OTHERS 
            THEN
                p_error_code := SQLCODE;
            END p_delete_member;
        /   


  /* review */
 CREATE OR REPLACE PROCEDURE p_delete_review(
    p_review_id  IN  review.review_id%TYPE,
    p_error_code OUT NUMBER)
    AS
        BEGIN
            DELETE
            FROM review
            WHERE p_review_id =  review.review_id;
            p_error_code := SQL%ROWCOUNT;
            IF (p_error_code = 1)  THEN  
                COMMIT;
            ELSE
                ROLLBACK;
            END IF;
            EXCEPTION
            WHEN OTHERS 
            THEN
                p_error_code := SQLCODE;
            END p_delete_review;
        / 


/* want_try */
 CREATE OR REPLACE PROCEDURE p_delete_want_try(
    p_want_try_m_id  IN  want_try.member_id%TYPE,
    p_want_try_w_id  IN  want_try.wine_id%TYPE,
    p_error_code OUT NUMBER)
    AS
        BEGIN
            DELETE
            FROM want_try
            WHERE p_want_try_m_id =  want_try.member_id 
                   AND p_want_try_w_id = want_try.wine_id;
            p_error_code := SQL%ROWCOUNT;
            IF (p_error_code = 1)  THEN  
                COMMIT;
            ELSE
                ROLLBACK;
            END IF;
            EXCEPTION
            WHEN OTHERS 
            THEN
                p_error_code := SQLCODE;
            END p_delete_want_try;
        / 
        
          /* wine */
 CREATE OR REPLACE PROCEDURE p_delete_wine(
    p_wine_id  IN  wine.wine_id%TYPE,
    p_error_code OUT NUMBER)
    AS
        BEGIN
            DELETE
            FROM wine
            WHERE p_wine_id = wine.wine_id;
            p_error_code := SQL%ROWCOUNT;
            IF (p_error_code = 1)  THEN  
                COMMIT;
            ELSE
                ROLLBACK;
            END IF;
            EXCEPTION
            WHEN OTHERS 
            THEN
                p_error_code := SQLCODE;
            END p_delete_wine;
        / 
        
/* DROP DROP DROP*/

DROP TABLE grow;
DROP TABLE have;
DROP TABLE from_c;
DROP TABLE want_try;
DROP TABLE follow;
DROP TABLE worldwide;
DROP TABLE autochthon;
DROP TABLE region;
DROP TABLE review;
DROP TABLE wine;
DROP TABLE country;
DROP TABLE winery;
DROP TABLE grape;
DROP TABLE member;




drop SEQUENCE seq_mem_id;
drop TRIGGER tri_mem_id;

drop SEQUENCE seq_review_id;
drop TRIGGER tri_review_id;

drop SEQUENCE seq_wine_id;
drop TRIGGER tri_wine_id;

drop SEQUENCE seq_autochthon_id;
drop TRIGGER tri_autochthon_id;

drop SEQUENCE seq_worldwide_id;
drop TRIGGER tri_worldwide_id;






select * from user_tables;