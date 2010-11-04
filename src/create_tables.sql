-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 11, 2009 at 10:05 AM
-- Server version: 5.0.45
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `Athene`
--

-- --------------------------------------------------------

--
-- Table structure for table `ilmo_answers`
--

CREATE TABLE IF NOT EXISTS `ilmo_answers` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `question_id` int(11) NOT NULL default '0',
  `answer` text,
  `user_id` int(11) default NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ilmo_masiinat`
--

CREATE TABLE IF NOT EXISTS `ilmo_masiinat` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `opens` datetime NOT NULL default '0000-00-00 00:00:00',
  `closes` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` text NOT NULL,
  `description` text,
  `eventdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `password` varchar(8) default NULL,
  `send_confirmation` tinyint(4) NOT NULL default '0',
  `confirmation_message` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ilmo_questions`
--

CREATE TABLE IF NOT EXISTS `ilmo_questions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `ilmo_id` int(11) NOT NULL default '0',
  `question` text NOT NULL,
  `type` text NOT NULL,
  `options` text NOT NULL,
  `public` tinyint(1) NOT NULL default '0',
  `required` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ilmo_users`
--

CREATE TABLE IF NOT EXISTS `ilmo_users` (
  `id` int(11) NOT NULL auto_increment,
  `ilmo_id` int(11) NOT NULL default '0',
  `id_string` text NOT NULL,
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `confirmed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


