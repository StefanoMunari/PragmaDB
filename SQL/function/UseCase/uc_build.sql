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

DROP FUNCTION IF EXISTS buildIdUC $
CREATE FUNCTION buildIdUC (Padre INT(5) )
    RETURNS VARCHAR(20)
BEGIN
    DECLARE last VARCHAR(20);
    IF Padre IS NULL
        THEN
            SET last= uc_findLastRoot();
        ELSE
            SET last= uc_findLastSibling(Padre);
    END IF;
    IF last IS NOT NULL
        THEN
            IF LOCATE('.',last) >0
                THEN
                    RETURN CONCAT(SUBSTRING(last,1,LENGTH(last)-LOCATE('.',REVERSE(last))),'.',postfixId(last)+1);
                ELSE
                    RETURN CONCAT(LEFT(last,2),postfixId(last)+1);
            END IF;
        ELSE
            RETURN generateIdUC((SELECT IdUC FROM UseCase u WHERE u.CodAuto=Padre));
    END IF;
END $