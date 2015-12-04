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

DROP PROCEDURE IF EXISTS changeParent $
CREATE PROCEDURE changeParent( IN CodAuto INT(5), Padre INT(5), Utente VARCHAR(4))
BEGIN
    DECLARE IdRequisito VARCHAR(20);
    DECLARE oldId VARCHAR(20);
    DECLARE ind INT(2) DEFAULT 1;
    SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto= CodAuto INTO oldId;
    IF Padre IS NOT NULL
        THEN
            SET IdRequisito = findSiblingGap(Padre,(SELECT r.Importanza FROM Requisiti r WHERE r.CodAuto = CodAuto));
        ELSE
            SET IdRequisito = findRootGap((SELECT r.Tipo FROM Requisiti r WHERE r.CodAuto=CodAuto),(SELECT r.Importanza FROM Requisiti r WHERE r.CodAuto=CodAuto));
    END IF;
    START TRANSACTION;
        SET FOREIGN_KEY_CHECKS=0;
        UPDATE Requisiti r SET r.IdRequisito= IdRequisito, r.Padre= Padre WHERE r.IdRequisito = oldId;
        SET FOREIGN_KEY_CHECKS=1;
        CALL insertRT(CodAuto,Utente);
    COMMIT;
    WHILE (SELECT COUNT(*) FROM Requisiti r WHERE r.IdRequisito = CONCAT(oldId,'.',ind)) > 0
        DO
            CALL changeParent((SELECT r.CodAuto FROM Requisiti r WHERE r.IdRequisito = CONCAT(oldId,'.',ind)),CodAuto,Utente);
            SET ind = ind +1;
    END WHILE;
END $

DROP PROCEDURE IF EXISTS changeTipo $
CREATE PROCEDURE changeTipo (CodAuto INT(5), Tipo ENUM('Funzionale','Vincolo','Qualita','Prestazionale'), Padre INT(5), Utente VARCHAR(4))
BEGIN 
    DECLARE newId VARCHAR(20);
    DECLARE oldId VARCHAR(20);
    DECLARE ind INT(2) DEFAULT 1;
    SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto = CodAuto INTO oldId;
    IF PADRE IS NULL
        THEN
            SET newId= findRootGap(Tipo,(SELECT r.Importanza FROM Requisiti r WHERE r.CodAuto = CodAuto));
        ELSE
            SET newId= findSiblingGap(Padre,(SELECT r.Importanza FROM Requisiti r WHERE r.CodAuto = CodAuto));
    END IF;
    START TRANSACTION;
        SET FOREIGN_KEY_CHECKS=0;
            UPDATE Requisiti r
            SET r.IdRequisito = newId, r.Padre= Padre, r.Tipo= Tipo
            WHERE r.CodAuto = CodAuto;
        SET FOREIGN_KEY_CHECKS=1;
        CALL insertRT(CodAuto,Utente);
    COMMIT;
    WHILE (SELECT COUNT(*) FROM Requisiti r WHERE r.IdRequisito = CONCAT(oldId,'.',ind)) > 0
        DO
            CALL changeTipo((SELECT r.CodAuto FROM Requisiti r WHERE r.IdRequisito = CONCAT(oldId,'.',ind)),Tipo,CodAuto,Utente);
            SET ind = ind +1;
    END WHILE;    
END$ 

DROP PROCEDURE IF EXISTS changeImportanza $
CREATE PROCEDURE changeImportanza (CodAuto INT(5), Importanza ENUM('Obbligatorio','Desiderabile','Facoltativo'), Utente VARCHAR(4))
BEGIN 
    DECLARE newId VARCHAR(20);
    START TRANSACTION;
        SET newId= (SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto= CodAuto);
        SET newId= CONCAT(LEFT(newId,2),LEFT(Importanza,1),SUBSTRING(newId FROM 4));
        SET FOREIGN_KEY_CHECKS=0;
            UPDATE Requisiti r 
            SET r.Importanza= Importanza, r.IdRequisito= newId 
            WHERE r.CodAuto= CodAuto;
        SET FOREIGN_KEY_CHECKS=1;
    COMMIT;
END$
