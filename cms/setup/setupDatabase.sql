-- echoCMS version 1.0.11
-- https://github.com/kewh/echoCMS
--
-- database setup
-- --------------
-- see README for setup instructions

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `setting` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`setting`, `value`) VALUES
('admin_email', 'admin@change.this'),
('bcrypt_cost', '12'),
('cms_page_logo', 'echocmsLogoMd.png'),
('cookie_name', 'echocms'),
('date_format', 'j M Y'),
('subtopics_updatable', '0'),
('email_notifications_on', '1'),
('image_bg_crop', 'fc8e5f'),
('image_bg_opacity', '0.8'),
('image_create_collage', '1'),
('image_create_landscape', '1'),
('image_create_panorama', '1'),
('image_create_portrait', '1'),
('image_create_square', '1'),
('image_create_fluid', '1'),
('image_quality', '80'),
('image_ratio_landscape', '4:3'),
('image_ratio_panorama', '3:1'),
('image_ratio_portrait', '3:4'),
('image_ratio_square', '1:1'),
('image_sizes_landscape', '1'),
('image_sizes_panorama', '1'),
('image_sizes_portrait', '1'),
('image_sizes_square', '1'),
('image_sizes_fluid', '1'),
('image_width_collage', '600'),
('image_width_landscape', '600'),
('image_width_panorama', '900'),
('image_width_portrait', '450'),
('image_width_square', '500'),
('image_maxside_fluid', '500'),
('image_resize_original', '0'),
('image_quality_original', '90'),
('image_maxside_original', '3000'),
('ip_ban_attempts', '8'),
('ip_ban_minutes', '5'),
('mail_charset', 'UTF-8'),
('topics_updatable', '0'),
('password_reset_minutes', '60'),
('remember_me_days', '30'),
('site_email', 'sitemail@example.com'),
('site_name', 'example.com'),
('site_timezone', 'Europe/London'),
('smtp', '0'),
('smtp_auth', '1'),
('smtp_host', 'mail@mailserver.com'),
('smtp_password', ''),
('smtp_port', '25'),
('smtp_security', 'tls'),
('smtp_username', 'smtp@username.com');

-- --------------------------------------------------------

