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

DROP PROCEDURE IF EXISTS rearrangeIdTest $
CREATE PROCEDURE rearrangeIdTest (IN TipoR ENUM('Validazione','Sistema','Integrazione','Unita','Regressione'),oldId VARCHAR(22))
BEGIN
DECLARE done INT(1) DEFAULT 0;
DECLARE next INT(10) DEFAULT NULL;
DECLARE cur CURSOR FOR SELECT t.CodAuto FROM Test t WHERE t.Tipo=TipoR AND CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)>CONVERT(SUBSTRING(oldId,3),UNSIGNED INT) ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT);
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    START TRANSACTION;
        OPEN cur;
        WHILE done < 1
            DO
                FETCH cur INTO next;
                IF done<1
                    THEN
                        UPDATE Test
                        SET IdTest = REPLACE(IdTest,SUBSTRING(IdTest,3),(CONVERT(SUBSTRING(IdTest,3),UNSIGNED INT))-1)
                        WHERE CodAuto=next;
                        CALL updateTestTime(next);
                END IF;
        END WHILE;
        CLOSE cur;
    COMMIT;
END $

DROP PROCEDURE IF EXISTS forwardIdTest $
CREATE PROCEDURE forwardIdTest (IN TipoR ENUM('Integrazione','Unita','Regressione'),idDaLiberare VARCHAR(22))
BEGIN
DECLARE done INT(1) DEFAULT 0;
DECLARE next INT(10) DEFAULT NULL;
DECLARE cur CURSOR FOR SELECT t.CodAuto FROM Test t WHERE t.Tipo=TipoR AND CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)>=CONVERT(SUBSTRING(idDaLiberare,3),UNSIGNED INT) ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT) DESC;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    START TRANSACTION;
        OPEN cur;
        WHILE done < 1
            DO
                FETCH cur INTO next;
                IF done<1
                    THEN
                        UPDATE Test
                        SET IdTest = REPLACE(IdTest,SUBSTRING(IdTest,3),(CONVERT(SUBSTRING(IdTest,3),UNSIGNED INT))+1)
                        WHERE CodAuto=next;
                        CALL updateTestTime(next);
                END IF;
        END WHILE;
        CLOSE cur;
    COMMIT;
END $
