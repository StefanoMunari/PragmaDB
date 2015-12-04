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

DROP PROCEDURE IF EXISTS insertUseCase $
CREATE PROCEDURE insertUseCase 
( IN Nome  VARCHAR(300), Diagramma   VARCHAR(50), Descrizione VARCHAR(10000), 
    Precondizioni  VARCHAR(10000), Postcondizioni VARCHAR(10000), Padre INT(5), 
    ScenarioPrincipale  VARCHAR(10000), Inclusioni VARCHAR(5000), Estensioni VARCHAR(5000),
    ScenariAlternativi VARCHAR(10000), ReqCorrelati VARCHAR(1000), AttoriCorrelati VARCHAR(60), Utente VARCHAR(4))
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE IdUC VARCHAR(20);

    START TRANSACTION;

        IF Padre IS NOT NULL THEN 
            SET legal = existPadreUseCase(Padre);/* se il Padre è NULL || esiste in UseCase -> legal >0 */
        END IF;
        
        CALL legalListAttori(legal,AttoriCorrelati);/*controllo se tutti gli attori sono legali*/
        CALL legalListRequisiti(legal,ReqCorrelati);/*controllo se tutti i requisiti sono legali*/

        IF legal THEN 

            SET IdUC = buildIdUC(Padre);

            START TRANSACTION;

                INSERT INTO UseCase(IdUC, Nome, Diagramma, Descrizione, Precondizioni, Postcondizioni, Padre, ScenarioPrincipale, Inclusioni, Estensioni, ScenariAlternativi, Time )
                    VALUES (IdUC, Nome, Diagramma, Descrizione, Precondizioni, Postcondizioni, Padre, ScenarioPrincipale, Inclusioni, Estensioni, ScenariAlternativi, CURRENT_TIMESTAMP);

                    CALL sortForest('UseCase');
            COMMIT;

            SET IdUC= LAST_INSERT_ID();/*uso CODAUTO DI UseCase*/
            
            CALL insertListAttori(IdUC,AttoriCorrelati);

            CALL insertListRequisiti(IdUC,ReqCorrelati,Utente);

        END IF;
    
    COMMIT;
END $
