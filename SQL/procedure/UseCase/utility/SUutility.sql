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

DROP PROCEDURE IF EXISTS insertIdRequisitoIdUC $
CREATE PROCEDURE insertIdRequisitoIdUC ( IdReq VARCHAR(20),UC VARCHAR(20))
BEGIN
    DECLARE CodReq INT(5) DEFAULT NULL;
    DECLARE CodU INT(5) DEFAULT NULL;
    SELECT CodAuto FROM Requisiti WHERE IdRequisito= IdReq INTO CodReq;
    SELECT CodAuto FROM UseCase WHERE IdUC= UC INTO CodU;
    IF CodReq IS NOT NULL AND CodU IS NOT NULL
        THEN
            INSERT INTO RequisitiUC(CodReq,UC)
            VALUES (CodReq,CodU);
    END IF;
END $


DROP PROCEDURE IF EXISTS SUinsertRQ $
CREATE PROCEDURE SUinsertRQ(
    IdU VARCHAR(20),
    List VARCHAR(1000)/*lista di Id separati da virgola*/
) 
BEGIN
    DECLARE Result VARCHAR(20) DEFAULT NULL;
    WHILE List <> ''
        DO
            START TRANSACTION;
            CALL parseIdList(List,',',Result);
            CALL insertIdRequisitoIdUC(Result,IdU);
            COMMIT;
    END WHILE;
END $