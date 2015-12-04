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

DROP FUNCTION IF EXISTS existPadreRequisito $
CREATE FUNCTION existPadreRequisito (Padre INT )
    RETURNS TINYINT(1)
BEGIN
    DECLARE hasPadre TINYINT(1) DEFAULT 0;
    SELECT COUNT(*) FROM Requisiti WHERE CodAuto = Padre INTO hasPadre;
    RETURN hasPadre;/*ritorna sempre 0 oppure 1*/
END $

DROP FUNCTION IF EXISTS legalParent $
CREATE FUNCTION legalParent (CodAuto INT(5), Padre INT(5))
    RETURNS TINYINT(1)
BEGIN
    RETURN  (SELECT r.Tipo = q.Tipo FROM Requisiti r, Requisiti q WHERE r.CodAuto = CodAuto AND q.CodAuto = Padre LIMIT 1 );/*ritorna 0,1*/
END $