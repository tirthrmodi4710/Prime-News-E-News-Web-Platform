-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 05:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enewsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin123', '2025-10-06 09:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Politics', '2025-10-06 09:10:37'),
(2, 'Technology', '2025-10-06 09:10:37'),
(3, 'Infrastructure', '2025-10-06 09:10:37'),
(4, 'Business', '2025-10-06 09:10:37'),
(5, 'Entertainment', '2025-10-06 09:10:37'),
(6, 'Religion', '2025-10-06 09:10:37'),
(7, 'Sports', '2025-10-06 09:10:37'),
(8, 'International', '2025-10-06 09:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `journalists`
--

CREATE TABLE `journalists` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journalists`
--

INSERT INTO `journalists` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'journalist', 'journalist123', '2025-10-06 09:10:37');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `full_content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `additional_images` text DEFAULT NULL,
  `videos` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `description`, `full_content`, `image`, `additional_images`, `videos`, `category_id`, `created_at`) VALUES
(11, 'Delhi Woman Attacks Sleeping Husband With Boiling Oil, Chilli Powder: Cops', 'On October 3, a 28-year-old pharmaceutical firm worker was brought to Safdarjung Hospital with severe burns and was admitted to the ICU in a critical condition.', 'New Delhi:\\r\\nAs Dinesh lay asleep in his Madangir house, his wife poured boiling oil and red chilli powder on him, sending him into screams that rent the dead calm of the night.\\r\\n\\r\\nOn October 3, a 28-year-old pharmaceutical firm worker was brought to Safdarjung Hospital with severe burns and was admitted to the ICU in a critical condition.\\r\\n\\r\\nAccording to an FIR lodged at Ambedkar Nagar Police Station the same day, Dinesh\\\'s wife poured hot oil on his torso around 3 am while he lay asleep, with the couple\\\'s eight-year-old daughter also in the home.\\r\\n\\r\\nDinesh told police that he had returned home late on October 2 after work, ate dinner, and went to bed. \\\"My wife and daughter were sleeping nearby. Around 3.15 am, I suddenly felt a sharp, burning pain across my body. I saw my wife standing and pouring boiling oil on my torso and face. Before I could get up or call for help, she sprinkled red chilli powder on my burns,\\\" he alleged in his complaint.\\r\\n\\r\\nWhen he protested, his wife retorted, \\\"\\\'Agar shor machaya to aur garam tel daal doongi\\\' (if you shout, I will pour more oil on you).\\\" But Dinesh could not suppress his screams. The commotion brought neighbours and his landlord\\\'s family, who lived on the floor below, rushing to the house.\\r\\n\\r\\nAnjali, the daughter of the house owner, was one of the people who made a run to check on him. \\\"My father went upstairs to see what was happening. The door was locked. His wife had locked the door from inside. We asked them to open the door. When the door finally opened, we saw him writhing in pain and his wife hiding inside the house,\\\" She told PTI.', '1759929801_cover_mdvof4p4_delhi-police-generic_625x300_04_August_25.webp', '[\"1759929801_additional_0_mdvof4p4_delhi-police-generic_625x300_04_August_25.webp\",\"1759929801_additional_1_OIP.jpeg\"]', '[]', 4, '2025-10-08 13:23:38'),
(12, 'PM Uses P Chidambaram\\\'s Remarks As Ammo', 'PM Modi said the then Congress-led government had succumbed to international pressure and decided not to take military action.', 'Training his guns on the Congress, using Union minister P Chidambaram\\\'s remarks as ammunition, Prime Minister Narendra Modi has said the opposition party had succumbed to international pressure in deciding not to take retaliatory action against Pakistan after the 26/11 attacks.\\r\\n\\r\\nIn a recent interview, Chidambaram, who took over as India\\\'s Home minister after the 2008 Mumbai terror attacks, said he was inclined towards retaliating against Pakistan but the government decided to exercise restraint after global pressure not to start a war. \\r\\n\\r\\nThe Congress leader said then US Secretary of State Condoleezza Rice had also travelled to New Delhi to meet him and Prime Minister Manmohan Singh and urged India not to take military action. \\r\\n\\r\\nReferring to Chidambaram\\\'s remarks - without naming him - during the inauguration of the Navi Mumbai International Airport on Wednesday, PM Modi said Mumbai, which is India\\\'s economic capital, was targeted in 2008, but the Congress-led government gave out a message of weakness and of kneeling in front of terrorism. ', '1759929724_cover_clouds-conifer-daylight-371589.jpg', '[\"1759929724_additional_0_clouds-conifer-daylight-371589.jpg\",\"1759929724_additional_1_keir-starmer-082050456-16x9_0.jpg\",\"1759929724_additional_2_OIP.jpeg\"]', '[\"1759929724_video_0_sample_video.mp4\"]', 1, '2025-10-08 13:23:41'),
(13, 'Mounjaro For Weight Loss Becomes Second-Highest Selling Drug In India In 6 Months', 'Mounjaro recorded Rs 80 crore in sales in September, trailing only GSK\\\'s antibiotic Augmentin, which registered Rs 85 crore', 'Barely six months after its launch, Eli Lilly\\\'s anti-obesity and diabetes drug Mounjaro has emerged as the second-largest brand in India\\\'s pharmaceutical market, reflecting the country\\\'s surging demand for weight-loss therapies.\\r\\n\\r\\nAccording to the latest Indian Pharmaceutical Market (IPM) data, Mounjaro recorded Rs 80 crore in sales in September, trailing only GSK\\\'s antibiotic Augmentin, which registered Rs 85 crore.\\r\\n\\r\\nWith cumulative revenue now touching Rs 233 crore, the once-a-week injectable drug has shaken up India\\\'s chronic therapy landscape.\\r\\n\\r\\nWhat Is Mounjaro\\r\\nMounjaro, known generically as tirzepatide, is once-a-week injectable drug originally developed to manage type 2 diabetes.\\r\\n\\r\\nIt works by mimicking two gut hormones-GLP-1 and GIP-that help regulate blood sugar levels and appetite.', '1759929956_cover_jr0bnokg_weight-loss_625x300_08_October_25.webp', '[\"1759929956_additional_0_edom4n48_thegrangemedicalpractice_625x300_08_October_25.webp\",\"1759929956_additional_1_jr0bnokg_weight-loss_625x300_08_October_25.webp\"]', '[\"1759929956_video_0_sample_video.mp4\"]', 4, '2025-10-08 13:26:06');

