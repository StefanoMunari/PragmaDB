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

DROP PROCEDURE IF EXISTS removeClasse $
CREATE PROCEDURE removeClasse ( IN Cod INT(5))
BEGIN
    START TRANSACTION;
        CALL removeEreditaDa(Cod);
        CALL removeEreditataDa(Cod);
        CALL removeRelazioneA(Cod);
        CALL removeRelazioneDa(Cod);
        CALL removeClasseRequisiti(Cod);
        DELETE FROM Classe WHERE CodAuto = Cod;
    COMMIT;
END $