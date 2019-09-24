
CREATE TABLE assessor (
    id     VARCHAR(8) NOT NULL,
    name   VARCHAR(80) NOT NULL
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE assessor ADD CONSTRAINT assessor_pk PRIMARY KEY ( id );


CREATE TABLE official (
    id     VARCHAR(8) NOT NULL,
    name   VARCHAR(80) NOT NULL
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE official ADD CONSTRAINT official_pk PRIMARY KEY ( id );


CREATE TABLE nomination_list (
    season              SMALLINT UNSIGNED NOT NULL,
    part_of_season      ENUM('Jaro', 'Podzim') NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    league_level_name   VARCHAR(20) NOT NULL
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE nomination_list ADD CONSTRAINT nomination_list_pk PRIMARY KEY ( season,
                                                                            part_of_season,
                                                                            official_id );


CREATE TABLE league (
    id           TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name    VARCHAR(80) NOT NULL,
    short_name   VARCHAR(20) NOT NULL,
    CONSTRAINT league_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE league
    ADD CONSTRAINT league__un UNIQUE ( full_name );


CREATE TABLE team (
    id           INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    club_id      VARCHAR(10) NOT NULL,
    full_name    VARCHAR(140) NOT NULL,
    short_name   VARCHAR(50),
    CONSTRAINT team_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE team
    ADD CONSTRAINT team__un UNIQUE ( full_name );


CREATE TABLE game (
    id                    INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season                SMALLINT UNSIGNED NOT NULL,
    is_autumn             TINYINT(1) NOT NULL,
    round                 TINYINT UNSIGNED NOT NULL,
    code                  VARCHAR(20) NOT NULL,
    referee_official_id   VARCHAR(8) NOT NULL,
    ar1_official_id       VARCHAR(8) NOT NULL,
    ar2_official_id       VARCHAR(8) NOT NULL,
    assessor_id           VARCHAR(8) NOT NULL,
    home_team_id          INTEGER UNSIGNED NOT NULL,
    away_team_id          INTEGER UNSIGNED NOT NULL,
    league_id             TINYINT UNSIGNED NOT NULL,
    CONSTRAINT game_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE game
    ADD CONSTRAINT game__un UNIQUE ( code );


CREATE TABLE offence (
    id           TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    short_name   VARCHAR(35) NOT NULL,
    full_name    VARCHAR(80) NOT NULL,
    CONSTRAINT offence_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;


CREATE TABLE red_card (
    id            INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    person        VARCHAR(80) NOT NULL,
    minute        VARCHAR(3),
    description   VARCHAR(1000),
    weeks         SMALLINT UNSIGNED,
    games         TINYINT UNSIGNED,
    fine          INTEGER UNSIGNED,
    fee           SMALLINT UNSIGNED,
    team_id       INTEGER UNSIGNED NOT NULL,
    offence_id    TINYINT UNSIGNED NOT NULL,
    game_id       INTEGER UNSIGNED NOT NULL,
    CONSTRAINT red_card_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;


CREATE TABLE yellow_card (
    id         INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    minute     VARCHAR(3) NOT NULL,
    game_id    INTEGER UNSIGNED NOT NULL,
    CONSTRAINT yellow_card_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;


CREATE TABLE admin (
    id        INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, 
    email     VARCHAR(180) NOT NULL, 
    roles     TEXT NOT NULL COMMENT '(DC2Type:json)', 
    password  VARCHAR(255) NOT NULL, 
    CONSTRAINT admin_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE admin
    ADD CONSTRAINT admin__un UNIQUE ( email );


CREATE TABLE post (
    id              INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, 
    admin_id        INTEGER UNSIGNED, 
    title           VARCHAR(255) NOT NULL, 
    published       DATETIME, 
    contents_md     LONGTEXT, 
    contents_html   LONGTEXT, 
    CONSTRAINT post_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;




CREATE TABLE stat_ar1_and_ar2_matches (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_ar1_and_ar2_matches_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_ar1_and_ar2_matches
    ADD CONSTRAINT stat_ar1_and_ar2_matches__un UNIQUE ( season,
                                                         is_autumn,
                                                         league_id,
                                                         official_id );

CREATE TABLE stat_ar1_matches (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_ar1_matches_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_ar1_matches
    ADD CONSTRAINT stat_ar1_matches__un UNIQUE ( season,
                                                 is_autumn,
                                                 league_id,
                                                 official_id );

CREATE TABLE stat_ar2_matches (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_ar2_matches_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_ar2_matches
    ADD CONSTRAINT stat_ar2_matches__un UNIQUE ( season,
                                                 is_autumn,
                                                 league_id,
                                                 official_id );

CREATE TABLE stat_assessor_matches (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    assessor_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_assessor_matches_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_assessor_matches
    ADD CONSTRAINT stat_assessor_matches__un UNIQUE ( season,
                                                      is_autumn,
                                                      league_id,
                                                      assessor_id );

CREATE TABLE stat_assessor_red (
    id                INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season            SMALLINT UNSIGNED NOT NULL,
    is_autumn         TINYINT(1) NOT NULL,
    number_of_cards   TINYINT UNSIGNED NOT NULL,
    league_id         TINYINT UNSIGNED NOT NULL,
    assessor_id       VARCHAR(8) NOT NULL,
    CONSTRAINT stat_assessor_red_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_assessor_red
    ADD CONSTRAINT stat_assessor_red__un UNIQUE ( season,
                                                  is_autumn,
                                                  league_id,
                                                  assessor_id );

CREATE TABLE stat_assessor_team (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    team_id             INTEGER UNSIGNED NOT NULL,
    assessor_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_assessor_team_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_assessor_team
    ADD CONSTRAINT stat_assessor_team__un UNIQUE ( season,
                                                   is_autumn,
                                                   league_id,
                                                   team_id,
                                                   assessor_id );

CREATE TABLE stat_assessor_yellow (
    id                INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season            SMALLINT UNSIGNED NOT NULL,
    is_autumn         TINYINT(1) NOT NULL,
    number_of_cards   TINYINT UNSIGNED NOT NULL,
    league_id         TINYINT UNSIGNED NOT NULL,
    assessor_id       VARCHAR(8) NOT NULL,
    CONSTRAINT stat_assessor_yellow_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_assessor_yellow
    ADD CONSTRAINT stat_assessor_yellow__un UNIQUE ( season,
                                                     is_autumn,
                                                     league_id,
                                                     assessor_id );

CREATE TABLE stat_official_assessor (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    assessor_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_official_assessor_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_official_assessor
    ADD CONSTRAINT stat_official_assessor__un UNIQUE ( season,
                                                       is_autumn,
                                                       league_id,
                                                       official_id,
                                                       assessor_id );

CREATE TABLE stat_official_home_team (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    team_id             INTEGER UNSIGNED NOT NULL,
    CONSTRAINT stat_official_home_team_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_official_home_team
    ADD CONSTRAINT stat_official_home_team__un UNIQUE ( season,
                                                        is_autumn,
                                                        league_id,
                                                        official_id,
                                                        team_id );

CREATE TABLE stat_official_matches (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_official_matches_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_official_matches
    ADD CONSTRAINT stat_official_matches__un UNIQUE ( season,
                                                      is_autumn,
                                                      league_id,
                                                      official_id );

CREATE TABLE stat_official_official (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id1        VARCHAR(8) NOT NULL,
    official_id2        VARCHAR(8) NOT NULL,
    CONSTRAINT stat_official_official_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_official_official
    ADD CONSTRAINT stat_official_official__un UNIQUE ( season,
                                                       is_autumn,
                                                       league_id,
                                                       official_id1,
                                                       official_id2 );

CREATE TABLE stat_official_team (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    team_id             INTEGER UNSIGNED NOT NULL,
    CONSTRAINT stat_official_team_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_official_team
    ADD CONSTRAINT stat_official_team__un UNIQUE ( season,
                                                   is_autumn,
                                                   league_id,
                                                   official_id,
                                                   team_id );

CREATE TABLE stat_referee_ar (
    id                    INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season                SMALLINT UNSIGNED NOT NULL,
    is_autumn             TINYINT(1) NOT NULL,
    number_of_matches     TINYINT UNSIGNED NOT NULL,
    league_id             TINYINT UNSIGNED NOT NULL,
    referee_official_id   VARCHAR(8) NOT NULL,
    ar_official_id        VARCHAR(8) NOT NULL,
    CONSTRAINT stat_referee_ar_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_ar
    ADD CONSTRAINT stat_referee_ar__un UNIQUE ( season,
                                                is_autumn,
                                                league_id,
                                                referee_official_id,
                                                ar_official_id );

CREATE TABLE stat_referee_assessor (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    assessor_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_referee_assessor_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_assessor
    ADD CONSTRAINT stat_referee_assessor__un UNIQUE ( season,
                                                      is_autumn,
                                                      league_id,
                                                      official_id,
                                                      assessor_id );

CREATE TABLE stat_referee_cards_minutes (
    id                INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season            SMALLINT UNSIGNED NOT NULL,
    card_type         ENUM('yellow', 'red') NOT NULL,
    minute            TINYINT UNSIGNED NOT NULL,
    number_of_cards   TINYINT UNSIGNED NOT NULL,
    official_id       VARCHAR(8) NOT NULL,
    league_id         TINYINT UNSIGNED NOT NULL,
    CONSTRAINT stat_referee_cards_minutes_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_cards_minutes
    ADD CONSTRAINT stat_referee_cards_minutes__un UNIQUE ( season,
                                                           card_type,
                                                           minute,
                                                           official_id,
                                                           league_id );

CREATE TABLE stat_referee_matches (
    id                  INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season              SMALLINT UNSIGNED NOT NULL,
    is_autumn           TINYINT(1) NOT NULL,
    number_of_matches   TINYINT UNSIGNED NOT NULL,
    league_id           TINYINT UNSIGNED NOT NULL,
    official_id         VARCHAR(8) NOT NULL,
    CONSTRAINT stat_referee_matches_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_matches
    ADD CONSTRAINT stat_referee_matches__un UNIQUE ( season,
                                                     is_autumn,
                                                     league_id,
                                                     official_id );

CREATE TABLE stat_referee_red (
    id                INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season            SMALLINT UNSIGNED NOT NULL,
    is_autumn         TINYINT(1) NOT NULL,
    number_of_cards   TINYINT UNSIGNED NOT NULL,
    league_id         TINYINT UNSIGNED NOT NULL,
    official_id       VARCHAR(8) NOT NULL,
    CONSTRAINT stat_referee_red_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_red
    ADD CONSTRAINT stat_referee_red__un UNIQUE ( season,
                                                 is_autumn,
                                                 league_id,
                                                 official_id );

CREATE TABLE stat_referee_red_offence (
    id                INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season            SMALLINT UNSIGNED NOT NULL,
    is_autumn         TINYINT(1) NOT NULL,
    number_of_cards   TINYINT UNSIGNED NOT NULL,
    league_id         TINYINT UNSIGNED NOT NULL,
    official_id       VARCHAR(8) NOT NULL,
    offence_id        TINYINT UNSIGNED NOT NULL,
    CONSTRAINT stat_referee_red_offence_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_red_offence
    ADD CONSTRAINT stat_referee_red_offence__un UNIQUE ( season,
                                                         is_autumn,
                                                         league_id,
                                                         official_id,
                                                         offence_id );

CREATE TABLE stat_referee_yellow (
    id                INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    season            SMALLINT UNSIGNED NOT NULL,
    is_autumn         TINYINT(1) NOT NULL,
    number_of_cards   TINYINT UNSIGNED NOT NULL,
    league_id         TINYINT UNSIGNED NOT NULL,
    official_id       VARCHAR(8) NOT NULL,
    CONSTRAINT stat_referee_yellow_pk PRIMARY KEY ( id )
)
COLLATE utf8mb4_unicode_ci;

ALTER TABLE stat_referee_yellow
    ADD CONSTRAINT stat_referee_yellow__un UNIQUE ( season,
                                                    is_autumn,
                                                    league_id,
                                                    official_id );

CREATE TABLE stat_referee_yellow_first (
    game_id       INTEGER UNSIGNED NOT NULL,
    season        SMALLINT UNSIGNED NOT NULL,
    is_autumn     TINYINT(1) NOT NULL,
    minute        TINYINT UNSIGNED NOT NULL,
    league_id     TINYINT UNSIGNED NOT NULL,
    official_id   VARCHAR(8) NOT NULL,
    CONSTRAINT stat_referee_yellow_first_pk PRIMARY KEY ( game_id )
)
COLLATE utf8mb4_unicode_ci;


-- FOREIGN KEYS --

ALTER TABLE game
    ADD CONSTRAINT game_ar1_official_id_fk FOREIGN KEY ( ar1_official_id )
        REFERENCES official ( id );

ALTER TABLE game
    ADD CONSTRAINT game_ar2_official_id_fk FOREIGN KEY ( ar2_official_id )
        REFERENCES official ( id );

ALTER TABLE game
    ADD CONSTRAINT game_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE game
    ADD CONSTRAINT game_away_team_id_fk FOREIGN KEY ( away_team_id )
        REFERENCES team ( id );

ALTER TABLE game
    ADD CONSTRAINT game_home_team_id_fk FOREIGN KEY ( home_team_id )
        REFERENCES team ( id );

ALTER TABLE game
    ADD CONSTRAINT game_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE game
    ADD CONSTRAINT game_referee_official_id_fk FOREIGN KEY ( referee_official_id )
        REFERENCES official ( id );

ALTER TABLE nomination_list
    ADD CONSTRAINT nomination_list_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE red_card
    ADD CONSTRAINT red_card_game_id_fk FOREIGN KEY ( game_id )
        REFERENCES game ( id )
        ON DELETE CASCADE;

ALTER TABLE red_card
    ADD CONSTRAINT red_card_offence_id_fk FOREIGN KEY ( offence_id )
        REFERENCES offence ( id );

ALTER TABLE red_card
    ADD CONSTRAINT red_card_team_id_fk FOREIGN KEY ( team_id )
        REFERENCES team ( id );

ALTER TABLE yellow_card
    ADD CONSTRAINT yellow_card_game_id_fk FOREIGN KEY ( game_id )
        REFERENCES game ( id )
        ON DELETE CASCADE;

ALTER TABLE post 
    ADD CONSTRAINT post_admin_id_fk FOREIGN KEY ( admin_id ) 
        REFERENCES admin ( id );

ALTER TABLE stat_ar1_and_ar2_matches
    ADD CONSTRAINT stat_ar1_and_ar2_matches_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_ar1_and_ar2_matches
    ADD CONSTRAINT stat_ar1_and_ar2_matches_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_ar1_matches
    ADD CONSTRAINT stat_ar1_matches_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_ar1_matches
    ADD CONSTRAINT stat_ar1_matches_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_ar2_matches
    ADD CONSTRAINT stat_ar2_matches_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_ar2_matches
    ADD CONSTRAINT stat_ar2_matches_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_assessor_matches
    ADD CONSTRAINT stat_assessor_matches_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE stat_assessor_matches
    ADD CONSTRAINT stat_assessor_matches_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_assessor_red
    ADD CONSTRAINT stat_assessor_red_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE stat_assessor_red
    ADD CONSTRAINT stat_assessor_red_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_assessor_team
    ADD CONSTRAINT stat_assessor_team_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE stat_assessor_team
    ADD CONSTRAINT stat_assessor_team_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_assessor_team
    ADD CONSTRAINT stat_assessor_team_team_id_fk FOREIGN KEY ( team_id )
        REFERENCES team ( id );

ALTER TABLE stat_assessor_yellow
    ADD CONSTRAINT stat_assessor_yellow_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE stat_assessor_yellow
    ADD CONSTRAINT stat_assessor_yellow_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_official_assessor
    ADD CONSTRAINT stat_official_assessor_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE stat_official_assessor
    ADD CONSTRAINT stat_official_assessor_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_official_assessor
    ADD CONSTRAINT stat_official_assessor_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_official_home_team
    ADD CONSTRAINT stat_official_home_team_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_official_home_team
    ADD CONSTRAINT stat_official_home_team_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_official_home_team
    ADD CONSTRAINT stat_official_home_team_team_id_fk FOREIGN KEY ( team_id )
        REFERENCES team ( id );

ALTER TABLE stat_official_matches
    ADD CONSTRAINT stat_official_matches_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_official_matches
    ADD CONSTRAINT stat_official_matches_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_official_official
    ADD CONSTRAINT stat_official_official_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_official_official
    ADD CONSTRAINT stat_official_official_official_id1_fk FOREIGN KEY ( official_id1 )
        REFERENCES official ( id );

ALTER TABLE stat_official_official
    ADD CONSTRAINT stat_official_official_official_id2_fk FOREIGN KEY ( official_id2 )
        REFERENCES official ( id );

ALTER TABLE stat_official_team
    ADD CONSTRAINT stat_official_team_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_official_team
    ADD CONSTRAINT stat_official_team_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_official_team
    ADD CONSTRAINT stat_official_team_team_id_fk FOREIGN KEY ( team_id )
        REFERENCES team ( id );

ALTER TABLE stat_referee_ar
    ADD CONSTRAINT stat_referee_ar_ar_official_id_fk FOREIGN KEY ( ar_official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_ar
    ADD CONSTRAINT stat_referee_ar_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_ar
    ADD CONSTRAINT stat_referee_ar_referee_official_id_fk FOREIGN KEY ( referee_official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_assessor
    ADD CONSTRAINT stat_referee_assessor_assessor_id_fk FOREIGN KEY ( assessor_id )
        REFERENCES assessor ( id );

ALTER TABLE stat_referee_assessor
    ADD CONSTRAINT stat_referee_assessor_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_assessor
    ADD CONSTRAINT stat_referee_assessor_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_cards_minutes
    ADD CONSTRAINT stat_referee_cards_minutes_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_cards_minutes
    ADD CONSTRAINT stat_referee_cards_minutes_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_matches
    ADD CONSTRAINT stat_referee_matches_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_matches
    ADD CONSTRAINT stat_referee_matches_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_red
    ADD CONSTRAINT stat_referee_red_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_red_offence
    ADD CONSTRAINT stat_referee_red_offence_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_red_offence
    ADD CONSTRAINT stat_referee_red_offence_offence_id_fk FOREIGN KEY ( offence_id )
        REFERENCES offence ( id );

ALTER TABLE stat_referee_red_offence
    ADD CONSTRAINT stat_referee_red_offence_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_red
    ADD CONSTRAINT stat_referee_red_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_yellow_first
    ADD CONSTRAINT stat_referee_yellow_first_game_id_fk FOREIGN KEY ( game_id )
        REFERENCES game ( id )
        ON DELETE CASCADE;

ALTER TABLE stat_referee_yellow_first
    ADD CONSTRAINT stat_referee_yellow_first_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_yellow_first
    ADD CONSTRAINT stat_referee_yellow_first_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );

ALTER TABLE stat_referee_yellow
    ADD CONSTRAINT stat_referee_yellow_league_id_fk FOREIGN KEY ( league_id )
        REFERENCES league ( id );

ALTER TABLE stat_referee_yellow
    ADD CONSTRAINT stat_referee_yellow_official_id_fk FOREIGN KEY ( official_id )
        REFERENCES official ( id );


-- INSERTS BASIC VALUES --
INSERT INTO `offence` (`id`, `short_name`, `full_name`) VALUES
(1, 'Ruka – zabránění branky', 'Zabránění soupeřovu družstvu dosáhnout branky'),
(2, 'Zmaření zjevné brankové možnosti', 'Zmaření zjevné brankové možnosti soupeřova družstva'),
(3, 'Surová hra', 'Surová hra'),
(4, 'Kousnutí nebo plivnutí', 'Plivnutí na soupeře nebo jinou osobu'),
(5, 'Hrubé nesportovní chování', 'Hrubé nesportovní chování'),
(6, 'Urážlivé výroky nebo gesta', 'Použití pohoršujících, urážlivých nebo ponižujících výroků nebo gest'),
(7, '2. žlutá karta', 'Druhé napomenutí během utkání');

INSERT INTO `official` (`id`, `name`) VALUES ('00000000', 'Laik');

INSERT INTO `assessor` (`id`, `name`) VALUES ('00000000', 'Bez delegáta');
