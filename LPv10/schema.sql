-- ============================================================
-- LinkParty.net — COMPLETE DATABASE SCHEMA v6
-- Database: linkhpiy_linkboost | Prefix: lb_
-- ============================================================
SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- Users
CREATE TABLE IF NOT EXISTS `lb_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff','client') DEFAULT 'client',
  `plan` enum('free','starter','pro','agency') DEFAULT 'free',
  `phone` varchar(50) DEFAULT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `api_key` varchar(64) DEFAULT NULL,
  `plan_expires_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Plans
CREATE TABLE IF NOT EXISTS `lb_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(50) NOT NULL UNIQUE,
  `price_monthly` decimal(10,2) DEFAULT 0.00,
  `price_yearly` decimal(10,2) DEFAULT 0.00,
  `max_projects` int(5) DEFAULT 1,
  `max_keywords` int(5) DEFAULT 10,
  `max_audits` int(5) DEFAULT 1,
  `max_orders` int(5) DEFAULT 3,
  `ai_tools` tinyint(1) DEFAULT 1,
  `seo_tools` tinyint(1) DEFAULT 1,
  `api_access` tinyint(1) DEFAULT 0,
  `white_label` tinyint(1) DEFAULT 0,
  `features` text DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(5) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subscriptions
CREATE TABLE IF NOT EXISTS `lb_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `status` enum('active','cancelled','expired','trial') DEFAULT 'active',
  `billing_cycle` enum('monthly','yearly') DEFAULT 'monthly',
  `amount` decimal(10,2) DEFAULT 0.00,
  `stripe_sub_id` varchar(255) DEFAULT NULL,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects (for SEO tracking)
CREATE TABLE IF NOT EXISTS `lb_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `country` varchar(10) DEFAULT 'US',
  `language` varchar(10) DEFAULT 'en',
  `status` tinyint(1) DEFAULT 1,
  `last_audit` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rank Tracking
CREATE TABLE IF NOT EXISTS `lb_rank_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `keyword` varchar(500) NOT NULL,
  `target_url` varchar(500) DEFAULT NULL,
  `country` varchar(10) DEFAULT 'US',
  `device` enum('desktop','mobile') DEFAULT 'desktop',
  `current_rank` int(5) DEFAULT NULL,
  `prev_rank` int(5) DEFAULT NULL,
  `best_rank` int(5) DEFAULT NULL,
  `last_checked` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rank History
CREATE TABLE IF NOT EXISTS `lb_rank_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_id` int(11) NOT NULL,
  `rank` int(5) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `checked_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site Audits
CREATE TABLE IF NOT EXISTS `lb_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `status` enum('pending','running','completed','failed') DEFAULT 'pending',
  `score` int(3) DEFAULT NULL,
  `issues_critical` int(5) DEFAULT 0,
  `issues_warning` int(5) DEFAULT 0,
  `issues_info` int(5) DEFAULT 0,
  `pages_crawled` int(5) DEFAULT 0,
  `result_data` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services (backlink packages)
CREATE TABLE IF NOT EXISTS `lb_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('guest_post','niche_edit','homepage_link','package') DEFAULT 'guest_post',
  `niche` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `min_da` int(3) DEFAULT 0,
  `min_dr` int(3) DEFAULT 0,
  `turnaround_days` int(3) DEFAULT 7,
  `price` decimal(10,2) DEFAULT 0.00,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(5) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders
CREATE TABLE IF NOT EXISTS `lb_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `client_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `status` enum('pending','in_progress','review','completed','cancelled') DEFAULT 'pending',
  `target_url` varchar(500) NOT NULL,
  `anchor_text` varchar(255) DEFAULT NULL,
  `niche` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `live_url` varchar(500) DEFAULT NULL,
  `report_url` varchar(500) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments
CREATE TABLE IF NOT EXISTS `lb_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `type` enum('order','subscription') DEFAULT 'order',
  `gateway` varchar(50) DEFAULT 'manual',
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invoices
CREATE TABLE IF NOT EXISTS `lb_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `status` enum('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
  `due_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog Posts
CREATE TABLE IF NOT EXISTS `lb_blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `slug` varchar(500) NOT NULL UNIQUE,
  `content` longtext DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(500) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `status` enum('draft','published') DEFAULT 'draft',
  `author_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pages (CMS)
CREATE TABLE IF NOT EXISTS `lb_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` varchar(500) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact Messages
CREATE TABLE IF NOT EXISTS `lb_contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings
CREATE TABLE IF NOT EXISTS `lb_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL UNIQUE,
  `value` text DEFAULT NULL,
  `group` varchar(50) DEFAULT 'general',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Log
