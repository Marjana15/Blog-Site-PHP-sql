-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2024 at 03:17 PM
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
-- Database: `blogsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `description`) VALUES
(1, 'Food', 'This is food category'),
(2, 'Uncategorized', 'This is uncategorised section'),
(7, 'Gaming', 'this is gaming category'),
(8, 'Movies', 'Movies category'),
(9, 'Science and Technology', 'This is Science Technology category'),
(10, 'Travel', 'this is travel category'),
(12, 'Music ', 'This is music category'),
(14, 'Sports', 'This is sports section\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `timestamp`) VALUES
(7, 53, 29, 'This illustration perfectly captures the futuristic essence of a modern blog website, showcasing the seamless integration of cutting-edge technology and interactive design. The holographic elements and abstract digital connections beautifully symbolize the advanced tools and AI-driven innovations powering today\'s blogging platforms. It’s a visually engaging representation that inspires creativity and innovation in web development!', '2024-12-19 15:10:50');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED NOT NULL,
  `receiver_id` int(11) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `author_id` int(11) UNSIGNED NOT NULL,
  `is_featured` tinyint(1) NOT NULL,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `body`, `thumbnail`, `date_time`, `category_id`, `author_id`, `is_featured`, `likes`) VALUES
(53, 'Building a Modern Blog Website: A Fusion of Science and Technology', '<p>The evolution of the digital age has redefined the way we share information and ideas. Blogs have become a cornerstone of this revolution, serving as powerful platforms for expression, education, and connection. But have you ever wondered about the science and technology behind a modern blog website?</p><p><br></p><h3>The Science of Content Engagement</h3><p>Behind every engaging blog lies the science of understanding human behavior. Here are key aspects:</p><ol><li><strong>Psychology of Design</strong>: The layout, colors, and typography influence how readers interact with your blog. For instance, clean interfaces and balanced white spaces improve readability and retention.</li><li><strong>Cognitive Load Theory</strong>: A well-organized blog minimizes cognitive load, helping readers process and retain information better. Categorization, tags, and intuitive navigation play a crucial role here.</li><li><strong>Search Engine Optimization (SEO)</strong>: Modern blogs leverage algorithms and analytics to make content discoverable. SEO involves understanding keyword trends, user intent, and search engine mechanics, rooted in data science.</li></ol><h3>The Technology Powering Blogs</h3><p>The technology stack behind a blog website is equally fascinating. Let’s break it down:</p><h4>1. <strong>Front-End Frameworks</strong></h4><ul><li><strong>HTML5 and CSS3</strong>: Define the structure and style of the blog.</li><li><strong>JavaScript Frameworks</strong>: Tools like React.js or Vue.js enhance interactivity and user experience.</li></ul><h4>2. <strong>Back-End Development</strong></h4><ul><li><strong>Node.js, PHP, or Python</strong>: Process user requests and manage data.</li><li><strong>Databases</strong>: Modern blogs often use relational databases like MySQL or NoSQL solutions like MongoDB for scalability.</li></ul><h4>3. <strong>Content Management Systems (CMS)</strong></h4><ul><li>Platforms like WordPress, Ghost, or custom-built CMSs simplify content creation and management.</li><li>Plugins and APIs enable functionalities like comment sections, social sharing, and analytics.</li></ul><h4>4. <strong>Cloud Hosting and Deployment</strong></h4><ul><li>Hosting solutions such as AWS, Google Cloud, or Vercel ensure speed, scalability, and uptime.</li><li>Content Delivery Networks (CDNs) distribute content across the globe for faster access.</li></ul><h4>5. <strong>AI and Machine Learning</strong></h4><ul><li>Recommendation engines suggest relevant posts.</li><li>Natural Language Processing (NLP) enhances grammar checks and SEO suggestions.</li><li>Sentiment analysis helps tailor content to audience preferences.</li></ul><h3>Future Trends in Blog Websites</h3><ol><li><strong>Immersive Content</strong>: Virtual and Augmented Reality (VR/AR) are set to revolutionize storytelling.</li><li><strong>AI-Powered Personalization</strong>: Dynamic content based on user preferences will dominate.</li><li><strong>Blockchain for Content Ownership</strong>: Decentralized platforms will ensure transparent content attribution and monetization.</li></ol><h3>Conclusion</h3><p>Building a modern blog website is a beautiful amalgamation of art, science, and technology. It’s not just about writing posts; it’s about creating an ecosystem where technology empowers human creativity to reach a global audience.</p><p>Whether you\'re a seasoned developer or an aspiring blogger, the interplay of science and technology in blogs reminds us that every click, read, and share is a testament to innovation.</p><p><strong>What do you think about technology\'s role in tomorrow\'s blogs? Share your thoughts in the comments below!</strong></p>', 'https://i.ibb.co/pzFQNGN/php7C7F.webp', '2024-12-19 09:10:25', 9, 29, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES
(17, 53, 29, '2024-12-19 09:10:36');

--
-- Triggers `post_likes`
--
DELIMITER $$
CREATE TRIGGER `after_like_delete` AFTER DELETE ON `post_likes` FOR EACH ROW BEGIN
    UPDATE posts
    SET likes = likes - 1
    WHERE id = OLD.post_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_like_insert` AFTER INSERT ON `post_likes` FOR EACH ROW BEGIN
    UPDATE posts
    SET likes = likes + 1
    WHERE id = NEW.post_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `password`, `avatar`, `is_admin`) VALUES
(29, 'Marjana', 'Begum', 'marjana15', 'ceo@dragonrecruiting.net', '$2y$10$PLmoK6cXu9zQdixuBQtApOO8NdWLuIgHk2P.I5Tb8tb29pDFzyVq6', 'https://i.ibb.co/5jpn10q/967fc3088e89.jpg', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_blog_category` (`category_id`),
  ADD KEY `FK_blog_author` (`author_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_post_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `FK_blog_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_blog_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_post_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
