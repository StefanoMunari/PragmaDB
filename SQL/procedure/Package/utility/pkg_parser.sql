DELIMITER $

DROP PROCEDURE IF EXISTS legalPackageList $
CREATE PROCEDURE legalPackageList (INOUT legal TINYINT, IN PkgL VARCHAR(1000)) 
BEGIN
	DECLARE CodAuto INT(5) DEFAULT NULL;
	WHILE legal AND PkgL <> ''
        DO
            CALL parseIdList(PkgL,',',CodAuto);
            SELECT COUNT(*) FROM Package r WHERE r.CodAuto= CodAuto INTO legal; 
    END WHILE;
END $