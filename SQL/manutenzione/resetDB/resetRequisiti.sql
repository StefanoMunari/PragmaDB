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
DELIMITER ;

SET @STATO = 'RESET_REQUISITI';
SELECT @STATO;
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/manutenzione/resetDB/clear.sql;
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/manutenzione/resetDB/dropper.sql;
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/DDL/set_mysql_variables.sql;
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/DDL/Creation.sql;