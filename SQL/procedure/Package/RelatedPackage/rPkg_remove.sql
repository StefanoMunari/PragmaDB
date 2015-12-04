DELIMITER $

DROP PROCEDURE IF EXISTS removeRelatedPackage $
CREATE PROCEDURE removeRelatedPackage(IN PkgSubject INT(5))
BEGIN
    DECLARE next INT(5);
    DECLARE done INT(1) DEFAULT 0;
    START TRANSACTION;
        BEGIN
            DECLARE cur CURSOR FOR SELECT Pack2 FROM RelatedPackage WHERE Pack1= PkgSubject UNION SELECT Pack1 FROM RelatedPackage WHERE Pack2= PkgSubject;

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

        DELETE FROM RelatedPackage WHERE Pack1= PkgSubject;
	DELETE FROM RelatedPackage WHERE Pack2= PkgSubject;
    COMMIT;
END $
