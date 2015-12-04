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

DROP FUNCTION IF EXISTS uc_findLastSibling $
CREATE FUNCTION uc_findLastSibling (Padre INT(5) )
	RETURNS VARCHAR(20)
BEGIN
    DECLARE nextSibling VARCHAR(20);
    DECLARE lastSibling VARCHAR(20);
    DECLARE done INT DEFAULT 0;
    BEGIN
        DECLARE cursorSibling CURSOR FOR 
        SELECT u.IdUC 
        FROM 
            UseCase u
        WHERE 
           u.Padre= Padre
        ORDER BY
        postfixId(u.IdUC);/*prendo tutti i fratelli ordinati*/
                
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cursorSibling;

        FETCH cursorSibling INTO lastSibling;
        IF done < 1 
        THEN
            REPEAT
              FETCH cursorSibling INTO nextSibling;
                IF (SELECT postfixId(lastSibling) ) < (SELECT postfixId(nextSibling) )
                    THEN
                        SET lastSibling = nextSibling;
                END IF;
            UNTIL done 
            END REPEAT;
        END IF;
        CLOSE cursorSibling;
        END;
    RETURN lastSibling;
END $

DROP FUNCTION IF EXISTS uc_findLastRoot $
CREATE FUNCTION uc_findLastRoot () RETURNS VARCHAR(20)
BEGIN
    DECLARE done INT(1) DEFAULT 0;
    DECLARE LastRoot VARCHAR(20) DEFAULT NULL;
    DECLARE nextRoot VARCHAR(20) DEFAULT NULL;
    DECLARE cursorRoot CURSOR FOR SELECT u.IdUC FROM UseCase u WHERE u.Padre IS NULL ORDER BY postfixId(u.IdUC);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cursorRoot;

    FETCH cursorRoot INTO lastRoot;
    IF done < 1 
    THEN
        REPEAT
          FETCH cursorRoot INTO nextRoot;
            IF (SELECT postfixId(lastRoot) ) < (SELECT postfixId(nextRoot) )
                THEN
                    SET lastRoot = nextRoot;
            END IF;
        UNTIL done 
        END REPEAT;
    END IF;
    CLOSE cursorRoot;
    RETURN lastRoot;
END $