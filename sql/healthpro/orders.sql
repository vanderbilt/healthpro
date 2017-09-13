CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `site` varchar(50) NOT NULL,
  `participant_id` varchar(50) NOT NULL,
  `rdr_id` VARCHAR(50) NULL DEFAULT NULL,
  `biobank_id` varchar(50) NOT NULL,
  `created_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_id` varchar(100) NOT NULL,
  `mayo_id` varchar(100) NULL DEFAULT NULL,
  `requested_samples` VARCHAR(255) NULL DEFAULT NULL,
  `printed_ts` timestamp NULL DEFAULT NULL,
  `collected_user_id` int(11) DEFAULT NULL,
  `collected_site` varchar(50) DEFAULT NULL,
  `collected_ts` timestamp NULL DEFAULT NULL,
  `collected_samples` varchar(255) DEFAULT NULL,
  `collected_notes` text,
  `processed_user_id` int(11) DEFAULT NULL,
  `processed_site` varchar(50) DEFAULT NULL,
  `processed_ts` timestamp NULL DEFAULT NULL,
  `processed_samples` varchar(255) DEFAULT NULL,
  `processed_samples_ts` VARCHAR(255) NULL DEFAULT NULL,
  `processed_centrifuge_type` varchar(50) NULL DEFAULT NULL,
  `processed_notes` text,
  `finalized_user_id` int(11) DEFAULT NULL,
  `finalized_site` varchar(50) DEFAULT NULL,
  `finalized_ts` timestamp NULL DEFAULT NULL,
  `finalized_samples` varchar(255) DEFAULT NULL,
  `finalized_notes` text,
  `fedex_tracking` varchar(50) NULL DEFAULT NULL,
  `type` varchar(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `participant_id` (`participant_id`),
  KEY `order_id` (`order_id`),
  KEY `mayo_id` (`mayo_id`)
) DEFAULT CHARSET=utf8mb4;
