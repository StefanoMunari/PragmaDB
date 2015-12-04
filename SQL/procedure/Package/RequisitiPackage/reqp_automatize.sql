DELIMITER $

DROP PROCEDURE IF EXISTS automatizeRequisitiPackage $
CREATE PROCEDURE automatizeRequisitiPackage ()
BEGIN
    DECLARE nextP INT(5);
    DECLARE doneP INT(1) DEFAULT 0;
    DECLARE prefix VARCHAR(5000) DEFAULT NULL;
    DECLARE curP CURSOR FOR SELECT DISTINCT p.CodAuto FROM Package p WHERE p.PrefixNome<>'Premi' AND ((p.CodAuto IN (SELECT p.Padre FROM Package p)) OR (p.CodAuto IN (SELECT c.ContenutaIn FROM Classe c)));
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET doneP = 1;

    START TRANSACTION;
        DELETE FROM RequisitiPackage;
        OPEN curP;
            WHILE doneP < 1
                DO
                    FETCH curP INTO nextP;
                    IF(doneP<1)
                        THEN
                            SELECT p.PrefixNome INTO prefix FROM Package p WHERE p.CodAuto=nextP;
                            CALL findRequisitiPackage(nextP,prefix);
                    END IF;
            END WHILE;
        CLOSE curP;
    COMMIT;
END $

DROP PROCEDURE IF EXISTS findRequisitiPackage $
CREATE PROCEDURE findRequisitiPackage (IN CodPackage INT(5), Prefix VARCHAR(5000))
BEGIN
    DECLARE nextR INT(5);
    DECLARE doneR INT(1) DEFAULT 0;
    DECLARE curR CURSOR FOR SELECT DISTINCT rc.CodReq FROM RequisitiClasse rc JOIN Classe c ON rc.CodClass=c.CodAuto WHERE LOCATE(Prefix,c.PrefixNome) > 0;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET doneR = 1;

    START TRANSACTION;
        OPEN curR;
            WHILE doneR < 1
                DO
                    FETCH curR INTO nextR;
                    IF(doneR<1)
                        THEN
                            INSERT INTO RequisitiPackage(CodPkg,CodReq)
                            VALUES (CodPackage,nextR);
                    END IF;
            END WHILE;
        CLOSE curR;
    COMMIT;
END $
