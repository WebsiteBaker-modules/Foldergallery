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
  `s_name` varchar(200){FIELD_COLLATION} NOT NULL DEFAULT '',
  `s_value` text NOT NULL
  ){TABLE_ENGINE};
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings`{FIELD_COLLATION} ;
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings` CHANGE `s_name` `s_name`  varchar(200) {FIELD_COLLATION}  NOT NULL DEFAULT '';
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings` CHANGE `s_value` `s_value` TEXT {FIELD_COLLATION} NOT NULL;
ALTER TABLE `{TABLE_PREFIX}mod_foldergallery_settings` ADD UNIQUE `ident_foldergallery` ( `section_id`,`s_name` );

-- --------------------------------------------------------

