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
/*

Pre= CodMet contiene un valore valido
	 TestL contiene una lista di valori interi (anche non validi) formati però dalla seguente sintassi:
	 CodAuto1,CodAuto2,CodAuto3,
	 */
DROP PROCEDURE IF EXISTS insertMetodiTest $
CREATE PROCEDURE insertMetodiTest  ( IN CodMetR INT(10), TestL VARCHAR(1000))
BEGIN
	DECLARE legal TINYINT DEFAULT 1;
	DECLARE CodTestR INT(10) DEFAULT NULL;
	DECLARE CodClass INT(5) DEFAULT NULL;

	CALL legalTestList(legal,TestL);
	IF CodMetR IS NOT NULL AND legal >0 /*controllo in più per CodMetR*/
	THEN
        WHILE TestL <> ''
        DO
            CALL parseIdList(TestL,',',CodTestR);/*si trova in UseCase->utility->uc_FK_utility*/
            INSERT INTO TestMetodi(CodTest,CodMet)
            VALUES (CodTestR,CodMetR);
            CALL updateTestTime(CodTestR);
        END WHILE;
        SELECT m.Classe INTO CodClass FROM Metodo m WHERE m.CodAuto=CodMetR;
	IF(CodClass IS NOT NULL)
	    THEN
	        CALL updateClasseTime(CodClass);
	END IF;
    END IF;
END $

DROP PROCEDURE IF EXISTS insertTestMetodi $
CREATE PROCEDURE insertTestMetodi  ( IN CodTestR INT(10), MetL VARCHAR(1000))
BEGIN
	DECLARE legal TINYINT DEFAULT 1;
	DECLARE CodMetR INT(10) DEFAULT NULL;
	DECLARE CodClass INT(5) DEFAULT NULL;

	CALL legalMetList(legal,MetL);
	IF CodTestR IS NOT NULL AND legal >0 /*controllo in più per CodTestR*/
	THEN
        WHILE MetL <> ''
        DO
            CALL parseIdList(MetL,',',CodMetR);/*si trova in UseCase->utility->uc_FK_utility*/
            INSERT INTO TestMetodi(CodTest,CodMet)
            VALUES (CodTestR,CodMetR);
            SELECT m.Classe INTO CodClass FROM Metodo m WHERE m.CodAuto=CodMetR;
            IF(CodClass IS NOT NULL)
	        THEN
	            CALL updateClasseTime(CodClass);
	    END IF;
        END WHILE;
        CALL updateTestTime(CodTestR);
    END IF;
END $
