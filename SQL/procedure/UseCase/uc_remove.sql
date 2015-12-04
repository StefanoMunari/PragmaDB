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
/*eliminare in TRANSACTION,durante la transazione prendere l'ultimo elemento del livello e reinserirlo nella posizione eliminata*/
DROP PROCEDURE IF EXISTS removeUseCase $
CREATE PROCEDURE removeUseCase ( IN Cod INT(5))
BEGIN
    DECLARE Id VARCHAR(20);
    DECLARE last VARCHAR(20) DEFAULT NULL;
    DECLARE parent INT(5);
    START TRANSACTION;
        SELECT u.IdUC FROM UseCase u WHERE u.CodAuto = Cod INTO Id;
        SELECT u.Padre FROM UseCase u WHERE u.CodAuto = Cod INTO parent;
        IF parent IS NOT NULL
            THEN
                SET last= uc_findLastSibling(parent);
            ELSE
                SET last= uc_findLastRoot();
        END IF;
        
        DELETE FROM UseCase WHERE CodAuto = Cod;
        
        IF last IS NOT NULL
        THEN
            CALL uc_rearrangeTree((SELECT u.CodAuto FROM UseCase u WHERE u.IdUC = last),parent);
        END IF;
	CALL sortForest('UseCase');
    COMMIT;
END $