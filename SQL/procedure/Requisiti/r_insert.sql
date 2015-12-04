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

DROP PROCEDURE IF EXISTS insertRequisito $
CREATE PROCEDURE insertRequisito ( IN Utente VARCHAR(4), Descrizione VARCHAR(10000), Tipo ENUM('Funzionale','Vincolo','Qualita','Prestazionale'), Importanza  ENUM('Obbligatorio','Desiderabile','Facoltativo'), Padre INT(5), Stato BOOL, Implementato BOOL, Soddisfatto  BOOL, IdFonte INT(5), UCCorrelati VARCHAR(1000))
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE IdRequisito VARCHAR(20);
    START TRANSACTION;
    IF Padre IS NOT NULL THEN 
        SET legal = existPadreRequisito(Padre);/* se il Padre è NULL || esiste in Requisiti -> legal >0 */
    END IF;

    CALL legalListUC(legal,UCCorrelati);

    IF legal THEN
        SELECT COUNT(*) FROM Fonti f WHERE f.CodAuto = IdFonte INTO legal;/* se IdFonte non corrisponde a nessun Fonte esistente -> legal =0*/
        IF legal THEN 
            SET IdRequisito = buildIdRequisito(Tipo,Importanza,Padre);
            INSERT INTO Requisiti(IdRequisito, Descrizione, Tipo, Importanza, Padre, Stato, Implementato, Fonte , Soddisfatto)
                VALUES (IdRequisito, Descrizione, Tipo, Importanza, Padre, Stato, Implementato, IdFonte , Soddisfatto);
            INSERT INTO ReqTracking( IdTrack, Descrizione, Tipo, Importanza, Padre, Stato, Implementato, Fonte, CodAuto, Utente, Time , Soddisfatto)
                VALUES (CONCAT(LAST_INSERT_ID(),"v0"), Descrizione, Tipo, Importanza, Padre, Stato, Implementato, IdFonte, LAST_INSERT_ID(),Utente, CURRENT_TIMESTAMP , Soddisfatto);
            /*ATTENZIONE, uso CODAUTO DI Requisito*/
            SET IdRequisito= LAST_INSERT_ID();
            CALL insertListUC(IdRequisito,UCCorrelati);
            CALL sortForest('Requisiti');
        END IF;
    END IF;
    COMMIT;
END $