CREATE TABLE IF NOT EXISTS `lb_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password Resets
CREATE TABLE IF NOT EXISTS `lb_password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tool Usage
CREATE TABLE IF NOT EXISTS `lb_tool_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `tool` varchar(100) NOT NULL,
  `input` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET foreign_key_checks = 1;

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Admin: admin@linkparty.net / Admin@1234
INSERT IGNORE INTO `lb_users` (`name`,`email`,`password`,`role`,`plan`,`status`,`email_verified`,`created_at`)
VALUES ('Admin','admin@linkparty.net','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','agency',1,1,NOW());

-- Demo: demo@linkparty.net / secret
INSERT IGNORE INTO `lb_users` (`name`,`email`,`password`,`role`,`plan`,`status`,`email_verified`,`created_at`)
VALUES ('Demo User','demo@linkparty.net','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','client','pro',1,1,NOW());

-- Plans
INSERT IGNORE INTO `lb_plans` (`name`,`slug`,`price_monthly`,`price_yearly`,`max_projects`,`max_keywords`,`max_audits`,`max_orders`,`ai_tools`,`seo_tools`,`api_access`,`white_label`,`features`,`is_popular`,`status`,`sort_order`) VALUES
('Free','free',0,0,1,10,1,3,1,1,0,0,'["Keyword Research","Basic SEO Tools","3 Link Orders","1 Project","AI Assistant"]',0,1,1),
('Starter','starter',29,249,3,100,5,20,1,1,0,0,'["All Free Features","3 Projects","100 Keywords Tracked","5 Site Audits","20 Link Orders","Priority Support"]',0,1,2),
('Pro','pro',79,699,10,500,20,100,1,1,1,0,'["All Starter Features","10 Projects","500 Keywords","20 Audits/mo","100 Orders","API Access","CSV Export"]',1,1,3),
('Agency','agency',199,1799,99,9999,999,999,1,1,1,1,'["Unlimited Projects","Unlimited Keywords","Unlimited Audits","White-label Reports","Dedicated Support","Custom Branding","API Access"]',0,1,4);

-- Settings
INSERT IGNORE INTO `lb_settings` (`key`,`value`,`group`) VALUES
('site_name','LinkParty','general'),
('site_tagline','Premium SEO & Backlink Platform','general'),
('hero_title','The Complete SEO & Link Building Platform','theme'),
('hero_sub','Keyword research, site audits, rank tracking, and premium backlinks — all in one place.','theme'),
('hero_cta','Start Free Today →','theme'),
('hero_cta2','View Pricing','theme'),
('stat1val','22+','theme'),('stat1lbl','SEO TOOLS','theme'),
('stat2val','12K+','theme'),('stat2lbl','PUBLISHER SITES','theme'),
('stat3val','98%','theme'),('stat3lbl','DELIVERY RATE','theme'),
('stat4val','2,400+','theme'),('stat4lbl','HAPPY CLIENTS','theme'),
('accent_color','#b8ff3c','theme'),
('bg_color','#080810','theme'),
('contact_email','admin@linkparty.net','general'),
('order_prefix','ORD-','general'),
('stripe_pub_key','','payment'),
('stripe_secret_key','','payment'),
('google_client_id','','oauth'),
('google_client_secret','','oauth'),
('footer_text','All rights reserved.','general');

-- Services
INSERT IGNORE INTO `lb_services` (`name`,`type`,`niche`,`description`,`min_da`,`min_dr`,`turnaround_days`,`price`,`is_featured`,`status`,`sort_order`,`created_at`) VALUES
('DA30+ Guest Post','guest_post','General','High-quality editorial guest post on a real DA30+ website.',30,25,7,49.00,1,1,1,NOW()),
('DA50+ Guest Post','guest_post','General','Premium guest post on DA50+ authority website.',50,45,10,99.00,1,1,2,NOW()),
('Niche Edit — DR40+','niche_edit','General','Link insertion into existing content on aged DR40+ domain.',40,35,5,69.00,1,1,3,NOW()),
('Tech/SaaS Guest Post','guest_post','Technology','Editorial placement on tech niche DA35+ site.',35,30,10,89.00,1,1,4,NOW()),
('Finance Guest Post','guest_post','Finance','DA40+ guest post in finance/fintech niche.',40,35,10,129.00,0,1,5,NOW()),
('Homepage Link DR50+','homepage_link','General','Permanent homepage backlink on DR50+ domain.',50,50,14,199.00,0,1,6,NOW());

-- Password fix (both passwords = "secret")
UPDATE `lb_users` SET `password`='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE email IN ('admin@linkparty.net','demo@linkparty.net');

-- ============================================================
-- UPGRADE v10: New Tables
-- ============================================================

-- Notifications
CREATE TABLE IF NOT EXISTS `lb_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Support Tickets
CREATE TABLE IF NOT EXISTS `lb_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(30) NOT NULL UNIQUE,
  `user_id` int(11) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `department` varchar(50) DEFAULT 'general',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `lb_ticket_replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_staff` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Coupons
CREATE TABLE IF NOT EXISTS `lb_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL UNIQUE,
  `type` enum('percent','fixed') DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL DEFAULT 0,
  `min_amount` decimal(10,2) DEFAULT 0,
  `max_uses` int(5) DEFAULT NULL,
  `uses` int(5) DEFAULT 0,
  `applies_to` enum('orders','plans','all') DEFAULT 'all',
  `expires_at` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews
