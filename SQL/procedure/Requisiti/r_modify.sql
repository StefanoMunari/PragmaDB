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

/*use TreeFunctions*/
/*se Padre = 0 ->non è cambiato
  se Padre =NULL ->cambiato,diventa radice
  se Padre =ID ->cambiato
  se UCCorrelati= '' -> la lista è stata cambiata in vuota;
  se UCCorrelati= NULL -> non è stata cambiata
 solo Padre,Tipo, Importanza, UCcorrelati possono essere NULL, gli altri vengono sempre passati anche se non cambiano
 Modified= indica se modificato almeno un campo diverso dai 4 sopra indicati(cioè Descrizione,Stato,Implementato e Fonte*/
DROP PROCEDURE IF EXISTS modifyRequisito $
CREATE PROCEDURE modifyRequisito ( IN CodAuto   INT(5), Descrizione VARCHAR(10000), Tipo  ENUM('Funzionale','Vincolo','Qualita','Prestazionale'), Importanza  ENUM('Obbligatorio','Desiderabile','Facoltativo'), Padre INT(5), Stato  BOOL , Implementato BOOL , Soddisfatto  BOOL, Fonte VARCHAR(10), Utente VARCHAR(4), Modified BOOLEAN, UCCorrelati VARCHAR(1000))
BEGIN/*se CodAuto non coincide -> l'update non fa nulla*/
    DECLARE last VARCHAR(20);
    DECLARE legal TINYINT(1) DEFAULT 1;
    START TRANSACTION;
    SET last= findLastSibling((SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto=CodAuto));
    IF Tipo IS NOT NULL
        THEN
            CALL changeTipo(CodAuto,Tipo,NULL,Utente);/*richiama insertRT*/
            CALL rearrangeTree((SELECT r.CodAuto FROM Requisiti r WHERE r.IdRequisito= last),(SELECT r.Padre FROM Requisiti r WHERE r.IdRequisito= last));
    END IF;
    IF Padre IS NULL OR Padre > 0
    THEN
        IF Padre IS NULL /*è un figlio che si stacca dal padre e diventa radice*/
            THEN
                CALL changeParent(CodAuto,Padre,Utente);/*richiama insertRT*/
                CALL rearrangeTree((SELECT r.CodAuto FROM Requisiti r WHERE r.IdRequisito= last),(SELECT r.Padre FROM Requisiti r WHERE r.IdRequisito= last));
            ELSE/*PADRE NOT NULL and <>0*/
                IF legalParent(CodAuto,Padre)
                    THEN
                        CALL changeParent(CodAuto,Padre,Utente);/*richiama insertRT*/
                        CALL rearrangeTree((SELECT r.CodAuto FROM Requisiti r WHERE r.IdRequisito= last),(SELECT r.Padre FROM Requisiti r WHERE r.IdRequisito= last));
                END IF;    
        END IF;
    END IF;
    IF Modified
        THEN
            UPDATE Requisiti r 
            SET r.Descrizione = Descrizione, r.Stato = Stato, r.Implementato = Implementato, r.Soddisfatto = Soddisfatto, r.Fonte = Fonte
            WHERE r.CodAuto = CodAuto;
    END IF;
    IF Importanza IS NOT NULL
        THEN
            CALL changeImportanza(CodAuto,Importanza,Utente);
            SET Modified = TRUE;
    END IF;
    IF UCCorrelati IS NOT NULL
     THEN
        IF UCCorrelati <> ''
            THEN
                CALL legalListUC(legal,UCCorrelati);
                IF legal
                    THEN
                        DELETE FROM RequisitiUC WHERE CodReq= CodAuto;
                        CALL insertListUC(CodAuto,UCCorrelati);
                        SET Modified= TRUE;/*traccio per timestamp*/
                END IF;
            ELSE
                CALL updateUC_on_delete(CodAuto);/*aggiorno timestamp di tutti gli UC correlati*/
                DELETE FROM RequisitiUC WHERE CodReq= CodAuto;
                SET Modified= TRUE;/*garantisce di aggiornare timestamp del Requisito*/
            END IF;
    END IF;
    IF Modified/*in questo caso so che devo aggiornare ReqTracking per quel Requisito*/
        THEN
            CALL insertRT(CodAuto,Utente);
    END IF;
    CALL sortForest('Requisiti');
    COMMIT;
END $