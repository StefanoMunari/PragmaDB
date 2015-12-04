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
Pre= ClSubject contiene 1 valore non vuoto corrispondente a 1 Classe(CodAuto), 
ClList contiene una lista non vuota formata dalla seguente sintassi:
CodAuto1,CodAuto2,CodAuto3,
Quindi una lista di Classe correlati a ClSubject.
All'interno della procedura viene controllato che tutti gli elementi della lista esistano prima di essere inseriti
*/
DROP PROCEDURE IF EXISTS insertEreditaDa $
CREATE PROCEDURE insertEreditaDa ( IN ClSubject INT(5), ClList VARCHAR(1000))/*ClSubject è figlio*/
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE CodCl INT(5) DEFAULT 0;
    START TRANSACTION;
        CALL legalClasseList(legal,ClList);
        IF legal > 0
        THEN
            WHILE ClList <> ''
            DO
                CALL parseIdList(ClList,',',CodCl);/*si trova in UseCase->utility->uc_FK_utility*/
                INSERT INTO EreditaDa(Padre,Figlio)
                VALUES (CodCl,ClSubject);
                CALL updateClasseTime(CodCl);/*aggiorna Time di CodCl, cioè di tutti le classi nella lista correlata*/
            END WHILE;
            CALL updateClasseTime(ClSubject);/*aggiorna Time di Classe Subject*/
        END IF;
    COMMIT;
END $


DROP PROCEDURE IF EXISTS insertEreditataDa $
CREATE PROCEDURE insertEreditataDa ( IN ClSubject INT(5), ClList VARCHAR(1000))/*ClSubject è Padre*/
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE CodCl INT(5) DEFAULT 0;
    START TRANSACTION;
        CALL legalClasseList(legal,ClList);
        IF legal > 0
        THEN
            WHILE ClList <> ''
            DO
                CALL parseIdList(ClList,',',CodCl);/*si trova in UseCase->utility->uc_FK_utility*/
                INSERT INTO EreditaDa(Padre,Figlio)
                VALUES (ClSubject,CodCl);
                CALL updateClasseTime(CodCl);/*aggiorna Time di CodCl, cioè di tutti le classi nella lista correlata*/
            END WHILE;
            CALL updateClasseTime(ClSubject);/*aggiorna Time di Classe Subject*/
        END IF;
    COMMIT;
END $