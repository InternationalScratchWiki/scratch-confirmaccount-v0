CREATE TABLE `account_requests` (
  `id` int(8) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `description` varchar(700) NOT NULL,
  `admin` varchar(20) NOT NULL,
  `state` int(2) NOT NULL DEFAULT 0,
  `acr_filename` varchar(255) DEFAULT NULL,
  `acr_storage_key` varchar(64) DEFAULT NULL,
  `acr_held` binary(14) DEFAULT NULL,
  `acr_comment` varchar(255) DEFAULT NULL
);

ALTER TABLE `account_requests_old`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `account_requests_old`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;
