-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 30, 2005 at 10:52 AM
-- Server version: 4.1.7
-- PHP Version: 4.3.9
-- 
-- Database: `vacationsf`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `vf_category`
-- 

DROP TABLE IF EXISTS `vf_category`;
CREATE TABLE `vf_category` (
  `cat_id` int(10) NOT NULL auto_increment,
  `short_name` varchar(10) NOT NULL default '',
  `descr` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Employee category' AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `vf_category`
-- 

INSERT INTO `vf_category` (`cat_id`, `short_name`, `descr`) VALUES (1, 'FT', 'Full Time'),
(2, 'PT', 'Part Time');

-- --------------------------------------------------------

-- 
-- Table structure for table `vf_config`
-- 

DROP TABLE IF EXISTS `vf_config`;
CREATE TABLE `vf_config` (
  `dept_id` varchar(10) NOT NULL default '0',
  `sub_dept_id` int(3) NOT NULL default '0',
  `emp_off_ttl` int(3) NOT NULL default '0',
  `days_notice` int(2) NOT NULL default '0',
  `last_vac_date` date NOT NULL default '0000-00-00',
  `people_off_type` char(1) NOT NULL default '0',
  `min_hours_per_day` int(2) NOT NULL default '0',
  `email` varchar(75) NOT NULL default '',
  `include_default` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Configuration table';

-- 
-- Dumping data for table `vf_config`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_department`
-- 

DROP TABLE IF EXISTS `vf_department`;
CREATE TABLE `vf_department` (
  `dept_id` int(10) NOT NULL auto_increment,
  `descr` varchar(100) NOT NULL default '',
  `sub_dept` char(1) NOT NULL default '',
  PRIMARY KEY  (`dept_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='department description' AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `vf_department`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_emp_sup_rel`
-- 

DROP TABLE IF EXISTS `vf_emp_sup_rel`;
CREATE TABLE `vf_emp_sup_rel` (
  `emp_id` varchar(10) NOT NULL default '0',
  `sup_id` varchar(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='relationship between an employee and supervisors';

-- 
-- Dumping data for table `vf_emp_sup_rel`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_emp_to_hours`
-- 

DROP TABLE IF EXISTS `vf_emp_to_hours`;
CREATE TABLE `vf_emp_to_hours` (
  `to_id` int(10) NOT NULL default '0',
  `emp_id` varchar(10) NOT NULL default '0',
  `hours` decimal(7,3) NOT NULL default '0.000',
  `year` int(4) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='relation table to employee and time off type(hours earned)';

-- 
-- Dumping data for table `vf_emp_to_hours`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_employee`
-- 

DROP TABLE IF EXISTS `vf_employee`;
CREATE TABLE `vf_employee` (
  `emp_id` varchar(10) NOT NULL default '0',
  `fname` varchar(30) NOT NULL default '',
  `mname` varchar(30) NOT NULL default '',
  `lname` varchar(30) NOT NULL default '',
  `username` varchar(20) NOT NULL default '',
  `email` varchar(75) NOT NULL default '',
  `password` varchar(75) NOT NULL default '',
  `dept_id` varchar(10) NOT NULL default '0',
  `sub_dept_id` int(3) NOT NULL default '0',
  `admin` int(1) NOT NULL default '0',
  `super_admin` int(1) NOT NULL default '0',
  `sup_id` varchar(10) NOT NULL default '0',
  `status` varchar(5) NOT NULL default '',
  `supervisor` int(1) NOT NULL default '0',
  `viewdeptvac` tinyint(1) NOT NULL default '0',
  `year` varchar(8) NOT NULL default '',
  `enabled` char(1) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='all employee information';

-- 
-- Dumping data for table `vf_employee`
-- 

INSERT INTO `vf_employee` (`emp_id`, `fname`, `mname`, `lname`, `username`, `email`, `password`, `dept_id`, `sub_dept_id`, `admin`, `super_admin`, `sup_id`, `status`, `supervisor`, `viewdeptvac`, `year`, `enabled`) VALUES ('ADMIN', 'Admin', '', '', 'ADMIN', '', '21232f297a57a5a743894a0e4a801fc3', '0', 0, 1, 1, '0', '', 1, 1, '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `vf_notable_dates`
-- 

DROP TABLE IF EXISTS `vf_notable_dates`;
CREATE TABLE `vf_notable_dates` (
  `id` int(10) NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `descr` char(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='store notable dates to display on the calendar' AUTO_INCREMENT=33 ;

-- 
-- Dumping data for table `vf_notable_dates`
-- 

INSERT INTO `vf_notable_dates` (`id`, `date`, `descr`) VALUES (1, '2004-07-04', 'Independence Day'),
(2, '2004-05-31', 'Memorial Day'),
(25, '2004-10-31', 'daylight savings ends'),
(4, '2004-09-06', 'Labor Day'),
(5, '2004-10-31', 'Halloween'),
(6, '2004-11-25', 'Thanksgiving Day'),
(7, '2004-12-25', 'Christmas Day'),
(14, '2005-05-30', 'Memorial Day'),
(11, '2005-07-04', 'Independence Day'),
(13, '2006-05-29', 'Memorial Day'),
(15, '2005-12-25', 'Christmas Day'),
(18, '2006-07-04', 'Independence Day'),
(20, '2006-09-04', 'Labor Day'),
(21, '2005-11-24', 'Thanksgiving Day'),
(23, '2005-10-31', 'Halloween'),
(24, '2006-10-31', 'Halloween'),
(31, '2005-09-05', 'Labor Day');

-- --------------------------------------------------------

-- 
-- Table structure for table `vf_off_perday`
-- 

DROP TABLE IF EXISTS `vf_off_perday`;
CREATE TABLE `vf_off_perday` (
  `day` date NOT NULL default '0000-00-00',
  `dept_id` varchar(10) NOT NULL default '0',
  `sub_dept_id` int(3) NOT NULL default '0',
  `total_off` char(3) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='365 days of time of values';

-- 
-- Dumping data for table `vf_off_perday`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_shift`
-- 

DROP TABLE IF EXISTS `vf_shift`;
CREATE TABLE `vf_shift` (
  `shift_id` varchar(10) NOT NULL default '0',
  `descr` varchar(100) NOT NULL default '',
  `dept_id` varchar(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='shift information';

-- 
-- Dumping data for table `vf_shift`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_sub_dept`
-- 

DROP TABLE IF EXISTS `vf_sub_dept`;
CREATE TABLE `vf_sub_dept` (
  `sub_dept_id` int(3) NOT NULL auto_increment,
  `descr` varchar(50) NOT NULL default '',
  `code` varchar(4) NOT NULL default '',
  `dept_id` int(2) NOT NULL default '0',
  PRIMARY KEY  (`sub_dept_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `vf_sub_dept`
-- 


-- --------------------------------------------------------


-- 
-- Table structure for table `vf_to_type`
-- 

DROP TABLE IF EXISTS `vf_to_type`;
CREATE TABLE IF NOT EXISTS `vf_to_type` (
  `to_id` int(11) NOT NULL auto_increment,
  `emp_viewable` char(1) NOT NULL default '',
  `code` varchar(15) NOT NULL default '',
  `descr` varchar(25) NOT NULL default '',
  `variable_hours` char(1) NOT NULL default '',
  `earned` char(1) NOT NULL default '',
  `dept_id` varchar(10) NOT NULL default '0',
  `shift_id` varchar(10) NOT NULL default '0',
  `year` varchar(8) NOT NULL default '',
  `type_date` date NOT NULL default '0000-00-00',
  `text_color` varchar(7) NOT NULL default '',
  `payroll` char(1) NOT NULL default '',
  `py_sequence` int(11) NOT NULL default '0',
  PRIMARY KEY  (`to_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Contains definitions of time off types' AUTO_INCREMENT=1 ;


-- 
-- Dumping data for table `vf_to_type`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_vacation`
-- 

DROP TABLE IF EXISTS `vf_vacation`;
CREATE TABLE `vf_vacation` (
  `vacation_id` int(12) NOT NULL auto_increment,
  `emp_id` varchar(10) NOT NULL default '0',
  `dept_id` varchar(10) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `hours` decimal(5,3) NOT NULL default '0.000',
  `to_id` int(10) NOT NULL default '0',
  `apprv_by` varchar(10) NOT NULL default '0',
  `deny` char(1) NOT NULL default '',
  `deny_reason` varchar(200) NOT NULL default '',
  `entered_by` varchar(10) NOT NULL default '0',
  `ovrride_time_ck` char(1) NOT NULL default '',
  `date_entered` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_approved` datetime NOT NULL default '0000-00-00 00:00:00',
  `possible` int(1) NOT NULL default '0',
  `replacement` varchar(50) NOT NULL default '',
  `note` text NOT NULL,
  `year` int(4) NOT NULL default '0',
  `form_request` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`vacation_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='stores all vacation data' AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `vf_vacation`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `vf_year`
-- 

DROP TABLE IF EXISTS `vf_year`;
CREATE TABLE `vf_year` (
  `year` int(4) NOT NULL default '0',
  `start` date NOT NULL default '0000-00-00',
  `end` date NOT NULL default '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='tracks actual dates for fiscal year';

-- 
-- Dumping data for table `vf_year`
-- 

INSERT INTO `vf_year` (`year`, `start`, `end`) VALUES (2008, '2008-01-01', '2008-12-31'),
(2009, '2009-01-01', '2009-12-31');
