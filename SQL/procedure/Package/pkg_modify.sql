DELIMITER $

/*PRE= modifyPackage viene invocata se ALMENO 1 valore è stato modificato:
Nome: assumo che il Nome passato sia un valore NON VUOTO, un valore NULL corrisponde a Nome non modificato
PrefixNome: assumo che sia corrispondente alla Prefix+NuovoNome e quindi coerente, un valore NULL corrisponde ad un PrefixNome non modificato
Descrizione: assumo che Descrizione sia non vuota, un valore NULL corrisponde ad un Descrizione non modificato
UML: assumo che UML sia non vuota, un valore 0 corrisponde ad un UML non modificato
Padre: assumo che Padre sia non vuota, un valore Padre == 0 corrisponde ad un UML non modificato
RelationType: DI DEFAULT è NULL! ciò significa che se viene modificata diventa o P oppure C, essendo un package è normale che 
abbia delle relazioni e non ha senso che da P o C ritorni ad essere NULL. quindi controllo SE != NULL -> Update*/
DROP PROCEDURE IF EXISTS modifyPackage $
CREATE PROCEDURE modifyPackage ( IN CodAuto     INT(5), Nome  VARCHAR(100), PrefixNome   VARCHAR(5000),
 Descrizione VARCHAR(10000), UML   VARCHAR(50), Padre INT(5), RelationType  ENUM('P','C'))
BEGIN
    DECLARE oldPrefixNome VARCHAR(5000);
    START TRANSACTION;
	IF PrefixNome IS NOT NULL
	THEN
            SELECT p.PrefixNome INTO oldPrefixNome
            FROM Package p
            WHERE p.CodAuto=CodAuto;
        END IF;
        IF Nome IS NOT NULL
        THEN
            UPDATE Package p 
            SET p.Nome= Nome
            WHERE p.CodAuto= CodAuto;
        END IF;
        IF Descrizione IS NOT NULL
        THEN
            UPDATE Package p 
            SET p.Descrizione= Descrizione
            WHERE p.CodAuto= CodAuto;
        END IF;
        IF UML <> '0'
        THEN
            UPDATE Package p 
            SET p.UML= UML
            WHERE p.CodAuto= CodAuto;
        END IF;
        IF Padre IS NULL OR Padre >0
        THEN
            UPDATE Package p 
            SET p.Padre= Padre
            WHERE p.CodAuto= CodAuto;
        END IF;
        /*IF RelationType IS NOT NULL
        THEN
            UPDATE Package p 
            SET p.RelationType= RelationType
            WHERE p.CodAuto= CodAuto;
        END IF;*/
	IF PrefixNome IS NOT NULL
	THEN
	    CALL updatePrefixNomeDiscendenti(oldPrefixNome,PrefixNome);
	    CALL automatizeRequisitiPackage();
	END IF;
        CALL updatePackageTime(CodAuto);
    COMMIT;
END $