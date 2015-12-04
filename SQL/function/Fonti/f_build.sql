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

DROP FUNCTION IF EXISTS buildIdFonte $
CREATE FUNCTION buildIdFonte () RETURNS VARCHAR(10)
BEGIN
DECLARE id VARCHAR(10);
DECLARE next VARCHAR(10);
DECLARE done INT(1) DEFAULT 0;
DECLARE cursorRoot CURSOR FOR SELECT f.IdFonte FROM Fonti f ORDER BY f.IdFonte;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cursorRoot;

    FETCH cursorRoot INTO id;
    IF done < 1 
    THEN
        REPEAT
          FETCH cursorRoot INTO next;
            IF  ((SELECT CONVERT((SELECT SUBSTRING(id,2)),UNSIGNED INT) ) < (SELECT CONVERT((SELECT SUBSTRING(next,2)),UNSIGNED INT) ))
                THEN
                    SET id = next;
            END IF;
        UNTIL done 
        END REPEAT;
    ELSE
        SET id='F0';
    END IF;
    CLOSE cursorRoot;
RETURN (SELECT CONCAT('F',(SELECT CONVERT((SELECT SUBSTRING(id,2)),UNSIGNED INT)+1)));
END $