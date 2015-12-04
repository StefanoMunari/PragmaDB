DELIMITER $

DROP PROCEDURE IF EXISTS removePackage $
CREATE PROCEDURE removePackage ( IN Cod INT(5))
BEGIN
    START TRANSACTION;
        CALL removeRelatedPackage(Cod);
        DELETE FROM Package WHERE CodAuto = Cod;
        CALL automatizeRequisitiPackage();
    COMMIT;
END $