CREATE TABLE IF NOT EXISTS `lb_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) DEFAULT 5,
  `review` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Referrals
CREATE TABLE IF NOT EXISTS `lb_referrals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referrer_id` int(11) NOT NULL,
  `referred_id` int(11) NOT NULL,
  `status` enum('pending','qualified','paid') DEFAULT 'pending',
  `commission` decimal(10,2) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Newsletter
CREATE TABLE IF NOT EXISTS `lb_newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL UNIQUE,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('subscribed','unsubscribed') DEFAULT 'subscribed',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Testimonials
CREATE TABLE IF NOT EXISTS `lb_testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `rating` tinyint(1) DEFAULT 5,
  `content` text NOT NULL,
  `is_featured` tinyint(1) DEFAULT 1,
  `sort_order` int(5) DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Publisher Directory
CREATE TABLE IF NOT EXISTS `lb_publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `niche` varchar(255) DEFAULT NULL,
  `da` int(3) DEFAULT 0,
  `dr` int(3) DEFAULT 0,
  `traffic` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0,
  `link_type` enum('guest_post','niche_edit','homepage') DEFAULT 'guest_post',
  `language` varchar(20) DEFAULT 'English',
  `country` varchar(50) DEFAULT 'US',
  `is_featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample testimonials
INSERT IGNORE INTO `lb_testimonials` (`name`,`role`,`avatar`,`rating`,`content`,`is_featured`,`sort_order`,`created_at`) VALUES
('Sarah K.','SEO Manager at TechCorp',NULL,5,'LinkParty delivered a DA52 guest post within 6 days. Rankings jumped 14 positions in 3 weeks. Best ROI I have seen from link building.',1,1,NOW()),
('Marcus D.','Founder, DigitalEdge Agency',NULL,5,'We use LinkParty for all our client campaigns. The AI autopilot tool alone saves us 10 hours a week. Highly recommended.',1,2,NOW()),
('Priya T.','E-commerce SEO Lead',NULL,5,'The rank tracker and site audit tools are incredibly detailed. The backlink quality is consistently high. Our organic traffic is up 340% in 6 months.',1,3,NOW()),
('James L.','Freelance SEO Consultant',NULL,4,'Great platform for link building at scale. The AI content writer is a game-changer. A few small UX improvements would make it perfect.',1,4,NOW()),
('Fatima R.','Growth Marketer',NULL,5,'Went from page 4 to page 1 in 60 days using the recommended strategy from the AI Autopilot. Absolutely incredible.',1,5,NOW()),
('Alex W.','Agency Owner, UK',NULL,5,'We manage 40+ clients through LinkParty. The white-label reports are professional and the dashboard is easy for clients to understand.',1,6,NOW());

-- Insert sample publishers
INSERT IGNORE INTO `lb_publishers` (`domain`,`niche`,`da`,`dr`,`traffic`,`price`,`link_type`,`language`,`country`,`is_featured`,`status`,`created_at`) VALUES
('techblog-example.com','Technology',52,48,'45K/mo',89.00,'guest_post','English','US',1,1,NOW()),
('financeinsider-ex.com','Finance',61,55,'80K/mo',129.00,'guest_post','English','US',1,1,NOW()),
('healthhub-example.com','Health',45,40,'30K/mo',69.00,'guest_post','English','UK',1,1,NOW()),
('bizgrowth-example.com','Business',38,35,'20K/mo',59.00,'niche_edit','English','AU',0,1,NOW()),
('saasreviews-ex.com','SaaS/Software',55,50,'60K/mo',99.00,'guest_post','English','US',1,1,NOW()),
('marketingpro-ex.com','Marketing',48,44,'35K/mo',79.00,'niche_edit','English','US',0,1,NOW());