-- --------------------------------------------------------

--
-- Table structure for table `news_likes`
--

CREATE TABLE `news_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_likes`
--

INSERT INTO `news_likes` (`id`, `user_id`, `news_id`, `liked_at`) VALUES
(1, 2, 11, '2025-10-10 07:06:40'),
(2, 2, 13, '2025-10-10 07:07:15'),
(3, 2, 12, '2025-10-10 07:13:31'),
(4, 1, 11, '2025-10-10 08:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `news_requests`
--

CREATE TABLE `news_requests` (
  `id` int(11) NOT NULL,
  `journalist_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `full_content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `additional_images` text DEFAULT NULL,
  `videos` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_requests`
--

INSERT INTO `news_requests` (`id`, `journalist_id`, `title`, `description`, `full_content`, `image`, `additional_images`, `videos`, `category_id`, `status`, `created_at`) VALUES
(4, 1, 'qawer', 'asddfghjkllo', 'aqswedrftgyhujikolzxcvbnmlpo', '1759746093_cover_2021-Dodge-Challenger-Mopar-Drag-Pak-004-1080.jpg', '[\"1759746093_additional_0_2022-Dodge-Challenger-SRT-Hellcat-Jailbreak-004-1080.jpg\",\"1759746093_additional_1_2022-Mini-John-Cooper-Works-24h-Race-Nurburgring-006-1080.jpg\"]', '[\"1759746093_video_0_WhatsApp Video 2025-10-06 at 14.57.51_be2bd1b0.mp4\"]', 2, '', '2025-10-06 10:21:33'),
(5, 1, 'admin_view_received_news', 'admin_view_received_newsadmin_view_received_news', 'admin_view_received_newsadmin_view_received_newsadmin_view_received_newsadmin_view_received_newsadmin_view_received_newsadmin_view_received_news', '1759746128_cover_2022-Dodge-Challenger-SRT-Hellcat-Jailbreak-004-1080.jpg', '[\"1759746128_additional_0_2022-Mini-John-Cooper-Works-24h-Race-Nurburgring-006-1080.jpg\",\"1759746128_additional_1_2023-Audi-R8-Coupe-V10-GT-RWD-008-1080.jpg\"]', '[\"1759746128_video_0_WhatsApp Video 2025-10-06 at 14.57.51_be2bd1b0.mp4\"]', 3, '', '2025-10-06 10:22:08'),
(6, 1, 'IT professional has brain stroke at 37: How stress', 'It’s a hectic Tuesday for 37-year-old Gaurav, the principal director of an IT company. Three years ago, he would be pacing up and down, stuck to his phone, talking to his colleagues and clients non-stop, ', 'Shoe thrown at Chief Justice Gavai: A 71-year-old advocate allegedly threw a shoe at Chief Justice of India B R Gavai Monday morning inside the Supreme Court. The Delhi Police reached the Supreme Court, and said further investigations are ongoing.\\r\\n\\r\\nThe Delhi Police stated that the advocate, identified as Rakesh Kishore, took out his sports shoes and threw them at CJI Gavai at around 11:35 am, during the proceedings in Court No 1.\\r\\n\\r\\n“He was picked up by the security personnel. He was handed over to the security unit of the Supreme Court. He is a resident of the Mayur Vihar area and a registered member of the Supreme Court Bar Association,” a senior police officer said.\\r\\n\\r\\nDuring the initial investigation, it came to the fore that the advocate was unhappy with the CJI’s remarks during a recent hearing of a plea seeking the restoration of a Lord Vishnu idol in the Khajuraho Temple complex in Madhya Pradesh.\\r\\n\\r\\nCJI Gavai appeared unruffled, and asked lawyers to continue the proceedings.\\r\\n\\r\\nWhile being taken out of the court, the man reportedly said, “Sanatan dharma ka apmaan nahi sahega Hindustan (India will not tolerate the insult of Sanatan dharma)”.\\r\\n\\r\\n“Now, the Delhi Police are coordinating with the registrar general of the Supreme Court, and the New Delhi District is probing the matter before taking any legal action,” the officer said.\\r\\n\\r\\nCJI Gavai has Z plus security cover, provided by the Security Division of the Delhi Police.\\r\\n\\r\\nA lawyer who was present in the court told The Indian Express that a man in a lawyer’s robes flung something at CJI Gavai. The lawyer said security personnel later told him that the person had thrown a shoe at the Chief Justice of India.\\r\\n\\r\\nThe lawyer also said that while being escorted out, the man was heard shouting some slogan.\\r\\n\\r\\nPresiding over a two-judge bench with Justice K Vinod Chandran, CJI Gavai made the comments on September 16 while dismissing the plea seeking the reconstruction of a dilapidated 7 feet tall Lord Vishnu idol at the Javari Temple in Khajuraho Temple complex.\\r\\n\\r\\n“This purely publicity interest litigation… Go and ask the deity himself to do something. If you are saying that you are a strong devotee of Lord Vishnu, then you pray and do some meditation,” CJI Gavai told the petitioner.\\r\\n\\r\\nCJI Gavai later said he “respects all religions” and clarified that his comments were made in the context that the Archaeological Survey of India (ASI) has jurisdiction over its upkeep. “I believe in all the religions, I respect all the (religions),” CJI Gavai said.', '1759746490_cover_LinkedIn Cover Image (18-09-2025).png', '[\"1759746490_additional_0_2022-Dodge-Challenger-SRT-Hellcat-Jailbreak-003-1080.jpg\",\"1759746490_additional_1_2023-Audi-TT-RS-Iconic-Edition-001-1080.jpg\",\"1759746490_additional_2_2023-Bentley-Continental-GT-V8-S-003-1080.jpg\"]', '[\"1759746490_video_0_WhatsApp Video 2025-10-06 at 14.57.51_be2bd1b0.mp4\"]', 1, '', '2025-10-06 10:28:10'),
(7, 1, 'PM Uses P Chidambaram\\\'s Remarks As Ammo', 'PM Modi said the then Congress-led government had succumbed to international pressure and decided not to take military action.', 'Training his guns on the Congress, using Union minister P Chidambaram\\\'s remarks as ammunition, Prime Minister Narendra Modi has said the opposition party had succumbed to international pressure in deciding not to take retaliatory action against Pakistan after the 26/11 attacks.\\r\\n\\r\\nIn a recent interview, Chidambaram, who took over as India\\\'s Home minister after the 2008 Mumbai terror attacks, said he was inclined towards retaliating against Pakistan but the government decided to exercise restraint after global pressure not to start a war. \\r\\n\\r\\nThe Congress leader said then US Secretary of State Condoleezza Rice had also travelled to New Delhi to meet him and Prime Minister Manmohan Singh and urged India not to take military action. \\r\\n\\r\\nReferring to Chidambaram\\\'s remarks - without naming him - during the inauguration of the Navi Mumbai International Airport on Wednesday, PM Modi said Mumbai, which is India\\\'s economic capital, was targeted in 2008, but the Congress-led government gave out a message of weakness and of kneeling in front of terrorism. ', '1759929724_cover_clouds-conifer-daylight-371589.jpg', '[\"1759929724_additional_0_clouds-conifer-daylight-371589.jpg\",\"1759929724_additional_1_keir-starmer-082050456-16x9_0.jpg\",\"1759929724_additional_2_OIP.jpeg\"]', '[\"1759929724_video_0_sample_video.mp4\"]', 1, '', '2025-10-08 13:22:04'),
(8, 1, 'Delhi Woman Attacks Sleeping Husband With Boiling Oil, Chilli Powder: Cops', 'On October 3, a 28-year-old pharmaceutical firm worker was brought to Safdarjung Hospital with severe burns and was admitted to the ICU in a critical condition.', 'New Delhi:\\r\\nAs Dinesh lay asleep in his Madangir house, his wife poured boiling oil and red chilli powder on him, sending him into screams that rent the dead calm of the night.\\r\\n\\r\\nOn October 3, a 28-year-old pharmaceutical firm worker was brought to Safdarjung Hospital with severe burns and was admitted to the ICU in a critical condition.\\r\\n\\r\\nAccording to an FIR lodged at Ambedkar Nagar Police Station the same day, Dinesh\\\'s wife poured hot oil on his torso around 3 am while he lay asleep, with the couple\\\'s eight-year-old daughter also in the home.\\r\\n\\r\\nDinesh told police that he had returned home late on October 2 after work, ate dinner, and went to bed. \\\"My wife and daughter were sleeping nearby. Around 3.15 am, I suddenly felt a sharp, burning pain across my body. I saw my wife standing and pouring boiling oil on my torso and face. Before I could get up or call for help, she sprinkled red chilli powder on my burns,\\\" he alleged in his complaint.\\r\\n\\r\\nWhen he protested, his wife retorted, \\\"\\\'Agar shor machaya to aur garam tel daal doongi\\\' (if you shout, I will pour more oil on you).\\\" But Dinesh could not suppress his screams. The commotion brought neighbours and his landlord\\\'s family, who lived on the floor below, rushing to the house.\\r\\n\\r\\nAnjali, the daughter of the house owner, was one of the people who made a run to check on him. \\\"My father went upstairs to see what was happening. The door was locked. His wife had locked the door from inside. We asked them to open the door. When the door finally opened, we saw him writhing in pain and his wife hiding inside the house,\\\" She told PTI.', '1759929801_cover_mdvof4p4_delhi-police-generic_625x300_04_August_25.webp', '[\"1759929801_additional_0_mdvof4p4_delhi-police-generic_625x300_04_August_25.webp\",\"1759929801_additional_1_OIP.jpeg\"]', '[]', 4, '', '2025-10-08 13:23:21'),
(9, 1, 'Mounjaro For Weight Loss Becomes Second-Highest Selling Drug In India In 6 Months', 'Mounjaro recorded Rs 80 crore in sales in September, trailing only GSK\\\'s antibiotic Augmentin, which registered Rs 85 crore', 'Barely six months after its launch, Eli Lilly\\\'s anti-obesity and diabetes drug Mounjaro has emerged as the second-largest brand in India\\\'s pharmaceutical market, reflecting the country\\\'s surging demand for weight-loss therapies.\\r\\n\\r\\nAccording to the latest Indian Pharmaceutical Market (IPM) data, Mounjaro recorded Rs 80 crore in sales in September, trailing only GSK\\\'s antibiotic Augmentin, which registered Rs 85 crore.\\r\\n\\r\\nWith cumulative revenue now touching Rs 233 crore, the once-a-week injectable drug has shaken up India\\\'s chronic therapy landscape.\\r\\n\\r\\nWhat Is Mounjaro\\r\\nMounjaro, known generically as tirzepatide, is once-a-week injectable drug originally developed to manage type 2 diabetes.\\r\\n\\r\\nIt works by mimicking two gut hormones-GLP-1 and GIP-that help regulate blood sugar levels and appetite.', '1759929956_cover_jr0bnokg_weight-loss_625x300_08_October_25.webp', '[\"1759929956_additional_0_edom4n48_thegrangemedicalpractice_625x300_08_October_25.webp\",\"1759929956_additional_1_jr0bnokg_weight-loss_625x300_08_October_25.webp\"]', '[\"1759929956_video_0_sample_video.mp4\"]', 4, '', '2025-10-08 13:25:56'),
(10, 1, 'Opinion | To Just Welcome The \\\'Immigrant\\\' Is Not The Point', 'Nations have the right to regulate entry, to balance openness with cohesion. But the tone of the current moment suggests something more corrosive ', 'There was a time - not so long ago - when the image of the United States was inseparable from the idea of arrival. The Statue of Liberty, torch aloft, stood not merely as a monument but as a promise: that the tired, the poor, the huddled masses yearning to breathe free might find refuge and renewal on its shores. That promise, however imperfectly kept, shaped the moral imagination of the twentieth century. Today, it is fading.\\r\\n\\r\\nAcross the Western world, a new sentiment is sweeping through political discourse and public life - a hardening of borders, not just physical, but psychological. Immigration, once framed as enrichment, is increasingly cast as erosion. The United States, long mythologised as a nation of immigrants, now finds its reputation for openness under strain. Europe, with its layered histories of colonial entanglement and postwar reconstruction, is riven by tensions over asylum seekers, refugees, and migrant labour. The rhetoric has sharpened; the welcome has waned.', '1759930499_cover_gupo5gp_us-illegal-immigration-generic-_625x300_26_October_24.webp', '[]', '[]', 6, 'pending', '2025-10-08 13:34:59');

-- --------------------------------------------------------

--
-- Table structure for table `other_news`
--

CREATE TABLE `other_news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `other_news`
--

INSERT INTO `other_news` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Weather', 'There are some chances of rain Drops', '2025-10-10 06:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `saved_news`
--

CREATE TABLE `saved_news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_news`
--

INSERT INTO `saved_news` (`id`, `user_id`, `news_id`, `saved_at`) VALUES
(4, 2, 13, '2025-10-09 14:11:22'),
(5, 2, 11, '2025-10-09 14:11:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_verified`, `created_at`) VALUES
(1, 'test1', '0g9l3stj12@zudpck.com', '$2y$10$nUUffHdinIYxdkJt7h4N/eQLQwwtedio0TuIuYUiAHkt0pgf3tS0.', 1, '2025-10-06 09:14:02'),
(2, 'test2', 'rdc1myqzzd@mrotzis.com', '$2y$10$O6OJ3ZtyDOb05nXip5q7TOLrrvb2cdMLOsBL3ZqE6xu.jxU6fzNVS', 1, '2025-10-06 11:43:30'),
(3, 'test3', 'rxgr1jsajx@xkxkud.com', '$2y$10$yZl175dekRr/H/Bf5I6RpOuuQY3ZUgcWZlWzNsQgSDll7d8Ixo5Bm', 1, '2025-10-06 13:23:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `journalists`
--
ALTER TABLE `journalists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `news_likes`
--
ALTER TABLE `news_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `news_requests`
--
ALTER TABLE `news_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journalist_id` (`journalist_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `other_news`
--
ALTER TABLE `other_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_news`
--
ALTER TABLE `saved_news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_save` (`user_id`,`news_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `journalists`
--
ALTER TABLE `journalists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `news_likes`
--
ALTER TABLE `news_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news_requests`
--
ALTER TABLE `news_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `other_news`
--
ALTER TABLE `other_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saved_news`
--
ALTER TABLE `saved_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news_likes`
--
ALTER TABLE `news_likes`
  ADD CONSTRAINT `news_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_likes_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news_requests`
--
ALTER TABLE `news_requests`
  ADD CONSTRAINT `news_requests_ibfk_1` FOREIGN KEY (`journalist_id`) REFERENCES `journalists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_requests_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_news`
--
ALTER TABLE `saved_news`
  ADD CONSTRAINT `saved_news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_news_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
