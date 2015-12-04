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

SET @STATO = 'DROPPER'$
SELECT @STATO$
DROP TABLE IF EXISTS ReqTracking$
DROP TABLE IF EXISTS RequisitiUC$
DROP TABLE IF EXISTS RequisitiPackage$
DROP TABLE IF EXISTS Utenti$
DROP TABLE IF EXISTS Fonti$
DROP TABLE IF EXISTS Requisiti$
DROP TABLE IF EXISTS Glossario$
DROP TABLE IF EXISTS AttoriUC$
DROP TABLE IF EXISTS UseCase$
DROP TABLE IF EXISTS Attori$
DROP TABLE IF EXISTS RelatedPackage$
DROP TABLE IF EXISTS EreditaDa$
DROP TABLE IF EXISTS Metodo$
DROP TABLE IF EXISTS Classe$
DROP TABLE IF EXISTS Relazione$
DROP TABLE IF EXISTS Attributo$
DROP TABLE IF EXISTS Parametro$
DROP TABLE IF EXISTS _MapUseCase$
DROP TABLE IF EXISTS _MapRequisiti$
DROP TABLE IF EXISTS Package$
DELIMITER ;

SHOW TABLES;