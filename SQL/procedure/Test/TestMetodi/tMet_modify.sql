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

DROP PROCEDURE IF EXISTS modifyMetodiTest $
CREATE PROCEDURE modifyMetodiTest(IN CodMetR INT(10), TestL VARCHAR(1000))
BEGIN
    START TRANSACTION;
        CALL removeMetodiTest(CodMetR);
        CALL insertMetodiTest(CodMetR,TestL);
    COMMIT;
END $

DROP PROCEDURE IF EXISTS modifyTestMetodi $
CREATE PROCEDURE modifyTestMetodi(IN CodTestR INT(10), MetL VARCHAR(1000))
BEGIN
    START TRANSACTION;
        CALL removeTestMetodi(CodTestR);
        CALL insertTestMetodi(CodTestR,MetL);
    COMMIT;
END $
