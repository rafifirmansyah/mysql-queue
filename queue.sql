/* create database and use */
DROP DATABASE IF EXISTS queue;
CREATE DATABASE queue;
USE queue;

/* create table jobs */
DROP TABLE IF EXISTS job;
CREATE TABLE job(
	idJob INT AUTO_INCREMENT PRIMARY KEY,
	clientId INT,
	payload TEXT,
	status enum('new', 'processing', 'done', 'error') DEFAULT 'new',
	updated DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	created DATETIME DEFAULT CURRENT_TIMESTAMP
);


DELIMITER $$
CREATE OR REPLACE FUNCTION pushJob (Vpayload TEXT)
RETURNS INT
BEGIN
/* pushJob - a function to create a new job*/
	INSERT IGNORE INTO job (payload) VALUES (Vpayload);
 	RETURN LAST_INSERT_ID();
END $$ 
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE PROCEDURE popJob (VclientId INT)
BEGIN	
/* popJob - A procedure to request the next 'new' job */
	UPDATE job SET 
		clientId = VclientId
        	WHERE status = 'processing'
        	ORDER BY idJob ASC LIMIT 1;

	SELECT idJob, created, payload
		FROM job
		WHERE clientId = VclientId
        	AND status = 'processing' LIMIT 1;
END$$
DELIMITER ;

DELIMITER $$
CREATE OR REPLACE FUNCTION markJob (
	VjobId INT,
	Vstatus enum('new', 'processing', 'done', 'error')
)
RETURNS INT
BEGIN	
/* markJob - A procedure to mark a job with a status */	
	UPDATE job SET 
		status = Vstatus
        	WHERE idJob = VjobId;
	RETURN AFFECTED_ROWS();
END$$
DELIMITER ;


