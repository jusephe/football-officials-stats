# football-officials-stats
Předmětem tohoto projektu jsou detailní statistiky o fotbalových rozhodčích a delegátech. Konkrétně se jedná o soutěže 
Pražského fotbalového svazu (Přebor a 1.A třída), avšak projekt je snadno rozšiřitelný i do dalších krajů a soutěží (viz níže). 

Projekt je rozdělen na dvě části - administrační a veřejnou (prezentační). 
V administrační části probíhá vkládání jednotlivých utkání a správa celého projektu. 
Data ze zápasů jsou čerpána z Informačního systému Fotbalové asociace České republiky, avšak i to lze změnit.
Veřejná část webu poskytuje jak statistické přehledy dle soutěží a sezón, tak i profily jednotlivých rozhodčích a delegátů.

Projekt je vytvořen pomocí frameworku Symfony 4.

Ve složce `db` lze nalézt create skript pro vytvoření databáze (MySQL) a databázová schémata. 

Soubor `BP_Supka_Josef_2019.pdf` je bakalářská práce popisující proces vzniku projektu.

## Možnosti rozšíření

### Přidání nové statistiky
Pro přidání nové statistiky stačí v databázi vytvořit příslušnou novou tabulku,
poté v třídě `StatsRepository` přidat metodu pro aktualizaci této tabulky a tu pak zavolat v metodě `updateAllStats()`.
Co se týče veřejné části webu, je třeba do příslušných tříd ve složce `Repository` přidat metody pro získávání dat nové statistiky. 
Dále ve složce `/templates/site` přidat do adresářů `season_stats` a `official_stats` šablony pro vykreslení dané statistiky, 
které je následně nutné vložit do šablon `season_stats.html.twig` a `official_profile.html.twig`, nebo `assessor_profile.html.twig`.

### Přidání soutěžní úrovně
Pro přidání soutěžní úrovně je potřeba vložit do tabulky `league` v databázi aspoň jeden záznam s ligou nové soutěžní úrovně. 
Tím bude zajištěno, že se název nové úrovně objeví jako možnost při přidávání nové ligy, 
administrátor pak může další ligy přidat sám.
Ve veřejné části je potřeba aktualizovat controllery `league` a `seasonStats`, a také metodu `getLeagues()` v `Service/ProfileConfig`. 
Žádné změny naopak nejsou potřeba v třídách v adresáři `Repository`.

### Rozšíření do jiného kraje
I díky zvolenému zdroji dat je projekt snadno přenositelný do jiných krajů, stačí jen lehce pozměnit Twig šablony, 
např. odkaz na tresty od disciplinární komise v souboru `/templates/admin/punishments.html.twig`.
V modulu veřejné části je třeba myslet pouze na to, aby controllery a šablony počítaly se správnými úrovněmi soutěží, 
které chceme v daném kraji sledovat. Lze tedy použít postup pro přidání soutěžní úrovně definovaný výše.
