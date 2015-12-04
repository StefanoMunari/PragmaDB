# PragmaDB

Software per la gestione di documenti di progetto, in particolare permette di gestire:
* Requisiti;
* Use Case;
* Attori;
* Fonti;
* Glossario;
* Classi;
* Package;
* Metriche;
* Test.

## Prerequisiti
Per utilizzare PragmaDB è necessario avere installato sulla macchina
host (virtuale o fisica che sia):
* *PHP*: versione > 5.3.10 => questa è stata utilizzata durante il progetto;
* *MySQL*: versione > 5.1 => necessita del supporto per stored procedure ricorsive.  

### Note preliminari
* Tutti gli elementi che possono appartenere ad una gerarchia (i.e. Requisiti,
  UseCase, Classi, Package etc.) possiedono un identificativo che viene gestito,
  modificato e spostato internamente al sistema secondo le indicazioni dell'utente.
* In ogni caso si rimanda al "manuale utente" che consiste in brevi indicazioni
  su come utilizzare il sistema. Purtroppo, a causa dei tempi stretti in cui
  è stato sviluppato, non si è riusciti a corredare il codice di una
  documentazione adeguata, sono presenti alcuni commenti direttamente nel codice.

## Installazione
## MySQL
#### Personalizzazione del sistema
Prima di caricare i dati di Mysql (tabelle, procedure, funzioni etc.):
* settare correttamente i path assoluti sostituendo la stringa:
```
<INSERIRE_PATH_ASSOLUTO>
```
con il path assoluto dell'host.
* modificare tutti i file .sql contenuti **nella directory manutenzione** in modo
che siano specifici per l'utilizzo da parte del vostro team
(inserire i valori per la creazione degli utenti, inserire i path etc.).
* creare nel server mysql un database con nome **pragmadb** associato ad un
utente con password.  

#### Caricamento mysql
* Effetuare l'accesso a MySQL con l'utente precedentemente creato.
* selezionare il database precedentemente creato:
```
use pragmadb;
```
* invocare:
```
SOURCE <PATH>/pragmadb/SQL/manutenzione/initializeDB/creator.sql
```
dove path va sostituito con il path assoluto dell'host che contiene
la cartella MySQL.

#### Extra
Sono presenti altri file di manutenzione: alcuni permettono di resettare tutte
le funzioni o le procedure, droppare le tabelle, eseguire dump del database etc.  
Ovviamente anche in questo caso vanno settati i vari path e gli eventuali altri valori.
### php
Per connettere php al database è necessario modificare in:
* PHP > Functions > mysql_fun.php
```
$host="INSERIRE_NOME_HOST";
$user="INSERIRE_NOME_UTENTE_DB";
$pwd="INSERIRE_PASSWD_DB";
$dbname="INSERIRE_NOME_DB";
```  
* PHP > Functions > urlLab.php  
settare il path in modo che punti alla directory pragmadb contenente la directory PHP;
* L'interfaccia php contiene dei link utili sulla parte destra, sono
customizzabili modificando:
 PHP > Utente > home.php  

## Funzionalità
Alcune sono descritte nel "manuale utente".  
Altre vengono brevissimamente menzionate qui:
* È possibile esportare qualsiasi documento direttamente in LaTeX;
* È possibile esportare le tabelle di tracciamento che vengono calcolate
internamente al sistema (i.e. tracciamento Requisit-UC, tracciamento TU-Metodi etc.);
Per quanto riguarda  Classi, Package, Metriche e Test sono semplici da utilizzare
quindi non richiedono particolari spiegazioni.

## Avvertenze finali
La sezione metriche serve da "cruscotto" in quanto contiene alcune metriche che
sono state utilizzate per avere sotto controllo la qualità dell'intero progetto
ed in particolare dell'architettura che abbiamo realizzato (qualità
  ritenute fondamentali perchè il prodotto realizzato potesse essere efficace
  soddisfacendo quindi i requisiti individuati).
  Possono essere prese d'esempio ma, in generale, le metriche riguardanti
  l'architettura vanno ragionate in base alle qualità architetturali *specifiche*
  che l'architettura dovrà dimostrare di possedere.

## Autori
*Munari Stefano* : parte MySQL  
*Vedovato Fabio* : parte PHP  

## Licenza
[Tutti i files .sql sono rilasciati sotto licenza GPLv3](https://github.com/StefanoMunari/PragmaDB/blob/master/LICENSE)
