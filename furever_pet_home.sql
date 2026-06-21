-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2026 at 03:44 PM
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
-- Database: `furever_pet_home`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` varchar(10) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `NumberPhone` varchar(15) NOT NULL,
  `Password` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `FirstName`, `LastName`, `Email`, `NumberPhone`, `Password`) VALUES
('ADM01', 'Super', 'Admin', 'super@furever.com', '0120001111', 'adminpass'),
('ADM02', 'Farhan', 'Hakim', 'farhan@furever.com', '0120002222', 'adminpass'),
('ADM03', 'Linda', 'Chew', 'linda@furever.com', '0120003333', 'adminpass'),
('ADM04', 'Muthu', 'Arasan', 'muthu@furever.com', '0120004444', 'adminpass'),
('ADM05', 'Syafiq', 'Ibrahim', 'syafiq@furever.com', '0120005555', 'adminpass'),
('ADM06', 'Emily', 'Lim', 'emily@furever.com', '0120006666', 'adminpass'),
('ADM07', 'Nadia', 'Azman', 'nadia@furever.com', '0120007777', 'adminpass'),
('ADM08', 'Albert', 'Teoh', 'albert@furever.com', '0120008888', 'adminpass'),
('ADM09', 'Aizat', 'Ariffin', 'aizat@furever.com', '0120009999', 'adminpass'),
('ADM10', 'Shanti', 'Devi', 'shanti@furever.com', '0120001010', 'adminpass'),
('ADM11', 'Hafiz', 'Roslan', 'hafiz@furever.com', '0120002020', 'adminpass'),
('ADM12', 'Chloe', 'Ng', 'chloe@furever.com', '0120003030', 'adminpass'),
('ADM13', 'Zidan', 'Fikri', 'zidan@furever.com', '0120004040', 'adminpass'),
('ADM14', 'Janice', 'Tan', 'janice@furever.com', '0120005050', 'adminpass'),
('ADM15', 'Rahmat', 'Said', 'rahmat@furever.com', '0120006060', 'adminpass'),
('ADM16', 'ain', 'ahmad', 'ain@furever.com', '01123456789', 'adminpass'),
('ADM17', 'Johan', 'Razak', 'Johan@furever.com', '0134567289', 'adminpass');

-- --------------------------------------------------------

--
-- Table structure for table `adopt_application`
--

CREATE TABLE `adopt_application` (
  `AdoptionID` varchar(10) NOT NULL,
  `ResidentID` varchar(5) NOT NULL,
  `PetID` varchar(10) NOT NULL,
  `Status` enum('Submit','Pending','Approved','Rejected') NOT NULL,
  `Reason` varchar(250) DEFAULT NULL,
  `RequestDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adopt_application`
--

INSERT INTO `adopt_application` (`AdoptionID`, `ResidentID`, `PetID`, `Status`, `Reason`, `RequestDate`) VALUES
('ADOP01', 'R0001', 'PET01', 'Approved', 'I want to register 2 cats this Saturday boss.', '2026-05-10 10:00:00'),
('ADOP02', 'R0002', 'PET02', 'Approved', 'Alhamdulillah, best program in Klang area!', '2026-05-11 11:30:00'),
('ADOP03', 'R0003', 'PET03', 'Pending', 'What time does the adoption counter open?', '2026-05-20 09:15:00'),
('ADOP04', 'R0004', 'PET04', 'Rejected', 'It opens at 10 AM sir Ravin.', '2026-05-12 14:00:00'),
('ADOP05', 'R0005', 'PET05', 'Submit', 'I can bring tools and paint myself later.', '2026-05-23 16:45:00'),
('ADOP06', 'R0006', 'PET06', 'Approved', 'Very useful, Bayu Perdana residents must attend.', '2026-05-14 08:00:00'),
('ADOP07', 'R0007', 'PET07', 'Pending', 'I bought 2 bags of food, where should I send it?', '2026-05-22 12:00:00'),
('ADOP08', 'R0008', 'PET08', 'Approved', 'How to give my sick pet medicine.', '2026-05-15 15:20:00'),
('ADOP09', 'R0009', 'PET09', 'Rejected', 'It is soo hard to give attention to my dog, what should I do?.', '2026-05-16 10:10:00'),
('ADOP10', 'R0010', 'PET10', 'Approved', 'You can send it directly to Bukit Tinggi shelter address', '2026-05-17 11:00:00'),
('ADOP11', 'R0011', 'PET11', 'Submit', 'So many cute cats, hard to choose.', '2026-05-23 13:00:00'),
('ADOP12', 'R0012', 'PET12', 'Pending', 'Congrats Bukit Raja association for club launch!', '2026-05-21 17:30:00'),
('ADOP13', 'R0013', 'PET13', 'Submit', 'The vinegar spray tip really works, tried at home.', '2026-05-23 14:15:00'),
('ADOP14', 'R0014', 'PET14', 'Approved', 'I have leftover wire mesh at home, I will donate it.', '2026-05-19 16:00:00'),
('ADOP15', 'R0015', 'PET15', 'Pending', 'This cat looks familiar, maybe neighbor\'s cat.', '2026-05-22 18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `CommentID` varchar(10) NOT NULL,
  `ResidentID` varchar(5) NOT NULL,
  `BoardID` varchar(10) NOT NULL,
  `Content` text NOT NULL,
  `Date` date NOT NULL,
  `ReplyID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`CommentID`, `ResidentID`, `BoardID`, `Content`, `Date`, `ReplyID`) VALUES
