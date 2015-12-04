DELIMITER $

DROP PROCEDURE IF EXISTS removeRequisitiPackage $
CREATE PROCEDURE removeRequisitiPackage  ( IN CodRequ INT(5))
BEGIN
    DECLARE next INT(5);
    DECLARE done INT(1) DEFAULT 0;
    START TRANSACTION;
        BEGIN
            DECLARE cur CURSOR FOR SELECT rp.CodPkg FROM RequisitiPackage rp WHERE rp.CodReq= CodRequ;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
            OPEN cur;
                WHILE done < 1 
                    DO
                        FETCH cur INTO next;
                        IF done<1
                        THEN
                        	CALL updatePackageTime(next);
                        END IF;
                END WHILE;
            CLOSE cur;
        END;
        DELETE FROM RequisitiPackage WHERE CodReq= CodRequ;
    COMMIT;
END $

DROP PROCEDURE IF EXISTS removePackageRequisiti $
CREATE PROCEDURE removePackageRequisiti  ( IN Pkg INT(5))
BEGIN
    START TRANSACTION;
        DELETE FROM RequisitiPackage
        WHERE CodPkg= Pkg;
        CALL updatePackageTime(Pkg);
    COMMIT;
END $