-- phpMyAdmin SQL Dump
-- version 4.6.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 13. Aug 2016 um 08:40
-- Server-Version: 10.1.14-MariaDB
-- PHP-Version: 7.0.6
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
--
-- Datenbank: `harald_db2_karlstetten_283`
--
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `{TABLE_PREFIX}mod_foldergallery_settings`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_foldergallery_settings`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_foldergallery_settings` (
  `section_id` int(11) NULL DEFAULT '0',
  `s_name` varchar(255) NOT NULL DEFAULT '',
  `s_value` text NOT NULL){TABLE_ENGINE=MyISAM};
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings` ADD UNIQUE `ident` ( `section_id`,`s_name` );
--ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings` CHANGE `s_name` `name`  varchar(255) NOT NULL DEFAULT '';
--ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings` CHANGE `s_value` `value` TEXT {FIELD_COLLATION} NOT NULL;

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `{TABLE_PREFIX}mod_foldergallery_categories`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_foldergallery_categories`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_foldergallery_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `categorie` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `parent` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `cat_name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `is_empty` int(11) NOT NULL DEFAULT '1',
  `position` int(11) NOT NULL DEFAULT '0',
  `niveau` int(11) NOT NULL DEFAULT '0',
  `has_child` int(11) NOT NULL DEFAULT '0',
  `childs` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `description` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
){TABLE_ENGINE=MyISAM};
-- ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_categories` CHANGE `niveau` `level` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_categories` CHANGE `categorie` `categorie` VARCHAR(78) {FIELD_COLLATION} NOT NULL DEFAULT '';
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_categories` CHANGE `description` `description` TEXT {FIELD_COLLATION} NOT NULL;
-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle `{TABLE_PREFIX}mod_foldergallery_files`
--
DROP TABLE IF EXISTS `{TABLE_PREFIX}mod_foldergallery_files`;
CREATE TABLE IF NOT EXISTS `{TABLE_PREFIX}mod_foldergallery_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(255){FIELD_COLLATION} NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `caption` text{FIELD_COLLATION} NOT NULL,
  PRIMARY KEY (`id`)
){TABLE_ENGINE=MyISAM};
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_files` ADD `section_id` INT(11) NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_files` ADD `active` INT(11) NOT NULL DEFAULT '1' AFTER `section_id`;
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_files` ADD `img_title` VARCHAR(255) {FIELD_COLLATION} NOT NULL DEFAULT '';

-- --------------------------------------------------------

