DELIMITER $

DROP PROCEDURE IF EXISTS insertGlossario $
CREATE PROCEDURE insertGlossario ( IN Identificativo VARCHAR(50), Name VARCHAR(50), Description VARCHAR(10000), First VARCHAR(50), FirstPlural VARCHAR(50), Text VARCHAR(50), Plural VARCHAR(50) )
BEGIN
    DECLARE IdTermin VARCHAR(4);
    START TRANSACTION;
    INSERT INTO Glossario(IdTermine, Identificativo, Name, Description, First, FirstPlural, Text, Plural, Time)/*insert fasulla che mi evita di dover risitemare la lista nel caso in cui vi fosse una duplicazione di una delle chiavi*/
        VALUES ('OO', Identificativo, Name, Description, First, FirstPlural, Text, Plural, CURRENT_TIMESTAMP);
    DELETE FROM Glossario WHERE IdTermine='OO';/*elimino il dato inserito con insert fasulla*/
    SET IdTermin = buildIdGlossario(Identificativo);
    INSERT INTO Glossario(IdTermine, Identificativo, Name, Description, First, FirstPlural, Text, Plural, Time)
        VALUES (IdTermin, Identificativo, Name, Description, First, FirstPlural, Text, Plural, CURRENT_TIMESTAMP);
    COMMIT;
END $
