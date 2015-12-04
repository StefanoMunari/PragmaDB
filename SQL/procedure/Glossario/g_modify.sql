DELIMITER $

DROP PROCEDURE IF EXISTS modifyGlossario $
CREATE PROCEDURE modifyGlossario ( IN CodAuto INT(5), Identificativo VARCHAR(50), Name VARCHAR(50), Description VARCHAR(10000), First VARCHAR(50), FirstPlural VARCHAR(50), Text VARCHAR(50), Plural VARCHAR(50) )
BEGIN
    DECLARE IdTermine VARCHAR(4);
    DECLARE oldId VARCHAR(4);
    START TRANSACTION;
        UPDATE Glossario g SET g.Name=Name, g.Description=Description, g.First=First, g.FirstPlural=FirstPlural, g.Text=Text, g.Plural=Plural, g.Time=CURRENT_TIMESTAMP WHERE g.CodAuto=CodAuto;
        IF Identificativo IS NOT NULL
            THEN
                UPDATE Glossario g SET g.Identificativo=Identificativo WHERE g.CodAuto=CodAuto;
                SET IdTermine=buildIdGlossario(Identificativo);
                SELECT g.IdTermine INTO oldId FROM Glossario g WHERE g.CodAuto=CodAuto;
                UPDATE Glossario g SET g.IdTermine=IdTermine WHERE g.CodAuto=CodAuto;
                CALL rearrangeIdGlossario(oldId);
        END IF;
    COMMIT;
END $
