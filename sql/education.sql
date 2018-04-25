-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-04-26 00:43:24
-- 服务器版本： 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `education`
--

drop education if exists education;
create database education;
use education;

-- --------------------------------------------------------

--
-- 表的结构 `teachers`
--

CREATE TABLE `teachers` (
  `number` bigint(20) NOT NULL,
  `name` text NOT NULL,
  `gender` set('男','女') NOT NULL,
  `grade` text NOT NULL,
  `school` text NOT NULL,
  `alias` text NOT NULL,
  `college` text NOT NULL,
  `subject` text NOT NULL,
  `experience` text NOT NULL,
  `style` text NOT NULL,
  `motto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `teachers`
--

INSERT INTO `teachers` (`number`, `name`, `gender`, `grade`, `school`, `alias`, `college`, `subject`, `experience`, `style`, `motto`) VALUES
(1, '白静', '女', '研究生15级', '西安电子科技大学', '西电', '数学与统计学院', '数学', '5年授课经验', '教学专业，有条理', '对未来最大的慷慨，是把一切献给现在'),
(2, '刘一林', '男', '本科14级', '西安电子科技大学', '西电', '物理与光电工程学院', '物理', '2年授课经验', '幽默容易相处，容易和学生打成一片，教学风格独特', '如果你想把剑挥出去，就要把心里的事放下'),
(3, '高成', '男', '本科14级', '陕西师范大学', '陕师大', '生物系', '生物', '3年授课经验', '教学专业，规划清晰，学生反映进步速度快', '一直认真你就赢了'),
(4, '黄歌', '女', '研究生15级', '西安电子科技大学', '西电', '数学与统计学院', '数学', '5年授课经验', '教学质量高，学生很满意，教学方法独特，授人以渔', '让梦成真的最好方法就是醒来'),
(5, '韩智慧', '女', '本科13级', '西安电子科技大学', '西电', '电子工程学院', '英语', '1年授课经验', '甜美亲切，大受学生欢迎', '世上没有走不通的路，只有不敢走的人'),
(6, '孔文彬', '男', '本科16级', '西安电子科技大学', '西电', '软件学院', '化学', '半年授课经验', '教学专业，规划清晰，性格活泼，教学方法独特', '走在黑暗里不可怕，只要心里有光'),
(7, '王示林', '男', '本科13级', '西安电子科技大学', '西电', '通信工程', '数学 物理 化学', '2年授课经验', '熟悉科目多，教学方法独特', '努力也是一种天赋'),
(8, '王敏', '女', '本科15级', '西安外国语大学', '西外', '英语系', '英语', '2年授课经验', '很有魅力，寓教于乐', '去追逐月亮吧，因为即使你坠落，也会落在群星之间'),
(9, '张涛', '男', '研究生17级', '西安电子科技大学', '西电', '机电工程学院', '数学', '3年授课经验', '教学专业，有条理，有规划，认真负责', '当你无路可走，你会更快学会飞'),
(10, '卓语', '男', '研究生17级', '西安电子科技大学', '西电', '电子工程学院', '化学', '3年授课经验', '教学专业，规划清晰，性格活泼，教学方法独特', '成功不是将来才有的，而是决定去做的那一刻积累而成的');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `users`
--

-- --------------------------------------------------------

--
-- 表的结构 `users_token`
--

CREATE TABLE `users_token` (
  `username` text NOT NULL,
  `user_token` text NOT NULL,
  `expire_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `users_token`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`number`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `teachers`
--
ALTER TABLE `teachers`
  MODIFY `number` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
