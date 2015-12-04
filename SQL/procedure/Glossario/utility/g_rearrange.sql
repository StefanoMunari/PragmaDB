DELIMITER $

DROP PROCEDURE IF EXISTS rearrangeIdGlossario $
CREATE PROCEDURE rearrangeIdGlossario (IN oldId VARCHAR(4))
BEGIN
DECLARE db VARCHAR(4);
DECLARE done INT(1) DEFAULT 0;
DECLARE ind INT(1) DEFAULT 1;
DECLARE cursorRoot CURSOR FOR SELECT g.IdTermine FROM Glossario g WHERE (LEFT(oldId,1) = LEFT(g.IdTermine,1)) ORDER BY LEFT(g.IdTermine,1),CONVERT((SUBSTRING(g.IdTermine,2)),UNSIGNED INT);

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    OPEN cursorRoot;

    FETCH cursorRoot INTO db;
    IF done < 1 
        THEN
            REPEAT
                IF (CONVERT((SUBSTRING(db,2)),UNSIGNED INT)=ind)
                    THEN
                        SET ind=ind+1;
                        FETCH cursorRoot INTO db;
                    ELSE
                        REPEAT
                            UPDATE Glossario g SET g.IdTermine=CONCAT((LEFT(db,1)),(CONVERT((SUBSTRING(db,2)),UNSIGNED INT)-1)) WHERE g.IdTermine=db;
                            FETCH cursorRoot INTO db;
                        UNTIL done
                        END REPEAT;
                END IF;
            UNTIL done
            END REPEAT;
    END IF;
    CLOSE cursorRoot;
END $
