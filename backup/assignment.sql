-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 07:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `phone`, `email`, `subject`, `message`, `submitted_at`) VALUES
(17, 'James ', '09123456789', 'jamesss@gmail.com', 'General Question', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '2025-07-19 15:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `cookbook_recipe`
--

CREATE TABLE `cookbook_recipe` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cooking_time` int(11) NOT NULL,
  `cuisine_type_id` int(11) NOT NULL,
  `difficulty_level_id` int(11) NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text NOT NULL,
  `ingredients` text NOT NULL,
  `recipe_photo` text NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cookbook_recipe`
--

INSERT INTO `cookbook_recipe` (`id`, `name`, `cooking_time`, `cuisine_type_id`, `difficulty_level_id`, `published_at`, `description`, `ingredients`, `recipe_photo`, `user_id`) VALUES
(6, 'Mexican Spiced Tacos', 35, 4, 3, '2025-07-06 12:07:01', 'Street tacos with North African spices and traditional Mexican techniques.', 'Corn tortillas, Ground beef or shredded chicken, Onion , Garlic, Tomato paste, Chili powder, Ground cumin, Smoked paprika, Oregano, Salt & pepper, Olive oil, Fresh cilantro', './images/1020005-we-nosh-tako-tacos-7377.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `cooking_tips`
--

CREATE TABLE `cooking_tips` (
  `id` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `published_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cooking_tips`
--

INSERT INTO `cooking_tips` (`id`, `tag`, `description`, `published_at`, `user_id`) VALUES
(3, 'Cooking Technique', 'For perfect fusion stir-fries, prep all ingredients first (mise en place) and use the highest heat your stove can provide. The wok hei (breath of the wok) makes all the difference!', '0000-00-00 00:00:00', 2),
(4, 'Flavor Balance', 'The key to successful fusion cooking is balancing the five tastes: sweet, sour, salty, bitter, and umami. Each cuisine emphasizes different combinations.', '0000-00-00 00:00:00', 3),
(5, 'Spice Blending', 'When creating fusion spice blends, start with small quantities and taste as you go. Toast whole spices before grinding for maximum flavor impact.', '0000-00-00 00:00:00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `cuisine_types`
--

CREATE TABLE `cuisine_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuisine_types`
--

INSERT INTO `cuisine_types` (`id`, `name`, `status`) VALUES
(1, 'Italian', 'active'),
(2, 'Thai', 'active'),
(3, 'Burmese', 'active'),
(4, 'Mexican', 'active'),
(5, 'Indian', 'active'),
(6, 'French', 'active'),
(7, 'International Dessert', 'active'),
(8, 'Japanese', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `dietary_preferences`
--

CREATE TABLE `dietary_preferences` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dietary_preferences`
--

INSERT INTO `dietary_preferences` (`id`, `name`, `status`) VALUES
(1, 'Vegan', 'active'),
(2, 'Vegetarian', 'active'),
(3, 'Gluten-Free', 'active'),
(4, 'Keto', 'active'),
(5, 'Halal', 'active'),
(6, 'Kosher', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `difficulty_levels`
--

CREATE TABLE `difficulty_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `difficulty_levels`
--

INSERT INTO `difficulty_levels` (`id`, `name`, `status`) VALUES
(1, 'easy', 'active'),
(2, 'medium', 'active'),
(3, 'hard', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(25) NOT NULL,
  `type` varchar(25) NOT NULL,
  `size` int(20) NOT NULL,
  `url` text NOT NULL,
  `uploaded_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `title`, `description`, `status`, `type`, `size`, `url`, `uploaded_date`) VALUES
(3, 'How to Slice a Yellow Chili Safely', 'This quick tutorial shows you how to prepare yellow chilies peppers with precision and safety.', 'active', 'video', 26007228, './videos/687bbfe434df3_video-1.mp4', '2025-07-19 22:25:16'),
(4, 'How to Slice Mushrooms', 'This tutorial shows the best way to clean, trim, and slice mushrooms evenly for sautés, soups, salads and more.', 'active', 'video', 23627555, './videos/687bc74e48604_video-2.mp4', '2025-07-19 22:56:54'),
(5, 'Topping the Perfect Pizza', 'From spreading the rich tomato sauce to layering on creamy mozzarella, vibrant veggies and savory meats.', 'active', 'video', 24292996, './videos/687bc7685c5d6_video-3.mp4', '2025-07-19 22:57:13'),
(6, 'Cooking Temperature Guide', 'Safe cooking temperatures and doneness levels for proteins from various cuisines.', 'active', 'pdf', 57136, './pdf/687bc837ba905_cooking-temperature-guide.pdf', '2025-07-19 23:00:47'),
(7, 'Cooking Time Reference', 'Comprehensive timing guide for cooking methods and ingredients across different cuisines.', 'active', 'pdf', 376622, './pdf/687bc85d13863_cooking-time-reference.pdf', '2025-07-19 23:01:25'),
(8, 'Spice Pairing Guide', 'Comprehensive spice pairing guide to spice combinations that work well in fusion cooking.', 'active', 'pdf', 213178, './pdf/687bc881877fc_spice-pairing-guide.pdf', '2025-07-19 23:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text NOT NULL,
  `prep_time_minutes` int(11) DEFAULT NULL,
  `cooking_time_minutes` int(11) DEFAULT NULL,
  `total_time_minutes` int(11) GENERATED ALWAYS AS (`prep_time_minutes` + `cooking_time_minutes`) STORED,
  `status` varchar(25) NOT NULL,
  `difficulty_level_id` int(11) NOT NULL,
  `cuisine_type_id` int(11) NOT NULL,
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipe_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `name`, `description`, `ingredients`, `prep_time_minutes`, `cooking_time_minutes`, `status`, `difficulty_level_id`, `cuisine_type_id`, `published_at`, `recipe_image`) VALUES
(1, 'Muskmelon Shake', 'Muskmelon shake is the ultimate summer fix. Sweet, juicy muskmelon turned into a cool and creamy shake – your new summer favorite.', 'Muskmelon 1 large, Sugar 3 tbs or to taste, Doodh (Milk) 1 & ½ Cup, Ice cream vanilla flavor 2 scoops, Ice cream vanilla flavor 1 scoop, Pista (Pistachios) sliced, Badam (Almond) sliced', 3, 3, 'active', 1, 5, '2025-07-02 13:48:55', './images/muskmelon-shake-01.jpg'),
(2, 'Mango Cheesecake', 'A delectable sweet delight for Eid. Make this easy no-bake mango cheesecake and enjoy the good times with family and friends.', 'Blue band margarine 80g, Cream cheese 300g, Bareek cheeni (Caster sugar) 1/3 Cup, Vanilla essence 1 tsp, Mango puree 1 & ½ Cup, Water ¼ Cup, Gelatin powder 1 tbs, Whipping cream 250ml, Mango puree 1 Cup, Bareek cheeni (Caster sugar) 3 tbs, Lemon juice ½ tsp, Water ¼ Cup, Gelatin powder 1 tbs, Aam (Mango) cubes, Cherry, Podina (Mint leaves)', 15, 25, 'active', 2, 7, '2025-07-04 13:50:07', './images/mango-cheesecake-01.jpg'),
(3, 'Malai Mutton Boti Skewers', 'Soft, creamy, and perfectly cooked—these Malai Mutton Boti Skewers are a must-try! Cooked to perfection in the Dawlance Air Fryer Microwave oven for that juicy tenderness every time.', 'Dahi (Yogurt) ¾ Cup, Cream 2-3 tbs Cup, Podina (Mint leaves) dried & crushed 1 tsp, Himalayan pink salt ¼ tsp or to taste, Chaat masala ¼ tsp, Kashmiri lal mirch (Kashmiri red chilli) powder 1/8 tsp, Boneless mutton cubes 750g, Adrak lehsan paste (Ginger garlic paste) 1 tbs, Himalayan pink salt 1 & ½ tsp or to taste, Meat tenderizer ½ tbs, Dahi (Yogurt) thick ¼ Cup, Cream 4 tbs, Hari mirch (Green chilli) paste 1 tbs, Dhania powder (Coriander powder) 1 tbs, Zeera powder (Cumin powder) 1 & ½ tsp, Safed mirch powder (White pepper powder) ½ tsp, Garam masala powder 1 & ½ tsp, Elaichi powder (Cardamom powder) ½ tsp, Baisan (Gram flour) 1 tbs, Kali mirch powder (Black pepper powder) 1 tsp, Jaifil powder (Nutmeg powder) 1 pinch, Lemon juice 1 tbs, Hara dhania (Fresh coriander) chopped.', 8, 5, 'active', 2, 5, '2025-07-04 14:24:10', './images/malai-mutton-boti-skewers-01.jpg'),
(4, 'Beef Malai Biryani', 'When bold spices meet creamy richness – Malai Beef Biryani becomes even more irresistible with Olper’s Cream.', 'Yogurt whisked ½ Cup, Raw papaya paste 3 tbs, Ginger garlic crushed 2 tbs, Green chilli crushed 2 tbs, Coriander seeds roasted & crushed 1 tbs, Red chilli crushed 1 tsp, Cumin seeds roasted & crushed 1 tbs, Desiccated coconut 2 tbs, Black pepper powder 1 tsp, Himalayan pink salt ½ tbs or to taste, Dried fenugreek leaves ½ tbs, Olper’s Cream ½ Cup,Fresh coriander chopped 1-2 tbs, Boneless beef 800g, Cooking oil 3-4 tbs, Cumin seeds 1 tbs, Cinnamon sticks 2, Cloves 5-6, Green cardamom 3-4, Black cardamom 1, Star anise 1, Bay leaf 1, Garlic sliced 1 tbs, Green chillies chopped 5-6, Onion sliced 1 large, Biryani masala 1 & ½ tbs, Lemon slices', 15, 25, 'active', 2, 5, '2025-07-04 14:52:41', './images/beef-malai-biryani-01.jpg'),
(5, 'Italian Chopped Sandwich', 'Chop it, stack it, press it — the ultimate Italian Chopped Sandwich is here. Loaded with pepperoni, crunchy lettuce, tangy pickles, and zesty bites, all tucked into toasted bread for that perfect crunch.', 'Iceberg lettuce 5-6 leaves, Mortadella 2-3 slices, Peperoni 8-10 slices, Tomato slices 3-4, Onion rings 6-8, Black pepper crushed ½ tsp, Himalayan pink salt ½ tsp or to taste, Italian seasoning 2 tsp, Cheddar cheese grated 1/3 Cup, Olive oil extra virgin 2 tbs, Vinegar ½ tbs, Mayonnaise 2-3 tbs, Bread of your choice, Butter as required', 8, 2, 'active', 2, 1, '2025-07-04 14:57:33', './images/italian-chopped-sandwich-01.jpg'),
(6, 'Tomato Pasta', 'Tomato Pasta – just in time to add a bold new twist to your menu. From the first bite to the last twirl, it’s packed with rich, tangy flavor that speaks for itself.', 'Cooking oil 2 tbs, Onion finely chopped 1 large, Garlic finely chopped 1 & ½ tbs, Tomato paste 2 tbs, Chicken Stock 1 Cup (Substitute: dissolve 1 chicken cube in 1 Cup water), Cream 50ml, Cheddar cheese grated 50g, Chilli oil 2-3 tbs, Black pepper powder ½ tsp, Italian herbs 1 tsp, Himalayan pink salt ½ tsp or to taste, paghetti 250g boiled with salt as per pack’s instruction, Fresh parsley chopped 1 tbs\r\n, Chilli oil, Fresh parsley chopped', 10, 7, 'active', 3, 1, '2025-07-04 15:06:52', './images/tomato-pasta-01.jpg'),
(7, 'Chocolate Custard Cake', 'Chocolatey, creamy, and melt-in-your-mouth delicious! This No-Oven Chocolate Custard Cake is a must-try for every dessert lover—pure indulgence in every bite!', 'Flour 1 & ¼ Cup, Cocoa powder 1/3 Cup, Baking powder ½ tbs, Instant coffee 2 tsp, Himalayan pink salt ¼ tsp, Eggs 4, Caster sugar 1 Cup, Milk ½ Cup, Vinegar 1 tsp, Vanilla essence 2 tsp, Cooking oil 1/3 Cup, Hot water ¼ Cup, Milk 2 Cups, Sugar ½ Cup, Custard powder 3 tbs, Cocoa powder ¼ Cup, Vanilla essence 1 tsp, Himalayan pink salt 1 pinch, Dark chocolate chopped ½ Cup, Whipping cream 100ml, Sugar syrup, Cake crumbs', 30, 15, 'active', 3, 7, '2025-07-04 15:28:43', './images/chocolate-custard-cake-01.jpg'),
(8, 'Almond Croissant', 'Homemade Almond Croissants: A buttery, flaky treat with a touch of almond. Try this easy recipe at home and let us know what you think!', 'Butter unsalted 50g, Caster sugar 5 tbs or to taste, Egg 1, Vanilla essence ½ tsp, Almond flour 1 Cup, Himalayan pink salt 1 pinch or to taste, Croissants 3-4, Almonds flakes, Icing sugar', 8, 10, 'active', 1, 6, '2025-07-04 15:44:06', './images/almond-croissant-01.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_dietary_preferences`
--

CREATE TABLE `recipe_dietary_preferences` (
  `recipe_id` int(11) NOT NULL,
  `dietary_preference_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_dietary_preferences`
--

INSERT INTO `recipe_dietary_preferences` (`recipe_id`, `dietary_preference_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 3),
(3, 3),
(4, 6),
(5, 5),
(6, 3),
(7, 4),
(8, 3),
(8, 4),
(8, 5);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `phone`, `password`) VALUES
(1, 'Myat Min Aung', 'myatminaung@gmail.com', '09427274346', '$2y$10$4gcdEGFtRKiq6gsc1/qG7OTQ6nhgTvEfaxahiQRac2OBmVmT0nm.2'),
(2, 'David K', 'david.k@gmail.com', '09123456789', '$2y$10$o5sLLIUobxlJ5U1S.I7TPOyDfEoDh5/05Mu2tFGXSkfSV8pRQmYx2'),
(3, 'Antonio R.', 'antonio.r@gmail.com', '09987654321', '$2y$10$Y6js.IZpc2KvQEnuHLuK8O8MxEJnnO/R3ofKBLYFJy5yEj5TlznGW'),
(4, 'Chef Maria', 'chef.maria@gmail.com', '09987651234', '$2y$10$fLkdIWuzXZOQCk0/qinHS.lKEsDHyjjJbtH8YjihfskPE2VFAP0D6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cookbook_recipe`
--
ALTER TABLE `cookbook_recipe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cooking_tips`
--
ALTER TABLE `cooking_tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cuisine_types`
--
ALTER TABLE `cuisine_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `dietary_preferences`
--
ALTER TABLE `dietary_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `difficulty_levels`
--
ALTER TABLE `difficulty_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe_dietary_preferences`
--
ALTER TABLE `recipe_dietary_preferences`
  ADD PRIMARY KEY (`recipe_id`,`dietary_preference_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `cookbook_recipe`
--
ALTER TABLE `cookbook_recipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cooking_tips`
--
ALTER TABLE `cooking_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cuisine_types`
--
ALTER TABLE `cuisine_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `dietary_preferences`
--
ALTER TABLE `dietary_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `difficulty_levels`
--
ALTER TABLE `difficulty_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
