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
buildIdR
    Costruisce IdRequisito t.c. corrisponde alla prossima posizione libera nell'albero
*/
DROP FUNCTION IF EXISTS buildIdRequisito $
CREATE FUNCTION buildIdRequisito ( Tipo ENUM('Funzionale','Vincolo','Qualita','Prestazionale') , Importanza ENUM('Obbligatorio','Desiderabile','Facoltativo'), Padre INT(5) ) RETURNS VARCHAR(20)
BEGIN
    DECLARE IdRequisito VARCHAR(20);
    DECLARE IdParent VARCHAR(20);
    DECLARE predecessor VARCHAR(20);
    DECLARE sibling VARCHAR(20) DEFAULT NULL;
    SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto = Padre INTO IdParent;
    IF IdParent IS NOT NULL
    THEN
        SELECT CONCAT(LEFT(IdParent,2),LEFT(Importanza,1),SUBSTRING(IdParent FROM 4),'.') INTO IdRequisito;/*padre controllato con checkMatchIdParentRequisito*/
        SET sibling = findLastSibling((SELECT CONCAT(IdRequisito,'0')));
        IF sibling IS NOT NULL
        THEN
            SELECT CONCAT(IdRequisito,postfixId(sibling)+1) INTO IdRequisito ;
        ELSE
            SELECT CONCAT(IdRequisito,'1') INTO IdRequisito;
        END IF;
    ELSE
        SET predecessor = findLastRoot(Tipo);
        IF predecessor IS NOT NULL
        THEN/*ok perchè ha lo stesso tipo ed importanza*/
            SELECT CONCAT(
                            LEFT(predecessor,2) ,
                            LEFT(Importanza,1),
                            (SUBSTRING(predecessor FROM 4)+1)
                        ) INTO IdRequisito;
        ELSE
            SET IdRequisito = generateIdRequisito(Tipo,Importanza);
        END IF;
    END IF;
RETURN IdRequisito;
END $