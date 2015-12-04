DELIMITER $

DROP PROCEDURE IF EXISTS removeFonte $
CREATE PROCEDURE removeFonte ( IN Cod INT(5))
BEGIN
    DECLARE oldId VARCHAR(10);
    START TRANSACTION;
    SELECT f.IdFonte INTO oldId FROM Fonti f WHERE f.CodAuto=Cod;
        DELETE FROM Fonti WHERE CodAuto = Cod;

        UPDATE Fonti f 
        SET f.IdFonte = CONCAT('F',postfixId(f.IdFonte)-1) 
        WHERE postfixId(f.IdFonte) > postfixId(oldId);
	CALL sortForest('Requisiti');
    COMMIT;
END$