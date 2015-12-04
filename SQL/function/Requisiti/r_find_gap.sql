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

/*DA USARE SOLO QUANDO SPOSTO SOTTOALBERI--->solo per rearrangeTree()*/
/*ritorna sempre la prima posizione libera che trova,ATTENZIONE:ritorna un id anche se la posizione non esiste*/
DROP FUNCTION IF EXISTS findSiblingGap $
CREATE FUNCTION findSiblingGap ( Padre INT(5), Importanza ENUM('Obbligatorio','Desiderabile','Facoltativo')) RETURNS VARCHAR(20)
BEGIN
    DECLARE done INT(1) DEFAULT 0;
    DECLARE gap VARCHAR(20) DEFAULT NULL;
    DECLARE next VARCHAR(20) DEFAULT NULL;
    DECLARE idPadre VARCHAR(20);
    SELECT r.IdRequisito FROM Requisiti r WHERE r.CodAuto = Padre INTO idPadre;
    BEGIN
        DECLARE cur CURSOR FOR SELECT r.IdRequisito FROM Requisiti r WHERE r.Padre = Padre AND hierarchyId(r.IdRequisito) = CONCAT(LEFT(idPadre,2),SUBSTRING(idPadre FROM 4)) ORDER BY postfixId(r.IdRequisito);

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

            OPEN cur;

            SET gap = CONCAT(idPadre,'.0');
            IF done < 1 
            THEN
                FETCH cur INTO next;
                  WHILE done < 1 AND (SELECT postfixId(gap)+1 ) = (SELECT postfixId(next) )
                        DO
                            SET gap = next;
                            FETCH cur INTO next;
                END WHILE;
            END IF;
            CLOSE cur;
    END;


    RETURN (CONCAT(LEFT(gap,2),LEFT(Importanza,1),SUBSTRING(prefixId(gap) FROM 4),'.',postfixId(gap)+1));
END $

/*trova il primo gap per la radice con Tipo e Importanza*/
DROP FUNCTION IF EXISTS findRootGap $
CREATE FUNCTION findRootGap ( Tipo ENUM('Funzionale','Vincolo','Qualita','Prestazionale'),Importanza ENUM('Obbligatorio','Desiderabile','Facoltativo')) RETURNS VARCHAR(20)
BEGIN
    DECLARE done INT(1) DEFAULT 0;
    DECLARE gap VARCHAR(20) DEFAULT NULL;
    DECLARE next VARCHAR(20) DEFAULT NULL;
    DECLARE postFixId INT(2) DEFAULT 0;
    DECLARE cur CURSOR FOR SELECT r.IdRequisito FROM Requisiti r WHERE r.Padre IS NULL AND r.Tipo = Tipo ORDER BY postfixId(r.IdRequisito);

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cur;

        IF done < 1 
        THEN
            FETCH cur INTO next;
              WHILE done < 1 AND (SELECT postFixId+1 ) = (SELECT postfixId(next) )
                    DO
                        SET gap = next;
                        SET postFixId = postfixId(gap);
                        FETCH cur INTO next;
            END WHILE;
        END IF;
        CLOSE cur;

    IF postFixId > 0
        THEN
            RETURN (CONCAT('R',LEFT(Tipo,1),LEFT(Importanza,1),(postfixId(gap)+1)));
        ELSE
            RETURN (CONCAT('R',LEFT(Tipo,1),LEFT(Importanza,1),1));
    END IF;
END $