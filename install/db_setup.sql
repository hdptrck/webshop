-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema webShop
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema webShop
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `webShop` ;

CREATE SCHEMA IF NOT EXISTS `webShop` DEFAULT CHARACTER SET utf8 ;
USE `webShop` ;

-- -----------------------------------------------------
-- Table `webShop`.`role`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`role` ;

CREATE TABLE IF NOT EXISTS `webShop`.`role` (
  `idRole` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idRole`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`webShopUser`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`webShopUser` ;

CREATE TABLE IF NOT EXISTS `webShop`.`webShopUser` (
  `idWebShopUser` INT NOT NULL AUTO_INCREMENT,
  `userToken` VARCHAR(256) NOT NULL,
  `firstname` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(256) NOT NULL,
  `active` TINYINT NOT NULL,
  `role_idRole` INT NOT NULL,
  PRIMARY KEY (`idWebShopUser`),
  INDEX `fk_webShopUser_role1_idx` (`role_idRole` ASC),
  UNIQUE INDEX `userToken_UNIQUE` (`userToken` ASC),
  CONSTRAINT `fk_webShopUser_role1`
    FOREIGN KEY (`role_idRole`)
    REFERENCES `webShop`.`role` (`idRole`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`item` ;

CREATE TABLE IF NOT EXISTS `webShop`.`item` (
  `idItem` INT NOT NULL AUTO_INCREMENT,
  `count` INT NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `description` VARCHAR(512) NULL,
  `isActive` TINYINT NOT NULL DEFAULT 1,
  `picture` VARCHAR(512) NULL,
  `thumb` VARCHAR(512) NULL,
  PRIMARY KEY (`idItem`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`orderLocation`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`orderLocation` ;

CREATE TABLE IF NOT EXISTS `webShop`.`orderLocation` (
  `idOrderLocation` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idOrderLocation`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`tbl_order`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`tbl_order` ;

CREATE TABLE IF NOT EXISTS `webShop`.`tbl_order` (
  `idOrder` INT NOT NULL AUTO_INCREMENT,
  `webShopUser_idWebShopUser` INT NOT NULL,
  `eventName` VARCHAR(45) NOT NULL,
  `eventPlace` VARCHAR(45) NULL,
  `pickUpDatetime` DATETIME NOT NULL,
  `returnDatetime` DATETIME NOT NULL,
  `orderLocation_idOrderLocation` INT NOT NULL,
  `isReady` TINYINT NOT NULL,
  `isReturned` TINYINT NOT NULL,	
  PRIMARY KEY (`idOrder`),
  INDEX `fk_orders_webShopUsers1_idx` (`webShopUser_idWebShopUser` ASC),
  INDEX `fk_orders_orderLocations1_idx` (`orderLocation_idOrderLocation` ASC),
  CONSTRAINT `fk_orders_webShopUsers1`
    FOREIGN KEY (`webShopUser_idWebShopUser`)
    REFERENCES `webShop`.`webShopUser` (`idWebShopUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_orderLocations1`
    FOREIGN KEY (`orderLocation_idOrderLocation`)
    REFERENCES `webShop`.`orderLocation` (`idOrderLocation`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`order_has_item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`order_has_item` ;

CREATE TABLE IF NOT EXISTS `webShop`.`order_has_item` (
  `order_idOrder` INT NOT NULL,
  `item_idItem` INT NOT NULL,
  `quantity` INT NOT NULL,
  PRIMARY KEY (`order_idOrder`, `item_idItem`),
  INDEX `fk_orders_has_items_items1_idx` (`item_idItem` ASC),
  INDEX `fk_orders_has_items_orders1_idx` (`order_idOrder` ASC),
  CONSTRAINT `fk_orders_has_items_orders1`
    FOREIGN KEY (`order_idOrder`)
    REFERENCES `webShop`.`tbl_order` (`idOrder`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_has_items_items1`
    FOREIGN KEY (`item_idItem`)
    REFERENCES `webShop`.`item` (`idItem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`category` ;

CREATE TABLE IF NOT EXISTS `webShop`.`category` (
  `idCategory` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `category_idCategory` INT NOT NULL,
  PRIMARY KEY (`idCategory`),
  INDEX `fk_category_category1_idx` (`category_idCategory` ASC),
  CONSTRAINT `fk_category_category1`
    FOREIGN KEY (`category_idCategory`)
    REFERENCES `webShop`.`category` (`idCategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`item_has_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`item_has_category` ;

CREATE TABLE IF NOT EXISTS `webShop`.`item_has_category` (
  `item_idItem` INT NOT NULL,
  `category_idCategory` INT NOT NULL,
  PRIMARY KEY (`item_idItem`, `category_idCategory`),
  INDEX `fk_item_has_category_category1_idx` (`category_idCategory` ASC),
  INDEX `fk_item_has_category_item1_idx` (`item_idItem` ASC),
  CONSTRAINT `fk_item_has_category_item1`
    FOREIGN KEY (`item_idItem`)
    REFERENCES `webShop`.`item` (`idItem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_item_has_category_category1`
    FOREIGN KEY (`category_idCategory`)
    REFERENCES `webShop`.`category` (`idCategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`passwordResetToken`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`passwordResetToken` ;

CREATE TABLE IF NOT EXISTS `webShop`.`passwordResetToken` (
  `token` VARCHAR(256) NOT NULL,
  `webShopUser_idWebShopUser` INT NOT NULL,
  `expire` DATETIME NOT NULL,
  INDEX `token` (`token` ASC),
  PRIMARY KEY (`token`),
  CONSTRAINT `fk_passwordResetToken_webShopUser1`
    FOREIGN KEY (`webShopUser_idWebShopUser`)
    REFERENCES `webShop`.`webShopUser` (`idWebShopUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webShop`.`rememberMeToken`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webShop`.`rememberMeToken` ;

CREATE TABLE IF NOT EXISTS `webShop`.`rememberMeToken` (
  `token` VARCHAR(256) NOT NULL,
  `webShopUser_idWebShopUser` INT NOT NULL,
  `expire` DATETIME NOT NULL,
  PRIMARY KEY (`token`),
  INDEX `fk_rememberMeToken_webShopUser1_idx` (`webShopUser_idWebShopUser` ASC),
  CONSTRAINT `fk_rememberMeToken_webShopUser1`
    FOREIGN KEY (`webShopUser_idWebShopUser`)
    REFERENCES `webShop`.`webShopUser` (`idWebShopUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- User `webShopBackend`@`localhost`
-- -----------------------------------------------------

DROP USER IF EXISTS 'webShopBackend'@'localhost';

CREATE USER 'webShopBackend'@'localhost' IDENTIFIED BY 'modul151webShop';
GRANT INSERT, SELECT, UPDATE, DELETE ON webshop.* TO 'webShopBackend'@'localhost';

-- -----------------------------------------------------
-- Default role
-- -----------------------------------------------------

INSERT INTO role VALUES (1, 'Default'), (2, 'Materialverwalter'), (3, 'Root');