DELIMITER $

/*l'utente non può modificar l'idfonte*/
DROP PROCEDURE IF EXISTS modifyFonte $
CREATE PROCEDURE modifyFonte (IN CodAuto    INT(5), Nome    VARCHAR(20), Descrizione    VARCHAR(10000) )
BEGIN
    START TRANSACTION;
        UPDATE Fonti f SET f.Nome = Nome, f.Descrizione = Descrizione, f.Time=CURRENT_TIMESTAMP WHERE f.CodAuto = CodAuto; 
    COMMIT;
END$