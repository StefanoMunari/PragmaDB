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

/*FUNCTIONS*/
SET @STATO = 'FUNZIONI'$
SELECT @STATO$
SET @STATO = 'FONTI'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Fonti/f_build.sql$
SET @STATO = 'GLOSSARIO'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Glossario/g_build.sql$
SET @STATO = 'REQTRACKING'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/ReqTracking/rt_utility.sql$
SET @STATO = 'REQUISITI'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Requisiti/r_build.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Requisiti/r_check.sql$
SET @STATO = 'REQUISITI_TREE'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Requisiti/r_find_gap.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Requisiti/r_find_last.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Requisiti/r_sibling.sql$
SET @STATO = 'REQUISITI_UTILITY'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Requisiti/r_tree_utility.sql$
SET @STATO = 'USECASE'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_build.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_check.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_find_last.sql$
SET @STATO = 'USECASE_UTILITY'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_build.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_check.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_find_gap.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_find_last.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/UseCase/uc_utility.sql$
SET @STATO = 'TEST'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/function/Test/t_build.sql$

DELIMITER ;