--
-- Table structure for table `topicsTable`
--
DROP TABLE IF EXISTS `topicsTable`;
CREATE TABLE `topicsTable` (
  `topic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subtopicsTable`
--
DROP TABLE IF EXISTS `subtopicsTable`;
CREATE TABLE `subtopicsTable` (
  `subtopic` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `last_ip` varchar(45) DEFAULT NULL,
  `last_dt` timestamp NULL DEFAULT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `isactive`, `last_ip`, `last_dt`, `dt`) VALUES
(1, 'admin@change.this', '$2y$12$0VEQpZ3cctJrbZEcV0H30.R1cBneR8z9m9udRC13upON2Bw/fCDgu', 1, '88:88:88:1', '2017-02-28 21:36:12', '2017-02-28 21:36:12');

-- --------------------------------------------------------

--
-- Table structure for table `itemsTable`
--

DROP TABLE IF EXISTS `itemsTable`;
CREATE TABLE `itemsTable` (
`id` int(11) NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `subtopic` varchar(40) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `text` text,
  `video` varchar(255) DEFAULT NULL,
  `download_src` varchar(255) DEFAULT NULL,
  `download_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `date` datetime NOT NULL,
  `expiredate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `imagesTable`
--

DROP TABLE IF EXISTS `imagesTable`;
CREATE TABLE `imagesTable` (
`id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `src` varchar(255) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `mx1` int(11) DEFAULT NULL,
  `mx2` int(11) DEFAULT NULL,
  `my1` int(11) DEFAULT NULL,
  `my2` int(11) DEFAULT NULL,
  `lx1` int(11) DEFAULT NULL,
  `lx2` int(11) DEFAULT NULL,
  `ly1` int(11) DEFAULT NULL,
  `ly2` int(11) DEFAULT NULL,
  `px1` int(11) DEFAULT NULL,
  `px2` int(11) DEFAULT NULL,
  `py1` int(11) DEFAULT NULL,
  `py2` int(11) DEFAULT NULL,
  `sx1` int(11) DEFAULT NULL,
  `sx2` int(11) DEFAULT NULL,
  `sy1` int(11) DEFAULT NULL,
  `sy2` int(11) DEFAULT NULL,
  `fx1` int(11) DEFAULT NULL,
  `fx2` int(11) DEFAULT NULL,
  `fy1` int(11) DEFAULT NULL,
  `fy2` int(11) DEFAULT NULL,
  `height_fluid` int(11) DEFAULT NULL,
  `width_fluid` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `web_images` tinyint(1) NOT NULL DEFAULT '0',
  `prime_aspect_ratio` char(9) DEFAULT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pendingItemsTable`
--

DROP TABLE IF EXISTS `pendingItemsTable`;
CREATE TABLE `pendingItemsTable` (
`id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `status` varchar(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `subtopic` varchar(40) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `text` text,
  `video` varchar(255) DEFAULT NULL,
  `download_src` varchar(255) DEFAULT NULL,
  `download_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pendingImagesTable`
--

DROP TABLE IF EXISTS `pendingImagesTable`;
CREATE TABLE `pendingImagesTable` (
  `id` int(11) NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `src` varchar(255) DEFAULT NULL,
  `seq` int(11) DEFAULT NULL,
  `mx1` int(11) DEFAULT NULL,
  `mx2` int(11) DEFAULT NULL,
  `my1` int(11) DEFAULT NULL,
  `my2` int(11) DEFAULT NULL,
  `lx1` int(11) DEFAULT NULL,
  `lx2` int(11) DEFAULT NULL,
  `ly1` int(11) DEFAULT NULL,
  `ly2` int(11) DEFAULT NULL,
  `px1` int(11) DEFAULT NULL,
  `px2` int(11) DEFAULT NULL,
  `py1` int(11) DEFAULT NULL,
  `py2` int(11) DEFAULT NULL,
  `sx1` int(11) DEFAULT NULL,
  `sx2` int(11) DEFAULT NULL,
  `sy1` int(11) DEFAULT NULL,
  `sy2` int(11) DEFAULT NULL,
  `fx1` int(11) DEFAULT NULL,
  `fx2` int(11) DEFAULT NULL,
  `fy1` int(11) DEFAULT NULL,
  `fy2` int(11) DEFAULT NULL,
  `height_fluid` int(11) DEFAULT NULL,
  `width_fluid` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `web_images` tinyint(1) NOT NULL DEFAULT '0',
  `prime_aspect_ratio` char(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pendingTagsTable`
--

DROP TABLE IF EXISTS `pendingTagsTable`;
CREATE TABLE `pendingTagsTable` (
  `id` int(11) NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `tag` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pendingTermsTable`
--

DROP TABLE IF EXISTS `pendingTermsTable`;
CREATE TABLE `pendingTermsTable` (
  `id` int(11) NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `term` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rkey` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `selector` varchar(40) NOT NULL,
  `validator` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(45) NOT NULL,
  `agent` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tagsTable`
--

DROP TABLE IF EXISTS `tagsTable`;
CREATE TABLE `tagsTable` (
  `id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `tag` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `termsTable`
--

DROP TABLE IF EXISTS `termsTable`;
CREATE TABLE `termsTable` (
  `id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `term` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `itemsTable`
--
ALTER TABLE `itemsTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attempts`
--
ALTER TABLE `attempts`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
 ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `imagesTable`
--
ALTER TABLE `imagesTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendingItemsTable`
--
ALTER TABLE `pendingItemsTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendingImagesTable`
--
ALTER TABLE `pendingImagesTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendingTagsTable`
--
ALTER TABLE `pendingTagsTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendingTermsTable`
--
ALTER TABLE `pendingTermsTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tagsTable`
--
ALTER TABLE `tagsTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `termsTable`
--
ALTER TABLE `termsTable`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `itemsTable`
--
ALTER TABLE `itemsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `imagesTable`
--
ALTER TABLE `imagesTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `pendingItemsTable`
--
ALTER TABLE `pendingItemsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `pendingImagesTable`
--
ALTER TABLE `pendingImagesTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `pendingTagsTable`
--
ALTER TABLE `pendingTagsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `pendingTermsTable`
--
ALTER TABLE `pendingTermsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tagsTable`
--
ALTER TABLE `tagsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `termsTable`
--
ALTER TABLE `termsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
