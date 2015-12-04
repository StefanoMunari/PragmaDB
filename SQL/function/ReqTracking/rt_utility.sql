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

DROP FUNCTION IF EXISTS getReqTrackingVersion $
CREATE FUNCTION getReqTrackingVersion (IdTrack VARCHAR(26)) RETURNS INT(5)
BEGIN
    RETURN CONVERT(RIGHT(IdTrack,LOCATE('v',REVERSE(IdTrack))-1),UNSIGNED INTEGER);/*-1 per togliere v*/
END$

DROP FUNCTION IF EXISTS findLastReqTracking $
CREATE FUNCTION findLastReqTracking ( CodAuto INT(5)) RETURNS VARCHAR(10)
BEGIN
    RETURN (SELECT a.IdTrack FROM ReqTracking a WHERE a.CodAuto = CodAuto AND getReqTrackingVersion(a.IdTrack) >= ALL 
    (SELECT getReqTrackingVersion(b.IdTrack) FROM ReqTracking b WHERE b.CodAuto = CodAuto));
END $