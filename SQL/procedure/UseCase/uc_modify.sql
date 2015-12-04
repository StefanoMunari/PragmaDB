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

DROP PROCEDURE IF EXISTS modifyUseCase $
CREATE PROCEDURE modifyUseCase 
( CodAuto INT(5), Nome  VARCHAR(300), Diagramma   VARCHAR(50), Descrizione VARCHAR(10000), 
    Precondizioni  VARCHAR(10000), Postcondizioni VARCHAR(10000), Padre INT(5), 
    ScenarioPrincipale  VARCHAR(10000), Inclusioni VARCHAR(5000), Estensioni VARCHAR(5000),
    ScenariAlternativi VARCHAR(10000), ReqCorrelati VARCHAR(1000), AttoriCorrelati VARCHAR(60), Utente VARCHAR(4))
BEGIN
    DECLARE last VARCHAR(20);
    DECLARE oldPadre INT(5);
    DECLARE legal TINYINT DEFAULT 1;
    START TRANSACTION;
        CALL legalListAttori(legal,AttoriCorrelati);/*controllo se tutti gli attori sono legali*/
        CALL legalListRequisiti(legal,ReqCorrelati);/*controllo se tutti i requisiti sono legali, ok anche per NULL*/


        IF legal
          
              THEN

                SELECT u.Padre FROM UseCase u WHERE u.CodAuto= CodAuto INTO oldPadre;


                IF Padre IS NOT NULL AND existPadreUseCase(Padre)
                    THEN
                        IF oldPadre IS NOT NULL
                            THEN
                                IF oldPadre <> Padre AND uc_legalKinship((SELECT u.IdUC FROM UseCase u WHERE u.CodAuto= CodAuto), (SELECT u.IdUC FROM UseCase u WHERE u.CodAuto= Padre))/*un padre non può essere figlio di un suo figlio*/
                                    THEN
                                        SET last= uc_findLastSibling(oldPadre);
                                        CALL uc_changeParent(CodAuto,Padre);/*cambio padre*/
                                        CALL uc_rearrangeTree((SELECT u.CodAuto FROM UseCase u WHERE u.IdUC= last), oldPadre);/*copro il buco*/
                                END IF;

                            ELSE/*oldPadre NULL,sicuramente != Padre*/
                                IF uc_legalKinship((SELECT u.IdUC FROM UseCase u WHERE u.CodAuto= CodAuto), (SELECT u.IdUC FROM UseCase u WHERE u.CodAuto= Padre))/*un padre non può essere figlio di un suo figlio*/
                                    THEN
                                        SET last= uc_findLastRoot();
                                        CALL uc_changeParent(CodAuto,Padre);/*cambio padre*/
                                        CALL uc_rearrangeTree((SELECT u.CodAuto FROM UseCase u WHERE u.IdUC= last), oldPadre);/*copro il buco*/
                                END IF;

                        END IF;
                    ELSE/*padre= NULL*/
                        IF oldPadre IS NOT NULL    /*oldPadre != padre*/
                            THEN
                                SET last= uc_findLastSibling(oldPadre);
                                CALL uc_changeParent(CodAuto,Padre);/*cambio padre*/
                                CALL uc_rearrangeTree((SELECT u.CodAuto FROM UseCase u WHERE u.IdUC= last), oldPadre);/*copro il buco*/
                        END IF;

                END IF;


                UPDATE  UseCase u
                SET u.Nome= Nome, u.Diagramma= Diagramma, u.Descrizione= Descrizione, u.Precondizioni= Precondizioni,
                u.Postcondizioni= Postcondizioni, u.ScenarioPrincipale= ScenarioPrincipale, u.Inclusioni=Inclusioni, u.Estensioni=Estensioni, u.ScenariAlternativi= ScenariAlternativi, u.Time= CURRENT_TIMESTAMP
                WHERE u.CodAuto= CodAuto;

                /*elimino tutti i legami precedenti e li risetto*/
                DELETE FROM AttoriUC WHERE UC = CodAuto;
                CALL insertListAttori(CodAuto,AttoriCorrelati);

                IF ReqCorrelati IS NOT NULL
                    THEN
                        DELETE FROM RequisitiUC WHERE UC = CodAuto;
                        IF ReqCorrelati = ''
                            THEN
                                CALL updateReq_on_delete(CodAuto,Utente);
                        END IF;
                        CALL insertListRequisiti(CodAuto,ReqCorrelati,Utente);/*probabilmente bug*/
                END IF;
                CALL sortForest('UseCase');
        END IF;
    COMMIT;
END $
