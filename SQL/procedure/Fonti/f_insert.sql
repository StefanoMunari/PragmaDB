DELIMITER $

DROP PROCEDURE IF EXISTS insertFonte $
CREATE PROCEDURE insertFonte ( IN Nome VARCHAR(20), Descrizione VARCHAR(10000) )
BEGIN
    DECLARE IdFonte VARCHAR(10);
    START TRANSACTION;
    SET IdFonte = buildIdFonte();
    INSERT INTO Fonti(IdFonte, Nome, Descrizione, Time )
        VALUES (IdFonte, Nome, Descrizione, CURRENT_TIMESTAMP );
    COMMIT;
END $