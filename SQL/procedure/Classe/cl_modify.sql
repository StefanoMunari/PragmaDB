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
/*
versione light, assumo che i controlli siano eseguiti in PHP.
CONTROLLO CHE I VALORI NON SIANO NULLI E CHE ESISTA IL PACKAGE IN CUI è CONTENUTA LA CLASSE
*/
DROP PROCEDURE IF EXISTS modifyClasse $
CREATE PROCEDURE modifyClasse ( IN CodAuto  INT(5), Nome  VARCHAR(100), PrefixNome   VARCHAR(5000),
 Descrizione VARCHAR(10000), Utilizzo VARCHAR(10000), ContenutaIn  INT(5),UML  VARCHAR(50))
BEGIN
    START TRANSACTION;
        IF Nome IS NOT NULL
        THEN
            UPDATE Classe c 
            SET c.Nome= Nome
            WHERE c.CodAuto= CodAuto;
        END IF;
	IF PrefixNome IS NOT NULL
	THEN
            UPDATE Classe c
            SET c.PrefixNome= PrefixNome
            WHERE c.CodAuto= CodAuto;
        END IF;
        IF Descrizione IS NOT NULL
        THEN
            UPDATE Classe c
            SET c.Descrizione= Descrizione
            WHERE c.CodAuto= CodAuto;
        END IF;
        IF Utilizzo <> '0'
        THEN
            UPDATE Classe c
            SET c.Utilizzo= Utilizzo
            WHERE c.CodAuto= CodAuto;
        END IF;
        IF UML <> '0'
        THEN
            UPDATE Classe c
            SET c.UML= UML
            WHERE c.CodAuto= CodAuto;
        END IF;
        IF ((ContenutaIn IS NOT NULL) AND ((SELECT COUNT(k.CodAuto) FROM Package k WHERE k.CodAuto= ContenutaIn) >0))
        THEN
            UPDATE Classe c
            SET c.ContenutaIn= ContenutaIn
            WHERE c.CodAuto= CodAuto;
        END IF;
        IF PrefixNome IS NOT NULL
	THEN
            CALL automatizeRequisitiPackage();
	END IF;
        CALL updateClasseTime(CodAuto);
    COMMIT;
END $