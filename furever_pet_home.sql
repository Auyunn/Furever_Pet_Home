-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 11:51 AM
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
('ADM15', 'Rahmat', 'Said', 'rahmat@furever.com', '0120006060', 'adminpass');

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

INSERT INTO `adopt_application`
(`AdoptionID`,`ResidentID`,`PetID`,`Status`,`Reason`,`RequestDate`)
VALUES
('ADOP01','R0001','PET01','Approved',NULL,'2026-05-10 10:00:00'),
('ADOP02','R0002','PET02','Approved',NULL,'2026-05-11 11:30:00'),
('ADOP03','R0003','PET03','Pending',NULL,'2026-05-20 09:15:00'),
('ADOP04','R0004','PET04','Rejected',
'Pemohon mempunyai komitmen kerja yang tinggi dan masa penjagaan haiwan adalah terhad.',
'2026-05-12 14:00:00'),
('ADOP05','R0005','PET05','Submit',NULL,'2026-05-23 16:45:00'),
('ADOP06','R0006','PET06','Approved',NULL,'2026-05-14 08:00:00'),
('ADOP07','R0007','PET07','Pending',NULL,'2026-05-22 12:00:00'),
('ADOP08','R0008','PET08','Approved',NULL,'2026-05-15 15:20:00'),
('ADOP09','R0009','PET09','Rejected',
'Kawasan kediaman yang dinyatakan tidak memenuhi keperluan keselamatan untuk penempatan anjing.',
'2026-05-16 10:10:00'),
('ADOP10','R0010','PET10','Approved',NULL,'2026-05-17 11:00:00'),
('ADOP11','R0011','PET11','Submit',NULL,'2026-05-23 13:00:00'),
('ADOP12','R0012','PET12','Pending',NULL,'2026-05-21 17:30:00'),
('ADOP13','R0013','PET13','Submit',NULL,'2026-05-23 14:15:00'),
('ADOP14','R0014','PET14','Approved',NULL,'2026-05-19 16:00:00'),
('ADOP15','R0015','PET15','Pending',NULL,'2026-05-22 18:00:00');

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
('COM01', 'R0001', 'BRD01', 'Saya nak daftar 2 ekor kucing saya sabtu ni bos.', '2026-05-01', NULL),
('COM02', 'R0002', 'BRD01', 'Alhamdulillah program terbaik dekat area Klang!', '2026-05-01', NULL),
('COM03', 'R0003', 'BRD02', 'Pukul berapa kaunter adoption mula dibuka ya?', '2026-05-03', NULL),
('COM04', 'R0006', 'BRD02', 'Mula pukul 10 pagi encik Ravin.', '2026-05-03', 'COM03'),
('COM05', 'R0004', 'BRD03', 'Saya boleh bawa cangkul dan cat sendiri nanti.', '2026-05-05', NULL),
('COM06', 'R0005', 'BRD03', 'Terima kasih orang Pandamaran sudi tolong.', '2026-05-06', 'COM05'),
('COM07', 'R0007', 'BRD04', 'Saya dah beli 2 beg makanan, boleh hantar ke mana?', '2026-05-10', NULL),
('COM08', 'R0008', 'BRD04', 'Boleh hantar terus ke alamat shelter Bukit Tinggi.', '2026-05-10', 'COM07'),
('COM09', 'R0009', 'BRD05', 'Sangat bermanfaat, saya penduduk Bayu Perdana wajib hadir.', '2026-05-12', NULL),
('COM10', 'R0010', 'BRD06', 'Saya dah transfer RM50 untuk dana bedah Rocky. Moga sembuh.', '2026-05-14', NULL),
('COM11', 'R0011', 'BRD07', 'Kucing comel belaka, rambang mata nak pilih.', '2026-05-15', NULL),
('COM12', 'R0012', 'BRD08', 'Syabas persatuan Bukit Raja atas pelancaran kelab!', '2026-05-16', NULL),
('COM13', 'R0013', 'BRD09', 'Tips spray cuka tu memang berkesan, dah cuba kat rumah.', '2026-05-17', NULL),
('COM14', 'R0014', 'BRD10', 'Saya ada baki jaring dawai pagar kat rumah, nanti saya derma.', '2026-05-18', NULL),
('COM15', 'R0015', 'BRD11', 'Macam kenal kucing ni, rasanya jiran blok sebelah punya.', '2026-05-19', NULL);

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
('BRD01', 'ORG01', 'Kempen Kembiri Kucing & Anjing Klang 2026', 'Jom kasi pet anda dengan harga subsidi RM50 sahaja sabtu ini!', 'camp01.jpg', '2026-05-01 09:00:00'),
('BRD02', 'ORG02', 'Hari Kesedaran Adopt Don’t Shop Botanic', 'Sertai kami di Central Park Botanic untuk sesi suai kenal haiwan angkat.', 'camp02.jpg', '2026-05-03 10:00:00'),
('BRD03', 'ORG05', 'Gotong-Royong Membersihkan Shelter Pandamaran', 'Sukarelawan diperlukan untuk mengecat dan mencuci sangkar anjing.', 'camp03.jpg', '2026-05-05 08:30:00'),
('BRD04', 'ORG07', 'Sumbangan Makanan Haiwan Diperlukan', 'Stok makanan kibbles kami di Bukit Tinggi berbaki untuk 3 hari sahaja.', 'camp04.jpg', '2026-05-10 14:00:00'),
('BRD05', 'ORG12', 'Taklimat Pengurusan Stray untuk Penduduk Bayu Perdana', 'Mari belajar cara urus populasi stray taman anda dengan kaedah TNR.', 'camp05.jpg', '2026-05-12 15:00:00'),
('BRD06', 'ORG03', 'Bantuan Kos Perubatan Kos Rocky', 'Anjing Rocky memerlukan RM800 untuk pembedahan tulang kaki.', 'camp06.jpg', '2026-05-14 11:00:00'),
('BRD07', 'ORG09', 'Sesi Adoption Day Pekan Teluk Pulai', 'Bawa pulang si bulu comel hari ini secara percuma.', 'camp07.jpg', '2026-05-15 09:00:00'),
('BRD08', 'ORG15', 'Pelancaran Kelab Komuniti Haiwan Bukit Raja', 'Platform rasmi komuniti pencinta haiwan perumahan Bukit Raja.', 'camp08.jpg', '2026-05-16 16:00:00'),
('BRD09', 'ORG13', 'Tips Mengurus Kucing Kampung Kencing Merata', 'Perkongsian artikel santai dari admin Kampung Delek Cat Sanctuary.', NULL, '2026-05-17 20:00:00'),
('BRD10', 'ORG04', 'Sumbangan Jaring Sangkar Meru', 'Mencari pembekal besi pagar dawai murah atau sumbangan terpakai.', 'camp09.jpg', '2026-05-18 10:30:00'),
('BRD11', 'ORG06', 'Hebahan Kucing Hilang - Dijumpai di Andalas', 'Kucing bertali leher merah dijumpai merayau di surau Andalas.', 'camp10.jpg', '2026-05-19 12:00:00'),
('BRD12', 'ORG08', 'Street Feeding Squad Kapar Diperlukan', 'Mencari sukarelawan hujung minggu untuk beri makan stray anjing kilang.', NULL, '2026-05-20 13:00:00'),
('BRD13', 'ORG11', 'Amaran Pembuangan Sisa Toksik Bahayakan Stray', 'Kes stray mati keracunan di parit besar Pelabuhan Klang.', NULL, '2026-05-21 08:00:00'),
('BRD14', 'ORG10', 'Ceramah Kesedaran Haiwan di Sekolah Meru', 'Program pendidikan awal kasih haiwan bersama murid SK Meru.', 'camp11.jpg', '2026-05-22 09:30:00'),
('BRD15', 'ORG14', 'Kempen Derma RM1 Seminggu Raja Muda Musa', 'Tabung kecemasan makanan jalanan untuk kucing lot kedai makan.', 'camp12.jpg', '2026-05-23 11:00:00');

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
(1, 'ORG01', 'Apakah syarat asas adopsi haiwan?', 'Pemohon mestilah berumur 18 tahun ke atas, mempunyai pendapatan stabil, dan mendapat keizinan keluarga/tuan rumah.'),
(2, 'ORG01', 'Adakah proses adopsi ini dikenakan bayaran?', 'Tiada yuran jualan, namun sumbangan ikhlas digalakkan untuk kos perubatan kembiri terdahulu.'),
(3, 'ORG02', 'Bolehkah saya pulangkan pet jika tidak serasi?', 'Kami nasihatkan tempoh percubaan 2 minggu. Jika gagal, boleh pulangkan semula demi keselamatan haiwan.'),
(4, 'ORG03', 'Bagaimana cara melaporkan haiwan kemalangan luar waktu pejabat?', 'Sila buat laporan segera dalam sistem dengan tanda butang \"Kecemasan\" atau hubungi talian hotline NGO kami.'),
(5, 'ORG05', 'Adakah anjing yang diadopsi sudah divaksin?', 'Ya, semua anjing dewasa yang sedia diadopsi telah menerima sekurang-kurangnya vaksin dos pertama.'),
(6, 'ORG06', 'Adakah kucing baka kampung senang dijaga?', 'Sangat mudah! Kucing tempatan mempunyai antibodi yang kuat dan kurang risiko penyakit genetik baka.'),
(7, 'ORG07', 'Bolehkah saya melawat haiwan sebelum memohon?', 'Boleh, sila buat janji temu melalui sistem sekurang-kurangnya 1 hari sebelum lawatan.'),
(8, 'ORG12', 'Apakah itu kaedah TNR yang diamalkan?', 'TNR bermaksud Trap-Neuter-Return (Tangkap, Kembiri, Lepas Semula) untuk mengawal pembiakan populasi stray.'),
(9, 'ORG15', 'Berapakah kos purata makanan kucing sebulan?', 'Sekitar RM50 hingga RM100 bergantung kepada jenis jenama makanan kering atau basah yang dipilih.'),
(10, 'ORG04', 'Bolehkah penduduk flat membela anjing?', 'Kebanyakan akta bangunan strata tidak membenarkan anjing bersaiz besar demi ketenteraman awam.'),
(11, 'ORG09', 'Adakah anak kucing umur 1 bulan boleh dipelihara?', 'Sebaiknya anak kucing menyusu ibu sehingga umur 2 bulan sebelum dipisahkan untuk adopt.'),
(12, 'ORG11', 'Bagaimana mahu menjadi sukarelawan hujung minggu?', 'Buka menu Help Center dan isi borang permohonan pendaftaran sukarelawan zon Klang.'),
(13, 'ORG13', 'Adakah kucing yang ditangkap akan dimatikan (euthanize)?', 'Tidak, NGO Furever Pet Home mengamalkan polisi sifar bunuh (No-Kill Policy) melainkan sakit kritikal tiada harapan.'),
(14, 'ORG08', 'Apakah jenis makanan dilarang untuk anjing?', 'Coklat, anggur, bawang, dan tulang ayam tajam amat merbahaya untuk kesihatan sistem pencernaan anjing.'),
(15, 'ORG14', 'Bagaimana mahu menukar status aduan kepada selesai?', 'Hanya pihak organisasi (NGO) yang menguruskan kes sahaja boleh mengemas kini status laporan setelah tindakan diambil.');

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
(1, 'ORG01', 'Panduan Asas Latihan Tandas Kucing', 'Cat', 'Letakkan bekas pasir di penjuru rumah yang sunyi, dedahkan anak kucing selepas makan.', 30.00),
(2, 'ORG01', 'Jadual Suntikan Vaksin Wajib Anjing', 'Dog', 'Dos 1 (6-8 minggu), Dos 2 (10-12 minggu), Dos 3 (14-16 minggu) dan Booster setahun sekali.', 150.00),
(3, 'ORG02', 'Cara Mandikan Kucing Takut Air', 'Cat', 'Gunakan air suam, lap perlahan menggunakan tuala basah berbanding siraman air pancur terus.', 15.00),
(4, 'ORG05', 'Teknik Berjalan Menggunakan Tali (Leash) Anjing Mongrel', 'Dog', 'Mulakan latihan di dalam rumah dahulu sebelum keluar ke taman rekreasi awam.', 25.00),
(5, 'ORG06', 'Pemakanan Seimbang Kucing Steril', 'Cat', 'Kucing yang dimandulkan mudah naik berat badan. Kurangkan makanan tinggi karbohidrat.', 70.00),
(6, 'ORG07', 'Tanda-Tanda Anjing Anda Dijangkiti Kutu', 'Dog', 'Sering menggaru telinga, kulit kemerahan, dan bintik hitam kutu di celah peha bulu.', 45.00),
(7, 'ORG09', 'Persediaan Menambut Kucing Baru Di Rumah', 'Cat', 'Sediakan bilik pengasingan khas selama 3 hari supaya kucing tidak stres dengan persekitaran baru.', 100.00),
(8, 'ORG15', 'Latihan Menahan Salakan Anjing (Stop Barking)', 'Dog', 'Gunakan teknik peneguhan positif (reward treats) apabila anjing bertenang dengar arahan.', 20.00),
(9, 'ORG04', 'Cara Membersihkan Karang Gigi Kucing', 'Cat', 'Gunakan berus gigi ubat khas haiwan atau berikan mainan jenis kunyah berserat catnip.', 12.00),
(10, 'ORG12', 'Rawatan Awal Luka Luar Anjing Jalanan', 'Dog', 'Bersihkan luka dengan cecair saline antiseptik, sapukan krim iodin ubat luka haiwan.', 35.00),
(11, 'ORG13', 'Atasi Masalah Kucing Suka Cakar Perabot Sofa', 'Cat', 'Sediakan papan pencakar khas (scratching post) berhampiran kawasan perabot utama.', 40.00),
(12, 'ORG08', 'Kepentingan Berjalan Petang Bersama Anjing', 'Dog', 'Membantu melepaskan tenaga terpendam anjing bagi mengelakkan tabiat merosakkan barang rumah.', 0.00),
(13, 'ORG10', 'Penjagaan Bulu Kucing Waktu Musim Panas', 'Cat', 'Sikat bulu mati setiap hari untuk mengelakkan pembentukan gumpalan bulu (hairball).', 18.00),
(14, 'ORG14', 'Kenali Penyakit Parvovirus Anjing', 'Dog', 'Penyakit virus berjangkit pembunuh nombor satu. Gejala termasuk cirit-birit berdarah dan muntah.', 300.00),
(15, 'ORG11', 'Cara Menghilangkan Bau Kencing Kucing Di Lantai', 'Cat', 'Gunakan pencuci jenis enzim organik, elakkan mop guna peluntur amonia kerana menarik kencing semula.', 22.00);

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
('INB01', NULL, 'ADOP01', 'Permohonan Meluluskan Rocky', 'Tahniah! Permohonan anda untuk mengambil Rocky sebagai anak angkat diluluskan.', '2026-05-12 09:00:00', 'Pet Adoption Application', 'Approve'),
('INB02', 'REP01', NULL, 'Aduan Anjing Bukit Tinggi Selesai', 'Skuad penyelamat kami telah mengambil anjing tersebut untuk rawatan kembiri.', '2026-05-11 15:00:00', 'Pet Report', 'Resolve'),
('INB03', NULL, 'ADOP02', 'Permohonan Meluluskan Luna', 'Sila hadir ke shelter Andalas Pet Haven untuk sesi penyerahan kucing Luna.', '2026-05-13 10:30:00', 'Pet Adoption Application', 'Approve'),
('INB04', 'REP02', NULL, 'Status Kucing Atas Pokok', 'Kucing berjaya diselamatkan dengan bantuan bomba petang tadi.', '2026-05-04 17:00:00', 'Pet Report', 'Resolve'),
('INB05', NULL, 'ADOP04', 'Permohonan Ditolak Bella', 'Dukacita dimaklumkan permohonan anda ditolak atas faktor kekangan masa jagaan.', '2026-05-13 11:00:00', 'Pet Adoption Application', 'Reject'),
('INB06', 'REP03', NULL, 'Aduan Anjing Liar Pandamaran', 'Aduan diterima. Pasukan pemantau dalam perjalanan membuat tinjauan lokasi.', '2026-05-05 21:00:00', 'Pet Report', 'In Progress'),
('INB07', NULL, 'ADOP06', 'Permohonan Meluluskan Max', 'Permohonan diluluskan. Sila bawa kolar anjing semasa hari pengambilan.', '2026-05-16 14:00:00', 'Pet Adoption Application', 'Approve'),
('INB08', NULL, 'ADOP03', 'Permohonan Kucing Simba Diproses', 'Mesej rasmi: Borang anda sedang disemak oleh jawatankuasa Botanic.', '2026-05-21 09:00:00', 'Pet Adoption Application', 'Pending'),
('INB09', 'REP08', NULL, 'Anjing Dilanggar Berjaya Diambil', 'Anjing patah peha telah selamat dihantar ke klinik veterinar Klang.', '2026-05-15 16:00:00', 'Pet Report', 'Resolve'),
('INB10', NULL, 'ADOP08', 'Permohonan Meluluskan Daisy', 'Tahniah permohonan anjing Daisy anda diluluskan.', '2026-05-17 10:00:00', 'Pet Adoption Application', 'Approve'),
('INB11', 'REP07', NULL, 'Aduan Keluarga Kucing Teluk Pulai', 'Kucing-kucing basah telah dipindahkan ke pusat perlindungan kering transit.', '2026-05-16 11:30:00', 'Pet Report', 'Resolve'),
('INB12', NULL, 'ADOP10', 'Permohonan Meluluskan Cookie', 'Borang lulus. Anda boleh datang ambil Cookie hujung minggu ini.', '2026-05-19 12:00:00', 'Pet Adoption Application', 'Approve'),
('INB13', 'REP14', NULL, 'Aduan Anjing Kurus Selesai', 'Ibu anjing dan anak-anak berjaya diselamatkan ke tempat teduh.', '2026-05-18 13:00:00', 'Pet Report', 'Resolve'),
('INB14', NULL, 'ADOP14', 'Permohonan Meluluskan Comel', 'Permohonan anda disahkan lulus oleh pihak Kampung Delek Cat Shelter.', '2026-05-21 15:30:00', 'Pet Adoption Application', 'Approve'),
('INB15', 'REP17', NULL, 'Aduan Jerat Kucing Selesai', 'Dawai besi di leher kucing telah berjaya dipotong dengan selamat.', '2026-05-23 18:30:00', 'Pet Report', 'Resolve');

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
  `Description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`OrgID`, `OrgName`, `NumberPhone`, `OrgAddress`, `Email`, `Password`, `Description`) VALUES
('ORG01', 'Klang Stray Rescue', '0333445566', 'No 2, Jalan Istana, 41000 Klang', 'contact@klangstray.org', 'orgpass', 'Membantu menyelamat dan memandulkan anjing serta kucing jalanan di sekitar pusat bandar Klang.'),
('ORG02', 'Bandar Botanic Animal Shelter', '0333221122', 'No 14, Jalan Jasmin, Bandar Botanic, 41200 Klang', 'botanic.pets@email.com', 'orgpass', 'Tempat perlindungan komuniti berpusat di Bandar Botanic untuk kucing terbiar.'),
('ORG03', 'Paws and Claws Klang NGO', '0129994444', 'Lot 451, Jalan Telok Gong, 42000 Klang', 'pawsclaws@email.com', 'orgpass', 'NGO yang memfokuskan kepada rawatan perubatan haiwan jalanan yang cedera akibat kemalangan.'),
('ORG04', 'Klang Valley Cat Rescue', '0165551212', 'No 8, Jalan Meru Utama, 41050 Klang', 'kvcat@email.com', 'orgpass', 'Komuniti pencinta kucing terbiar khusus untuk kawasan Meru dan Kapar.'),
('ORG05', 'Pandamaran Dog Hope Sanctuary', '0331687777', 'Kawasan Perindustrian Pandamaran, 42000 Klang', 'pandamarandogs@email.com', 'orgpass', 'Menyediakan perlindungan sementara serta program pengadopsian anjing jalanan.'),
('ORG06', 'Andalas Pet Haven', '0137778888', 'No 22, Jalan Taman Sri Andalas, 41200 Klang', 'andalashaven@email.com', 'orgpass', 'Pusat jagaan haiwan mini yang diuruskan oleh sukarelawan penduduk Sri Andalas.'),
('ORG07', 'Bukit Tinggi Furry Friends', '0176663333', 'Lorong Batu Nilam 21A, Bandar Bukit Tinggi, 41200 Klang', 'btfurry@email.com', 'orgpass', 'Aktif menjalankan kempen kesedaran pemandulan haiwan di kawasan perumahan.'),
('ORG08', 'Kapar Stray Angels', '0112345678', 'Batu 4, Jalan Kapar, 42200 Klang', 'kaparstrays@email.com', 'orgpass', 'Bergerak aktif memberi makanan (street feeding) dan menyelamat stray di kawasan Kapar.'),
('ORG09', 'Teluk Pulai Kitty Shelter', '0142223334', 'No 5, Lorong Sg. Udang, Teluk Pulai, 41100 Klang', 'tp_kitty@email.com', 'orgpass', 'Tempat perlindungan khas kucing jalanan yang uzur dan anak kucing tanpa ibu.'),
('ORG10', 'Meru Animal Welfare', '0333921111', 'Jalan Kassim, Pekan Meru, 41050 Klang', 'meru_welfare@email.com', 'orgpass', 'Persatuan kebajikan haiwan terbiar luar bandar Klang.'),
('ORG11', 'Port Klang Stray Care', '0194445556', 'Jalan Pelabuhan Utara, 42000 Pelabuhan Klang', 'portcare@email.com', 'orgpass', 'Menyelamat anjing-anjing terbiar di sekitar kawasan zon industri dan pelabuhan.'),
('ORG12', 'Taman Bayu Perdana Pet Shield', '0127771112', 'Jalan Prima, Taman Bayu Perdana, 41200 Klang', 'bayushield@email.com', 'orgpass', 'Menyediakan khidmat klinik kembiri komuniti kos rendah untuk stray.'),
('ORG13', 'Kampung Delek Cat Sanctuary', '0138889991', 'Jalan Haji Omar, Kampung Delek, 41050 Klang', 'kdelek_cat@email.com', 'orgpass', 'Rumah transit kucing persendirian bertaraf komuniti kampung.'),
('ORG14', 'Raja Muda Musa Furry Rescue', '0168882223', 'Lorong Berantas, Off Jalan Raja Muda Musa, 41100 Klang', 'rmm_rescue@email.com', 'orgpass', 'Menangani isu haiwan liar terbiar di sekitar premis kedai makan lama Klang.'),
('ORG15', 'Bukit Raja Hope Society', '0172224445', 'Jalan Singgahsana, Bandar Bukit Raja, 41050 Klang', 'braja_hope@email.com', 'orgpass', 'Pertubuhan berdaftar yang menghubungkan pemaju perumahan dengan NGO mesra haiwan.');

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
('PET01', 'ORG01', 'Dog', 'Local Mixed', 2, 'Pusat Bandar Klang', 1, 'None', 'dog01.jpg', 'Male', 'Rocky', 1),
('PET02', 'ORG01', 'Cat', 'Domestic Short Hair', 1, 'Taman Sri Andalas', 1, 'Sensitive Stomach', 'cat01.jpg', 'Female', 'Luna', 1),
('PET03', 'ORG02', 'Cat', 'Persian Mix', 3, 'Bandar Botanic', 1, 'None', 'cat02.jpg', 'Male', 'Simba', 1),
('PET04', 'ORG03', 'Dog', 'Stray Mongrel', 1, 'Telok Gong', 0, 'Skin Allergy', 'dog02.jpg', 'Female', 'Bella', 1),
('PET05', 'ORG04', 'Cat', 'Siamese Mix', 2, 'Taman Meru', 1, 'None', 'cat03.jpg', 'Male', 'Milo', 1),
('PET06', 'ORG05', 'Dog', 'German Shepherd Mix', 4, 'Pandamaran', 1, 'None', 'dog03.jpg', 'Male', 'Max', 1),
('PET07', 'ORG06', 'Cat', 'Tabby Cat', 1, 'Taman Sri Andalas', 1, 'Chicken Allergy', 'cat04.jpg', 'Female', 'Oyen', 1),
('PET08', 'ORG07', 'Dog', 'Terrier Mix', 2, 'Bandar Bukit Tinggi', 1, 'None', 'dog04.jpg', 'Female', 'Daisy', 1),
('PET09', 'ORG08', 'Dog', 'Local Breed', 3, 'Pekan Kapar', 0, 'None', 'dog05.jpg', 'Male', 'Bruno', 1),
('PET10', 'ORG09', 'Cat', 'Calico Cat', 1, 'Teluk Pulai', 1, 'None', 'cat05.jpg', 'Female', 'Cookie', 1),
('PET11', 'ORG10', 'Cat', 'Mainecoon Mix', 2, 'Pekan Meru', 1, 'Flies Bites Sensitive', 'cat06.jpg', 'Male', 'Tigger', 1),
('PET12', 'ORG11', 'Dog', 'Hound Mix', 3, 'Pelabuhan Klang', 1, 'None', 'dog06.jpg', 'Female', 'Lucky', 1),
('PET13', 'ORG12', 'Cat', 'Tuxedo Cat', 1, 'Taman Bayu Perdana', 1, 'None', 'cat07.jpg', 'Male', 'Felix', 1),
('PET14', 'ORG13', 'Cat', 'Kampung Cat', 0, 'Kampung Delek', 0, 'None', 'cat08.jpg', 'Female', 'Comel', 1),
('PET15', 'ORG15', 'Dog', 'Retriever Mix', 2, 'Bandar Bukit Raja', 1, 'None', 'dog07.jpg', 'Male', 'Buddy', 1);

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
('REP01', 'R0001', 'ORG01', 'Anjing Hitam Kurus', 'Depan Kedai Mamak Bukit Tinggi', 'Anjing berkeliaran dengan kaki tempang, kelihatan lapar.', 'Resolved', 'rep01.jpg'),
('REP02', 'R0002', 'ORG02', 'Kucing Pokok', 'Taman Permainan Sri Andalas', 'Kucing tersangkut atas pokok tinggi sejak semalam.', 'Resolved', 'rep02.jpg'),
('REP03', 'R0003', 'ORG05', 'Kumpulan Anjing Liar', 'Kawasan Depot Bas Pandamaran', 'Ada sekumpulan anjing menyalak waktu malam, merisaukan penduduk.', 'Pending', 'rep03.jpg'),
('REP04', 'R0005', 'ORG04', 'Kucing Sakit Mata', 'Pasar Malam Pekan Meru', 'Anak kucing jalanan sakit mata teruk berhampiran tong sampah.', 'Submit', 'rep04.jpg'),
('REP07', 'R0007', 'ORG09', 'Ibu Kucing & Anak', 'Bawah Jambatan Teluk Pulai', 'Satu keluarga kucing terbiar basah lencun akibat hujan lebat.', 'Resolved', 'rep05.jpg'),
('REP08', 'R0008', 'ORG03', 'Anjing Dilanggar', 'Jalan Persiaran Raja Muda Musa', 'Anjing dilanggar lari kereta, patah peha tepi divider.', 'Resolved', 'rep06.jpg'),
('REP09', 'R0009', 'ORG13', 'Kucing Sesat Baka', 'Lorong Belakang Jeti Sg Udang', 'Kucing baka parsi berbulu comot, dipercayai dibuang pemilik.', 'Pending', 'rep07.jpg'),
('REP10', 'R0011', 'ORG08', 'Anjing Kurap', 'Kawasan Tepi Longkang Jalan Kapar', 'Anjing kurap kronik tiada bulu memerlukan bantuan NGO segera.', 'Submit', 'rep08.jpg'),
('REP11', 'R0012', 'ORG15', 'Kucing Cedera Telinga', 'Komersial Lot Bandar Bukit Raja', 'Kucing telinga berdarah diserang haiwan lain.', 'Submit', 'rep09.jpg'),
('REP12', 'R0004', 'ORG11', 'Anjing Tersangkut Pagar', 'Zon Bebas Pelabuhan Klang (PKFZ)', 'Anjing tersangkut di celah pagar kawat kilang.', 'Resolved', 'rep10.jpg'),
('REP13', 'R0006', 'ORG12', 'Kucing Cedera Ekor', 'Kawasan Kedai Bayu Perdana', 'Ekor kucing tersepit pintu premis kedai terbiar.', 'Pending', 'rep11.jpg'),
('REP14', 'R0010', 'ORG07', 'Anjing Kurus Kelaparan', 'Padang Bola Lorong Batu Nilam', 'Anjing betina baru melahirkan anak, sangat kurus kering.', 'Resolved', 'rep12.jpg'),
('REP15', 'R0013', 'ORG10', 'Anak Kucing Flat', 'Tangga Blok C Flat Meru', '3 ekor anak kucing ditinggalkan dalam kotak kertas.', 'Submit', 'rep13.jpg'),
('REP16', 'R0014', 'ORG15', 'Anjing Ganas', 'Bulatan Persiaran Bukit Raja', 'Anjing mengejar penunggang motosikal yang lalu lalang.', 'Pending', 'rep14.jpg'),
('REP17', 'R0015', 'ORG14', 'Kucing Luka Leher', 'Belakang Restoran Kampung Kuantan', 'Leher kucing terjerat dawai besi karat.', 'Resolved', 'rep15.jpg');

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
('R0001', 'Ahmad', 'Faizal', '0123456789', 'ahmad.faizal@email.com', 'pass123', 'No 12, Jalan Batu Unjur 1, Taman Bayu Perdana, 41200 Klang', 1, 4500.00),
('R0002', 'Siti', 'Aishah', '0139876543', 'siti.aishah@email.com', 'pass123', 'No 45, Jalan Sri Damai, Taman Sri Andalas, 41200 Klang', 1, 3800.00),
('R0003', 'Ravin', 'Kumar', '0171112223', 'ravin.kumar@email.com', 'pass123', 'B-3-5, Flat Bukit Tinggi, Bandar Bukit Tinggi, 41200 Klang', 1, 2800.00),
('R0004', 'Mei', 'Ling', '0164445556', 'mei.ling@email.com', 'pass123', 'No 88, Jalan Kim Chuan, Pandamaran, 42000 Klang', 1, 6000.00),
('R0005', 'Muhammad', 'Amir', '0112223334', 'amir.res@email.com', 'pass123', 'No 7, Jalan Haji Sirat, Kampung Delek Kanan, 41050 Klang', 1, 3200.00),
('R0006', 'Chong', 'Wei', '0198887776', 'chong.wei@email.com', 'pass123', 'No 15, Jalan Kasuarina 4, Bandar Botanic, 41200 Klang', 1, 7500.00),
('R0007', 'Fatima', 'Zahra', '0145556667', 'fatima.z@email.com', 'pass123', 'No 23, Jalan Teluk Pulai, 41100 Klang', 1, 4100.00),
('R0008', 'Suresh', 'Maniam', '0129998887', 'suresh.m@email.com', 'pass123', 'No 4, Jalan Melawis, Taman Melawis, 41100 Klang', 1, 5200.00),
('R0009', 'Nurul', 'Huda', '0134443332', 'nurul.huda@email.com', 'pass123', 'No 19, Jalan Sungai Udang, Kampung Sungai Udang, 41000 Klang', 1, 3000.00),
('R0010', 'Kevin', 'Tan', '0187776665', 'kevin.tan@email.com', 'pass123', 'No 102, Jalan Raja Muda Musa, 41100 Klang', 1, 4800.00),
('R0011', 'Zulkifli', 'Rahman', '0115554443', 'zul.rahman@email.com', 'pass123', 'No 33, Jalan Meru, Taman Meru, 41050 Klang', 1, 3500.00),
('R0012', 'Grace', 'Anand', '0162229991', 'grace.a@email.com', 'pass123', 'No 56, Jalan Langat, Bandar Puteri, 41200 Klang', 1, 5900.00),
('R0013', 'Asraf', 'Ghani', '0173338882', 'asraf.g@email.com', 'pass123', 'No 14, Jalan Kapar, Pekan Kapar, 42200 Klang', 1, 2600.00),
('R0014', 'Michelle', 'Wong', '0124441119', 'michelle.w@email.com', 'pass123', 'No 8, Jalan Zapin 2, Bandar Bukit Raja, 41050 Klang', 1, 6700.00),
('R0015', 'Khairul', 'Anwar', '0196662221', 'khairul.a@email.com', 'pass123', 'No 51, Jalan Kampung Kuantan, 41050 Klang', 1, 3900.00);

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
