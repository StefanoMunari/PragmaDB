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

DROP PROCEDURE IF EXISTS insertRT $
CREATE PROCEDURE insertRT (IN CodAuto INT(5) , Utente VARCHAR(4))
BEGIN
    DECLARE lastIdTrack VARCHAR(10);
    SET lastIdTrack = findLastReqTracking(CodAuto);
    INSERT INTO ReqTracking( IdTrack, Descrizione, Tipo, Importanza, Padre, Stato, Implementato, Fonte, CodAuto, Utente, Time , Soddisfatto)
    (SELECT CONCAT(CodAuto,'v',getReqTrackingVersion(lastIdTrack)+1),r.Descrizione,r.Tipo,r.Importanza,r.Padre,r.Stato,r.Implementato,r.Fonte,CodAuto,Utente,CURRENT_TIMESTAMP,r.Soddisfatto 
    FROM Requisiti r
    WHERE r.CodAuto = CodAuto);
END$