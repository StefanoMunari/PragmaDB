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

DROP FUNCTION IF EXISTS uc_findSiblingGap $
CREATE FUNCTION uc_findSiblingGap ( Padre INT(5) ) 
    RETURNS VARCHAR(20)
BEGIN
    DECLARE done INT(1) DEFAULT 0;
    DECLARE gap VARCHAR(20) DEFAULT NULL;
    DECLARE next VARCHAR(20) DEFAULT NULL;
    DECLARE postFixId INT(2) DEFAULT 0;
    DECLARE idPadre VARCHAR(20);
    SELECT u.IdUC FROM UseCase u WHERE u.CodAuto = Padre INTO idPadre;
    BEGIN
        DECLARE cur CURSOR FOR SELECT u.IdUC FROM UseCase u WHERE idPadre= uc_hierarchyId(u.IdUC) ORDER BY postfixId(u.IdUC);

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

            OPEN cur;

            IF done < 1 
            THEN
                FETCH cur INTO next;
                  WHILE done < 1 AND (SELECT postFixId+1) = (SELECT postfixId(next) )
                        DO
                            SET gap = next;
                            SET postFixId = postfixId(gap);
                            FETCH cur INTO next;
                END WHILE;
            END IF;
            CLOSE cur;
    END;

    IF postfixId >0
        THEN
            RETURN (CONCAT(idPadre,'.',postfixId(gap)+1));
        ELSE
            RETURN (CONCAT(idPadre,'.',1));
    END IF;
END $

DROP FUNCTION IF EXISTS uc_findRootGap $
CREATE FUNCTION uc_findRootGap () 
    RETURNS VARCHAR(20)
BEGIN
    DECLARE done INT(1) DEFAULT 0;
    DECLARE gap VARCHAR(20) DEFAULT NULL;
    DECLARE next VARCHAR(20) DEFAULT NULL;
    DECLARE postFixId INT(2) DEFAULT 0;
    DECLARE cur CURSOR FOR SELECT u.IdUC FROM UseCase u WHERE u.Padre IS NULL ORDER BY postfixId(u.IdUC);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cur;

        IF done < 1 
        THEN
            FETCH cur INTO next;
              WHILE done < 1 AND (SELECT postFixId+1 ) = (SELECT postfixId(next) )
                    DO
                        SET gap = next;
                        SET postFixId = postfixId(gap);
                        FETCH cur INTO next;
            END WHILE;
        END IF;
        CLOSE cur;

    IF postFixId > 0
        THEN
            RETURN (CONCAT('UC',(postfixId(gap)+1)));
        ELSE
            RETURN 'UC1';
    END IF;
END $