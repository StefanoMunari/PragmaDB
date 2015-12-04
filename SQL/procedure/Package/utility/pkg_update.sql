DELIMITER $

DROP PROCEDURE IF EXISTS updatePackageTime $
CREATE PROCEDURE updatePackageTime( IN Pkg INT(5))
BEGIN
	START TRANSACTION;
	    UPDATE Package p 
	    SET p.Time= CURRENT_TIMESTAMP
	    WHERE p.CodAuto= Pkg;
    COMMIT;
END $

DROP PROCEDURE IF EXISTS updatePrefixNomeDiscendenti $
CREATE PROCEDURE updatePrefixNomeDiscendenti( IN oldPrefixNome VARCHAR(5000), newPrefixNome VARCHAR(5000))
BEGIN
	DECLARE next INT(5);
    	DECLARE done INT(1) DEFAULT 0;
	DECLARE curP CURSOR FOR SELECT p.CodAuto FROM Package p WHERE LOCATE(oldPrefixNome,p.PrefixNome) > 0;
	DECLARE curC CURSOR FOR SELECT c.CodAuto FROM Classe c WHERE LOCATE(oldPrefixNome,c.PrefixNome) > 0;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	
	START TRANSACTION;
            	OPEN curP;
               	   WHILE done < 1 
                      DO
                         FETCH curP INTO next;
			 IF(done<1)
			 THEN
			 	UPDATE Package
			 	SET PrefixNome = REPLACE(PrefixNome,oldPrefixNome,newPrefixNome)
			 	WHERE CodAuto=next;
                         	CALL updatePackageTime(next);
			END IF;
                   END WHILE;
     		CLOSE curP;

		SET done=0;
		OPEN curC;

               	   WHILE done < 1 
                      DO
                         FETCH curC INTO next;
			 IF(done<1)
			 THEN
			 	UPDATE Classe
			 	SET PrefixNome = REPLACE(PrefixNome,oldPrefixNome,newPrefixNome)
			 	WHERE CodAuto=next;
                         	CALL updateClasseTime(next);
			END IF;
                   END WHILE;
     		CLOSE curC;
    	COMMIT;
END $
