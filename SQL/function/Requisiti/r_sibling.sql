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

/*invocato solo su figli*/
DROP FUNCTION IF EXISTS getNextSibling $
CREATE FUNCTION getNextSibling ( IdRequisito VARCHAR(20)) RETURNS VARCHAR(20)
BEGIN
    DECLARE existence INT(1);
    IF LOCATE('.',IdRequisito) > 0
        THEN
            SELECT CONCAT(prefixId(IdRequisito),'.',postfixId(IdRequisito)+1) INTO IdRequisito;
        ELSE
            SELECT CONCAT(LEFT(IdRequisito,3),postfixId(IdRequisito)+1) INTO IdRequisito;
    END IF;
    SELECT COUNT(*) FROM Requisiti r WHERE r.IdRequisito = IdRequisito INTO existence;
    IF  existence > 0
        THEN
            RETURN IdRequisito;
        ELSE
            RETURN NULL;
    END IF;
END $