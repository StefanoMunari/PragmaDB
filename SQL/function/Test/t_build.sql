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

DROP FUNCTION IF EXISTS buildIdTest $
CREATE FUNCTION buildIdTest (TipoR ENUM('Validazione','Sistema','Integrazione','Unita','Regressione')) RETURNS VARCHAR(22)
BEGIN
DECLARE IdReq VARCHAR(4) DEFAULT NULL;
DECLARE lastN INT DEFAULT 0;
DECLARE indexN INT DEFAULT 1;
    SELECT MAX(CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)) INTO lastN FROM Test t WHERE t.Tipo=TipoR;
    IF(lastN IS NOT NULL)
        THEN
            SET indexN=lastN+1;
    END IF;
    IF(TipoR='Integrazione')
        THEN
            RETURN (CONCAT('TI',indexN));
        ELSEIF(TipoR='Unita')
            THEN
                RETURN (CONCAT('TU',indexN));
        ELSE
            RETURN (CONCAT('TR',indexN));
    END IF;
END $
