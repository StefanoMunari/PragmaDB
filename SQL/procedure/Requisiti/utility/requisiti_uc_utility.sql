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

DROP PROCEDURE IF EXISTS legalListUC $
CREATE PROCEDURE legalListUC ( INOUT legal TINYINT, IN UCL VARCHAR(1000)) 
BEGIN
    DECLARE CodAuto INT(5) DEFAULT NULL;
    WHILE legal AND UCL <> ''
        DO
            CALL parseIdList(UCL,',',CodAuto);
            SELECT COUNT(*) FROM UseCase r WHERE r.CodAuto= CodAuto INTO legal; 
    END WHILE;
END $

DROP PROCEDURE IF EXISTS insertListUC $
CREATE PROCEDURE insertListUC ( IN CodReq VARCHAR(20), IN UCL VARCHAR(1000)) 
BEGIN
    DECLARE CodUC INT(5) DEFAULT 0;
    WHILE UCL <> ''
        DO
            CALL parseIdList(UCL,',',CodUC);
            CALL insertRequisitoUC(CodReq,CodUC);
            UPDATE UseCase SET Time= CURRENT_TIMESTAMP WHERE CodAuto= CodUC;
    END WHILE;
END $

DROP PROCEDURE IF EXISTS updateUC_on_delete $
CREATE PROCEDURE updateUC_on_delete (IN Cod INT(5))
BEGIN
    UPDATE UseCase SET Time= CURRENT_TIMESTAMP WHERE CodAuto= ANY (SELECT r.UC FROM RequisitiUC r WHERE r.CodReq= Cod);
END $