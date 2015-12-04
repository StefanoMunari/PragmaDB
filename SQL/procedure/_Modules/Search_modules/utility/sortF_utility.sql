/*
Copyright (C) 2015 Stefano Munari
Il programma è un software libero; potete redistribuirlo e/o secondo i termini della come pubblicato 
dalla Free Software Foundation; sia la versione 2, 
sia (a vostra scelta) ogni versione successiva.

Questo programma è distribuito nella speranza che sia utile 
ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di 
POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE. 
Vedere la GNU General Public License per ulteriori dettagli.

Dovreste aver ricevuto una copia della GNU General Public License
in questo programma; se non l'avete ricevuta, scrivete alla Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
DELIMITER $

/*PRIVATE*/
DROP PROCEDURE IF EXISTS mergeForestRQ $
CREATE PROCEDURE mergeForestRQ()
BEGIN/*funzionale-qualita-vincolo-prestazionale*/
    DECLARE Typo VARCHAR(20) DEFAULT 'Funzionale';
    DECLARE done INT;
    DECLARE root INT(5);

    WHILE Typo <> 'END'
        DO
        SET done= 0;
        SET root= NULL;
        BEGIN
            DECLARE typeCursor CURSOR FOR
            SELECT r.CodAuto 
            FROM Requisiti r 
            WHERE r.Padre IS NULL AND r.Tipo= Typo ORDER BY CONVERT(SUBSTRING(r.IdRequisito,4), UNSIGNED INTEGER);
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
            OPEN typeCursor;

            FETCH typeCursor INTO root;
            IF done < 1
                THEN
                    REPEAT
                            CALL parseRQtoPreorderTree(root);
                            FETCH typeCursor INTO root;
                        UNTIL done 
                    END REPEAT;
            END IF;
            CLOSE typeCursor;
        IF Typo = 'Funzionale' 
            THEN 
                SET Typo = 'Prestazionale';
            ELSE
                IF Typo = 'Prestazionale' 
                    THEN 
                        SET Typo= 'Qualita';
                ELSE
                    IF Typo = 'Qualita' 
                        THEN 
                            SET Typo = 'Vincolo';
                    ELSE
                        IF Typo = 'Vincolo'
                            THEN 
                                SET Typo = 'END';
                        END IF;
                    END IF;
                END IF;
        END IF;
        END;
    END WHILE;
END $

/*PRIVATE*/
DROP PROCEDURE IF EXISTS mergeForestUC $
CREATE PROCEDURE mergeForestUC()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE root INT(5) DEFAULT NULL;
    DECLARE typeCursor CURSOR FOR
    SELECT r.CodAuto 
    FROM UseCase r 
    WHERE r.Padre IS NULL ORDER BY CONVERT(SUBSTRING(r.IdUC,3), UNSIGNED INTEGER);
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    OPEN typeCursor;

    FETCH typeCursor INTO root;
    IF done < 1
        THEN
            REPEAT
                    CALL parseUCtoPreorderTree(root);
                    FETCH typeCursor INTO root;
                UNTIL done 
            END REPEAT;
    END IF;
    CLOSE typeCursor;
END $

/*PRIVATE*/
DROP PROCEDURE IF EXISTS parseRQtoPreorderTree $
CREATE PROCEDURE parseRQtoPreorderTree( IN Padre INT(5))
BEGIN
    DECLARE ind INT(2) DEFAULT 1;
    DECLARE IdPadre VARCHAR(20);
    DECLARE PTipo ENUM('Funzionale','Vincolo','Qualita','Prestazionale');

    SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto= Padre INTO IdPadre;
    SELECT r.Tipo FROM Requisiti r WHERE r.CodAuto= Padre INTO PTipo;
    /*SET List= CONCAT(List,IdPadre,',');//DEBUG*/
    START TRANSACTION;
        INSERT INTO _MapRequisiti(CodAuto)
        VALUES (Padre);
    COMMIT;

    WHILE (SELECT COUNT(*) FROM Requisiti r WHERE r.Tipo= PTipo AND SUBSTRING(r.IdRequisito,4) = CONCAT(SUBSTRING(IdPadre,4),'.',ind)) > 0
        DO
            CALL parseRQtoPreorderTree((SELECT r.CodAuto FROM Requisiti r WHERE r.Tipo= PTipo AND SUBSTRING(r.IdRequisito,4) = CONCAT(SUBSTRING(IdPadre,4),'.',ind)));
            SET ind = ind +1;
    END WHILE;
END $

DROP PROCEDURE IF EXISTS parseUCtoPreorderTree $
CREATE PROCEDURE parseUCtoPreorderTree( IN Padre INT(5))
BEGIN
    DECLARE ind INT(2) DEFAULT 1;
    DECLARE IdPadre VARCHAR(20);

    SELECT r.IdUC FROM UseCase r WHERE r.CodAuto= Padre INTO IdPadre;

    /*SET List= CONCAT(List,IdPadre,',');//DEBUG*/
    START TRANSACTION;
        INSERT INTO _MapUseCase(CodAuto)
        VALUES (Padre);
    COMMIT;

    WHILE (SELECT COUNT(*) FROM UseCase r WHERE r.IdUC = CONCAT(IdPadre,'.',ind)) > 0
        DO
            CALL parseUCtoPreorderTree((SELECT r.CodAuto FROM UseCase r WHERE r.IdUC = CONCAT(IdPadre,'.',ind)));
            SET ind = ind +1;
    END WHILE;
END $