('COM01', 'R0001', 'BRD01', 'I want to register 2 cats this Saturday boss.', '2026-05-01', NULL),
('COM02', 'R0002', 'BRD01', 'Alhamdulillah, best program in Klang area!', '2026-05-01', NULL),
('COM03', 'R0003', 'BRD02', 'What time does the adoption counter open?', '2026-05-03', NULL),
('COM04', 'R0006', 'BRD02', 'It opens at 10 AM sir Ravin.', '2026-05-03', 'COM03'),
('COM05', 'R0004', 'BRD03', 'I can bring tools and paint myself later.', '2026-05-05', NULL),
('COM06', 'R0005', 'BRD03', 'Thank you Pandamaran residents for helping.', '2026-05-06', 'COM05'),
('COM07', 'R0007', 'BRD04', 'I bought 2 bags of food, where should I send it?', '2026-05-10', NULL),
('COM08', 'R0008', 'BRD04', 'You can send it directly to Bukit Tinggi shelter address.', '2026-05-10', 'COM07'),
('COM09', 'R0009', 'BRD05', 'Very useful, Bayu Perdana residents must attend.', '2026-05-12', NULL),
('COM10', 'R0010', 'BRD06', 'I transferred RM50 for Rocky surgery fund. Get well soon.', '2026-05-14', NULL),
('COM11', 'R0011', 'BRD07', 'So many cute cats, hard to choose.', '2026-05-15', NULL),
('COM12', 'R0012', 'BRD08', 'Congrats Bukit Raja association for club launch!', '2026-05-16', NULL),
('COM13', 'R0013', 'BRD09', 'The vinegar spray tip really works, tried at home.', '2026-05-17', NULL),
('COM14', 'R0014', 'BRD10', 'I have leftover wire mesh at home, I will donate it.', '2026-05-18', NULL),
('COM15', 'R0015', 'BRD11', 'This cat looks familiar, maybe neighbor\'s cat.', '2026-05-19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `community_board`
--

CREATE TABLE `community_board` (
  `BoardID` varchar(10) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Content` text DEFAULT NULL,
  `Photo` varchar(100) DEFAULT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_board`
--

INSERT INTO `community_board` (`BoardID`, `OrgID`, `Title`, `Content`, `Photo`, `Date`) VALUES
('BRD01', 'ORG01', 'Spray & Neuter Campaign for Cats & Dogs Klang 2026', 'Come neuter your pets at subsidized price RM50 this Saturday!', 'camp01.jpg', '2026-05-01 09:00:00'),
('BRD02', 'ORG02', 'Adopt Don\'t Stop Awareness Day Botanic', 'Join us at Central Park Botanic for pet adoption session.', 'camp02.png', '2026-05-03 10:00:00'),
('BRD03', 'ORG05', 'Pandamaran Shelter Cleanup Volunteer Day', 'Volunteers needed to paint and clean dog cages.', 'camp03.png', '2026-05-05 08:30:00'),
('BRD04', 'ORG07', 'Pet Food Donation Drive Needed', 'Our Bukit Tinggi shelter food stock only lasts 3 days.', 'camp04.png', '2026-05-10 14:00:00'),
('BRD05', 'ORG12', 'Stray Management Talk for Bayu Perdana Residents', 'Learn stray control using TNR method.', 'camp05.png', '2026-05-12 15:00:00'),
('BRD06', 'ORG03', 'Rocky Medical Fund Assistance', 'Dog Rocky needs RM800 for surgery.', 'camp06.png', '2026-05-14 11:00:00'),
('BRD07', 'ORG09', 'Teluk Pulai Adoption Day Session', 'Bring home a cute pet today for free.', 'camp07.png', '2026-05-15 09:00:00'),
('BRD08', 'ORG15', 'Bukit Raja Animal Community Club Launch', 'Official pet lovers community platform.', 'camp08.png', '2026-05-16 16:00:00'),
('BRD09', 'ORG13', 'Tips for Managing Cats Urinating Indoors', 'Informational article from admin.', 'camp09.png', '2026-05-17 20:00:00'),
('BRD10', 'ORG04', 'Meru Wire Fence Donation Drive', 'Looking for donated wire fencing materials.', 'camp10.png', '2026-05-18 10:30:00'),
('BRD11', 'ORG06', 'Missing Cat Found in Andalas', 'Red collar cat found near mosque.', 'camp11.jpg', '2026-05-19 12:00:00'),
('BRD12', 'ORG08', 'Street Feeding Volunteers Needed', 'Weekend volunteers needed for stray feeding.', 'camp12.jpg', '2026-05-20 13:00:00'),
('BRD13', 'ORG11', 'Toxic Waste Warning Affecting Strays', 'Cases of poisoned strays reported.', 'camp13.jpg', '2026-05-21 08:00:00'),
('BRD14', 'ORG10', 'Animal Awareness School Talk', 'Education program for students.', 'camp14.jpg', '2026-05-22 09:30:00'),
('BRD15', 'ORG14', 'RM1 Weekly Donation Campaign', 'Emergency food fund for street cats.', 'camp15.jpg', '2026-05-23 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `FaqID` int(10) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `Question` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`FaqID`, `OrgID`, `Question`, `Description`) VALUES
(1, 'ORG01', 'What are the basic requirements for pet adoption?', 'Applicant must be 18 years and above, have stable income, and obtain family/landlord permission.'),
(2, 'ORG01', 'Is there any adoption fee?', 'No selling fee, but voluntary donations are encouraged for previous neutering medical costs.'),
(3, 'ORG02', 'Can I return the pet if it is not compatible?', 'We recommend a 2-week trial period. If unsuccessful, the pet may be returned for its safety.'),
(4, 'ORG03', 'How to report animal emergencies outside office hours?', 'Please submit a report in the system using the \"Emergency\" button or contact the NGO hotline.'),
(5, 'ORG05', 'Are adopted dogs vaccinated?', 'Yes, all adult dogs available for adoption have received at least the first vaccine dose.'),
(6, 'ORG06', 'Are local breed cats easy to care for?', 'Very easy! Local cats have strong immunity and lower genetic disease risks.'),
(7, 'ORG07', 'Can I visit the animals before applying?', 'Yes, please make an appointment at least 1 day before visiting.'),
(8, 'ORG12', 'What is the TNR method?', 'TNR stands for Trap-Neuter-Return to control stray population growth.'),
(9, 'ORG15', 'What is the average monthly cost of cat food?', 'Around RM50 to RM100 depending on dry or wet food brand.'),
(10, 'ORG04', 'Can flat residents keep dogs?', 'Most strata housing regulations do not allow large dogs for public safety.'),
(11, 'ORG09', 'Can 1-month-old kittens be kept?', 'Kittens should ideally stay with their mother until 2 months old before adoption.'),
(12, 'ORG11', 'How to become a weekend volunteer?', 'Open Help Center menu and fill out the volunteer registration form for Klang zone.'),
(13, 'ORG13', 'Will stray cats be euthanized?', 'No, Furever Pet Home follows a No-Kill Policy unless the animal is critically ill with no hope.'),
(14, 'ORG08', 'What foods are prohibited for dogs?', 'Chocolate, grapes, onions, and sharp chicken bones are dangerous for dogs.'),
(15, 'ORG14', 'How to mark a report as completed?', 'Only the managing organization can update report status after action is taken.');

-- --------------------------------------------------------

--
-- Table structure for table `guidelines`
--

CREATE TABLE `guidelines` (
  `GuidelineID` int(10) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `PetType` enum('Dog','Cat') NOT NULL,
  `Description` text DEFAULT NULL,
  `Budget` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guidelines`
--

INSERT INTO `guidelines` (`GuidelineID`, `OrgID`, `Title`, `PetType`, `Description`, `Budget`) VALUES
(1, 'ORG01', 'Basic Cat Toilet Training Guide', 'Cat', 'Place litter box in a quiet corner, introduce kitten after meals.', 30.00),
(2, 'ORG01', 'Mandatory Dog Vaccination Schedule', 'Dog', 'Dose 1 (6-8 weeks), Dose 2 (10-12 weeks), Dose 3 (14-16 weeks), Booster annually.', 150.00),
(3, 'ORG02', 'How to Bathe Fearful Cats', 'Cat', 'Use warm water, gently wipe with a wet towel instead of direct showering.', 15.00),
(4, 'ORG05', 'Leash Walking Training for Mixed Breed Dogs', 'Dog', 'Start indoors before progressing to public parks.', 25.00),
(5, 'ORG06', 'Balanced Diet for Neutered Cats', 'Cat', 'Neutered cats gain weight easily. Reduce high-carbohydrate food.', 70.00),
(6, 'ORG07', 'Signs Your Dog Has Fleas', 'Dog', 'Frequent scratching, red skin, and black flea spots on fur.', 45.00),
(7, 'ORG09', 'Preparing a New Cat at Home', 'Cat', 'Prepare a separate isolation room for 3 days to reduce stress.', 100.00),
(8, 'ORG15', 'Dog Barking Control Training', 'Dog', 'Use positive reinforcement (treat rewards) when dog stays calm.', 20.00),
(9, 'ORG04', 'How to Clean Cat Teeth Tartar', 'Cat', 'Use pet toothbrush or fiber chew toys with catnip.', 12.00),
(10, 'ORG12', 'First Aid for Injured Stray Dogs', 'Dog', 'Clean wound with saline solution and apply iodine ointment.', 35.00),
(11, 'ORG13', 'How to Stop Cats Scratching Sofas', 'Cat', 'Provide scratching posts near furniture.', 40.00),
(12, 'ORG08', 'Importance of Evening Dog Walks', 'Dog', 'Helps release energy and prevents destructive behavior.', 0.00),
(13, 'ORG10', 'Cat Fur Care in Hot Weather', 'Cat', 'Brush daily to prevent hairball formation.', 18.00),
(14, 'ORG14', 'Understanding Canine Parvovirus', 'Dog', 'Highly contagious fatal virus causing diarrhea and vomiting.', 300.00),
(15, 'ORG11', 'How to Remove Cat Urine Smell from Floor', 'Cat', 'Use enzymatic cleaner, avoid ammonia bleach which attracts re-marking.', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `InboxID` varchar(10) NOT NULL,
  `ReportID` varchar(10) DEFAULT NULL,
  `AdoptionID` varchar(10) DEFAULT NULL,
  `Title` varchar(40) DEFAULT NULL,
  `Message` text DEFAULT NULL,
  `DateTime` datetime DEFAULT current_timestamp(),
  `Type` enum('Pet Adoption Application','Pet Report') DEFAULT NULL,
  `Status` enum('Approve','Reject','Pending','In Progress','Resolve') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inbox`
--

INSERT INTO `inbox` (`InboxID`, `ReportID`, `AdoptionID`, `Title`, `Message`, `DateTime`, `Type`, `Status`) VALUES
('INB01', NULL, 'ADOP01', 'Application Approved for Rocky', 'Congratulations! Your application to adopt Rocky has been approved.', '2026-05-12 09:00:00', 'Pet Adoption Application', 'Approve'),
('INB02', 'REP01', NULL, 'Bukit Tinggi Dog Case Resolved', 'Our rescue team has taken the dog for neutering treatment.', '2026-05-11 15:00:00', 'Pet Report', 'Resolve'),
('INB03', NULL, 'ADOP02', 'Application Approved for Luna', 'Please visit Andalas Pet Haven shelter for Luna handover session.', '2026-05-13 10:30:00', 'Pet Adoption Application', 'Approve'),
('INB04', 'REP02', NULL, 'Cat on Tree Status', 'The cat was successfully rescued with the help of the fire department this afternoon.', '2026-05-04 17:00:00', 'Pet Report', 'Resolve'),
('INB05', NULL, 'ADOP04', 'Application Rejected for Bella', 'We regret to inform you that your application was rejected due to time constraints.', '2026-05-13 11:00:00', 'Pet Adoption Application', 'Reject'),
('INB06', 'REP03', NULL, 'Pandamaran Stray Dog Report', 'Report received. Monitoring team is on the way to the location.', '2026-05-05 21:00:00', 'Pet Report', 'In Progress'),
('INB07', NULL, 'ADOP06', 'Application Approved for Max', 'Application approved. Please bring a dog collar during collection day.', '2026-05-16 14:00:00', 'Pet Adoption Application', 'Approve'),
('INB08', NULL, 'ADOP03', 'Simba Cat Application Under Review', 'Official message: Your application is currently being reviewed by the Botanic committee.', '2026-05-21 09:00:00', 'Pet Adoption Application', 'Pending'),
('INB09', 'REP08', NULL, 'Hit Dog Successfully Rescued', 'The injured dog has been safely sent to Klang veterinary clinic.', '2026-05-15 16:00:00', 'Pet Report', 'Resolve'),
('INB10', NULL, 'ADOP08', 'Application Approved for Daisy', 'Congratulations, your adoption of Daisy has been approved.', '2026-05-17 10:00:00', 'Pet Adoption Application', 'Approve'),
('INB11', 'REP07', NULL, 'Teluk Pulai Cat Family Case', 'The wet cats have been relocated to a dry temporary shelter center.', '2026-05-16 11:30:00', 'Pet Report', 'Resolve'),
('INB12', NULL, 'ADOP10', 'Application Approved for Cookie', 'Application approved. You may collect Cookie this weekend.', '2026-05-19 12:00:00', 'Pet Adoption Application', 'Approve'),
('INB13', 'REP14', NULL, 'Malnourished Dog Case Resolved', 'Mother dog and puppies have been safely moved to a shelter.', '2026-05-18 13:00:00', 'Pet Report', 'Resolve'),
('INB14', NULL, 'ADOP14', 'Application Approved for Comel', 'Your application has been approved by Kampung Delek Cat Shelter.', '2026-05-21 15:30:00', 'Pet Adoption Application', 'Approve'),
('INB15', 'REP17', NULL, 'Cat Neck Trap Case Resolved', 'The wire around the cat\'s neck has been safely removed.', '2026-05-23 18:30:00', 'Pet Report', 'Resolve');

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `OrgID` varchar(10) NOT NULL,
  `OrgName` varchar(100) NOT NULL,
  `NumberPhone` varchar(15) NOT NULL,
  `OrgAddress` varchar(255) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(12) NOT NULL,
  `Description` text NOT NULL,
  `Status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`OrgID`, `OrgName`, `NumberPhone`, `OrgAddress`, `Email`, `Password`, `Description`, `Status`) VALUES
('ORG01', 'Klang Stray Rescue', '0333445566', 'No 2, Jalan Istana, 41000 Klang', 'contact@klangstray.ngo.com', 'orgpass', '', 0),
('ORG02', 'Bandar Botanic Animal Shelter', '0333221122', 'No 14, Jalan Jasmin, Bandar Botanic, 41200 Klang', 'botanic.pets@ngo.com', 'orgpass', '', 1),
('ORG03', 'Paws and Claws Klang NGO', '0129994444', 'Lot 451, Jalan Telok Gong, 42000 Klang', 'pawsclaws@ngo.com', 'orgpass', '', 0),
('ORG04', 'Klang Valley Cat Rescue', '0165551212', 'No 8, Jalan Meru Utama, 41050 Klang', 'kvcat@ngo.com', 'orgpass', '', 1),
('ORG05', 'Pandamaran Dog Hope Sanctuary', '0331687777', 'Kawasan Perindustrian Pandamaran, 42000 Klang', 'pandamarandogs@ngo.com', 'orgpass', '', 1),
('ORG06', 'Andalas Pet Haven', '0137778888', 'No 22, Jalan Taman Sri Andalas, 41200 Klang', 'andalashaven@ngo.com', 'orgpass', '', 1),
('ORG07', 'Bukit Tinggi Furry Friends', '0176663333', 'Lorong Batu Nilam 21A, Bandar Bukit Tinggi, 41200 Klang', 'btfurry@ngo.com', 'orgpass', '', 1),
('ORG08', 'Kapar Stray Angels', '0112345678', 'Batu 4, Jalan Kapar, 42200 Klang', 'kaparstrays@ngo.com', 'orgpass', '', 1),
('ORG09', 'Teluk Pulai Kitty Shelter', '0142223334', 'No 5, Lorong Sg. Udang, Teluk Pulai, 41100 Klang', 'tp.kitty@ngo.com', 'orgpass', '', 1),
('ORG10', 'Meru Animal Welfare', '0333921111', 'Jalan Kassim, Pekan Meru, 41050 Klang', 'meru.welfare@ngo.com', 'orgpass', '', 1),
('ORG11', 'Port Klang Stray Care', '0194445556', 'Jalan Pelabuhan Utara, 42000 Pelabuhan Klang', 'portcare@ngo.com', 'orgpass', '', 1),
('ORG12', 'Taman Bayu Perdana Pet Shield', '0127771112', 'Jalan Prima, Taman Bayu Perdana, 41200 Klang', 'bayushield@ngo.com', 'orgpass', '', 1),
('ORG13', 'Kampung Delek Cat Sanctuary', '0138889991', 'Jalan Haji Omar, Kampung Delek, 41050 Klang', 'kdelek.cat@ngo.com', 'orgpass', '', 1),
('ORG14', 'Raja Muda Musa Furry Rescue', '0168882223', 'Lorong Berantas, Off Jalan Raja Muda Musa, 41100 Klang', 'rmm.rescue@ngo.com', 'orgpass', '', 1),
('ORG15', 'Bukit Raja Hope Society', '0172224445', 'Jalan Singgahsana, Bandar Bukit Raja, 41050 Klang', 'braja.hope@ngo.com', 'orgpass', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE `pet` (
  `PetID` varchar(10) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `PetType` enum('Dog','Cat') NOT NULL,
  `Breed` varchar(100) NOT NULL,
  `Age` int(11) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Neutered` tinyint(1) NOT NULL,
  `Allergies` varchar(255) DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `PetName` varchar(100) NOT NULL,
  `IsAvailable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet`
--

INSERT INTO `pet` (`PetID`, `OrgID`, `PetType`, `Breed`, `Age`, `Location`, `Neutered`, `Allergies`, `Photo`, `Gender`, `PetName`, `IsAvailable`) VALUES
('PET01', 'ORG01', 'Dog', 'Local Mixed', 26, 'Pusat Bandar Klang', 1, 'None', '1781968283_RockyORG1.jpg', 'Male', 'Rocky', 1),
('PET02', 'ORG01', 'Cat', 'Domestic Short Hair', 28, 'Taman Sri Andalas', 1, 'Sensitive Stomach', '1781968304_LunaORG1.jpg', 'Female', 'Luna', 1),
('PET03', 'ORG02', 'Cat', 'Persian Mix', 3, 'Bandar Botanic', 1, 'None', '1781967640_SimbaORG2.jpg', 'Male', 'Simba', 1),
('PET04', 'ORG03', 'Dog', 'Stray Mongrel', 3, 'Telok Gong', 0, 'Skin Allergy', '1781968575_BellaORG3.webp', 'Female', 'Bella', 1),
('PET05', 'ORG04', 'Cat', 'Siamese Mix', 30, 'Taman Meru', 1, 'None', '1781969146_MiloORG4.webp', 'Male', 'Milo', 1),
('PET06', 'ORG05', 'Dog', 'German Shepherd Mix', 44, 'Pandamaran', 1, 'None', '1781970403_MaxORG5.jpg', 'Male', 'Max', 1),
('PET07', 'ORG06', 'Cat', 'Tabby Cat', 1, 'Taman Sri Andalas', 1, 'Chicken Allergy', '1781970485_OyenORG6.webp', 'Female', 'Oyen', 1),
('PET08', 'ORG07', 'Dog', 'Terrier Mix', 14, 'Bandar Bukit Tinggi', 1, 'None', '1781970552_DaisyORG7.jpg', 'Female', 'Daisy', 1),
('PET09', 'ORG08', 'Dog', 'Local Breed', 17, 'Pekan Kapar', 0, 'None', '1781970755_BrunoORG8.jpg', 'Male', 'Bruno', 1),
('PET10', 'ORG09', 'Cat', 'Calico Cat', 4, 'Teluk Pulai', 1, 'None', '1781970856_CookieORG9.jpg', 'Female', 'Cookie', 1),
('PET11', 'ORG10', 'Cat', 'Mainecoon Mix', 2, 'Pekan Meru', 1, 'Flies Bites Sensitive', '1781970991_TigerORG10.jpg', 'Male', 'Tigger', 1),
('PET12', 'ORG11', 'Dog', 'Hound Mix', 27, 'Pelabuhan Klang', 1, 'None', '1781971387_LuckyORG11 (2).jpg', 'Female', 'Lucky', 1),
('PET13', 'ORG12', 'Cat', 'Tuxedo Cat', 21, 'Taman Bayu Perdana', 1, 'None', '1781971492_FelixORG12.webp', 'Male', 'Felix', 1),
('PET14', 'ORG13', 'Cat', 'Kampung Cat', 35, 'Kampung Delek', 0, 'None', '1781971668_ComelORG13.webp', 'Female', 'Comel', 1),
('PET15', 'ORG15', 'Dog', 'Retriever Mix', 25, 'Bandar Bukit Raja', 1, 'None', '1781972201_BuddyORG15.jpg', 'Male', 'Buddy', 1),
('PET16', 'ORG05', 'Dog', 'Dog', 32, 'Kawasan Perindustrian Pandamaran, 42000 Klang', 1, 'Allergy to human male', '1781956237_Lokey.jpg', 'Male', 'Lokey', 1),
('PET17', 'ORG02', 'Cat', 'Maine Coon', 26, 'Bandar Botanic', 1, 'None', '1781967661_LucyORG2.jpg', 'Female', 'Lucy', 1),
('PET18', 'ORG02', 'Cat', 'Maine Coon', 9, 'Bandar Botanic', 1, 'None', '1781967683_CymbaORG2.jpg', 'Male', 'Cymba', 1),
('PET19', 'ORG02', 'Dog', 'Golden Retriever', 24, 'Kawasan Perindustrian Pandamaran, 42000 Klang', 1, 'Allergy to butterflies', '1781967700_RockyORG2.jpg', 'Male', 'Rocky', 1),
('PET20', 'ORG01', 'Cat', 'Local Mixed', 22, 'Bandar Botanic', 1, 'Allergy to cucumber', '1781968351_MinieORG1.jpg', 'Male', 'Minie', 1),
('PET21', 'ORG03', 'Dog', 'Maltipoos', 5, 'Bandar Botanic', 1, 'None', '1781968709_FinnORG3.png', 'Male', 'Finn', 1),
('PET22', 'ORG03', 'Dog', 'Maltipoos', 5, 'Bandar Botanic', 1, 'None', '1781968735_FionaORG3.jpg', 'Female', 'Fiona', 1),
('PET23', 'ORG03', 'Dog', 'Pug', 3, 'Bandar Botanic', 0, 'None', '1781968874_SushiORG3.jpg', 'Male', 'Sushi', 1),
('PET24', 'ORG04', 'Cat', 'Siamese Mix', 5, 'Jalan Meru Utama', 1, 'Need flees oinment', '1781969705_KowORG4.jpg', 'Male', 'Kow', 1),
('PET25', 'ORG04', 'Cat', 'Siamese Mix', 15, 'Taman Meru', 1, 'None', '1781970283_FirawnORG4.jpg', 'Male', 'Firawn', 1),
('PET26', 'ORG07', 'Dog', 'Terrier Mix', 20, 'Bukit Tinggi', 1, 'None', '1781970627_ChibiORG7.webp', 'Female', 'Chibi', 1),
('PET27', 'ORG10', 'Cat', 'British Short Hair', 23, 'Pekan Meru', 0, 'None', '1781971146_AbuORG10.jpg', 'Male', 'Abu', 1),
('PET28', 'ORG11', 'Dog', 'Sphered', 30, 'Pelabuhan Klang', 0, 'None', '1781971325_HopeORG11.jpg', 'Male', 'Hope', 1),
('PET29', 'ORG14', 'Cat', 'Kampung Cat', 17, 'Jalan Raja Muda Musa', 0, 'None', '1781971906_SweepyORG14.webp', 'Female', 'Sweepy', 1),
('PET30', 'ORG14', 'Dog', 'Sphered', 17, 'Jalan Raja Muda Musa', 0, 'None', '1781971948_SunnyORG14.webp', 'Male', 'Sunny', 1),
('PET31', 'ORG15', 'Dog', 'Bulldog', 30, 'Bandar Bukti Raja', 1, 'Need flees oinment', '1781972059_GoofyORG15.webp', 'Male', 'Goofy', 1),
('PET32', 'ORG15', 'Cat', 'British Short Hair', 23, 'Bandar Bukti Raja', 0, 'None', '1781972122_GrumpyORG15.jpg', 'Female', 'Grumpy', 1);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `ReportID` varchar(10) NOT NULL,
  `ResidentID` varchar(5) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `PetName` varchar(100) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Status` enum('Submit','Pending','Resolved') NOT NULL,
  `Photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`ReportID`, `ResidentID`, `OrgID`, `PetName`, `Location`, `Description`, `Status`, `Photo`) VALUES
('REP01', 'R0001', 'ORG01', 'Skinny Black Dog', 'In front of Mamak Shop Bukit Tinggi', 'Dog wandering with a limp leg, looks hungry.', 'Resolved', 'rep01.jpg'),
('REP02', 'R0002', 'ORG02', 'Tree Cat', 'Sri Andalas Playground', 'Cat stuck on a tall tree since yesterday.', 'Resolved', 'rep02.jpg'),
('REP03', 'R0003', 'ORG05', 'Group of Stray Dogs', 'Pandamaran Bus Depot Area', 'A group of dogs barking at night, worrying residents.', 'Pending', 'rep03.jpg'),
('REP04', 'R0005', 'ORG04', 'Cat with Eye Infection', 'Meru Town Night Market', 'Stray kitten with severe eye infection near trash bin.', 'Submit', 'rep04.jpg'),
('REP07', 'R0007', 'ORG09', 'Mother Cat & Kittens', 'Under Teluk Pulai Bridge', 'A stray cat family drenched from heavy rain.', 'Resolved', 'rep05.jpg'),
('REP08', 'R0008', 'ORG03', 'Hit Dog', 'Persiaran Raja Muda Musa Road', 'Dog hit by a car in a hit-and-run, broken thigh near divider.', 'Resolved', 'rep06.jpg'),
('REP09', 'R0009', 'ORG13', 'Lost Pedigree Cat', 'Back Alley Jetty Sg Udang', 'Persian pedigree cat with dirty fur, believed abandoned by owner.', 'Pending', 'rep07.jpg'),
('REP10', 'R0011', 'ORG08', 'Mange Dog', 'Drainside Area Jalan Kapar', 'Dog with chronic mange, no fur, urgently needs NGO help.', 'Submit', 'rep08.jpg'),
('REP11', 'R0012', 'ORG15', 'Cat with Ear Injury', 'Commercial Lot Bandar Bukit Raja', 'Cat with bleeding ear attacked by another animal.', 'Submit', 'rep09.jpg'),
('REP12', 'R0004', 'ORG11', 'Dog Stuck in Fence', 'Port Klang Free Zone (PKFZ)', 'Dog stuck between factory wire fence.', 'Resolved', 'rep10.jpg'),
('REP13', 'R0006', 'ORG12', 'Cat with Tail Injury', 'Bayu Perdana Shop Area', 'Cat’s tail trapped in abandoned shop door.', 'Pending', 'rep11.jpg'),
('REP14', 'R0010', 'ORG07', 'Starving Skinny Dog', 'Batu Nilam Lane Football Field', 'Female dog just gave birth, extremely skinny.', 'Resolved', 'rep12.jpg'),
('REP15', 'R0013', 'ORG10', 'Flat Kittens', 'Block C Flat Meru Staircase', '3 kittens left inside a cardboard box.', 'Submit', 'rep13.jpg'),
('REP16', 'R0014', 'ORG15', 'Aggressive Dog', 'Bukit Raja Roundabout', 'Dog chasing passing motorcyclists.', 'Pending', 'rep14.jpg'),
('REP17', 'R0015', 'ORG14', 'Cat with Neck Wound', 'Behind Kampung Kuantan Restaurant', 'Cat’s neck entangled in rusty wire.', 'Resolved', 'rep15.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `resident`
--

CREATE TABLE `resident` (
  `ResidentID` varchar(5) NOT NULL,
  `FirstName` varchar(20) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `NumberPhone` varchar(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(12) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Status` tinyint(1) DEFAULT 1,
  `Salary` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident`
--

INSERT INTO `resident` (`ResidentID`, `FirstName`, `LastName`, `NumberPhone`, `Email`, `Password`, `Address`, `Status`, `Salary`) VALUES
('R0001', 'Ahmad', 'Faizal', '0123456789', 'ahmad.faizal@gmail.com', 'pass123', 'No 12, Jalan Batu Unjur 1, Taman Bayu Perdana, 41200 Klang', 0, 0.00),
('R0002', 'Siti', 'Aishah', '0139876543', 'siti.aishah@gmail.com', 'pass123', 'No 45, Jalan Sri Damai, Taman Sri Andalas, 41200 Klang', 0, 0.00),
('R0003', 'Ravin', 'Kumar', '0171112223', 'ravin.kumar@gmail.com', 'pass123', 'B-3-5, Flat Bukit Tinggi, Bandar Bukit Tinggi, 41200 Klang', 0, 0.00),
('R0004', 'Mei', 'Ling', '0164445556', 'mei.ling@gmail.com', 'pass123', 'No 88, Jalan Kim Chuan, Pandamaran, 42000 Klang', 0, 0.00),
('R0005', 'Muhammad', 'Amir', '0112223334', 'amir.res@gmail.com', 'pass123', 'No 7, Jalan Haji Sirat, Kampung Delek Kanan, 41050 Klang', 0, 0.00),
('R0006', 'Chong', 'Wei', '0198887776', 'chong.wei@gmail.com', 'pass123', 'No 15, Jalan Kasuarina 4, Bandar Botanic, 41200 Klang', 0, 0.00),
('R0007', 'Fatima', 'Zahra', '0145556667', 'fatima.z@gmail.com', 'pass123', 'No 23, Jalan Teluk Pulai, 41100 Klang', 0, 0.00),
('R0008', 'Suresh', 'Maniam', '0129998887', 'suresh.m@gmail.com', 'pass123', 'No 4, Jalan Melawis, Taman Melawis, 41100 Klang', 0, 0.00),
('R0009', 'Nurul', 'Huda', '0134443332', 'nurul.huda@gmail.com', 'pass123', 'No 19, Jalan Sungai Udang, Kampung Sungai Udang, 41000 Klang', 0, 0.00),
('R0010', 'Kevin', 'Tan', '0187776665', 'kevin.tan@gmail.com', 'pass123', 'No 102, Jalan Raja Muda Musa, 41100 Klang', 0, 0.00),
('R0011', 'Zulkifli', 'Rahman', '0115554443', 'zul.rahman@gmail.com', 'pass123', 'No 33, Jalan Meru, Taman Meru, 41050 Klang', 0, 0.00),
('R0012', 'Grace', 'Anand', '0162229991', 'grace.a@gmail.com', 'pass123', 'No 56, Jalan Langat, Bandar Puteri, 41200 Klang', 0, 0.00),
('R0013', 'Asraf', 'Ghani', '0173338882', 'asraf.g@gmail.com', 'pass123', 'No 14, Jalan Kapar, Pekan Kapar, 42200 Klang', 0, 0.00),
('R0014', 'Michelle', 'Wong', '0124441119', 'michelle.w@gmail.com', 'pass123', 'No 8, Jalan Zapin 2, Bandar Bukit Raja, 41050 Klang', 0, 0.00),
('R0015', 'Khairul', 'Anwar', '0196662221', 'khairul.a@gmail.com', 'pass123', 'No 51, Jalan Kampung Kuantan, 41050 Klang', 0, 0.00),
--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `adopt_application`
--
ALTER TABLE `adopt_application`
  ADD PRIMARY KEY (`AdoptionID`),
  ADD KEY `ResidentID` (`ResidentID`),
  ADD KEY `PetID` (`PetID`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `ResidentID` (`ResidentID`),
  ADD KEY `BoardID` (`BoardID`),
  ADD KEY `ReplyID` (`ReplyID`) USING BTREE;

--
-- Indexes for table `community_board`
--
ALTER TABLE `community_board`
  ADD PRIMARY KEY (`BoardID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`FaqID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `guidelines`
--
ALTER TABLE `guidelines`
  ADD PRIMARY KEY (`GuidelineID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`InboxID`),
  ADD KEY `ReportID` (`ReportID`),
  ADD KEY `AdoptionID` (`AdoptionID`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`OrgID`);

--
-- Indexes for table `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`PetID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `ResidentID` (`ResidentID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `resident`
--
ALTER TABLE `resident`
  ADD PRIMARY KEY (`ResidentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `FaqID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `guidelines`
--
ALTER TABLE `guidelines`
  MODIFY `GuidelineID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adopt_application`
--
ALTER TABLE `adopt_application`
  ADD CONSTRAINT `adopt_application_ibfk_1` FOREIGN KEY (`ResidentID`) REFERENCES `resident` (`ResidentID`),
  ADD CONSTRAINT `adopt_application_ibfk_2` FOREIGN KEY (`PetID`) REFERENCES `pet` (`PetID`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`ResidentID`) REFERENCES `resident` (`ResidentID`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`BoardID`) REFERENCES `community_board` (`BoardID`),
  ADD CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`ReplyID`) REFERENCES `comment` (`CommentID`);

--
-- Constraints for table `community_board`
--
ALTER TABLE `community_board`
  ADD CONSTRAINT `community_board_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `guidelines`
--
ALTER TABLE `guidelines`
  ADD CONSTRAINT `guidelines_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `inbox`
--
ALTER TABLE `inbox`
  ADD CONSTRAINT `inbox_ibfk_1` FOREIGN KEY (`ReportID`) REFERENCES `report` (`ReportID`) ON DELETE CASCADE,
  ADD CONSTRAINT `inbox_ibfk_2` FOREIGN KEY (`AdoptionID`) REFERENCES `adopt_application` (`AdoptionID`) ON DELETE CASCADE;

--
-- Constraints for table `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`ResidentID`) REFERENCES `resident` (`ResidentID`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
