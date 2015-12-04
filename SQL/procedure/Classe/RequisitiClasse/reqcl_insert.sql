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

Pre= CodReq contiene un valore valido
	 ClassL contiene una lista di valori interi (anche non validi) formati però dalla seguente sintassi:
	 CodAuto1,CodAuto2,CodAuto3,
	 */
DROP PROCEDURE IF EXISTS insertRequisitiClasse $
CREATE PROCEDURE insertRequisitiClasse  ( IN CodReq INT(5), ClassL VARCHAR(1000))
BEGIN
	DECLARE legal TINYINT DEFAULT 1;
	DECLARE CodClass INT(5) DEFAULT NULL;

	CALL legalClasseList(legal,ClassL);
	IF CodReq IS NOT NULL AND legal >0 /*controllo in più per CodReq*/
	THEN
        WHILE ClassL <> ''
        DO
            CALL parseIdList(ClassL,',',CodClass);/*si trova in UseCase->utility->uc_FK_utility*/
            INSERT INTO RequisitiClasse(CodReq,CodClass)
            VALUES (CodReq,CodClass);
            CALL updateClasseTime(CodClass);
        END WHILE;
        CALL automatizeRequisitiPackage();
    END IF;
END $

DROP PROCEDURE IF EXISTS insertClasseRequisiti $
CREATE PROCEDURE insertClasseRequisiti  ( IN Class INT(5), ReqL VARCHAR(1000))
BEGIN
	DECLARE legal TINYINT DEFAULT 1;
	DECLARE CodReq INT(5) DEFAULT NULL;

	CALL legalListRequisiti(legal,ReqL);
	IF Class IS NOT NULL AND legal >0 /*controllo in più per Class*/
	THEN
        WHILE ReqL <> ''
        DO
            CALL parseIdList(ReqL,',',CodReq);/*si trova in UseCase->utility->uc_FK_utility*/
            INSERT INTO RequisitiClasse(CodClass,CodReq)
            VALUES (Class,CodReq);
        END WHILE;
        CALL updateClasseTime(Class);
        CALL automatizeRequisitiPackage();
    END IF;
END $