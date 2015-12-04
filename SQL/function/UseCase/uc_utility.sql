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

DROP FUNCTION IF EXISTS uc_hierarchyId $
CREATE FUNCTION uc_hierarchyId (id VARCHAR(20) ) RETURNS VARCHAR(20)
BEGIN
    IF LOCATE('.',id) >0
        THEN
            RETURN CONCAT( LEFT(id,2),SUBSTRING(id,3,(LENGTH(id)- LOCATE('.',REVERSE(id)) -2)));
        ELSE/*radice*/
            RETURN LEFT(id,2);
    END IF;
END $

/*postFixId posso usare quella di Requisiti,va bene anche per UC*/

DROP FUNCTION IF EXISTS generateIdUC $
CREATE FUNCTION generateIdUC ( IdPadre VARCHAR(20)) RETURNS VARCHAR(20)
BEGIN
    IF IdPadre IS NOT NULL
        THEN
            RETURN CONCAT(IdPadre,'.1');
        ELSE
            RETURN 'UC1';
    END IF;
END $

DROP FUNCTION IF EXISTS uc_legalKinship $
CREATE FUNCTION uc_legalKinship(IdUC VARCHAR(20), newPadre VARCHAR(20)) RETURNS TINYINT
BEGIN
    RETURN NOT (IdUC = LEFT(newPadre,LENGTH(IdUC)));
END $