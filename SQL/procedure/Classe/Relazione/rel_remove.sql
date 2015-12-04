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

DROP PROCEDURE IF EXISTS removeRelazioneDa $
CREATE PROCEDURE removeRelazioneDa( IN DaR  INT(5))
BEGIN
    DECLARE next INT(5);
    DECLARE done INT(1) DEFAULT 0;
    START TRANSACTION;
    	BEGIN
    	    DECLARE cur CURSOR FOR SELECT A FROM Relazione WHERE Da= DaR;
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
        DELETE FROM Relazione WHERE Da= DaR;
    COMMIT;
END $

DROP PROCEDURE IF EXISTS removeRelazioneA $
CREATE PROCEDURE removeRelazioneA  ( IN AR  INT(5))
BEGIN
    DECLARE next INT(5);
    DECLARE done INT(1) DEFAULT 0;
    START TRANSACTION;
        BEGIN
    	    DECLARE cur CURSOR FOR SELECT Da FROM Relazione WHERE A= AR;
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
        DELETE FROM Relazione WHERE A= AR;
    COMMIT;
END $
