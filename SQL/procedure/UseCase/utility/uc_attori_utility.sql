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

DROP PROCEDURE IF EXISTS insertAttoreUC $
CREATE PROCEDURE insertAttoreUC ( Attore INT(5),UC  INT(5))
BEGIN
    INSERT INTO AttoriUC(Attore,UC)
    VALUES (Attore,UC);
END $

DROP PROCEDURE IF EXISTS legalListAttori $
CREATE PROCEDURE legalListAttori ( INOUT legal TINYINT, IN AttoriL VARCHAR(60)) 
BEGIN
	DECLARE CodAuto INT(5) DEFAULT NULL;
    WHILE legal AND AttoriL <> ''
        DO
            CALL parseIdList(AttoriL,',',CodAuto);
            SELECT COUNT(*) FROM Attori a WHERE a.CodAuto= CodAuto INTO legal; 
    END WHILE;
END $

DROP PROCEDURE IF EXISTS insertListAttori $
CREATE PROCEDURE insertListAttori ( IN CodUC VARCHAR(20), IN AttoriL VARCHAR(60)) 
BEGIN
	DECLARE CodAttore INT(5) DEFAULT 0;
	WHILE AttoriL <> ''/*AttoriUC*/
        DO
            CALL parseIdList(AttoriL,',',CodAttore);
            CALL insertAttoreUC(CodAttore,CodUC);
    END WHILE;
END $