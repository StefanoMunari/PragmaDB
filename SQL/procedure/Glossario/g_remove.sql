/* NB:MYSQL non ammette funzioni ricorsive, solamente procedure ricorsive con un limite max di 255 chiamate ricorsive*/
DELIMITER $

DROP PROCEDURE IF EXISTS removeGlossario $
CREATE PROCEDURE removeGlossario ( IN CodiceAuto INT(5) )
BEGIN
    DECLARE oldId VARCHAR(4);
    START TRANSACTION;
        SELECT g.IdTermine INTO oldId FROM Glossario g WHERE g.CodAuto=CodiceAuto;
        DELETE FROM Glossario WHERE CodAuto=CodiceAuto;
        CALL rearrangeIdGlossario(oldId);
    COMMIT;
END $