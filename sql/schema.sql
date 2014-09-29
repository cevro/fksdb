SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


-- -----------------------------------------------------
-- Table `contest`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contest` ;

CREATE  TABLE IF NOT EXISTS `contest` (
  `contest_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`contest_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = '(sub)semináře';


-- -----------------------------------------------------
-- Table `event_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event_type` ;

CREATE  TABLE IF NOT EXISTS `event_type` (
  `event_type_id` INT NOT NULL AUTO_INCREMENT ,
  `contest_id` INT(11) NOT NULL ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`event_type_id`) ,
  INDEX `fk_event_type_contest1_idx` (`contest_id` ASC) ,
  CONSTRAINT `fk_event_type_contest1`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event` ;

CREATE  TABLE IF NOT EXISTS `event` (
  `event_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `event_type_id` INT NOT NULL ,
  `year` TINYINT(4) NOT NULL COMMENT 'ročník semináře' ,
  `event_year` TINYINT(4) NOT NULL COMMENT 'ročník akce' ,
  `begin` DATE NOT NULL COMMENT 'první den akce' ,
  `end` DATE NOT NULL COMMENT 'poslední den akce, u jednodenní akce shodný s begin' ,
  `registration_begin` DATE NULL DEFAULT NULL COMMENT 'případný počátek webové registrace' ,
  `registration_end` DATE NULL DEFAULT NULL COMMENT 'případný konec webové registrace' ,
  `name` VARCHAR(255) NOT NULL COMMENT 'název akce' ,
  `fb_album_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'id galerie na Facebooku' ,
  `report` TEXT NULL DEFAULT NULL COMMENT '(HTML) zápis z proběhlé akce' ,
  `parameters` TEXT NULL COMMENT 'optional parameters\nin Neon syntax,\nscheme is define with action' ,
  PRIMARY KEY (`event_id`) ,
  INDEX `fk_event_event_type1_idx` (`event_type_id` ASC) ,
  UNIQUE INDEX `UQ_EVENT_YEAR` (`event_year` ASC, `event_type_id` ASC) ,
  CONSTRAINT `fk_event_event_type1`
    FOREIGN KEY (`event_type_id` )
    REFERENCES `event_type` (`event_type_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person` ;

CREATE  TABLE IF NOT EXISTS `person` (
  `person_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `family_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'Příjmení (nebo více příjmení oddělených jednou mezerou)' ,
  `other_name` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'Křestní jména, von, de atd., oddělená jednou mezerou' ,
  `display_name` VARCHAR(511) CHARACTER SET 'utf8' COLLATE 'utf8_czech_ci' NULL DEFAULT NULL COMMENT 'zobrazované jméno, liší-li se od <other_name> <family_name>' ,
  `gender` ENUM('M','F') CHARACTER SET 'utf8' NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`person_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci
COMMENT = 'řazení: <family_name><other_name>, zobrazení <other_name> <f';


-- -----------------------------------------------------
-- Table `event_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event_status` ;

CREATE  TABLE IF NOT EXISTS `event_status` (
  `status` VARCHAR(20) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`status`) )
ENGINE = InnoDB
COMMENT = 'list of allowed statuses (for data integrity)';


-- -----------------------------------------------------
-- Table `event_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event_participant` ;

CREATE  TABLE IF NOT EXISTS `event_participant` (
  `event_participant_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `event_id` INT(11) NOT NULL ,
  `person_id` INT(11) NOT NULL ,
  `note` TEXT NULL DEFAULT NULL COMMENT 'poznámka' ,
  `status` VARCHAR(20) NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'čas vytvoření přihlášky' ,
  `accomodation` TINYINT(1) NULL ,
  PRIMARY KEY (`event_participant_id`) ,
  INDEX `action_id` (`event_id` ASC) ,
  INDEX `person_id` (`person_id` ASC) ,
  INDEX `fk_event_participant_e_status1_idx` (`status` ASC) ,
  CONSTRAINT `action_application_ibfk_1`
    FOREIGN KEY (`event_id` )
    REFERENCES `event` (`event_id` ),
  CONSTRAINT `action_application_ibfk_2`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` ),
  CONSTRAINT `fk_event_participant_e_status1`
    FOREIGN KEY (`status` )
    REFERENCES `event_status` (`status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `region`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `region` ;

CREATE  TABLE IF NOT EXISTS `region` (
  `region_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `country_iso` CHAR(2) NOT NULL COMMENT 'ISO 3166-1' ,
  `nuts` VARCHAR(5) NOT NULL COMMENT 'NUTS of the EU region\nor ISO 3166-1 for other countries' ,
  `name` VARCHAR(255) NOT NULL COMMENT 'name of the region in the language intelligible in that region' ,
  PRIMARY KEY (`region_id`) ,
  UNIQUE INDEX `nuts` (`nuts` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Ciselnik regionu pro vyber skoly v registraci';


-- -----------------------------------------------------
-- Table `address`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `address` ;

CREATE  TABLE IF NOT EXISTS `address` (
  `address_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `first_row` VARCHAR(255) NULL DEFAULT NULL COMMENT 'doplňkový řádek adresy (např. bytem u X Y)' ,
  `second_row` VARCHAR(255) NULL DEFAULT NULL COMMENT 'ještě doplňkovější řádek adresy (nikdo neví)' ,
  `target` VARCHAR(255) NOT NULL COMMENT 'ulice č.p./or., vesnice č.p./or., poštovní přihrádka atd.' ,
  `city` VARCHAR(255) NOT NULL COMMENT 'město doručovací pošty' ,
  `postal_code` CHAR(5) NULL DEFAULT NULL COMMENT 'PSČ (pro ČR a SR)' ,
  `region_id` INT(11) NOT NULL COMMENT 'detekce státu && formátovacích zvyklostí' ,
  PRIMARY KEY (`address_id`) ,
  INDEX `region_id` (`region_id` ASC) ,
  CONSTRAINT `address_ibfk_1`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`region_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'adresa jako poštovní nikoli územní identifikátor, immutable.';


-- -----------------------------------------------------
-- Table `login`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `login` ;

CREATE  TABLE IF NOT EXISTS `login` (
  `login_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `person_id` INT(11) NULL ,
  `login` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Login name' ,
  `hash` CHAR(40) NULL DEFAULT NULL COMMENT 'sha1(login_id . md5(password)) as hexadecimal' ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `last_login` DATETIME NULL DEFAULT NULL ,
  `active` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`login_id`) ,
  UNIQUE INDEX `login` (`login` ASC) ,
  UNIQUE INDEX `person_id_UNIQUE` (`person_id` ASC) ,
  CONSTRAINT `login_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `auth_token`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `auth_token` ;

CREATE  TABLE IF NOT EXISTS `auth_token` (
  `token_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `login_id` INT(11) NOT NULL ,
  `token` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(31) NOT NULL COMMENT 'type of token (from programmer\'s POV)' ,
  `data` VARCHAR(255) NULL COMMENT 'various purpose data' ,
  `since` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `until` TIMESTAMP NULL DEFAULT NULL ,
  UNIQUE INDEX `token_UNIQUE` (`token` ASC) ,
  PRIMARY KEY (`token_id`) ,
  INDEX `fk_auth_token_login1_idx` (`login_id` ASC) ,
  CONSTRAINT `fk_auth_token_login1`
    FOREIGN KEY (`login_id` )
    REFERENCES `login` (`login_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contestant_base`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contestant_base` ;

CREATE  TABLE IF NOT EXISTS `contestant_base` (
  `ct_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `contest_id` INT(11) NOT NULL COMMENT 'seminář' ,
  `year` TINYINT(4) NOT NULL COMMENT 'Rocnik semináře' ,
  `person_id` INT(11) NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`ct_id`) ,
  UNIQUE INDEX `contest_id` (`contest_id` ASC, `year` ASC, `person_id` ASC) ,
  INDEX `person_id` (`person_id` ASC) ,
  CONSTRAINT `contestant_base_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `contestant_base_ibfk_3`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
COMMENT = 'Instance ucastnika (v konkretnim rocniku a semináři)';


-- -----------------------------------------------------
-- Table `dakos_person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dakos_person` ;

CREATE  TABLE IF NOT EXISTS `dakos_person` (
  `dakos_id` INT(11) NOT NULL COMMENT 'Id účastníka z dakosího exportu' ,
  `person_id` INT(11) NOT NULL ,
  PRIMARY KEY (`dakos_id`) ,
  INDEX `person_id` (`person_id` ASC) ,
  CONSTRAINT `dakos_person_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Identifikace osoby z DaKoSu';


-- -----------------------------------------------------
-- Table `school`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `school` ;

CREATE  TABLE IF NOT EXISTS `school` (
  `school_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name_full` VARCHAR(255) NULL DEFAULT NULL COMMENT 'plný název školy' ,
  `name` VARCHAR(255) NOT NULL COMMENT 'zkrácený název školy (na obálku)' ,
  `name_abbrev` VARCHAR(32) NOT NULL COMMENT 'Zkratka pouzivana napr. ve vysledkove listine' ,
  `address_id` INT(11) NOT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Kontaktní e-mail' ,
  `ic` CHAR(8) NULL DEFAULT NULL COMMENT 'IČ (osm číslic)' ,
  `izo` VARCHAR(32) NULL DEFAULT NULL COMMENT 'IZO kód (norma?)' ,
  `active` TINYINT(1) NULL DEFAULT NULL COMMENT 'Platný záznam školy' ,
  `note` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`school_id`) ,
  UNIQUE INDEX `ic` (`ic` ASC) ,
  UNIQUE INDEX `izo` (`izo` ASC) ,
  INDEX `address_id` (`address_id` ASC) ,
  CONSTRAINT `school_ibfk_1`
    FOREIGN KEY (`address_id` )
    REFERENCES `address` (`address_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `dakos_school`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `dakos_school` ;

CREATE  TABLE IF NOT EXISTS `dakos_school` (
  `dakos_SKOLA_Id` INT(11) NOT NULL ,
  `school_id` INT(11) NOT NULL ,
  PRIMARY KEY (`dakos_SKOLA_Id`) ,
  INDEX `school_id` (`school_id` ASC) ,
  CONSTRAINT `dakos_school_ibfk_1`
    FOREIGN KEY (`school_id` )
    REFERENCES `school` (`school_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `olddb_person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `olddb_person` ;

CREATE  TABLE IF NOT EXISTS `olddb_person` (
  `olddb_uid` INT(11) NOT NULL COMMENT 'users.id ze staré DB' ,
  `person_id` INT(11) NOT NULL ,
  `olddb_redundant` TINYINT(1) NOT NULL COMMENT 'Tato data se nezkopírovala' ,
  PRIMARY KEY (`olddb_uid`) ,
  INDEX `person_id` (`person_id` ASC) ,
  CONSTRAINT `olddb_person_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `org`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `org` ;

CREATE  TABLE IF NOT EXISTS `org` (
  `org_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `person_id` INT(11) NOT NULL ,
  `contest_id` INT(11) NOT NULL ,
  `since` TINYINT(4) NOT NULL COMMENT 'od kterého ročníku orguje' ,
  `until` TINYINT(4) NULL DEFAULT NULL COMMENT 'v kterém rončíku skončil' ,
  `role` VARCHAR(255) NULL DEFAULT NULL COMMENT 'hlavní org, úlohář, etc.' ,
  `order` TINYINT(4) NOT NULL COMMENT 'pořadí pro řazení ve výpisech' ,
  `contribution` TEXT NULL ,
  PRIMARY KEY (`org_id`) ,
  UNIQUE INDEX `contest_id` (`contest_id` ASC, `person_id` ASC) ,
  UNIQUE INDEX `contest_id_2` (`contest_id` ASC) ,
  INDEX `person_id` (`person_id` ASC) ,
  CONSTRAINT `org_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `org_ibfk_2`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` )
    ON DELETE CASCADE
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `role` ;

CREATE  TABLE IF NOT EXISTS `role` (
  `role_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(16) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`role_id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `grant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `grant` ;

CREATE  TABLE IF NOT EXISTS `grant` (
  `grant_id` INT(11) NULL AUTO_INCREMENT ,
  `login_id` INT(11) NOT NULL ,
  `role_id` INT(11) NOT NULL ,
  `contest_id` INT NOT NULL ,
  INDEX `right_id` (`role_id` ASC) ,
  PRIMARY KEY (`grant_id`) ,
  UNIQUE INDEX `grant_UNIQUE` (`role_id` ASC, `login_id` ASC, `contest_id` ASC) ,
  INDEX `fk_grant_contest1_idx` (`contest_id` ASC) ,
  INDEX `permission_ibfk_1_idx` (`login_id` ASC) ,
  CONSTRAINT `permission_ibfk_1`
    FOREIGN KEY (`login_id` )
    REFERENCES `login` (`login_id` )
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
  CONSTRAINT `permission_ibfk_2`
    FOREIGN KEY (`role_id` )
    REFERENCES `role` (`role_id` )
    ON DELETE CASCADE,
  CONSTRAINT `fk_grant_contest1`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `person_info`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person_info` ;

CREATE  TABLE IF NOT EXISTS `person_info` (
  `person_id` INT(11) NOT NULL ,
  `born` DATE NULL DEFAULT NULL COMMENT 'datum narození' ,
  `id_number` VARCHAR(32) NULL DEFAULT NULL COMMENT 'číslo OP či ekvivalent' ,
  `born_id` VARCHAR(32) NULL DEFAULT NULL COMMENT 'rodné číslo (pouze u CZ, SK)' ,
  `phone` VARCHAR(32) NULL DEFAULT NULL COMMENT 'tel. číslo' ,
  `im` VARCHAR(32) NULL DEFAULT NULL COMMENT 'ICQ, XMPP, etc.' ,
  `note` TEXT NULL DEFAULT NULL COMMENT 'ostatní/poznámka' ,
  `uk_login` VARCHAR(8) NULL DEFAULT NULL COMMENT 'CAS login, pro orgy' ,
  `account` VARCHAR(32) NULL DEFAULT NULL COMMENT 'bankovní účet jako text' ,
  `agreed` DATETIME NULL DEFAULT NULL COMMENT 'čas posledního souhlasu ze zprac. os. ú. nebo null' ,
  `birthplace` VARCHAR(255) NULL DEFAULT NULL COMMENT 'název města narození osoby' ,
  `email` VARCHAR(255) NULL ,
  `origin` TEXT NULL COMMENT 'Odkud se o nás dozvěděl.' ,
  `tex_signature` VARCHAR(32) NULL DEFAULT NULL COMMENT 'zkratka používaná v TeXových vzorácích' ,
  `domain_alias` VARCHAR(32) NULL COMMENT 'alias v doméně fykos.cz' ,
  `career` TEXT NULL COMMENT 'co studuje/kde pracuje' ,
  `homepage` VARCHAR(255) NULL COMMENT 'URL osobní homepage' ,
  `fb_id` VARCHAR(255) NULL ,
  `linkedin_id` VARCHAR(255) NULL ,
  PRIMARY KEY (`person_id`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  UNIQUE INDEX `uk_login_UNIQUE` (`uk_login` ASC) ,
  UNIQUE INDEX `born_id_UNIQUE` (`born_id` ASC) ,
  UNIQUE INDEX `fb_id_UNIQUE` (`fb_id` ASC) ,
  UNIQUE INDEX `domain_alias_UNIQUE` (`domain_alias` ASC) ,
  UNIQUE INDEX `tex_signature_UNIQUE` (`tex_signature` ASC) ,
  UNIQUE INDEX `linkedin_id_UNIQUE` (`linkedin_id` ASC) ,
  CONSTRAINT `person_info_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE CASCADE
    ON UPDATE RESTRICT)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Podrobné informace o osobě, zde jsou všechny osobní údaje (t';


-- -----------------------------------------------------
-- Table `post_contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `post_contact` ;

CREATE  TABLE IF NOT EXISTS `post_contact` (
  `post_contact_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `person_id` INT(11) NOT NULL ,
  `address_id` INT(11) NOT NULL ,
  `type` ENUM('P','D') NOT NULL COMMENT 'doručovací (Delivery), trvalá (Permanent)' ,
  PRIMARY KEY (`post_contact_id`) ,
  INDEX `person_id` (`person_id` ASC) ,
  INDEX `address_id` (`address_id` ASC) ,
  UNIQUE INDEX `person_id_type` (`person_id` ASC, `type` ASC) ,
  CONSTRAINT `post_contact_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE CASCADE,
  CONSTRAINT `post_contact_ibfk_2`
    FOREIGN KEY (`address_id` )
    REFERENCES `address` (`address_id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Přiřazení adres lidem vztahem M:N';


-- -----------------------------------------------------
-- Table `psc_region`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `psc_region` ;

CREATE  TABLE IF NOT EXISTS `psc_region` (
  `psc` CHAR(5) NOT NULL ,
  `region_id` INT(11) NOT NULL ,
  PRIMARY KEY (`psc`) ,
  INDEX `region_id` (`region_id` ASC) ,
  CONSTRAINT `psc_region_ibfk_1`
    FOREIGN KEY (`region_id` )
    REFERENCES `region` (`region_id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'mapování českých a slovenských PSČ na evidovaný region';


-- -----------------------------------------------------
-- Table `mail_batch`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mail_batch` ;

CREATE  TABLE IF NOT EXISTS `mail_batch` (
  `mail_batch_id` INT NOT NULL AUTO_INCREMENT ,
  `flag_id` INT(11) NULL ,
  `description` TEXT NULL COMMENT 'druh rozesílané pošty (brožurka, pozvánka, etc.)' ,
  `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`mail_batch_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mail_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mail_log` ;

CREATE  TABLE IF NOT EXISTS `mail_log` (
  `person_id` INT(11) NOT NULL ,
  `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `address_id` INT NULL ,
  `email` VARCHAR(255) NULL ,
  `mail_batch_id` INT(11) NOT NULL ,
  INDEX `person_id` (`person_id` ASC) ,
  PRIMARY KEY (`person_id`) ,
  INDEX `fk_mail_log_mail_batch1_idx` (`mail_batch_id` ASC) ,
  INDEX `fk_mail_log_address1_idx` (`address_id` ASC) ,
  CONSTRAINT `si_log_ibfk_1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE CASCADE,
  CONSTRAINT `fk_mail_log_mail_batch1`
    FOREIGN KEY (`mail_batch_id` )
    REFERENCES `mail_batch` (`mail_batch_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mail_log_address1`
    FOREIGN KEY (`address_id` )
    REFERENCES `address` (`address_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'v tabulce se loguje historická hodnota adresy nebo emailu, k' /* comment truncated */;


-- -----------------------------------------------------
-- Table `task`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `task` ;

CREATE  TABLE IF NOT EXISTS `task` (
  `task_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `label` VARCHAR(16) NOT NULL COMMENT 'Oznaceni ulohy, treba \"23-4-5\"' ,
  `name_cs` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Jmeno ulohy' ,
  `name_en` VARCHAR(255) NULL ,
  `contest_id` INT(11) NOT NULL COMMENT 'seminář' ,
  `year` TINYINT(4) NOT NULL COMMENT 'Rocnik seminare' ,
  `series` TINYINT(4) NOT NULL COMMENT 'Serie' ,
  `tasknr` TINYINT(4) NULL DEFAULT NULL COMMENT 'Uloha' ,
  `points` TINYINT(4) NULL DEFAULT NULL COMMENT 'Maximalni pocet bodu' ,
  `submit_start` DATETIME NULL DEFAULT NULL COMMENT 'Od kdy se smi submitovat' ,
  `submit_deadline` DATETIME NULL DEFAULT NULL COMMENT 'Do kdy' ,
  PRIMARY KEY (`task_id`) ,
  INDEX `contest_id` (`contest_id` ASC) ,
  UNIQUE INDEX `contest_id_year_series_tasknr` (`contest_id` ASC, `year` ASC, `series` ASC, `tasknr` ASC) ,
  CONSTRAINT `task_ibfk_1`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `submit`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `submit` ;

CREATE  TABLE IF NOT EXISTS `submit` (
  `submit_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ct_id` INT(11) NOT NULL COMMENT 'Contestant' ,
  `task_id` INT(11) NOT NULL COMMENT 'Task' ,
  `submitted_on` DATETIME NULL ,
  `source` ENUM('post','upload') NOT NULL COMMENT 'odkud přišlo řešení' ,
  `note` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Pocet stranek a jine poznamky' ,
  `raw_points` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Pred prepoctem' ,
  `calc_points` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Cache spoctenych bodu.\n' ,
  PRIMARY KEY (`submit_id`) ,
  UNIQUE INDEX `cons_uniq` (`ct_id` ASC, `task_id` ASC) ,
  INDEX `task_id` (`task_id` ASC) ,
  CONSTRAINT `submit_ibfk_1`
    FOREIGN KEY (`ct_id` )
    REFERENCES `contestant_base` (`ct_id` ),
  CONSTRAINT `submit_ibfk_2`
    FOREIGN KEY (`task_id` )
    REFERENCES `task` (`task_id` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `event_has_org`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `event_has_org` ;

CREATE  TABLE IF NOT EXISTS `event_has_org` (
  `event_id` INT(11) NOT NULL ,
  `person_id` INT(11) NOT NULL ,
  PRIMARY KEY (`event_id`, `person_id`) ,
  INDEX `fk_event_has_org_person1_idx` (`person_id` ASC) ,
  CONSTRAINT `fk_action_has_org_event1`
    FOREIGN KEY (`event_id` )
    REFERENCES `event` (`event_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_has_org_person1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `task_contribution`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `task_contribution` ;

CREATE  TABLE IF NOT EXISTS `task_contribution` (
  `contribution_id` INT NOT NULL AUTO_INCREMENT ,
  `task_id` INT(11) NOT NULL ,
  `person_id` INT(11) NOT NULL ,
  `type` ENUM('author', 'solution', 'grade') NOT NULL ,
  PRIMARY KEY (`contribution_id`) ,
  INDEX `fk_org_task_contribution_task1_idx` (`task_id` ASC) ,
  INDEX `fk_task_contribution_person1_idx` (`person_id` ASC) ,
  CONSTRAINT `fk_org_task_contribution_task1`
    FOREIGN KEY (`task_id` )
    REFERENCES `task` (`task_id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_task_contribution_person1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e_fyziklani_team`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_fyziklani_team` ;

CREATE  TABLE IF NOT EXISTS `e_fyziklani_team` (
  `e_fyziklani_team_id` INT NOT NULL AUTO_INCREMENT ,
  `event_id` INT(11) NOT NULL ,
  `name` VARCHAR(30) NOT NULL ,
  `status` VARCHAR(20) NOT NULL ,
  `teacher_id` INT(11) NULL COMMENT 'kontaktní osoba' ,
  `teacher_accomodation` TINYINT(1) NOT NULL ,
  `teacher_present` TINYINT(1) NOT NULL ,
  `category` CHAR(1) NOT NULL ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `phone` VARCHAR(30) NULL ,
  `note` TEXT NULL ,
  PRIMARY KEY (`e_fyziklani_team_id`) ,
  INDEX `fk_e_fyziklani_team_event1_idx` (`event_id` ASC) ,
  INDEX `fk_e_fyziklani_team_person1_idx` (`teacher_id` ASC) ,
  INDEX `fk_e_fyziklani_team_e_status1_idx` (`status` ASC) ,
  CONSTRAINT `fk_e_fyziklani_team_event1`
    FOREIGN KEY (`event_id` )
    REFERENCES `event` (`event_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_e_fyziklani_team_person1`
    FOREIGN KEY (`teacher_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_e_fyziklani_team_e_status1`
    FOREIGN KEY (`status` )
    REFERENCES `event_status` (`status` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e_fyziklani_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_fyziklani_participant` ;

CREATE  TABLE IF NOT EXISTS `e_fyziklani_participant` (
  `event_participant_id` INT NOT NULL ,
  `e_fyziklani_team_id` INT NOT NULL ,
  PRIMARY KEY (`event_participant_id`) ,
  INDEX `fk_e_fyziklani_participant_e_fyziklani_team1_idx` (`e_fyziklani_team_id` ASC) ,
  UNIQUE INDEX `uq_team_participan` (`event_participant_id` ASC, `e_fyziklani_team_id` ASC) ,
  CONSTRAINT `fk_e_participant_fyziklani_event_participant1`
    FOREIGN KEY (`event_participant_id` )
    REFERENCES `event_participant` (`event_participant_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_e_fyziklani_participant_e_fyziklani_team1`
    FOREIGN KEY (`e_fyziklani_team_id` )
    REFERENCES `e_fyziklani_team` (`e_fyziklani_team_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e_fyziklani_participant_with_team`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_fyziklani_participant_with_team` ;

CREATE  TABLE IF NOT EXISTS `e_fyziklani_participant_with_team` (
  `team_id` INT NULL ,
  `participant_id` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`participant_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `stored_query`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `stored_query` ;

CREATE  TABLE IF NOT EXISTS `stored_query` (
  `query_id` INT NOT NULL AUTO_INCREMENT ,
  `qid` VARCHAR(16) NULL COMMENT 'identifikátor pro URL, práva apod.\ndotazy s QIDem nelze mazat' ,
  `name` VARCHAR(32) NOT NULL COMMENT 'název dotazu, identifikace pro človkěka' ,
  `description` TEXT NULL ,
  `sql` TEXT NOT NULL ,
  `php_post_proc` VARCHAR(255) NULL ,
  PRIMARY KEY (`query_id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  UNIQUE INDEX `qid_UNIQUE` (`qid` ASC) )
ENGINE = InnoDB
COMMENT = 'Uložené SQL dotazy s možností parametrizace z aplikace.';


-- -----------------------------------------------------
-- Table `stored_query_parameter`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `stored_query_parameter` ;

CREATE  TABLE IF NOT EXISTS `stored_query_parameter` (
  `parameter_id` INT NOT NULL AUTO_INCREMENT ,
  `query_id` INT NOT NULL ,
  `name` VARCHAR(16) NOT NULL COMMENT 'název parametru pro použití v SQL' ,
  `description` TEXT NULL ,
  `type` ENUM('integer', 'string') NOT NULL COMMENT 'datový typ paramtru' ,
  `default_integer` INT(11) NULL COMMENT 'implicitní hodnota' ,
  `default_string` VARCHAR(255) NULL COMMENT 'implicitní hodnota' ,
  PRIMARY KEY (`parameter_id`) ,
  INDEX `fk_stored_query_parameter_stored_query1_idx` (`query_id` ASC) ,
  UNIQUE INDEX `uq_query_id_name` (`query_id` ASC, `name` ASC) ,
  CONSTRAINT `fk_stored_query_parameter_stored_query1`
    FOREIGN KEY (`query_id` )
    REFERENCES `stored_query` (`query_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `global_session`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `global_session` ;

CREATE  TABLE IF NOT EXISTS `global_session` (
  `session_id` CHAR(32) NOT NULL ,
  `login_id` INT(11) NOT NULL COMMENT 'the only data\nfield of the session' ,
  `since` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `until` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `remote_ip` VARCHAR(45) NULL COMMENT 'IP adresa klienta' ,
  PRIMARY KEY (`session_id`) ,
  INDEX `fk_auth_token_login1_idx` (`login_id` ASC) ,
  CONSTRAINT `fk_auth_token_login10`
    FOREIGN KEY (`login_id` )
    REFERENCES `login` (`login_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Stores global sessions for SSO (single sign-on/off)';


-- -----------------------------------------------------
-- Table `flag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `flag` ;

CREATE  TABLE IF NOT EXISTS `flag` (
  `flag_id` INT NOT NULL ,
  `fid` VARCHAR(16) NOT NULL ,
  `name` VARCHAR(64) NOT NULL ,
  `description` TEXT NULL ,
  `type` ENUM('global','contest','ac_year','contest_year') NOT NULL COMMENT 'rozsah platnosti flagu' ,
  PRIMARY KEY (`flag_id`) ,
  UNIQUE INDEX `name_UNIQUE` (`fid` ASC) )
ENGINE = InnoDB
COMMENT = 'general purpose flag for the person (for presentation layer)' /* comment truncated */;


-- -----------------------------------------------------
-- Table `contest_year`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contest_year` ;

CREATE  TABLE IF NOT EXISTS `contest_year` (
  `contest_id` INT NOT NULL ,
  `year` TINYINT(4) NOT NULL ,
  `ac_year` SMALLINT(4) NOT NULL COMMENT 'první rok akademického roku,\n2013/2014->2013' ,
  PRIMARY KEY (`contest_id`, `year`) ,
  INDEX `ac_year_idx` (`ac_year` ASC) ,
  CONSTRAINT `fk_contest_year_contest1`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'mapování ročníků semináře na akademické roky';


-- -----------------------------------------------------
-- Table `person_has_flag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person_has_flag` ;

CREATE  TABLE IF NOT EXISTS `person_has_flag` (
  `person_flag_id` INT NOT NULL ,
  `person_id` INT NOT NULL ,
  `flag_id` INT NOT NULL ,
  `contest_id` INT NULL ,
  `ac_year` SMALLINT(4) NULL ,
  `value` TINYINT NOT NULL DEFAULT 1 ,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`person_flag_id`) ,
  UNIQUE INDEX `person_flag_year_ct_UQ` (`person_id` ASC, `flag_id` ASC, `contest_id` ASC, `ac_year` ASC) ,
  INDEX `fk_person_has_flag_person_flag1_idx` (`flag_id` ASC) ,
  INDEX `fk_person_has_flag_contest1_idx` (`contest_id` ASC) ,
  INDEX `fk_person_has_flag_contest_year1_idx` (`ac_year` ASC) ,
  CONSTRAINT `fk_person_has_flag_person1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_has_flag_person_flag1`
    FOREIGN KEY (`flag_id` )
    REFERENCES `flag` (`flag_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_has_flag_contest1`
    FOREIGN KEY (`contest_id` )
    REFERENCES `contest` (`contest_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_has_flag_contest_year1`
    FOREIGN KEY (`ac_year` )
    REFERENCES `contest_year` (`ac_year` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'person\'s flags are per year';


-- -----------------------------------------------------
-- Table `study_year`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `study_year` ;

CREATE  TABLE IF NOT EXISTS `study_year` (
  `study_year` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`study_year`) )
ENGINE = InnoDB
COMMENT = 'table just enforeces referential integrity';


-- -----------------------------------------------------
-- Table `person_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person_history` ;

CREATE  TABLE IF NOT EXISTS `person_history` (
  `person_history_id` INT NOT NULL AUTO_INCREMENT ,
  `person_id` INT NOT NULL ,
  `ac_year` SMALLINT(4) NOT NULL COMMENT 'první rok akademického roku,\n2013/2014 -> 2013' ,
  `school_id` INT NULL ,
  `class` VARCHAR(16) NULL COMMENT 'označení třídy' ,
  `study_year` TINYINT(1) NULL COMMENT 'ročník, který studuje' ,
  PRIMARY KEY (`person_history_id`) ,
  UNIQUE INDEX `UQ_AC_YEAR` (`person_id` ASC, `ac_year` ASC) ,
  INDEX `fk_person_history_school1_idx` (`school_id` ASC) ,
  INDEX `fk_person_history_contest_year1_idx` (`ac_year` ASC) ,
  INDEX `fk_person_history_study_year1_idx` (`study_year` ASC) ,
  CONSTRAINT `fk_person_history_school1`
    FOREIGN KEY (`school_id` )
    REFERENCES `school` (`school_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_history_person1`
    FOREIGN KEY (`person_id` )
    REFERENCES `person` (`person_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_history_contest_year1`
    FOREIGN KEY (`ac_year` )
    REFERENCES `contest_year` (`ac_year` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_history_study_year1`
    FOREIGN KEY (`study_year` )
    REFERENCES `study_year` (`study_year` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'atributy osoby řezané dle akademického roku';


-- -----------------------------------------------------
-- Table `e_dsef_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_dsef_participant` ;

CREATE  TABLE IF NOT EXISTS `e_dsef_participant` (
  `event_participant_id` INT NOT NULL ,
  `e_dsef_group_id` INT NOT NULL ,
  `arrival_time` TIME NULL ,
  `lunch_count` TINYINT(2) NULL DEFAULT 0 ,
  `message` VARCHAR(255) NULL ,
  PRIMARY KEY (`event_participant_id`) ,
  CONSTRAINT `fk_e_dsef_participant_event_participant1`
    FOREIGN KEY (`event_participant_id` )
    REFERENCES `event_participant` (`event_participant_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e_vikend_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_vikend_participant` ;

CREATE  TABLE IF NOT EXISTS `e_vikend_participant` (
  `event_participant_id` INT NOT NULL ,
  `answer` VARCHAR(64) NULL ,
  `gives_lecture` VARCHAR(64) NULL ,
  `gives_lecture_desc` TEXT NULL ,
  `wants_lecture` VARCHAR(64) NULL ,
  PRIMARY KEY (`event_participant_id`) ,
  CONSTRAINT `fk_e_vikend_participant_event_participant1`
    FOREIGN KEY (`event_participant_id` )
    REFERENCES `event_participant` (`event_participant_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e_sous_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_sous_participant` ;

CREATE  TABLE IF NOT EXISTS `e_sous_participant` (
  `event_participant_id` INT NOT NULL ,
  `diet` TEXT NULL ,
  `special_diet` TINYINT(1) NOT NULL ,
  `health_restrictions` TEXT NULL ,
  `tshirt_size` VARCHAR(20) NULL ,
  `price` DECIMAL(6,2) NULL ,
  PRIMARY KEY (`event_participant_id`) ,
  CONSTRAINT `fk_e_sous_participant_event_participant1`
    FOREIGN KEY (`event_participant_id` )
    REFERENCES `event_participant` (`event_participant_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e_tsaf_participant`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `e_tsaf_participant` ;

CREATE  TABLE IF NOT EXISTS `e_tsaf_participant` (
  `event_participant_id` INT NOT NULL ,
  `price` DECIMAL(6,2) NULL ,
  `tshirt_size` VARCHAR(20) NULL ,
  `jumper_size` VARCHAR(20) NULL ,
  PRIMARY KEY (`event_participant_id`) ,
  CONSTRAINT `fk_e_tsaf_participant_event_participant1`
    FOREIGN KEY (`event_participant_id` )
    REFERENCES `event_participant` (`event_participant_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `task_study_year`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `task_study_year` ;

CREATE  TABLE IF NOT EXISTS `task_study_year` (
  `task_id` INT(11) NOT NULL ,
  `study_year` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`study_year`, `task_id`) ,
  INDEX `fk_task_study_year_study_year1_idx` (`study_year` ASC) ,
  CONSTRAINT `fk_task_study_year_task1`
    FOREIGN KEY (`task_id` )
    REFERENCES `task` (`task_id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_task_study_year_study_year1`
    FOREIGN KEY (`study_year` )
    REFERENCES `study_year` (`study_year` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT)
ENGINE = InnoDB
COMMENT = 'specification of allowed study years for a task';



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
