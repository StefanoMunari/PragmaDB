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

DROP PROCEDURE IF EXISTS removeMetodiTest $
CREATE PROCEDURE removeMetodiTest(IN Met INT(10))
BEGIN
    DECLARE next INT(10);
    DECLARE done INT(1) DEFAULT 0;
    DECLARE codClasse INT(5);
    START TRANSACTION;
        BEGIN
            DECLARE cur CURSOR FOR SELECT CodTest FROM TestMetodi WHERE CodMet= Met;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
            OPEN cur;
                WHILE done < 1 
                    DO
                        FETCH cur INTO next;
			IF done<1
			THEN
                            CALL updateTestTime(next);
			END IF;
                END WHILE;
            CLOSE cur;
        END;
        DELETE FROM TestMetodi WHERE CodMet= Met;
        SELECT m.Classe INTO codClasse FROM Metodo m WHERE m.CodAuto=Met;
        CALL updateClasseTime(codClasse);
    COMMIT;
END $

DROP PROCEDURE IF EXISTS removeTestMetodi $
CREATE PROCEDURE removeTestMetodi(IN Test INT(10))
BEGIN
    DECLARE next INT(10);
    DECLARE done INT(1) DEFAULT 0;
    START TRANSACTION;
        BEGIN
            DECLARE cur CURSOR FOR SELECT DISTINCT m.Classe FROM TestMetodi tm JOIN Metodo m ON tm.CodMet=m.CodAuto WHERE tm.CodTest= Test;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
            OPEN cur;
                WHILE done < 1 
                    DO
                        FETCH cur INTO next;
			IF done<1
			THEN
                            CALL updateClasseTime(next);
			END IF;
                END WHILE;
            CLOSE cur;
        END;
        DELETE FROM TestMetodi WHERE CodTest= Test;
        CALL updateTestTime(Test);
    COMMIT;
END $
