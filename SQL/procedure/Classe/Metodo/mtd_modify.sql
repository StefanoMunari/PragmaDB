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

/*OBBLIGATORIO PASSARE SEMPRE UN VALORE PER CLASSE, PERCHè FACCIO UPDATE TIME IN OGNI CASO*/
DROP PROCEDURE IF EXISTS modifyMetodo$
CREATE PROCEDURE modifyMetodo ( CodAutoR    INT(10), AccessModR ENUM('-','+','#'), NomeR  VARCHAR(800),
ReturnTypeR  VARCHAR(800), DescrizioneR VARCHAR(10000), ClasseR INT(5))
BEGIN
    START TRANSACTION;
        IF AccessModR IS NOT NULL
            THEN
                UPDATE Metodo
                SET AccessMod = AccessModR
                WHERE CodAuto = CodAutoR;
        END IF;
        IF NomeR IS NOT NULL
            THEN
                UPDATE Metodo
                SET Nome = NomeR
                WHERE CodAuto = CodAutoR;
        END IF;
        IF ReturnTypeR <> '0'
            THEN
                UPDATE Metodo
                SET ReturnType = ReturnTypeR
                WHERE CodAuto = CodAutoR;
        END IF;
        IF DescrizioneR IS NOT NULL
            THEN
                UPDATE Metodo
                SET Descrizione = DescrizioneR
                WHERE CodAuto = CodAutoR;
        END IF;
        IF (SELECT m.Classe FROM Metodo m WHERE m.CodAuto = CodAutoR) <> ClasseR
            THEN
                UPDATE Metodo
                SET Classe = ClasseR
                WHERE CodAuto = CodAutoR;
        END IF;        
        CALL updateClasseTime(ClasseR);
    COMMIT;
END $