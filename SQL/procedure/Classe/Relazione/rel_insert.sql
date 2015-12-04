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
Pre= Da contiene 1 valore non vuoto corrispondente a 1 Classe(CodAuto), 
A contiene una lista non vuota formata dalla seguente sintassi:
CodAuto1,CodAuto2,CodAuto3,
All'interno della procedura viene controllato che tutti gli elementi della lista esistano prima di essere inseriti
*/
DROP PROCEDURE IF EXISTS insertRelazioneDa $
CREATE PROCEDURE insertRelazioneDa ( IN Da  INT(5), AList  VARCHAR(1000))
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE CodCl INT(5) DEFAULT 0;
    IF ((SELECT COUNT(p.CodAuto) FROM Classe p WHERE p.CodAuto= Da)> 0) AND ((SELECT COUNT(p.CodAuto) FROM Classe p WHERE p.CodAuto= AList) > 0)
    THEN
    START TRANSACTION;
        CALL legalClasseList(legal,AList);
        IF legal > 0
        THEN
            WHILE AList <> ''
            DO
                CALL parseIdList(AList,',',CodCl);/*si trova in UseCase->utility->uc_FK_utility*/
                INSERT INTO Relazione(Da,A)
                VALUES  (Da,CodCl);
                CALL updateClasseTime(CodCl);
            END WHILE;
            CALL updateClasseTime(Da);
        COMMIT;
	END IF;
    END IF;
END $

DROP PROCEDURE IF EXISTS insertRelazioneA $
CREATE PROCEDURE insertRelazioneA ( IN A INT(5), DaList VARCHAR(1000))
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE CodCl INT(5) DEFAULT 0;
    IF ((SELECT COUNT(p.CodAuto) FROM Classe p WHERE p.CodAuto= DaList)> 0) AND ((SELECT COUNT(p.CodAuto) FROM Classe p WHERE p.CodAuto= A) > 0)
    THEN
    START TRANSACTION;
        CALL legalClasseList(legal,DaList);
        IF legal > 0
        THEN
            WHILE DaList <> ''
            DO
                CALL parseIdList(DaList,',',CodCl);/*si trova in UseCase->utility->uc_FK_utility*/
                INSERT INTO Relazione(Da,A)
                VALUES  (CodCl,A);
                CALL updateClasseTime(CodCl);
            END WHILE;
            CALL updateClasseTime(A);
        COMMIT;
        END IF;
    END IF;
END $
