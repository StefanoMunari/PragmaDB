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

DROP PROCEDURE IF EXISTS insertRequisitoUC $
CREATE PROCEDURE insertRequisitoUC ( CodReq INT(5),UC INT(5))
BEGIN
	INSERT INTO RequisitiUC(CodReq,UC)
	VALUES (CodReq,UC);
END $

DROP PROCEDURE IF EXISTS legalListRequisiti $
CREATE PROCEDURE legalListRequisiti ( INOUT legal TINYINT, IN ReqL VARCHAR(1000)) 
BEGIN
	DECLARE CodAuto INT(5) DEFAULT NULL;
	WHILE legal AND ReqL <> ''
        DO
            CALL parseIdList(ReqL,',',CodAuto);
            SELECT COUNT(*) FROM Requisiti r WHERE r.CodAuto= CodAuto INTO legal; 
    END WHILE;
END $

DROP PROCEDURE IF EXISTS insertListRequisiti $
CREATE PROCEDURE insertListRequisiti ( IN CodUC VARCHAR(20), IN ReqL VARCHAR(1000), IN Utente VARCHAR(4)) 
BEGIN
	DECLARE CodReq INT(5) DEFAULT 0;
	WHILE ReqL <> ''/*RequisitiUC*/
	    DO
	        CALL parseIdList(ReqL,',',CodReq);
	        CALL insertRequisitoUC(CodReq,CodUC);
	        CALL insertRT(CodReq,Utente);
	END WHILE;
END $

DROP PROCEDURE IF EXISTS updateReq_on_delete $
CREATE PROCEDURE updateReq_on_delete (IN Cod INT(5), IN Utente VARCHAR(4))
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE next INT(5);
    DECLARE cur CURSOR FOR
    SELECT r.CodReq FROM RequisitiUC r WHERE r.UC= Cod;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    IF done < 1 
	    THEN
	        FETCH cur INTO next;
	          WHILE done < 1
	                DO
	                    CALL insertRT(next,Utente);
	                    FETCH cur INTO next;
	        END WHILE;
    END IF;
    CLOSE cur;
END $