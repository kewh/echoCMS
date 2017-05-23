SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DROP TABLE IF EXISTS `attempts`;
CREATE TABLE `attempts` (
`id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `date` datetime NOT NULL,
  `expiredate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `setting` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `config` (`setting`, `value`) VALUES
('admin_email', 'admin@change.this'),
('bcrypt_cost', '12'),
('cms_page_logo', 'echocmsLogoMd.png'),
('cookie_name', 'echocms'),
('date_format', 'j M Y'),
('elements_updatable', '1'),
('email_notifications_on', '1'),
('image_bg_crop', 'fc8e5f'),
('image_bg_opacity', '0.8'),
('image_create_landscape', '1'),
('image_create_panorama', '1'),
('image_create_portrait', '1'),
('image_create_square', '1'),
('image_quality', '75'),
('image_ratio_landscape', '4:3'),
('image_ratio_panorama', '3:1'),
('image_ratio_portrait', '3:4'),
('image_ratio_square', '1:1'),
('image_sizes_landscape', '1'),
('image_sizes_panorama', '1'),
('image_sizes_portrait', '1'),
('image_sizes_square', '1'),
('image_width_landscape', '600'),
('image_width_panorama', '900'),
('image_width_portrait', '450'),
('image_width_square', '500'),
('ip_ban_attempts', '8'),
('ip_ban_minutes', '5'),
('mail_charset', 'UTF-8'),
('pages_updatable', '1'),
('password_reset_minutes', '60'),
('remember_me_days', '30'),
('site_email', 'sitemail@example.com'),
('site_name', 'example.com'),
('site_timezone', 'Europe/London'),
('smtp', '0'),
('smtp_auth', '0'),
('smtp_host', 'mail@mailserver.com'),
('smtp_password', ''),
('smtp_port', '25'),
('smtp_security', 'tls'),
('smtp_username', 'smtp@username.com');

DROP TABLE IF EXISTS `elementsTable`;
CREATE TABLE `elementsTable` (
  `element` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `web_images` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8;

INSERT INTO `imagesTable` (`id`, `content_id`, `src`, `seq`, `mx1`, `mx2`, `my1`, `my2`, `lx1`, `lx2`, `ly1`, `ly2`, `px1`, `px2`, `py1`, `py2`, `sx1`, `sx2`, `sy1`, `sy2`, `height`, `width`, `alt`, `web_images`) VALUES
(121, 5, '2017-05-06-20-47-05-KW10127jpg.jpg', 0, 0, 600, 84, 284, 35, 565, 0, 398, 249, 547, 0, 398, 22, 419, 0, 398, 398, 600, '', 0),
(122, 5, '2017-05-06-20-42-51-KW10139jpg.jpg', 1, 0, 600, 120, 321, 115, 600, 16, 380, 198, 496, 0, 398, 176, 573, 0, 398, 398, 600, 'Indian elephant by lake', 0),
(129, 6, '2017-05-06-20-52-42-PC190141jpg.jpg', 0, 0, 600, 159, 359, 72, 589, 3, 390, 128, 466, 0, 450, 92, 503, 12, 423, 450, 600, 'Tiger behind rock', 0),
(130, 6, '2017-05-06-20-49-27-PC190272jpg.jpg', 1, 0, 600, 62, 262, 0, 600, 0, 450, 113, 450, 0, 450, 81, 531, 0, 450, 450, 600, 'Tiger on track', 0),
(155, 7, '2017-05-16-17-18-42-DSC4472jpg.jpg', 0, 0, 600, 118, 318, 11, 484, 39, 394, 137, 435, 0, 398, 191, 449, 104, 363, 398, 600, 'Lion walking across a track', 0),
(156, 7, '2017-05-16-17-20-32-DSC4463jpg.jpg', 1, 0, 600, 31, 231, 35, 565, 0, 398, 151, 449, 0, 398, 211, 355, 91, 235, 398, 600, 'Lion walking forward on flat grassland ', 0),
(157, 7, '2017-05-06-21-00-25-DSC4514jpg.jpg', 2, 0, 600, 138, 338, 57, 587, 0, 398, 187, 486, 0, 398, 82, 480, 0, 398, 398, 600, 'Two lions walking by a track', 0),
(167, 3, '2017-05-16-17-35-38-DSC3894jpg.jpg', 0, 0, 398, 397, 530, 0, 398, 301, 600, 0, 398, 27, 558, 158, 379, 331, 552, 600, 398, 'Elephant by a tree', 0),
(168, 3, '2017-05-06-18-31-44-DSC3887jpg.jpg', 1, 0, 402, 231, 365, 0, 402, 20, 322, 86, 402, 0, 421, 0, 402, 0, 402, 600, 402, 'Elephant foraging in tree', 0),
(169, 3, '2017-05-06-18-27-54-DSC3871jpg.jpg', 2, 0, 600, 110, 300, 35, 593, 0, 398, 277, 591, 0, 398, 152, 571, 0, 398, 398, 600, 'Old bull elephant near Twyfelfontein, Namibia', 0),
(172, 4, '2017-05-10-13-22-11-DSC5856jpg.jpg', 0, 0, 402, 120, 254, 0, 402, 113, 415, 49, 375, 93, 528, 0, 402, 122, 524, 600, 402, 'Cheetah', 0),
(173, 4, '2017-05-10-13-21-49-DSC5836jpg.jpg', 1, 0, 600, 61, 251, 0, 559, 0, 398, 57, 371, 0, 398, 53, 421, 48, 398, 398, 600, 'image of cheetah in profile', 0),
(174, 2, '2017-05-03-20-15-22-DSC5106jpg.jpg', 1, 0, 600, 73, 273, 25, 556, 0, 398, 212, 510, 0, 398, 162, 560, 0, 398, 398, 600, 'Rhino in Namibia, Africa', 0),
(175, 2, '2017-05-16-17-49-02-DSC5113jpg.jpg', 1, 0, 600, 98, 298, 11, 541, 0, 398, 151, 449, 0, 398, 158, 463, 45, 351, 398, 600, 'Rhino walking away', 0);

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
  `page` varchar(255) DEFAULT NULL,
  `element` varchar(40) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `text` text,
  `video` varchar(255) DEFAULT NULL,
  `download_src` varchar(255) DEFAULT NULL,
  `download_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

INSERT INTO `itemsTable` (`id`, `pending_id`, `created`, `createdBy`, `updated`, `updatedBy`, `status`, `date`, `page`, `element`, `heading`, `caption`, `text`, `video`, `download_src`, `download_name`) VALUES
(1, NULL, '2017-05-02 16:54:52', 1, '2017-05-16 10:55:28', 1, 'live', '2017-05-07 10:55:28', 'header', '', 'Examples', '', '&lt;p&gt;This&amp;nbsp;header&amp;nbsp;is used by all example pages.&lt;/p&gt;', NULL, NULL, NULL),
(2, NULL, '2017-05-06 18:03:11', 1, '2017-05-16 17:50:51', 1, 'live', '2017-05-03 17:50:51', 'article', 'Africa', 'Rhino in Africa', 'Rhino in Etosha Nationa Park, Namibia, Africa.', '&lt;p&gt;This is text about rhinoceros (/raɪˈnɒsərəs/, meaning &quot;nose horn&quot;), often abbreviated to rhino, it is one of any five extant species of odd-toed ungulates in the family Rhinocerotidae, as well as any of the numerous extinct species. Two of these extant species are native to Africa and three to Southern Asia.&lt;/p&gt;', NULL, NULL, NULL),
(3, NULL, '2017-05-06 18:34:59', 1, '2017-05-16 17:41:10', 1, 'live', '2017-05-06 17:41:10', 'article', 'Africa', 'Elephant in Africa', 'Old Bull Elephant near Twyfelfontein, Namibia, Africa', '&lt;p&gt;This is text about Desert elephants. They are not a distinct species of elephant but are African bush elephants (Loxodonta africana) that have made their homes in the Namib and Sahara deserts.&amp;nbsp;Donec id nisi quis sem viverra hendrerit at ultrices enim. Nulla ac turpis est. Nullam hendrerit nisl et lectus blandit, non congue dui tempor. Nunc quis tortor ac elit porta porttitor a eu eros. Aenean auctor eleifend rhoncus. Donec varius venenatis lacus, et iaculis sem posuere eu. Aenean fringilla at sapien a faucibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.&lt;/p&gt;', NULL, NULL, NULL),
(4, NULL, '2017-05-06 18:48:29', 1, '2017-05-16 17:47:15', 1, 'live', '2017-05-06 17:47:15', 'article', 'Africa', 'Cheetah in Africa', 'Cheetah on a termite mound, Namibia, Africa.', '&lt;p&gt;This is text about the cheetah (Acinonyx jubatus) which is a large felid of the subfamily Felinae.&amp;nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vitae nisl molestie, imperdiet nisi ac, vehicula nunc. Donec id nisi quis sem viverra hendrerit at ultrices enim. Nulla ac turpis est. Nullam hendrerit nisl et lectus blandit, non congue dui tempor. Nunc quis tortor ac elit porta porttitor a eu eros. Aenean auctor eleifend rhoncus. Donec varius venenatis lacus, et iaculis sem posuere eu. Aenean fringilla at sapien a faucibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.&lt;/p&gt;', NULL, 'Cheetah.pdf', 'PDF of Cheetah'),
(5, NULL, '2017-05-06 20:47:58', 1, '2017-05-10 22:56:41', 1, 'live', '2017-05-06 22:56:41', 'article', 'India', 'Elephant in India', 'Elephant by lake in Nagarhole National Park, India', '&lt;p&gt;This is text about the Indian elephant (Elephas maximus indicus) which is one of three recognized subspecies of the Asian elephant and native to mainland Asia.&amp;nbsp;Aenean auctor eleifend rhoncus. Donec varius venenatis lacus, et iaculis sem posuere eu. Aenean fringilla at sapien a faucibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.&lt;/p&gt;', NULL, NULL, NULL),
(6, NULL, '2017-05-06 20:51:34', 1, '2017-05-10 23:09:06', 1, 'live', '2017-05-06 23:09:06', 'article', 'India', 'Tiger in India', 'Tiger on track in Ranthambore National Park, India', '&lt;p&gt;The Bengal tiger (Panthera tigris tigris) is the most numerous tiger subspecies. By 2011, the total population was estimated at fewer than 2,500 individuals with a decreasing trend. None of the Tiger Conservation Landscapes within the Bengal tiger''s range is considered large enough to support an effective population size of 250 adult individuals. Since 2010, it has been listed as Endangered.&lt;/p&gt;', NULL, NULL, NULL),
(7, NULL, '2017-05-06 21:02:55', 1, '2017-05-16 17:33:43', 1, 'live', '2017-05-06 17:33:43', 'article', 'Africa', 'Lion in Africa', 'Lion in Etosha National Park, Namibia, Africa', '&lt;p&gt;This is text about the lion (Panthera leo) which is one of the big cats in the genus Panthera and a member of the family Felidae. The commonly used term African lion collectively denotes the several subspecies in Africa.&amp;nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras vitae nisl molestie, imperdiet nisi ac, vehicula nunc. Donec id nisi quis sem viverra hendrerit at ultrices enim.&lt;/p&gt;', NULL, NULL, NULL),
(8, NULL, '2017-05-07 15:53:57', 1, '2017-05-16 11:53:50', 1, 'live', '2017-05-07 11:53:50', 'example1', 'aside', 'Example 1', '', '&lt;p&gt;This aside box is used only for this Example 1 page.&lt;/p&gt;\r\n&lt;p&gt;All items with the topic of&amp;nbsp;&lt;span style=&quot;color: #999999;&quot;&gt;''article''&lt;/span&gt; are displayed on the right.&lt;/p&gt;\r\n&lt;p&gt;Each item is displayed with the first image, the heading and the caption.&lt;/p&gt;\r\n&lt;p&gt;The &lt;span style=&quot;color: #999999;&quot;&gt;''view details''&lt;/span&gt; buttons link to Example 2 which displays full details for the item selected.&lt;/p&gt;', NULL, NULL, NULL),
(12, NULL, '2017-05-10 12:02:47', 1, '2017-05-16 11:39:32', 1, 'live', '2017-05-10 11:39:32', 'example2', 'aside', 'Example 2', '', '&lt;p&gt;This aside box is used only for this Example 2 page.&lt;/p&gt;\r\n&lt;p&gt;This example displays most of the details for a single item, see &lt;a href=&quot;https://github.com/kewh/echoCMS/blob/master/README.md&quot;&gt;README&lt;/a&gt; for complete list.&lt;/p&gt;\r\n&lt;p&gt;The next &amp;amp; previous buttons link back to this Example 2 page with parameters in the URL query string to display the next or previous item.&lt;/p&gt;\r\n&lt;p&gt;The tags buttons link to Example 3 which displays all items with the requested tag.&lt;/p&gt;\r\n&lt;p&gt;The panorama format of the first image is displayed above, with the srcset including all 3 image sizes. Each image is shown below with all 4 of its separately cropped formats in x1 size.&lt;/p&gt;', NULL, NULL, NULL),
(13, NULL, '2017-05-11 00:02:57', 1, '2017-05-16 11:58:06', 1, 'live', '2017-05-11 11:58:06', 'example3', 'aside', 'Example 3', '', '&lt;p&gt;This aside box is used only for this Example 3 page.&lt;/p&gt;\r\n&lt;p&gt;This example displays items requested via a tag link, from either the Example 2 page or from this page.&lt;/p&gt;', NULL, NULL, NULL),
(14, NULL, '2017-05-16 16:49:54', 1, '2017-05-16 23:45:52', 1, 'live', '2017-05-16 23:45:52', 'index', '', 'Index', '', '&lt;p&gt;These examples are intended to be code examples to show how PHP statements are used in HTML to get data from the CMS, echo it on to the page, and then (particularly) how the this/prev/next and tab links work.&lt;/p&gt;\r\n&lt;p&gt;See the &lt;a href=&quot;https://github.com/kewh/echoCMS/blob/master/README.md&quot;&gt;README&lt;/a&gt;&amp;nbsp;on Github for more details.&lt;/p&gt;', NULL, NULL, NULL);

DROP TABLE IF EXISTS `pagesTable`;
CREATE TABLE `pagesTable` (
  `page` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `web_images` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

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
  `page` varchar(255) DEFAULT NULL,
  `element` varchar(40) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `text` text,
  `video` varchar(255) DEFAULT NULL,
  `download_src` varchar(255) DEFAULT NULL,
  `download_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pendingTagsTable`;
CREATE TABLE `pendingTagsTable` (
`id` int(11) NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `tag` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pendingTermsTable`;
CREATE TABLE `pendingTermsTable` (
`id` int(11) NOT NULL,
  `pending_id` int(11) DEFAULT NULL,
  `term` varchar(40) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
`id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rkey` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
`id` int(11) NOT NULL,
  `selector` varchar(40) NOT NULL,
  `validator` varchar(255) NOT NULL,
  `uid` int(11) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(45) NOT NULL,
  `agent` varchar(200) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tagsTable`;
CREATE TABLE `tagsTable` (
`id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `tag` varchar(40) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

INSERT INTO `tagsTable` (`id`, `content_id`, `tag`) VALUES
(73, 5, 'Elephant'),
(74, 5, 'India'),
(83, 6, 'India'),
(84, 6, 'Cat'),
(103, 7, 'Africa'),
(104, 7, 'Cat'),
(111, 3, 'Africa'),
(112, 3, 'Elephant'),
(115, 4, 'Africa'),
(116, 4, 'Cat'),
(117, 2, 'Africa'),
(118, 2, 'Rhino');

DROP TABLE IF EXISTS `termsTable`;
CREATE TABLE `termsTable` (
`id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `term` varchar(40) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8;

INSERT INTO `termsTable` (`id`, `content_id`, `term`) VALUES
(165, 5, 'Elephant'),
(166, 5, 'India'),
(175, 6, 'Tiger'),
(176, 6, 'India'),
(198, 1, 'Examples'),
(203, 12, 'Example'),
(204, 8, 'Example'),
(205, 13, 'Example'),
(230, 7, 'Lion'),
(231, 7, 'Africa'),
(238, 3, 'Elephant'),
(239, 3, 'Africa'),
(242, 4, 'Cheetah'),
(243, 4, 'Africa'),
(244, 2, 'Rhino'),
(245, 2, 'Africa'),
(249, 14, 'Index');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `last_ip` varchar(45) DEFAULT NULL,
  `last_dt` timestamp NULL DEFAULT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `email`, `password_hash`, `isactive`, `last_ip`, `last_dt`, `dt`) VALUES
(1, 'admin@change.this', '$2y$12$0VEQpZ3cctJrbZEcV0H30.R1cBneR8z9m9udRC13upON2Bw/fCDgu', 1, '::1', '2017-05-02 15:40:57', '2017-02-28 21:36:12');


ALTER TABLE `attempts`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `config`
 ADD UNIQUE KEY `setting` (`setting`);

ALTER TABLE `imagesTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `itemsTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `pendingImagesTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `pendingItemsTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `pendingTagsTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `pendingTermsTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `requests`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `sessions`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `tagsTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `termsTable`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `attempts`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `imagesTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=176;
ALTER TABLE `itemsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
ALTER TABLE `pendingImagesTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39;
ALTER TABLE `pendingItemsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
ALTER TABLE `pendingTagsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pendingTermsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57;
ALTER TABLE `requests`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `sessions`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
ALTER TABLE `tagsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=119;
ALTER TABLE `termsTable`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=250;
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
