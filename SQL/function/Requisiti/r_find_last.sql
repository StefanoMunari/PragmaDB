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

/*CONTRATTO DI findLastSibling:
    PRE= (IdRequisito è un Requisito che possiede un Padre NOT NULL) 
    IdRequisito(string) ==> LastSibling(string) se esiste
                        ==> NULL se non esiste
    t.c. è sempre l'ultimo fratello dx corrispondente al livello in cui si trova IdRequisito
    ANCHE SE I FRATELLI nel livello interessato NON SONO CONTIGUI, altrimenti NULL
*/
DROP FUNCTION IF EXISTS findLastSibling $
CREATE FUNCTION findLastSibling ( IdRequisito VARCHAR(20)) RETURNS VARCHAR(20)
BEGIN
    DECLARE hierarchyId VARCHAR(20);
    DECLARE postfixId INT(5);
    DECLARE nextSibling VARCHAR(20);
    DECLARE lastSibling VARCHAR(20);
    DECLARE done INT DEFAULT 0;
    SELECT postfixId(IdRequisito) INTO postfixId;/* seleziona SEMPRE TUTTE le ultime cifre DOPO L'ULTIMO PUNTO che mi servono per trovare l'ultimo successore */
    SELECT hierarchyId(IdRequisito) INTO hierarchyId;
    BEGIN
        DECLARE cursorSibling CURSOR FOR 
        SELECT r.IdRequisito 
        FROM 
            Requisiti r
        WHERE 
            hierarchyId = (SELECT hierarchyId(r.IdRequisito))/*devono avere lo stesso livello gerarchico*/
            AND
            postfixId < (SELECT postfixId(r.IdRequisito));/*prendo tutti i fratelli "maggiori"*/
                
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
END$

/*CONTRATTO DI findLastRoot:

    PRE= (Tipo è un tipo appartenente all'insieme di tipi definito in Requisiti) 
    Tipo(string) ==> LasRoot(string) se esiste
                 ==> NULL se non esiste 
    t.c. LastRoot è sempre l'ultima radice dx, altrimenti NULL
*/
DROP FUNCTION IF EXISTS findLastRoot $
CREATE FUNCTION findLastRoot ( Tipo VARCHAR(13)) RETURNS VARCHAR(20)
BEGIN
    DECLARE done INT(1) DEFAULT 0;
    DECLARE LastRoot VARCHAR(20) DEFAULT NULL;
    DECLARE nextRoot VARCHAR(20) DEFAULT NULL;
    DECLARE cursorRoot CURSOR FOR SELECT r.IdRequisito FROM Requisiti r WHERE r.Padre IS NULL AND r.Tipo = Tipo ORDER BY CONVERT(SUBSTRING(r.IdRequisito,4),UNSIGNED INTEGER);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cursorRoot;

        FETCH cursorRoot INTO lastRoot;
        IF done < 1 
        THEN
            REPEAT
              FETCH cursorRoot INTO nextRoot;
                IF CONVERT(SUBSTRING(lastRoot,4),UNSIGNED INTEGER) < CONVERT(SUBSTRING(nextRoot,4),UNSIGNED INTEGER)
                    THEN
                        SET lastRoot = nextRoot;
                END IF;
            UNTIL done 
            END REPEAT;
        END IF;
        CLOSE cursorRoot;
    RETURN lastRoot;
END $