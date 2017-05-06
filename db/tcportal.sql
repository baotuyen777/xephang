-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 05, 2017 at 10:10 AM
-- Server version: 5.5.52
-- PHP Version: 5.5.9-1ubuntu4.21

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tcportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `uid` int(10) unsigned NOT NULL,
  `sid` varchar(64) NOT NULL DEFAULT '',
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `cache` int(11) NOT NULL DEFAULT '0',
  `session` longtext,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `uid` (`uid`),
  KEY `timestamp` (`timestamp`),
  KEY `uid_2` (`uid`),
  KEY `sid` (`sid`),
  KEY `timestamp_2` (`timestamp`),
  KEY `uid_3` (`uid`),
  KEY `uid_4` (`uid`),
  KEY `sid_2` (`sid`),
  KEY `timestamp_3` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`uid`, `sid`, `hostname`, `timestamp`, `cache`, `session`, `name`) VALUES
(22, '5au2v6art6iobtb6alst1mf8h0', '172.20.98.229', 1493953732, 0, '', 'pknhi'),
(26, '7v38495o34mrdlcl8339f1i252', '172.20.100.217', 1493953732, 0, 'flash|a:0:{}', 'pknoi4'),
(29, 'anj6q7lh11mb4da4cnb1794t26', '172.20.97.144', 1493953732, 0, '', 'pkranghammat'),
(20, 'c8vufncndj8u5ao2llgg6guqb7', '172.20.97.209', 1493953733, 0, 'flash|a:0:{}', 'pkgiaosu'),
(32, 'f27hu18i7qbditk7g98k6ll2s0', '172.20.98.226', 1493953732, 0, 'flash|a:0:{}', 'pkngoai'),
(21, 'h0a2mtp4f49r5vrodd0g5pl782', '172.20.100.202', 1493953732, 0, 'flash|a:0:{}', 'pkcoxuongkhop'),
(35, 'hd7rulrlqmkvtqpgna6t8gggk2', '172.20.98.253', 1493953732, 0, 'flash|a:0:{}', 'pknoi6'),
(18, 'knjnij98vjpchbrb4oum1bia75', '172.20.98.183', 1493953738, 0, 'flash|a:0:{}', 'pkmat'),
(1, 'magudhjdoop1i84ab09va0nc14', '172.20.98.24', 1493953731, 0, '', 'pkdalieu'),
(27, 'ovo634kef7geqckqj5tn1k4ee1', '172.20.103.100', 1493953732, 0, 'flash|a:0:{}', 'pknoi5'),
(23, 'q64rqu9s0vc2hvtnv1f5tjj465', '172.20.98.48', 1493953732, 0, 'flash|a:0:{}', 'pknoi1'),
(17, 'vhsssn0cdogf5hhpiu600h41m3', '172.20.100.158', 1493953732, 0, 'flash|a:0:{}', 'pknoi3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `mail` varchar(64) DEFAULT '',
  `mode` tinyint(4) NOT NULL DEFAULT '0',
  `sort` tinyint(4) DEFAULT '0',
  `threshold` tinyint(4) DEFAULT '0',
  `theme` varchar(255) NOT NULL DEFAULT '',
  `signature` varchar(255) NOT NULL DEFAULT '',
  `created` int(11) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `online_time` bigint(20) unsigned DEFAULT '1' COMMENT 'total online time (second)',
  `login` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: chưa active, 1: đã active, 2: đã update user info',
  `timezone` varchar(8) DEFAULT NULL,
  `language` varchar(12) NOT NULL DEFAULT '',
  `picture` varchar(255) NOT NULL DEFAULT '',
  `init` varchar(64) DEFAULT '',
  `data` longtext,
  `money` int(11) DEFAULT '0',
  `uniqid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`),
  KEY `access` (`access`),
  KEY `created` (`created`),
  KEY `mail` (`mail`),
  KEY `status` (`status`),
  KEY `uniqid` (`uniqid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `name`, `pass`, `mail`, `mode`, `sort`, `threshold`, `theme`, `signature`, `created`, `access`, `online_time`, `login`, `status`, `timezone`, `language`, `picture`, `init`, `data`, `money`, `uniqid`) VALUES
(1, 'pkdalieu', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493944050, 0, NULL, '', '', '', NULL, 0, NULL),
(9, 'pknoi2', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493769056, 0, NULL, '', '', '', NULL, 0, NULL),
(10, 'pksieuam4', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 0, 0, NULL, '', '', '', NULL, 0, NULL),
(11, 'pksieuam3', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 0, 0, NULL, '', '', '', NULL, 0, NULL),
(12, 'pksieuam2', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 0, 0, NULL, '', '', '', NULL, 0, NULL),
(13, 'pksieuam1', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 0, 0, NULL, '', '', '', NULL, 0, NULL),
(14, 'pkxquang2', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 0, 0, NULL, '', '', '', NULL, 0, NULL),
(17, 'pknoi3', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493942613, 0, NULL, '', '', '', NULL, 0, NULL),
(18, 'pkmat', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493943206, 0, NULL, '', '', '', NULL, 0, NULL),
(20, 'pkgiaosu', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493943076, 0, NULL, '', '', '', NULL, 0, NULL),
(21, 'pkcoxuongkhop', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493943178, 0, NULL, '', '', '', NULL, 0, NULL),
(22, 'pknhi', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493942362, 0, NULL, '', '', '', NULL, 0, NULL),
(23, 'pknoi1', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493942559, 0, NULL, '', '', '', NULL, 0, NULL),
(26, 'pknoi4', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493856203, 0, NULL, '', '', '', NULL, 0, NULL),
(27, 'pknoi5', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493942471, 0, NULL, '', '', '', NULL, 0, NULL),
(29, 'pkranghammat', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493945173, 0, NULL, '', '', '', NULL, 0, NULL),
(30, 'pksan', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493860476, 0, NULL, '', '', '', NULL, 0, NULL),
(31, 'pktaimuihong', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493946774, 0, NULL, '', '', '', NULL, 0, NULL),
(32, 'pkngoai', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493942773, 0, NULL, '', '', '', NULL, 0, NULL),
(33, 'pkdinhduong', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1491440497, 0, NULL, '', '', '', NULL, 0, NULL),
(34, 'pksuckhoetamthan', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1480550569, 0, NULL, '', '', '', NULL, 0, NULL),
(35, 'pknoi6', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493948113, 0, NULL, '', '', '', NULL, 0, NULL),
(36, 'pknoi7', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1482287708, 0, NULL, '', '', '', NULL, 0, NULL),
(37, 'test', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1480476470, 0, NULL, '', '', '', NULL, 0, NULL),
(38, 'pkhohap', 'e10adc3949ba59abbe56e057f20f883e', '', 0, 0, 0, '', '', 0, 0, 1, 1493769673, 0, NULL, '', '', '', NULL, 0, NULL);
