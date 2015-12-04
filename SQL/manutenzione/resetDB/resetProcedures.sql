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

/*PROCEDURES*/
SET @STATO = 'PROCEDURE'$
SELECT @STATO$
SET @STATO = 'CLASSE/'$
SELECT @STATO$
SET @STATO = 'Classe'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/cl_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/cl_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/cl_remove.sql$
SET @STATO = 'Classe/Attributo'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Attributo/attr_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Attributo/attr_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Attributo/attr_remove.sql$
SET @STATO = 'CLASSE/METODO'$
SELECT @STATO$
SET @STATO = 'Metodo'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/mtd_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/mtd_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/mtd_remove.sql$
SET @STATO = 'Metodo/Parametro'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/Parametro/par_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/Parametro/par_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/Parametro/par_remove.sql$
SET @STATO = 'Metodo/utility'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Metodo/utility/mtd_parser.sql$
SET @STATO = 'Classe/EreditaDa'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/EreditaDa/ered_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/EreditaDa/ered_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/EreditaDa/ered_remove.sql$
SET @STATO = 'Classe/Relazione'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Relazione/rel_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Relazione/rel_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/Relazione/rel_remove.sql$
SET @STATO = 'Classe/RequisitiClasse'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/RequisitiClasse/reqcl_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/RequisitiClasse/reqcl_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/RequisitiClasse/reqcl_remove.sql$
SET @STATO = 'Classe/utility'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/utility/cl_parser.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Classe/utility/cl_update.sql$
SET @STATO = 'PACKAGE/'$
SELECT @STATO$
SET @STATO = 'Package'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/pkg_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/pkg_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/pkg_remove.sql$
SET @STATO = 'Package/RelatedPackage'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RelatedPackage/rPkg_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RelatedPackage/rPkg_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RelatedPackage/rPkg_remove.sql$
SET @STATO = 'Package/RequisitiPackage'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RequisitiPackage/reqp_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RequisitiPackage/reqp_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RequisitiPackage/reqp_remove.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/RequisitiPackage/reqp_automatize.sql$
SET @STATO = 'Package/utility'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/utility/pkg_parser.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Package/utility/pkg_update.sql$
SET @STATO = 'FONTI'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Fonti/f_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Fonti/f_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Fonti/f_remove.sql$
SET @STATO = 'GLOSSARIO'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Glossario/g_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Glossario/g_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Glossario/g_remove.sql$
SET @STATO = 'GLOSSARIO_UTILITY'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Glossario/utility/g_rearrange.sql$
SET @STATO = 'REQTRACKING'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/ReqTracking/RT_insert.sql$
SET @STATO = 'REQUISITI'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Requisiti/r_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Requisiti/r_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Requisiti/r_remove.sql$
SET @STATO = 'REQUISITI_UTILITY'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Requisiti/utility/r_change.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Requisiti/utility/r_rearrange.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Requisiti/utility/requisiti_uc_utility.sql$
SET @STATO = 'UTENTI'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Utenti/u_insert.sql$
SET @STATO = 'ATTORI'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Attori/a_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Attori/a_modify.sql$
SET @STATO = 'USECASE'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/uc_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/uc_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/uc_remove.sql$
SET @STATO = 'USECASE_UTILITY'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/utility/uc_attori_utility.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/utility/uc_change.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/utility/uc_FK_utility.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/utility/uc_rearrange.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/UseCase/utility/uc_requisiti_utility.sql$
SET @STATO = 'TEST/'$
SELECT @STATO$
SET @STATO = 'Test'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/t_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/t_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/t_remove.sql$
SET @STATO = 'Test/TestMetodi'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/TestMetodi/tMet_insert.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/TestMetodi/tMet_modify.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/TestMetodi/tMet_remove.sql$
SET @STATO = 'Test/utility'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/utility/t_rearrange.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/utility/test_parser.sql$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/Test/utility/test_update.sql$
SET @STATO = '_MODULES'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/_Modules/Search_modules/sortForest.sql$
SET @STATO = '_Modules/utility'$
SELECT @STATO$
SOURCE <INSERIRE_PATH_ASSOLUTO>/pragmadb/SQL/procedure/_Modules/Search_modules/utility/sortF_utility.sql$

DELIMITER ;