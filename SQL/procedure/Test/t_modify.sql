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

/*PRE= modifyTest viene invocata se ALMENO 1 valore è stato modificato:
Tipo: assumo che il Tipo passato sia un valore NON VUOTO, un valore NULL corrisponde ad un Tipo non modificato
Descrizione: assumo che la Descrizione passata sia un valore NON VUOTO, un valore NULL corrisponde ad una Descrizione non modificata
Implementato: assumo Implementato passato sia un valore NON VUOTO, un valore NULL corrisponde ad un Implementato non modificato
Eseguito: assumo Eseguito passato sia un valore NON VUOTO, un valore NULL corrisponde ad un Eseguito non modificato
Esito: assumo Esito passato sia un valore NON VUOTO, un valore NULL corrisponde ad un Esito non modificato
Requisito: assumo che Requisito possa esssere NULL, un valore Requisito == 0 corrisponde ad un Requisito non modificato
Package: assumo che Package possa esssere NULL, un valore Package == 0 corrisponde ad un Package non modificato
SE CAMBIA TIPO -> Ricalcolo ID
*/
DROP PROCEDURE IF EXISTS modifyTest $
CREATE PROCEDURE modifyTest ( IN CodAutoR INT(10), TipoR ENUM('Validazione','Sistema','Integrazione','Unita','Regressione'), DescrizioneR VARCHAR(10000), ImplementatoR  BOOL, EseguitoR  BOOL, EsitoR  BOOL, RequisitoR INT(5), PackageR INT(5))
BEGIN
    DECLARE oldTipo ENUM('Validazione','Sistema','Integrazione','Unita','Regressione') DEFAULT NULL;
    DECLARE oldId VARCHAR(22) DEFAULT NULL;
    DECLARE IdTestR VARCHAR(22) DEFAULT NULL;
    START TRANSACTION;

	IF TipoR IS NOT NULL
	THEN
            SELECT t.Tipo INTO oldTipo
            FROM Test t
            WHERE t.CodAuto=CodAutoR;
            IF oldTipo<>'Validazione' AND oldTipo<>'Sistema'
                THEN
                    SELECT t.IdTest INTO oldId
                    FROM Test t
                    WHERE t.CodAuto=CodAutoR;
            END IF;
            IF TipoR<>'Validazione' AND TipoR<>'Sistema'
                THEN
                    SET IdTestR = buildIdTest(TipoR);
            END IF;
            UPDATE Test t
            SET t.IdTest=IdTestR
            WHERE t.CodAuto=CodAutoR;
        END IF;

        IF TipoR IS NOT NULL
        THEN
            UPDATE Test t 
            SET t.Tipo= TipoR
            WHERE t.CodAuto= CodAutoR;
        END IF;
        IF DescrizioneR IS NOT NULL
        THEN
            UPDATE Test t 
            SET t.Descrizione= DescrizioneR
            WHERE t.CodAuto= CodAutoR;
        END IF;
        IF ImplementatoR IS NOT NULL
        THEN
            UPDATE Test t 
            SET t.Implementato= ImplementatoR
            WHERE t.CodAuto= CodAutoR;
        END IF;
        IF EseguitoR IS NOT NULL
        THEN
            UPDATE Test t 
            SET t.Eseguito= EseguitoR
            WHERE t.CodAuto= CodAutoR;
        END IF;
        IF EsitoR IS NOT NULL
        THEN
            UPDATE Test t 
            SET t.Esito= EsitoR
            WHERE t.CodAuto= CodAutoR;
        END IF;
        IF RequisitoR IS NULL OR RequisitoR >0
        THEN
            UPDATE Test t 
            SET t.Requisito= RequisitoR
            WHERE t.CodAuto= CodAutoR;
        END IF;
        IF PackageR IS NULL OR PackageR >0
        THEN
            UPDATE Test t 
            SET t.Package= PackageR
            WHERE t.CodAuto= CodAutoR;
        END IF;

        IF oldTipo='Unita'
            THEN
                CALL removeTestMetodi(CodAutoR);
        END IF;
        IF oldId IS NOT NULL
            THEN
                CALL rearrangeIdTest(oldTipo,oldId);
        END IF;

        CALL updateTestTime(CodAutoR);
    COMMIT;
END $
