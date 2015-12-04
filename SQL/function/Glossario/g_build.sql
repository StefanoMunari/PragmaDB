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

DROP FUNCTION IF EXISTS buildIdGlossario $
CREATE FUNCTION buildIdGlossario (Identificativo VARCHAR(50)) RETURNS VARCHAR(3)
BEGIN
DECLARE db VARCHAR(4);
DECLARE id VARCHAR(4) DEFAULT 'OO';
DECLARE lastgoodid VARCHAR(4);
DECLARE done INT(1) DEFAULT 0;
DECLARE cursorRoot CURSOR FOR SELECT g.IdTermine FROM Glossario g WHERE (LEFT(Identificativo,1) = LEFT(g.IdTermine,1)) ORDER BY LEFT(g.IdTermine,1),CONVERT((SUBSTRING(g.IdTermine,2)),UNSIGNED INT);
DECLARE cursorRep CURSOR FOR SELECT g.IdTermine FROM Glossario g WHERE (SUBSTRING(g.IdTermine,-1) = 'n') ORDER BY g.IdTermine;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    OPEN cursorRoot;

    FETCH cursorRoot INTO db;
    IF done < 1 
        THEN
            REPEAT
                IF (Identificativo < (SELECT g.Identificativo FROM Glossario g WHERE g.IdTermine=db))
                    THEN
                        SET id = db;
                        REPEAT
                            UPDATE Glossario g SET g.IdTermine=(CONCAT((LEFT(db,1)),(CONVERT((SUBSTRING(db,2)),UNSIGNED INT)+1),('n'))) WHERE g.IdTermine=db;
                            FETCH cursorRoot INTO db;
                        UNTIL done
                        END REPEAT;
                    ELSE
                        SET lastgoodid = db;
                        FETCH cursorRoot INTO db;
                END IF;
            UNTIL done 
            END REPEAT;
        ELSE
            SET id= CONCAT((LEFT(Identificativo,1)),'1');
    END IF;
    CLOSE cursorRoot;
    SET done = 0;
    OPEN cursorRep;
    FETCH cursorRep INTO db;
    IF done < 1
        THEN
            REPEAT
                UPDATE Glossario g SET g.IdTermine = LEFT(g.IdTermine,LENGTH(g.IdTermine)-1) WHERE g.IdTermine=db;
                FETCH cursorRep INTO db;
            UNTIL done
            END REPEAT;
    END IF;
    IF id='OO'
        THEN
            RETURN (CONCAT((SUBSTRING(Identificativo,1,1)),(CONVERT((SUBSTRING(lastgoodid,2)),UNSIGNED INT)+1)));
        ELSE
            RETURN id;
    END IF;
END $
