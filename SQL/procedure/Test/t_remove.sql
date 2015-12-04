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

DROP PROCEDURE IF EXISTS removeTest $
CREATE PROCEDURE removeTest ( IN Cod INT(10))
BEGIN
    DECLARE TipoR ENUM('Validazione','Sistema','Integrazione','Unita','Regressione') DEFAULT NULL;
    DECLARE oldId VARCHAR(22) DEFAULT NULL;
    START TRANSACTION;
        SELECT t.Tipo INTO TipoR FROM Test t WHERE t.CodAuto=Cod;
        IF((TipoR IS NOT NULL) AND (TipoR<>'Validazione' AND TipoR<>'Sistema'))
            THEN
                SELECT t.IdTest INTO oldId FROM Test t WHERE t.CodAuto=Cod;
                IF(TipoR='Unita')
                    THEN
                        CALL removeTestMetodi(Cod);
                END IF;
        END IF;
        DELETE FROM Test WHERE CodAuto = Cod;
        IF(oldId IS NOT NULL)
            THEN
	        CALL rearrangeIdTest(TipoR,oldId);
        END IF;
    COMMIT;
END $
