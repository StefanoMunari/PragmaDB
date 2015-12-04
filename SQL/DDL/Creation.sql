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

USE pragmadb;

CREATE TABLE Utenti(
    Username        VARCHAR(4) PRIMARY KEY,
    Nome            VARCHAR(12) NOT NULL,
    Cognome         VARCHAR(9) NOT NULL,
    Password        VARCHAR(40) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Fonti (
    CodAuto INT PRIMARY KEY AUTO_INCREMENT,
    IdFonte     VARCHAR(10) UNIQUE NOT NULL,
    Nome        VARCHAR(20) NOT NULL,
    Descrizione VARCHAR(10000) NOT NULL,
    Time        DATETIME
) ENGINE=InnoDB;

CREATE TABLE Requisiti(
    CodAuto    INT(5) PRIMARY KEY AUTO_INCREMENT,
    IdRequisito  VARCHAR(20) UNIQUE NOT NULL,
    Descrizione VARCHAR(10000) NOT NULL,
    Tipo   ENUM('Funzionale','Vincolo','Qualita','Prestazionale')  NOT NULL,
    Importanza   ENUM('Obbligatorio','Desiderabile','Facoltativo') NOT NULL,
    Padre       INT(5),
    Stato       BOOL DEFAULT '0' NOT NULL,/*Accettato, Non Accettato*/
    Implementato    BOOL  DEFAULT  '0'  NOT NULL,
    Fonte       INT(5)  NOT NULL,
    Soddisfatto    BOOL  DEFAULT  '0'  NOT NULL,/*Soddisfatto o Non Soddisfatto*/
    FOREIGN KEY  (Padre)  REFERENCES  Requisiti(CodAuto)  ON DELETE  CASCADE  ON UPDATE  CASCADE,
    FOREIGN KEY  (Fonte)  REFERENCES  Fonti(CodAuto)  ON DELETE  RESTRICT  ON UPDATE CASCADE
    )  ENGINE=InnoDB;

CREATE TABLE ReqTracking (
    IdTrack     VARCHAR(10) PRIMARY KEY,
    Descrizione VARCHAR(10000) NOT NULL,
    Tipo        ENUM('Funzionale','Vincolo','Qualita','Prestazionale') NOT NULL,
    Importanza  ENUM('Obbligatorio','Desiderabile','Facoltativo') NOT NULL,
    Padre       INT(5),
    Stato       BOOL DEFAULT '0' NOT NULL,
    Implementato    BOOL DEFAULT '0' NOT NULL,
    Fonte       INT(5) NOT NULL,
    CodAuto     INT(5) NOT NULL,
    Utente      VARCHAR(4) NOT NULL,
    Time        DATETIME,
    Soddisfatto    BOOL  DEFAULT  '0'  NOT NULL,
    FOREIGN KEY (CodAuto) REFERENCES Requisiti(CodAuto)
                ON DELETE CASCADE
                ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Glossario (
    CodAuto     INT(5) PRIMARY KEY AUTO_INCREMENT,
    IdTermine   VARCHAR(4) UNIQUE NOT NULL,
    Identificativo  VARCHAR(50) UNIQUE NOT NULL,
    Name        VARCHAR(50) UNIQUE NOT NULL,
    Description VARCHAR(10000) NOT NULL,
    First       VARCHAR(50) UNIQUE,
    FirstPlural VARCHAR(50) UNIQUE,
    Text        VARCHAR(50) UNIQUE,
    Plural      VARCHAR(50) UNIQUE,
    Time        DATETIME
) ENGINE=InnoDB;

CREATE TABLE Attori (
    CodAuto     INT(5) PRIMARY KEY AUTO_INCREMENT,
    Nome   VARCHAR(20) UNIQUE NOT NULL,
    Descrizione VARCHAR(10000) DEFAULT NULL,
    Time DATETIME
) ENGINE=InnoDB;

CREATE TABLE UseCase (
    CodAuto     INT(5) PRIMARY KEY AUTO_INCREMENT,
    IdUC   VARCHAR(20) UNIQUE NOT NULL,
    Nome  VARCHAR(50) UNIQUE NOT NULL,
    Diagramma   VARCHAR(50) UNIQUE DEFAULT NULL,
    Descrizione VARCHAR(10000) NOT NULL,
    Precondizioni       VARCHAR(10000) NOT NULL,
    Postcondizioni VARCHAR(10000) NOT NULL,
    Padre       INT(5) DEFAULT NULL,
    ScenarioPrincipale      VARCHAR(10000) NOT NULL,
    Inclusioni      VARCHAR(5000) DEFAULT NULL,
    Estensioni      VARCHAR(5000) DEFAULT NULL,
    ScenariAlternativi      VARCHAR(10000) DEFAULT NULL,
    Time        DATETIME,
    FOREIGN KEY  (Padre)  REFERENCES  UseCase(CodAuto)  ON DELETE  CASCADE  ON UPDATE  CASCADE
) ENGINE=InnoDB;

CREATE TABLE AttoriUC (
    Attore  INT(5),
    UC      INT(5),
    PRIMARY KEY(Attore,UC),
    FOREIGN KEY (Attore) REFERENCES Attori(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (UC) REFERENCES UseCase(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE RequisitiUC (
    CodReq  INT(5),
    UC      INT(5),
    PRIMARY KEY(CodReq,UC),
    FOREIGN KEY (CodReq) REFERENCES Requisiti(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (UC) REFERENCES UseCase(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE Package (
    CodAuto     INT(5) PRIMARY KEY AUTO_INCREMENT,
    Nome  VARCHAR(100) NOT NULL,
    PrefixNome   VARCHAR(5000) NOT NULL,/*Prefisso+Nome*/
    Descrizione VARCHAR(10000) NOT NULL,
    UML   VARCHAR(50) UNIQUE DEFAULT NULL,
    Padre INT(5) DEFAULT NULL,
    RelationType   ENUM('P','C') NOT NULL,
    Time        DATETIME,
    FOREIGN KEY (Padre) REFERENCES Package(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE RelatedPackage (
    Pack1  INT(5),
    Pack2  INT(5),
    PRIMARY KEY(Pack1,Pack2),
    FOREIGN KEY (Pack1) REFERENCES Package(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (Pack2) REFERENCES Package(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Classe (
    CodAuto    INT(5) PRIMARY KEY AUTO_INCREMENT,
    Nome  VARCHAR(100) NOT NULL,
    PrefixNome   VARCHAR(5000) NOT NULL,/*Prefisso+Nome*/
    Descrizione VARCHAR(10000) NOT NULL,
    Utilizzo VARCHAR(10000) DEFAULT NULL,
    ContenutaIn     INT(5) NOT NULL,
    UML   VARCHAR(50) UNIQUE DEFAULT NULL,
    Time        DATETIME,
    FOREIGN KEY (ContenutaIn) REFERENCES Package(CodAuto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE EreditaDa (
    Padre  INT(5),
    Figlio  INT(5),
    PRIMARY KEY(Padre,Figlio),
    FOREIGN KEY (Padre) REFERENCES Classe(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (Figlio) REFERENCES Classe(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Relazione (
    CodAuto    INT(5) PRIMARY KEY AUTO_INCREMENT,
    Da  INT(5),
    A  INT(5),
    FOREIGN KEY (Da) REFERENCES Classe(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (A) REFERENCES Classe(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Attributo (
    CodAuto    INT(5) PRIMARY KEY AUTO_INCREMENT,
    AccessMod ENUM('-','+','#') NOT NULL,
    Nome  VARCHAR(800) NOT NULL,
    Tipo  VARCHAR(800) NOT NULL,
    Descrizione VARCHAR(10000) NOT NULL,
    Classe  INT(5) NOT NULL,
    FOREIGN KEY (Classe) REFERENCES Classe(CodAuto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
 ) ENGINE=InnoDB;

 CREATE TABLE Metodo (
    CodAuto    INT(10) PRIMARY KEY AUTO_INCREMENT,
    AccessMod ENUM('-','+','#') NOT NULL,
/*Il PrefixNome bisogna andare a prenderselo dalla classe che lo contiene*/
    Nome  VARCHAR(800) NOT NULL,
    ReturnType  VARCHAR(800) DEFAULT NULL,
    Descrizione VARCHAR(10000) NOT NULL,
    Classe  INT(5) NOT NULL,
    FOREIGN KEY (Classe) REFERENCES Classe(CodAuto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
 ) ENGINE=InnoDB;

 CREATE TABLE Parametro (
    CodAuto    INT(10) PRIMARY KEY AUTO_INCREMENT,
    Nome  VARCHAR(800) NOT NULL,
    Tipo  VARCHAR(800) NOT NULL,
    Descrizione VARCHAR(10000) NOT NULL,
    Metodo  INT(10) NOT NULL,
    FOREIGN KEY (Metodo) REFERENCES Metodo(CodAuto)
            ON DELETE CASCADE
            ON UPDATE CASCADE
 ) ENGINE=InnoDB;

CREATE TABLE RequisitiPackage (
    CodReq  INT(5),
    CodPkg     INT(5),
    PRIMARY KEY(CodReq,CodPkg),
    FOREIGN KEY (CodReq) REFERENCES Requisiti(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (CodPkg) REFERENCES Package(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE RequisitiClasse (
    CodReq  INT(5),
    CodClass    INT(5),
    PRIMARY KEY(CodReq,CodClass),
    FOREIGN KEY (CodReq) REFERENCES Requisiti(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (CodClass) REFERENCES Classe(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Test (
    CodAuto       INT(10) PRIMARY KEY AUTO_INCREMENT,
/*Validazione,Sistema -> Id NULL perché calcolato con IdRequisitoCorrelato; altri casi è num progressivo*/
    IdTest        VARCHAR(22) UNIQUE DEFAULT NULL,
    Tipo          ENUM('Validazione','Sistema','Integrazione','Unita','Regressione') NOT NULL,
    Descrizione   VARCHAR(10000) NOT NULL,
    Implementato  BOOL  DEFAULT  '0'  NOT NULL,
    Eseguito      BOOL  DEFAULT  '0'  NOT NULL,
    Esito         BOOL  DEFAULT  '0'  NOT NULL,
    Requisito     INT(5) DEFAULT NULL,
    Package       INT(5) DEFAULT NULL,
    Time          DATETIME,
    FOREIGN KEY  (Requisito)  REFERENCES  Requisiti(CodAuto) ON DELETE  CASCADE  ON UPDATE  CASCADE,
    FOREIGN KEY  (Package)  REFERENCES  Package(CodAuto) ON DELETE  CASCADE  ON UPDATE  CASCADE
) ENGINE=InnoDB;

CREATE TABLE TestMetodi (
    CodTest  INT(10),
    CodMet   INT(10),
    PRIMARY KEY(CodTest,CodMet),
    FOREIGN KEY (CodTest) REFERENCES Test(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (CodMet) REFERENCES Metodo(CodAuto) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

#_Modules/Search_modules->MapTables
CREATE TABLE _MapRequisiti (
    Position     INT(5) PRIMARY KEY AUTO_INCREMENT,
    CodAuto      INT(5) UNIQUE NOT NULL,
    FOREIGN KEY  (CodAuto)  REFERENCES  Requisiti(CodAuto) ON DELETE  CASCADE  ON UPDATE  CASCADE
) ENGINE=InnoDB;

CREATE TABLE _MapUseCase (
    Position     INT(5) PRIMARY KEY AUTO_INCREMENT,
    CodAuto      INT(5) UNIQUE NOT NULL,
    FOREIGN KEY  (CodAuto)  REFERENCES  UseCase(CodAuto)  ON DELETE  CASCADE  ON UPDATE  CASCADE
) ENGINE=InnoDB;
