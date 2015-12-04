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

DROP FUNCTION IF EXISTS hierarchyId $
CREATE FUNCTION hierarchyId (id VARCHAR(20) ) RETURNS VARCHAR(20)
BEGIN
    IF LOCATE('.',id) >0
        THEN
            RETURN CONCAT( LEFT(id,2),SUBSTRING(id,4,(LENGTH(id)- LOCATE('.',REVERSE(id)) -3)));
        ELSE/*radice*/
            RETURN LEFT(id,2);
    END IF;
END $

/*ritorna sempre una stringa che corrisponde al prefisso dell'IdRequisito (cioè tutto l'id escluso l'ultimo "." e il numero che si trova dopo esso)*/
DROP FUNCTION IF EXISTS prefixId $
CREATE FUNCTION prefixId (id VARCHAR(20) ) RETURNS VARCHAR(20)
BEGIN
    IF LOCATE('.',id) >0
        THEN
            RETURN (SELECT LEFT(
                            id,
                            (
                                (SELECT LENGTH(id))-
                                (SELECT LOCATE('.',REVERSE(id)))
                            )
                        )
                );
        ELSE/*radice*/
            RETURN LEFT(id,3);
    END IF;
END $


/*ritorna sempre un numero che corrisponde al numero postfisso dell'IdRequisito*/
DROP FUNCTION IF EXISTS postfixId $
CREATE FUNCTION postfixId (id VARCHAR(20) ) RETURNS INT(5)
BEGIN
    DECLARE postfixInt INT(5);
    IF (SELECT LOCATE('.',id)) > 0
    THEN/*è un figlio*/
        SET postfixInt= CONVERT(
                        (SELECT RIGHT(
                                    id, 
                                    (SELECT LOCATE('.',(REVERSE(id)))-1)
                                )
                        ),
                        UNSIGNED INTEGER
                        );
    ELSE/*è una radice*/
        SET postfixInt= SUBSTRING(id FROM locateFirstInt(id));/*è una radice->posso invocare locateFirstInt()*/
    END IF;
    RETURN postfixInt;
END $

/*CONTRATTO DI locateFirstInt

    PRE= (id è UN NODO RADICE,vale a dire che non contiene altri numeri oltre a quello che lo identifica rispetto alle altre radici della foresta)
    id(string) ==> position(int)
               ==> position(int) == 0
    se trova la posizione tra le prime 100 restituisce un indice >0
    altrimenti restituisce un indice = 0 che non indica nessuna posizione in una stringa
*/
DROP FUNCTION IF EXISTS locateFirstInt $
CREATE FUNCTION locateFirstInt (id VARCHAR(20) ) RETURNS INT(3)
BEGIN
    DECLARE position INT(3) DEFAULT 0;
    DECLARE ind INT(3) DEFAULT 1;
    WHILE position = 0 AND ind <100 DO
        SELECT LOCATE(ind,id) INTO position;
        SET ind = ind+1;
    END WHILE;
    RETURN position;
END $

/*ritorna il numero di occorrenze di element in idString*/
DROP FUNCTION IF EXISTS occurrencesInString $
CREATE FUNCTION occurrencesInString (idString VARCHAR(20), element VARCHAR(5) ) RETURNS INT(2)
BEGIN
    RETURN 
        (SELECT ROUND(((SELECT LENGTH(idString)) - (SELECT LENGTH( (SELECT REPLACE ( idString, element, "")) ))) / (SELECT LENGTH(element))));
END $

/*
CONTRATTO DI generateIdRequisito
NB:l'integrità dei parametri deve essere garantita,cioè devono essere parametri ragionevoli Tipo e Importanza devono coincidere con quelli disponibili
chiamata solo in caso di necessità di costruire un requisito da zero 
-> crea un IdRequisito nel senso che questo Tipo di requisito non era 
precedentemente presente nel DB*/
DROP FUNCTION IF EXISTS generateIdRequisito $
CREATE FUNCTION generateIdRequisito ( Tipo VARCHAR(13), Importanza VARCHAR(12)) RETURNS VARCHAR(20)
BEGIN
    DECLARE Id VARCHAR(20);
    SELECT CONCAT('R',(SELECT LEFT(Tipo,1)),(SELECT LEFT(Importanza,1)),'1') INTO Id;
    RETURN Id;
END $

DROP FUNCTION IF EXISTS r_legalKinship $
CREATE FUNCTION r_legalKinship(IdReq VARCHAR(20), newPadre VARCHAR(20)) RETURNS TINYINT
BEGIN
    RETURN NOT (IdReq = LEFT(newPadre,LENGTH(IdReq)));
END $