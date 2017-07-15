-- MySQL dump 10.13  Distrib 5.5.23, for Win32 (x86)
--
-- Host: localhost    Database: OnlineOrderingSystem
-- ------------------------------------------------------
-- Server version	5.5.23

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `yd_cart`
--

DROP TABLE IF EXISTS `yd_cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_cart` (
  `cart_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '购物车id',
  `member_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订餐用户会员id',
  `restaurant_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '餐厅id',
  `foods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '菜品id',
  `foods_name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜品名',
  `foods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '菜品数量',
  `logo` varchar(100) NOT NULL COMMENT '菜品典型图',
  PRIMARY KEY (`cart_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='购物车表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_cart`
--

LOCK TABLES `yd_cart` WRITE;
/*!40000 ALTER TABLE `yd_cart` DISABLE KEYS */;
INSERT INTO `yd_cart` VALUES (1,3,5,5,'口水鸡',1,'/Public/Upload/Foods/2017-04-10/58eb424b9b918.jpg'),(2,3,7,3,'美味酸菜鱼',1,'/Public/Upload/Foods/2017-04-10/58eb39bbea369.jpg');
/*!40000 ALTER TABLE `yd_cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_clicked_use`
--

DROP TABLE IF EXISTS `yd_clicked_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_clicked_use` (
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '用户会员ID',
  `comment_id` mediumint(8) unsigned NOT NULL COMMENT '评论的ID',
  PRIMARY KEY (`member_id`,`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户会员点击过有用的评论';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_clicked_use`
--

LOCK TABLES `yd_clicked_use` WRITE;
/*!40000 ALTER TABLE `yd_clicked_use` DISABLE KEYS */;
INSERT INTO `yd_clicked_use` VALUES (3,12),(3,13),(3,14),(3,15),(3,16),(3,17),(3,18),(3,19),(3,20),(3,21),(3,22),(3,23),(3,24),(3,25),(3,26),(3,27),(35,11),(35,12),(35,13),(35,14);
/*!40000 ALTER TABLE `yd_clicked_use` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_comment`
--

DROP TABLE IF EXISTS `yd_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_comment` (
  `comment_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(1000) NOT NULL COMMENT '评论的内容',
  `star` tinyint(3) unsigned NOT NULL DEFAULT '3' COMMENT '打的分',
  `addtime` int(10) unsigned NOT NULL COMMENT '评论时间',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员ID',
  `foods_id` mediumint(8) unsigned NOT NULL COMMENT '菜品的ID',
  `used` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '有用的数量',
  PRIMARY KEY (`comment_id`),
  KEY `foods_id` (`foods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='评论表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_comment`
--

LOCK TABLES `yd_comment` WRITE;
/*!40000 ALTER TABLE `yd_comment` DISABLE KEYS */;
INSERT INTO `yd_comment` VALUES (11,'42423',5,1492918395,3,1,1),(12,'不错',5,1492933703,35,1,2),(13,'可以可以',4,1492933728,35,1,3),(14,'好好',5,1492933739,35,1,2),(15,'111',5,1492937488,3,1,1),(16,'11',4,1492937539,3,1,1),(17,'131',4,1492937575,3,1,1),(18,'131',3,1492937612,3,1,1),(19,'32额',5,1492937692,3,1,1),(20,'全额二群',3,1492937749,3,1,1),(21,'31213',4,1492937773,3,1,1),(22,'test',5,1492938137,3,1,1),(23,'test1',5,1492938197,3,1,1),(24,'怎么样',3,1492951485,35,4,1),(25,'good',5,1492960558,3,3,1),(26,'111',5,1493005169,3,4,1),(27,'77',4,1494255209,3,5,1),(28,'还不错',5,1494582229,35,1,0),(29,'11',3,1494604314,3,6,0);
/*!40000 ALTER TABLE `yd_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_foods`
--

DROP TABLE IF EXISTS `yd_foods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_foods` (
  `foods_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `foods_name` varchar(45) NOT NULL COMMENT '菜品名称',
  `cate_id` smallint(5) unsigned NOT NULL COMMENT '主分类的id',
  `restaurant_id` smallint(5) unsigned NOT NULL COMMENT '餐厅的id',
  `shop_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本店价',
  `points` int(10) unsigned NOT NULL COMMENT '赠送积分',
  `xp` int(10) unsigned NOT NULL COMMENT '赠送经验值',
  `is_promote` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否促销',
  `promote_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '促销价',
  `promote_start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '促销开始时间',
  `promote_end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '促销结束时间',
  `logo` varchar(150) NOT NULL DEFAULT '' COMMENT 'logo原图',
  `sm_logo` varchar(150) NOT NULL DEFAULT '' COMMENT 'logo缩略图',
  `is_hot` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否热卖',
  `is_new` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否新品',
  `is_best` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否精品',
  `is_on_sale` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架：1：上架，0：下架',
  `sort_num` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '排序数字',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否放到回收站：1：是，0：否',
  `addtime` int(10) unsigned NOT NULL COMMENT '添加时间',
  `foods_desc` longtext COMMENT '菜品描述',
  `inventory` int(5) NOT NULL DEFAULT '0' COMMENT '库存量',
  `total_sales` int(10) NOT NULL DEFAULT '0' COMMENT '菜品总销量',
  PRIMARY KEY (`foods_id`),
  KEY `shop_price` (`shop_price`),
  KEY `cate_id` (`cate_id`),
  KEY `restaurant_id` (`restaurant_id`),
  KEY `is_on_sale` (`is_on_sale`),
  KEY `is_hot` (`is_hot`),
  KEY `is_new` (`is_new`),
  KEY `is_best` (`is_best`),
  KEY `is_delete` (`is_delete`),
  KEY `sort_num` (`sort_num`),
  KEY `promote_start_time` (`promote_start_time`),
  KEY `promote_end_time` (`promote_end_time`),
  KEY `addtime` (`addtime`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='菜品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_foods`
--

LOCK TABLES `yd_foods` WRITE;
/*!40000 ALTER TABLE `yd_foods` DISABLE KEYS */;
INSERT INTO `yd_foods` VALUES (1,'干瘪四季豆',15,7,25.00,20,20,1,24.00,1491840000,1492444800,'/Public/Upload/Foods/2017-04-10/58eb35e36798e.jpg','/Public/Upload/Foods/2017-04-10/small_0-58eb35e36798e.jpg',1,1,0,1,1,0,1491809763,'<p><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\"><span style=\"color:rgb(51,51,51);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;font-size:14px;background-color:rgb(255,255,255);\">原料：四季豆、干辣椒、胡椒粉、白糖、料酒、肉末、大蒜、芽菜、生抽</span></span></p><p><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">描述：嫩脆的豆角和干辣椒、芽菜、肉沫煸炒，微辣咸鲜，真的是一道下饭的硬菜！ </span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">做四季豆的关键就是要煮熟煮透，未煮熟的四季豆中含有皂苷和胰蛋白酶抑制物，会刺激人体的肠胃，造成食物中毒，出现胃肠炎症状。</span></p>',77,14),(2,'川味【麻婆豆腐】',14,7,15.00,15,15,0,0.00,0,0,'/Public/Upload/Foods/2017-04-10/58eb3826aac96.jpg','/Public/Upload/Foods/2017-04-10/small_0-58eb3826aac96.jpg',0,1,0,1,2,0,1491810342,'<p>描述：<span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">麻婆豆腐是四川省汉族传统名菜之一，属于川菜系。主要原料为配料和豆腐，材料主要有豆腐、牛肉末(也可以用猪肉)、辣椒和花椒等。麻来自花椒，辣来自辣椒，这道菜突出了川菜\"麻辣\"的特点。此菜大约在清代同治初年(1874年以后)，由成都市北郊万福桥一家名为\"陈兴盛饭铺\"的小饭店老板娘陈刘氏所创。因为陈刘氏脸上有麻点，人称陈麻婆，她发明的烧豆腐就被称为\"陈麻婆豆腐\"。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">此菜色泽淡黄，豆腐软嫩而有光泽、其味麻、辣、酥、香、嫩、鲜、烫、活；</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">麻：指豆腐在起锅时，要洒上适量的花椒末。花椒要用汉源进贡朝廷的贡椒，麻味纯正，沁人心脾。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">辣：是选用龙潭寺大红袍油椒制作豆瓣，剁细炼熟，加以少量熟油海椒烹脍豆腐，又辣又香。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">烫：豆腐的特质保持了整道菜出锅后的温度，不容易冷却，每下一次筷子，吃到的都是刚出炉的味道。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">香：就是起锅立即上桌，闻不到制豆腐石膏味，冷浸豆腐的水锈味，各色佐料原有的难闻气味，只有勾起食欲的香味。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">酥：指炼好的牛肉馅子，色泽金黄，红酥不板，一颗颗，一粒粒，入口就酥，沾牙就化。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">嫩：指的是豆腐下锅，煎氽得法，色白如玉，有楞有角，一捻即碎，故住宅大多用小勺舀食。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">鲜：指全菜原料，俱皆新鲜，鲜嫩翠绿，红白相宜，色味俱鲜，无可挑剔。</span><br /><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">活：是陈麻婆豆腐店的一项绝技：豆腐上桌，寸把长的蒜苗，在碗内根根直立，翠绿湛兰，油泽甚艳，仿佛刚从畦地采摘切碎，活灵活现，但夹之入口，俱皆熟透，毫无生涩味道</span></p>',74,25),(3,'美味酸菜鱼',15,7,50.00,50,50,1,48.00,1491148800,1497715200,'/Public/Upload/Foods/2017-04-10/58eb39bbea369.jpg','/Public/Upload/Foods/2017-04-10/small_0-58eb39bbea369.jpg',1,1,1,1,3,0,1491810747,'<p><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">酸菜鱼是一道川菜，麻辣鲜香十分好吃，也是很受四川人喜欢的一道菜，制作也相当的简单</span></p>',115,28),(4,'麻辣水煮鱼',15,5,60.00,60,60,1,55.00,1491148800,1493136000,'/Public/Upload/Foods/2017-04-10/58eb3ea8c0cf5.jpg','/Public/Upload/Foods/2017-04-10/small_0-58eb3ea8c0cf5.jpg',1,1,0,1,4,0,1491812008,'<p>麻辣水煮鱼</p>',125,64),(5,'口水鸡',15,5,50.00,50,50,1,45.00,1491926400,1493136000,'/Public/Upload/Foods/2017-04-10/58eb424b9b918.jpg','/Public/Upload/Foods/2017-04-10/small_0-58eb424b9b918.jpg',1,1,1,1,5,0,1491812939,'<p>口水鸡</p>',0,20),(6,'剁椒鱼头',15,5,70.00,60,60,1,66.00,1492099200,1495641600,'/Public/Upload/foods/2017-04-10/58eb432219eb5.jpg','/Public/Upload/foods/2017-04-10/small_0-58eb432219eb5.jpg',1,1,1,1,6,0,1491813141,'<p><span style=\"color:rgb(88,88,88);font-family:\'Hiragino Sans GB\', STHeiti, \'微软雅黑\', \'Microsoft YaHei\', Helvetica, Arial, serif;background-color:rgb(255,255,255);\">剁椒鱼头又被称作“鸿运当头”。是湘潭的一道名菜。红火剁椒覆盖在白嫩的胖头鱼肉上，完美体现了湘菜的精髓。这道菜一向采用蒸的方式烹制，我们使用收汁功能烹制这道菜可以尽可能保留了鱼的原汁原味，味道更加浓郁</span></p>',35,15),(7,'锅包肉',16,5,90.00,90,90,1,89.00,1493222400,1493395200,'/Public/Upload/Foods/2017-04-29/59043540410f3.jpg','/Public/Upload/Foods/2017-04-29/small_0-59043540410f3.jpg',0,1,0,1,100,0,1493448000,'<p>烹饪技巧</p><p><span style=\"color:rgb(34,34,34);font-family:Consolas, \'Lucida Console\', \'Courier New\', monospace;font-size:15px;white-space:pre-wrap;background-color:rgb(255,255,255);\">炸的时候要有耐心一点一点炸。我放的淀粉少如果想要饭店那样就多放淀粉，我也是第一次做，没有经验。大家多沟通。</span></p>',100,0),(8,'如意白肉卷',18,5,66.00,60,60,0,0.00,0,0,'/Public/Upload/foods/2017-04-29/590436554bd86.jpg','/Public/Upload/foods/2017-04-29/small_0-590436554bd86.jpg',1,0,0,1,98,0,1493448262,'<p>烹饪技巧:<br /></p><p><span style=\"color:rgb(34,34,34);font-family:Consolas, \'Lucida Console\', \'Courier New\', monospace;font-size:15px;white-space:pre-wrap;background-color:rgb(255,255,255);\">1、放山楂是为了让肉能更好的煮透。</span></p><p><span style=\"color:rgb(34,34,34);font-family:Consolas, \'Lucida Console\', \'Courier New\', monospace;font-size:15px;white-space:pre-wrap;background-color:rgb(255,255,255);\">2、用筷子扎一下，来确定肉是否煮透;但要注意的是不要煮过头，那样口感就不好了。 </span></p>',100,0),(9,'酥焖鲫鱼',22,7,100.00,100,100,0,0.00,0,0,'/Public/Upload/Foods/2017-04-29/59043731573e5.jpg','/Public/Upload/Foods/2017-04-29/small_0-59043731573e5.jpg',0,1,1,1,95,0,1493448497,'<p><span style=\"color:rgb(34,34,34);font-family:Consolas, \'Lucida Console\', \'Courier New\', monospace;font-size:15px;white-space:pre-wrap;background-color:rgb(255,255,255);\">这道菜基本做出了我想要的口味儿，酸甜咸香酥，主要是肉很入味儿，吃起来刺也都软了。稍稍不恰当的就是鱼用的比较大，要是鱼再稍小些，就更加完美了。</span></p>',100,0),(10,'酥焖鲫鱼',22,7,100.00,100,100,0,0.00,0,0,'/Public/Upload/Foods/2017-04-29/590437361af8b.jpg','/Public/Upload/Foods/2017-04-29/small_0-590437361af8b.jpg',0,0,0,1,95,1,1493448502,'<p><span style=\"color:rgb(34,34,34);font-family:Consolas, \'Lucida Console\', \'Courier New\', monospace;font-size:15px;white-space:pre-wrap;background-color:rgb(255,255,255);\">这道菜基本做出了我想要的口味儿，酸甜咸香酥，主要是肉很入味儿，吃起来刺也都软了。稍稍不恰当的就是鱼用的比较大，要是鱼再稍小些，就更加完美了。</span></p>',100,0),(13,'花雕熏鱼',20,7,56.00,55,55,0,0.00,0,0,'/Public/Upload/foods/2017-04-29/59043843128f3.jpg','/Public/Upload/foods/2017-04-29/small_0-59043843128f3.jpg',0,1,1,1,100,0,1493448760,'<p><span style=\"color:rgb(34,34,34);font-family:Consolas, \'Lucida Console\', \'Courier New\', monospace;font-size:15px;white-space:pre-wrap;background-color:rgb(255,255,255);\">　　做花雕熏鱼，用的花雕酒越好当然味道就越正了，八年陈酿六十多块钱，五年陈酿的二十八块多，三年陈酿好像十多块钱。我取中买了瓶五年陈酿的，味道足够了，用加饭酒做更经济实惠。 </span></p>',410,0);
/*!40000 ALTER TABLE `yd_foods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_foods_cate`
--

DROP TABLE IF EXISTS `yd_foods_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_foods_cate` (
  `cate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(20) NOT NULL COMMENT '分类名称',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类的ID，0：代表顶级',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COMMENT='菜品分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_foods_cate`
--

LOCK TABLES `yd_foods_cate` WRITE;
/*!40000 ALTER TABLE `yd_foods_cate` DISABLE KEYS */;
INSERT INTO `yd_foods_cate` VALUES (1,'常见菜式',0),(2,'菜系',0),(3,'口味',0),(4,'场合',0),(5,'烹饪方式',0),(6,'家常菜',1),(7,'素菜',1),(8,'荤菜',1),(9,'凉菜',1),(10,'下饭菜',1),(11,'面食',1),(12,'小吃',1),(13,'糕点',1),(14,'甜点',1),(15,'川菜',2),(16,'东北菜',2),(17,'粤菜',2),(18,'湘菜',2),(19,'鲁菜',2),(20,'淮扬菜',2),(21,'闽菜',2),(22,'浙菜',2),(23,'徽菜',2),(24,'贵州菜',2),(25,'清淡',3),(26,'咖喱',3),(27,'麻辣',3),(28,'辣',3),(29,'香辣',3),(30,'甜',3),(31,'酸辣',3),(32,'孜然',3),(33,'酸',3),(34,'酸甜',3),(35,'早餐',4),(36,'晚餐',4),(37,'下午茶',4),(38,'宵夜',4),(39,'中餐',4),(40,'拌',5),(41,'蒸',5),(42,'红烧',5),(43,'煮',5),(44,'煎',5),(45,'炸',5),(46,'卤',5),(47,'烤',5),(48,'焖',5),(55,'二人世界',4),(56,'快餐',4),(57,'快手菜',4),(58,'海鲜',1);
/*!40000 ALTER TABLE `yd_foods_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_foods_expand_cate`
--

DROP TABLE IF EXISTS `yd_foods_expand_cate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_foods_expand_cate` (
  `foods_id` mediumint(8) unsigned NOT NULL COMMENT '菜品id',
  `cate_id` smallint(5) unsigned NOT NULL COMMENT '分类id',
  `restaurant_id` smallint(5) unsigned NOT NULL COMMENT '餐厅id',
  KEY `goods_id` (`foods_id`),
  KEY `cate_id` (`cate_id`),
  KEY `restaurant_id` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜品扩展分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_foods_expand_cate`
--

LOCK TABLES `yd_foods_expand_cate` WRITE;
/*!40000 ALTER TABLE `yd_foods_expand_cate` DISABLE KEYS */;
INSERT INTO `yd_foods_expand_cate` VALUES (1,6,7),(1,29,7),(1,36,7),(1,40,7),(4,8,5),(4,27,5),(4,43,5),(5,6,5),(5,40,5),(5,9,5),(2,27,7),(2,48,7),(7,25,5),(8,9,5),(8,40,5),(10,33,7),(10,36,7),(9,33,7),(9,36,7),(13,36,7),(13,8,7),(13,43,7),(3,29,7),(3,6,7),(3,36,7),(3,43,7),(6,48,5),(6,29,5),(6,8,5);
/*!40000 ALTER TABLE `yd_foods_expand_cate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_foods_pics`
--

DROP TABLE IF EXISTS `yd_foods_pics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_foods_pics` (
  `foods_pic_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pic` varchar(150) NOT NULL COMMENT '图片',
  `sm_pic` varchar(150) NOT NULL COMMENT '缩略图',
  `foods_id` mediumint(8) unsigned NOT NULL COMMENT '菜品的id',
  PRIMARY KEY (`foods_pic_id`),
  KEY `foods_id` (`foods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='菜品图片';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_foods_pics`
--

LOCK TABLES `yd_foods_pics` WRITE;
/*!40000 ALTER TABLE `yd_foods_pics` DISABLE KEYS */;
INSERT INTO `yd_foods_pics` VALUES (1,'/Public/Upload/FoodsPic/2017-04-10/58eb35e38198e.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb35e38198e.jpg',1),(2,'/Public/Upload/FoodsPic/2017-04-10/58eb35e395e6e.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb35e395e6e.jpg',1),(3,'/Public/Upload/FoodsPic/2017-04-10/58eb35e3a9b8d.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb35e3a9b8d.jpg',1),(4,'/Public/Upload/FoodsPic/2017-04-10/58eb3826bf757.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb3826bf757.jpg',2),(5,'/Public/Upload/FoodsPic/2017-04-10/58eb3826cc08d.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb3826cc08d.jpg',2),(6,'/Public/Upload/FoodsPic/2017-04-10/58eb3826d8900.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb3826d8900.jpg',2),(7,'/Public/Upload/FoodsPic/2017-04-10/58eb39bc0a2b9.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb39bc0a2b9.jpg',3),(8,'/Public/Upload/FoodsPic/2017-04-10/58eb39bc1661b.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb39bc1661b.jpg',3),(9,'/Public/Upload/FoodsPic/2017-04-10/58eb39bc22f82.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb39bc22f82.jpg',3),(10,'/Public/Upload/FoodsPic/2017-04-10/58eb3ea90b8d7.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb3ea90b8d7.jpg',4),(11,'/Public/Upload/FoodsPic/2017-04-10/58eb3ea918c76.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb3ea918c76.jpg',4),(12,'/Public/Upload/FoodsPic/2017-04-10/58eb424badbda.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb424badbda.jpg',5),(13,'/Public/Upload/FoodsPic/2017-04-10/58eb424bb7fda.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb424bb7fda.jpg',5),(14,'/Public/Upload/FoodsPic/2017-04-10/58eb424bc1d65.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb424bc1d65.jpg',5),(15,'/Public/Upload/FoodsPic/2017-04-10/58eb43158055f.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb43158055f.jpg',6),(16,'/Public/Upload/FoodsPic/2017-04-10/58eb43158d5e7.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb43158d5e7.jpg',6),(17,'/Public/Upload/FoodsPic/2017-04-10/58eb431599fa2.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb431599fa2.jpg',6),(18,'/Public/Upload/FoodsPic/2017-04-10/58eb4315a690c.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb4315a690c.jpg',6),(19,'/Public/Upload/FoodsPic/2017-04-10/58eb4315b2d4a.jpg','/Public/Upload/FoodsPic/2017-04-10/small_0-58eb4315b2d4a.jpg',6),(20,'/Public/Upload/FoodsPic/2017-04-29/5904354096988.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-5904354096988.jpg',7),(21,'/Public/Upload/FoodsPic/2017-04-29/59043540b5a07.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043540b5a07.jpg',7),(22,'/Public/Upload/FoodsPic/2017-04-29/59043646becbe.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043646becbe.jpg',8),(23,'/Public/Upload/FoodsPic/2017-04-29/59043646dea32.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043646dea32.jpg',8),(24,'/Public/Upload/FoodsPic/2017-04-29/5904364706b6e.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-5904364706b6e.jpg',8),(25,'/Public/Upload/FoodsPic/2017-04-29/590436471c753.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-590436471c753.jpg',8),(26,'/Public/Upload/FoodsPic/2017-04-29/59043733af021.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043733af021.jpg',9),(27,'/Public/Upload/FoodsPic/2017-04-29/59043733ee57c.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043733ee57c.jpg',9),(28,'/Public/Upload/FoodsPic/2017-04-29/590437342f258.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-590437342f258.jpg',9),(29,'/Public/Upload/FoodsPic/2017-04-29/590437384edc1.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-590437384edc1.jpg',10),(30,'/Public/Upload/FoodsPic/2017-04-29/590437391e053.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-590437391e053.jpg',10),(31,'/Public/Upload/FoodsPic/2017-04-29/590437397447a.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-590437397447a.jpg',10),(35,'/Public/Upload/FoodsPic/2017-04-29/59043838e4d86.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043838e4d86.jpg',13),(36,'/Public/Upload/FoodsPic/2017-04-29/5904383902a0a.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-5904383902a0a.jpg',13),(37,'/Public/Upload/FoodsPic/2017-04-29/59043839206f1.jpg','/Public/Upload/FoodsPic/2017-04-29/small_0-59043839206f1.jpg',13);
/*!40000 ALTER TABLE `yd_foods_pics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_guestbook`
--

DROP TABLE IF EXISTS `yd_guestbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_guestbook` (
  `gb_id` int(12) NOT NULL AUTO_INCREMENT,
  `member_id` int(10) NOT NULL COMMENT '留言者会员id',
  `restaurant_id` int(10) NOT NULL COMMENT '餐厅id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '主题',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '留言者姓名',
  `face` varchar(150) NOT NULL DEFAULT '1.gif' COMMENT '留言者头像',
  `email` varchar(200) NOT NULL DEFAULT '' COMMENT '邮箱',
  `qq` varchar(100) NOT NULL DEFAULT '',
  `mood` varchar(150) NOT NULL DEFAULT '' COMMENT '留言者心情',
  `body` text COMMENT '留言主体内容',
  `reply` text COMMENT '餐厅回复',
  `addtime` int(10) DEFAULT '0' COMMENT '留言时间',
  `replytime` int(11) DEFAULT '0',
  `ip` varchar(26) NOT NULL DEFAULT '' COMMENT '留言者IP地址',
  `ifreply` int(1) DEFAULT '0' COMMENT '是否回复',
  PRIMARY KEY (`gb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='用户留言表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_guestbook`
--

LOCK TABLES `yd_guestbook` WRITE;
/*!40000 ALTER TABLE `yd_guestbook` DISABLE KEYS */;
INSERT INTO `yd_guestbook` VALUES (5,3,7,'再便宜点就好了','小刚','2.gif','178315889@qq.com','12345632','s11','再便宜点就好了再便宜点就好了','好的好的',1492844737,1492863644,'127.0.0.1',1),(6,3,7,'好happy','小蒋','9.gif','1273398724@qq.com','12345645','s18','好便宜！好便宜！好便宜！好便宜！','多谢多谢多谢多谢',1492845629,1492863715,'127.0.0.1',1),(9,3,7,'还不错','小强','1.gif','734674018@qq.com','1273398724','s4','可以可以','欢迎下次光临',1492866404,1492866663,'127.0.0.1',1),(11,3,5,'不错不错','小红','2.gif','747751602@qq.com','12345645','s1','不错不错','欧克欧克',1492866807,1492916754,'127.0.0.1',1),(12,35,5,'魔侠传之唐吉可德','admin','1.gif','747751602@qq.com','12345645','s11','122232454',NULL,1493871179,0,'127.0.0.1',0);
/*!40000 ALTER TABLE `yd_guestbook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_impression`
--

DROP TABLE IF EXISTS `yd_impression`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_impression` (
  `imp_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `imp_name` varchar(30) NOT NULL COMMENT '印象的标题',
  `imp_count` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '印象出现的次数',
  `foods_id` mediumint(8) unsigned NOT NULL COMMENT '菜品ID',
  PRIMARY KEY (`imp_id`),
  KEY `foods_id` (`foods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='订餐用户印象表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_impression`
--

LOCK TABLES `yd_impression` WRITE;
/*!40000 ALTER TABLE `yd_impression` DISABLE KEYS */;
INSERT INTO `yd_impression` VALUES (1,'美味',1,1),(2,'味道不错',1,4),(3,'1',1,4),(4,'121',3,4),(5,'21',1,4),(6,'45',1,4),(7,'111',1,5),(8,'111',1,4),(9,'good',1,2),(10,'123',1,4),(11,'242',1,1),(12,'不错',1,1),(13,'还可以',1,1),(14,'11',1,1),(15,'呵呵呵',1,1),(16,'1331',1,1),(17,'231',1,1),(18,'131',2,1),(19,'31',1,1),(20,'切切',1,1),(21,'131q',1,1),(22,'5',1,1),(23,'test',1,1),(24,'test1',1,1),(25,'good',1,3),(26,'313',1,4),(27,'11',1,4),(28,'777',1,5),(29,'ggg',1,1),(30,'11',1,6);
/*!40000 ALTER TABLE `yd_impression` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_manager`
--

DROP TABLE IF EXISTS `yd_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_manager` (
  `manager_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `manager_name` varchar(30) NOT NULL COMMENT '管理员账号',
  `password` char(32) NOT NULL COMMENT '管理员密码',
  `is_use` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用 1：启用0：禁用',
  PRIMARY KEY (`manager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_manager`
--

LOCK TABLES `yd_manager` WRITE;
/*!40000 ALTER TABLE `yd_manager` DISABLE KEYS */;
INSERT INTO `yd_manager` VALUES (1,'admin','d20289f0da3f92761dbc8a7c07e7df42',1),(2,'bob','d20289f0da3f92761dbc8a7c07e7df42',1),(3,'Hellen','d20289f0da3f92761dbc8a7c07e7df42',1);
/*!40000 ALTER TABLE `yd_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_manager_role`
--

DROP TABLE IF EXISTS `yd_manager_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_manager_role` (
  `manager_id` tinyint(3) unsigned NOT NULL COMMENT '管理员的id',
  `role_id` smallint(5) unsigned NOT NULL COMMENT '角色的id',
  KEY `manager_id` (`manager_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_manager_role`
--

LOCK TABLES `yd_manager_role` WRITE;
/*!40000 ALTER TABLE `yd_manager_role` DISABLE KEYS */;
INSERT INTO `yd_manager_role` VALUES (1,1),(2,3),(3,2);
/*!40000 ALTER TABLE `yd_manager_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_member`
--

DROP TABLE IF EXISTS `yd_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_member` (
  `member_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL COMMENT '会员账号，邮箱',
  `nickname` varchar(15) NOT NULL DEFAULT '',
  `password` char(32) NOT NULL COMMENT '密码',
  `face` varchar(150) NOT NULL DEFAULT '' COMMENT '头像',
  `sm_face` varchar(150) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '地址',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `addtime` int(10) unsigned NOT NULL COMMENT '注册时间',
  `email_code` char(32) NOT NULL DEFAULT '' COMMENT '邮件验证的验证码，当会员验证通过之后，会把这个字段清空，所以如果这个字段为空就说明会员已经通过email验证了',
  `points` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `xp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',
  `is_use` tinyint(2) NOT NULL DEFAULT '1',
  `surplus_money` int(10) NOT NULL DEFAULT '10000',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='会员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_member`
--

LOCK TABLES `yd_member` WRITE;
/*!40000 ALTER TABLE `yd_member` DISABLE KEYS */;
INSERT INTO `yd_member` VALUES (3,'1273398724@qq.com','小明','cc867572202ebd8cda14cf40c6145ba0','/Public/Home/Images/Face/1.gif','/Public/Home/Images/Face/1.gif','西科大新区东6e1111而是','18380581435',1490157242,'',11750,11750,1,8860),(5,'734674018@qq.com','','cc867572202ebd8cda14cf40c6145ba0','','','','',1490950987,'',240,240,1,9760),(35,'747751602@qq.com','小海','cc867572202ebd8cda14cf40c6145ba0','','','西科大','18380581435',1491024254,'',20,20,1,9965),(36,'178315889@qq.com','','cc867572202ebd8cda14cf40c6145ba0','','','','',1491024331,'',0,0,1,10000);
/*!40000 ALTER TABLE `yd_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_member_address`
--

DROP TABLE IF EXISTS `yd_member_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_member_address` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `address` varchar(100) NOT NULL COMMENT '收货地址',
  `member_id` int(10) unsigned NOT NULL COMMENT '订餐会员id',
  `receiver` varchar(20) NOT NULL COMMENT '收货人姓名',
  `receiver_mobile` varchar(11) NOT NULL COMMENT '收货人手机号码',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8 COMMENT='订餐用户地址表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_member_address`
--

LOCK TABLES `yd_member_address` WRITE;
/*!40000 ALTER TABLE `yd_member_address` DISABLE KEYS */;
INSERT INTO `yd_member_address` VALUES (3,'绵阳西科大新区121',3,'夏明131','13086565665',1),(151,'3213',3,'12313','13086565665',0),(152,'31231',3,'113123','13086565665',0),(153,'1111111',5,'111111','18380581437',1),(154,'西科大新区东6e1111而是',35,'1231','18380581437',1);
/*!40000 ALTER TABLE `yd_member_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_member_level`
--

DROP TABLE IF EXISTS `yd_member_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_member_level` (
  `level_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(30) NOT NULL COMMENT '级别名称',
  `bottom_num` int(10) unsigned NOT NULL COMMENT '积分下限',
  `top_num` int(10) unsigned NOT NULL COMMENT '积分上限',
  `rate` tinyint(3) unsigned NOT NULL DEFAULT '100' COMMENT '折扣率，以百分比，如：9折=90',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='会员级别';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_member_level`
--

LOCK TABLES `yd_member_level` WRITE;
/*!40000 ALTER TABLE `yd_member_level` DISABLE KEYS */;
INSERT INTO `yd_member_level` VALUES (1,'普通会员',0,5000,100),(2,'白银会员',5000,10000,96),(3,'黄金会员',10000,50000,90),(4,'白金会员',50000,100000,85),(5,'钻石会员',100000,2000000,80),(6,'黑卡会员',2000000,4000000,75);
/*!40000 ALTER TABLE `yd_member_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_member_price`
--

DROP TABLE IF EXISTS `yd_member_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_member_price` (
  `foods_id` mediumint(8) unsigned NOT NULL COMMENT '菜品id',
  `level_id` mediumint(8) unsigned NOT NULL COMMENT '级别id',
  `price` decimal(10,2) NOT NULL COMMENT '这个级别的价格',
  KEY `foods_id` (`foods_id`),
  KEY `level_id` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员级别价格表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_member_price`
--

LOCK TABLES `yd_member_price` WRITE;
/*!40000 ALTER TABLE `yd_member_price` DISABLE KEYS */;
INSERT INTO `yd_member_price` VALUES (1,6,23.00),(1,5,23.00),(1,4,24.00),(1,3,24.00),(1,2,25.00),(1,1,25.00),(4,1,60.00),(4,2,60.00),(4,3,58.00),(4,4,55.00),(4,5,53.00),(4,6,50.00),(5,1,50.00),(5,2,50.00),(5,3,49.00),(5,4,48.00),(5,5,47.00),(5,6,46.00),(2,1,15.00),(2,2,15.00),(2,3,14.00),(2,4,14.00),(2,5,14.00),(2,6,13.00),(8,1,66.00),(8,2,65.00),(8,3,64.00),(10,1,100.00),(10,2,99.00),(10,3,98.00),(10,4,97.00),(9,1,100.00),(9,2,99.00),(9,3,98.00),(9,4,97.00),(13,1,56.00),(13,2,55.00),(13,3,54.00),(13,4,53.00),(3,1,50.00),(3,2,49.00),(3,3,49.00),(3,4,48.00),(3,5,48.00),(3,6,40.00),(6,1,60.00),(6,2,59.00),(6,3,58.00),(6,4,57.00),(6,5,56.00),(6,6,55.00);
/*!40000 ALTER TABLE `yd_member_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_order`
--

DROP TABLE IF EXISTS `yd_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_order` (
  `order_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `addtime` int(10) unsigned NOT NULL COMMENT '下单时间',
  `receiver_name` varchar(30) NOT NULL COMMENT '收货人姓名',
  `receiver_mobile` varchar(30) NOT NULL COMMENT '收货人电话',
  `receiver_address` varchar(30) NOT NULL COMMENT '收货人地址',
  `total_price` decimal(10,2) NOT NULL COMMENT '定单总价',
  `pay_method` varchar(30) NOT NULL COMMENT '支付方式',
  `delivery_method` varchar(100) NOT NULL COMMENT '送货方式',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态，0：未支付 1：已支付 2:取消订单',
  `deliver_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态，0：未发货 1：已发货 2：已收到货',
  PRIMARY KEY (`order_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COMMENT='订单基本信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_order`
--

LOCK TABLES `yd_order` WRITE;
/*!40000 ALTER TABLE `yd_order` DISABLE KEYS */;
INSERT INTO `yd_order` VALUES (1,3,1491809856,'113123','13086565665','31231',25.00,'支付宝支付','送货人员送货上门',1,0),(2,3,1491809960,'夏明131','13086565665','绵阳西科大新区121',50.00,'货到付款','送货人员送货上门',1,0),(3,3,1491810400,'113123','13086565665','31231',15.00,'支付宝支付','送货人员送货上门',1,0),(4,3,1491824199,'113123','13086565665','31231',60.00,'支付宝支付','送货人员送货上门',1,0),(5,3,1492169473,'113123','13086565665','31231',110.00,'支付宝支付','送货人员送货上门',1,0),(6,3,1492175678,'113123','13086565665','31231',110.00,'支付宝支付','送货人员送货上门',1,0),(7,3,1492181407,'113123','13086565665','31231',55.00,'支付宝支付','送货人员送货上门',2,0),(8,3,1492182353,'113123','13086565665','31231',45.00,'支付宝支付','送货人员送货上门',1,0),(9,3,1492188200,'12313','13086565665','3213',45.00,'支付宝支付','送货人员送货上门',1,0),(10,3,1492188235,'夏明131','13086565665','绵阳西科大新区121',48.00,'支付宝支付','送货人员送货上门',1,0),(11,3,1492188277,'12313','13086565665','3213',24.00,'支付宝支付','送货人员送货上门',1,0),(12,3,1492188290,'12313','13086565665','3213',48.00,'支付宝支付','送货人员送货上门',1,0),(13,3,1492188338,'夏明131','13086565665','绵阳西科大新区121',110.00,'支付宝支付','送货人员送货上门',2,0),(14,3,1492188435,'夏明131','13086565665','绵阳西科大新区121',120.00,'支付宝支付','送货人员送货上门',1,0),(15,3,1492188463,'夏明131','13086565665','绵阳西科大新区121',180.00,'支付宝支付','送货人员送货上门',1,0),(16,3,1492188484,'夏明131','13086565665','绵阳西科大新区121',180.00,'支付宝支付','送货人员送货上门',1,0),(17,3,1492188566,'夏明131','13086565665','绵阳西科大新区121',180.00,'支付宝支付','送货人员送货上门',1,0),(18,3,1492188598,'夏明131','13086565665','绵阳西科大新区121',135.00,'支付宝支付','送货人员送货上门',2,0),(19,3,1492188806,'夏明131','13086565665','绵阳西科大新区121',110.00,'支付宝支付','送货人员送货上门',1,0),(20,3,1492188916,'夏明131','13086565665','绵阳西科大新区121',180.00,'支付宝支付','送货人员送货上门',1,0),(21,3,1492189087,'夏明131','13086565665','绵阳西科大新区121',135.00,'支付宝支付','送货人员送货上门',1,0),(22,3,1492189321,'夏明131','13086565665','绵阳西科大新区121',90.00,'支付宝支付','送货人员送货上门',1,0),(24,3,1492190828,'夏明131','13086565665','绵阳西科大新区121',55.00,'支付宝支付','送货人员送货上门',1,0),(25,3,1492191062,'夏明131','13086565665','绵阳西科大新区121',45.00,'支付宝支付','送货人员送货上门',1,0),(26,3,1492191108,'夏明131','13086565665','绵阳西科大新区121',55.00,'支付宝支付','送货人员送货上门',1,0),(27,3,1492225743,'113123','13086565665','31231',55.00,'货到付款','送货人员送货上门',1,0),(28,3,1492225858,'夏明131','13086565665','绵阳西科大新区121',48.00,'货到付款','送货人员送货上门',2,0),(29,3,1492226397,'夏明131','13086565665','绵阳西科大新区121',96.00,'支付宝支付','送货人员送货上门',2,0),(31,3,1492226654,'夏明131','13086565665','绵阳西科大新区121',45.00,'支付宝支付','送货人员送货上门',0,0),(32,3,1492227553,'夏明131','13086565665','绵阳西科大新区121',55.00,'支付宝支付','送货人员送货上门',1,0),(33,3,1492257127,'夏明131','13086565665','绵阳西科大新区121',165.00,'支付宝支付','送货人员送货上门',1,0),(34,3,1492257188,'夏明131','13086565665','绵阳西科大新区121',270.00,'支付宝支付','送货人员送货上门',1,0),(35,3,1492257645,'12313','13086565665','3213',192.00,'货到付款','送货人员送货上门',1,0),(36,5,1492327665,'111111','18380581437','1111111',110.00,'支付宝支付','送货人员送货上门',1,0),(37,5,1492327923,'111111','18380581437','1111111',110.00,'支付宝支付','送货人员送货上门',1,0),(38,3,1492660030,'夏明131','13086565665','绵阳西科大新区121',140.00,'支付宝支付','送货人员送货上门',1,0),(39,3,1492660090,'夏明131','13086565665','绵阳西科大新区121',450.00,'支付宝支付','送货人员送货上门',1,0),(40,3,1492660113,'夏明131','13086565665','绵阳西科大新区121',90.00,'支付宝支付','送货人员送货上门',1,0),(41,3,1492758132,'夏明131','13086565665','绵阳西科大新区121',15.00,'支付宝支付','送货人员送货上门',1,0),(42,3,1492758167,'夏明131','13086565665','绵阳西科大新区121',300.00,'支付宝支付','送货人员送货上门',1,0),(43,3,1492916619,'夏明131','13086565665','绵阳西科大新区121',55.00,'支付宝支付','送货人员送货上门',1,0),(44,3,1492918333,'夏明131','13086565665','绵阳西科大新区121',25.00,'支付宝支付','送货人员送货上门',1,0),(45,3,1492960275,'夏明131','13086565665','绵阳西科大新区121',225.00,'支付宝支付','送货人员送货上门',1,0),(46,3,1493036523,'夏明131','13086565665','绵阳西科大新区121',160.00,'支付宝支付','送货人员送货上门',1,0),(47,3,1493132995,'夏明131','13086565665','绵阳西科大新区121',110.00,'支付宝支付','送货人员送货上门',1,0),(48,3,1493133513,'12313','13086565665','3213',189.00,'支付宝支付','送货人员送货上门',1,0),(49,3,1493367280,'夏明131','13086565665','绵阳西科大新区121',60.00,'支付宝支付','送货人员送货上门',1,0),(50,3,1493367400,'夏明131','13086565665','绵阳西科大新区121',300.00,'支付宝支付','送货人员送货上门',1,0),(51,3,1493367556,'夏明131','13086565665','绵阳西科大新区121',120.00,'支付宝支付','送货人员送货上门',1,0),(52,3,1493367647,'夏明131','13086565665','绵阳西科大新区121',300.00,'支付宝支付','送货人员送货上门',1,0),(53,3,1493367716,'夏明131','13086565665','绵阳西科大新区121',360.00,'支付宝支付','送货人员送货上门',1,0),(54,3,1493367769,'夏明131','13086565665','绵阳西科大新区121',98.00,'支付宝支付','送货人员送货上门',1,0),(55,3,1493367850,'夏明131','13086565665','绵阳西科大新区121',300.00,'支付宝支付','送货人员送货上门',1,0),(56,3,1492792600,'夏明131','13086565665','绵阳西科大新区121',50.00,'支付宝支付','送货人员送货上门',1,0),(57,3,1492879055,'夏明131','13086565665','绵阳西科大新区121',364.00,'支付宝支付','送货人员送货上门',1,0),(58,3,1492965502,'夏明131','13086565665','绵阳西科大新区121',2400.00,'支付宝支付','送货人员送货上门',2,0),(59,3,1492965923,'夏明131','13086565665','绵阳西科大新区121',2400.00,'支付宝支付','送货人员送货上门',2,0),(60,3,1492965949,'夏明131','13086565665','绵阳西科大新区121',960.00,'支付宝支付','送货人员送货上门',1,0),(61,3,1493052390,'夏明131','13086565665','绵阳西科大新区121',1100.00,'支付宝支付','送货人员送货上门',1,0),(62,3,1493138817,'夏明131','13086565665','绵阳西科大新区121',60.00,'支付宝支付','送货人员送货上门',1,0),(63,3,1493225237,'夏明131','13086565665','绵阳西科大新区121',75.00,'支付宝支付','送货人员送货上门',0,0),(64,3,1493225249,'夏明131','13086565665','绵阳西科大新区121',25.00,'支付宝支付','送货人员送货上门',1,0),(65,3,1493398729,'夏明131','13086565665','绵阳西科大新区121',25.00,'支付宝支付','送货人员送货上门',1,0),(66,3,1493342035,'夏明131','13086565665','绵阳西科大新区121',164.00,'支付宝支付','送货人员送货上门',1,0),(67,3,1493350057,'夏明131','13086565665','绵阳西科大新区121',580.00,'支付宝支付','送货人员送货上门',1,0),(68,3,1493436903,'夏明131','13086565665','绵阳西科大新区121',58.00,'支付宝支付','送货人员送货上门',1,0),(69,3,1493350666,'夏明131','13086565665','绵阳西科大新区121',116.00,'支付宝支付','送货人员送货上门',1,0),(70,3,1493566581,'夏明131','13086565665','绵阳西科大新区121',24.00,'支付宝支付','送货人员送货上门',1,0),(71,3,1493566764,'夏明131','13086565665','绵阳西科大新区121',58.00,'支付宝支付','送货人员送货上门',1,0),(72,3,1493876415,'夏明131','13086565665','绵阳西科大新区121',24.00,'支付宝支付','送货人员送货上门',0,0),(73,35,1494582203,'1231','18380581437','西科大新区东6e1111而是',25.00,'支付宝支付','送货人员送货上门',1,0);
/*!40000 ALTER TABLE `yd_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_order_foods`
--

DROP TABLE IF EXISTS `yd_order_foods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_order_foods` (
  `order_id` mediumint(8) unsigned NOT NULL COMMENT '订单id',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '会员id',
  `foods_id` mediumint(8) unsigned NOT NULL COMMENT '菜品ID',
  `foods_price` decimal(10,2) NOT NULL COMMENT '菜品的价格',
  `foods_num` int(10) unsigned NOT NULL COMMENT '购买的数量',
  KEY `order_id` (`order_id`),
  KEY `foods_id` (`foods_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_order_foods`
--

LOCK TABLES `yd_order_foods` WRITE;
/*!40000 ALTER TABLE `yd_order_foods` DISABLE KEYS */;
INSERT INTO `yd_order_foods` VALUES (1,3,1,25.00,1),(2,3,1,25.00,2),(3,3,2,15.00,1),(4,3,6,60.00,1),(5,3,4,55.00,2),(6,3,4,55.00,2),(7,3,4,55.00,1),(8,3,5,45.00,1),(9,3,5,45.00,1),(10,3,3,48.00,1),(11,3,1,24.00,1),(12,3,1,24.00,2),(13,3,4,55.00,2),(14,3,6,60.00,2),(15,3,5,45.00,4),(16,3,5,45.00,4),(17,3,5,45.00,4),(18,3,5,45.00,3),(19,3,4,55.00,2),(20,3,6,60.00,3),(21,3,5,45.00,3),(22,3,5,45.00,2),(24,3,4,55.00,1),(25,3,5,45.00,1),(26,3,4,55.00,1),(27,3,4,55.00,1),(28,3,3,48.00,1),(29,3,3,48.00,2),(31,3,5,45.00,1),(32,3,4,55.00,1),(33,3,4,55.00,3),(34,3,5,45.00,6),(35,3,3,48.00,4),(36,5,4,55.00,2),(37,5,4,55.00,2),(38,3,5,45.00,2),(38,3,1,25.00,2),(39,3,5,45.00,10),(40,3,5,45.00,2),(41,3,2,15.00,1),(42,3,2,15.00,20),(43,3,4,55.00,1),(44,3,1,25.00,1),(45,3,5,45.00,5),(46,3,1,25.00,2),(46,3,4,55.00,2),(47,3,4,55.00,2),(48,3,3,48.00,3),(48,3,5,45.00,1),(49,3,4,60.00,1),(50,3,4,60.00,5),(51,3,4,60.00,2),(52,3,4,60.00,5),(53,3,4,60.00,6),(54,3,3,49.00,2),(55,3,4,60.00,5),(56,3,1,25.00,2),(57,3,3,48.00,3),(57,3,4,55.00,4),(58,3,3,48.00,50),(59,3,3,48.00,50),(60,3,3,48.00,20),(61,3,4,55.00,20),(62,3,2,15.00,4),(63,3,1,25.00,3),(64,3,1,25.00,1),(65,3,1,25.00,1),(66,3,6,58.00,2),(66,3,1,24.00,2),(67,3,6,58.00,10),(68,3,6,58.00,1),(69,3,6,58.00,2),(70,3,1,24.00,1),(71,3,4,58.00,1),(72,3,1,24.00,1),(73,35,1,25.00,1);
/*!40000 ALTER TABLE `yd_order_foods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_privilege`
--

DROP TABLE IF EXISTS `yd_privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_privilege` (
  `pri_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pri_name` varchar(30) NOT NULL COMMENT '权限名称',
  `module_name` varchar(30) NOT NULL COMMENT '模块名称',
  `controller_name` varchar(30) NOT NULL COMMENT '控制器名称',
  `action_name` varchar(30) NOT NULL COMMENT '方法名称',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上级权限的id，0：代表顶级权限',
  PRIMARY KEY (`pri_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COMMENT='权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_privilege`
--

LOCK TABLES `yd_privilege` WRITE;
/*!40000 ALTER TABLE `yd_privilege` DISABLE KEYS */;
INSERT INTO `yd_privilege` VALUES (1,'菜品管理','null','null','null',0),(2,'菜品列表','Admin','Manager','foodsList',1),(4,'菜品分类列表','Admin','FoodsCate','foodsCateList',44),(7,'餐厅管理','null','null','null',0),(8,'餐厅列表','Admin','Manager','restaurantList',7),(10,'餐厅菜品评论列表','Admin','Manager','restaurantFoodsCommentList',7),(12,'权限管理','null','null','null',0),(13,'权限列表','Admin','Privilege','privilegeList',12),(14,'角色列表','Admin','Role','roleList',12),(15,'管理员列表','Admin','Manager','managerList',12),(17,'Ajax改变管理员账号是否禁用','Admin','Manager','ajaxChangeIsuse',15),(18,'权限添加','Admin','Privilege','privilegeAdd',13),(19,'权限修改','Admin','Privilege','privilegeEdit',13),(20,'权限删除','Admin','Privilege','privilegeDelete',13),(21,'角色添加','Admin','Role','roleAdd',14),(22,'角色修改','Admin','Role','roleEdit',14),(23,'角色删除','Admin','Role','roleDelete',14),(24,'管理员添加','Admin','Manager','managerAdd',15),(25,'管理员修改','Admin','Manager','managerEdit',15),(26,'管理员删除','Admin','Manager','managerDelete',15),(28,'菜品修改','Admin','Foods','foodsEdit',2),(29,'菜品删除','Admin','Foods','foodsDelete',2),(30,'菜品分类添加','Admin','FoodsCate','foodsCateAdd',4),(31,'菜品分类修改','Admin','FoodsCate','foodsCateEdit',4),(32,'菜品分类删除','Admin','FoodsCate','foodsCateDelete',4),(36,'会员管理','null','null','null',0),(37,'订单管理','null','null','null',0),(38,'会员等级列表','Admin','MemberLevel','memberLevelList',36),(39,'会员等级添加','Admin','MemberLevel','memberLevelAdd',38),(40,'会员列表','Admin','Member','memberList',36),(42,'订单列表','Admin','Order','orderList',37),(43,'餐厅激活','Admin','Manager','applyConfirmByAjax',8),(44,'菜品分类管理','null','null','null',0),(45,'会员等级修改','Admin','MemberLevel','memberLevelEdit',38),(46,'会员等级删除','Admin','MemberLevel','memberLevelDelete',38),(48,'会员添加','Admin','Member','memberAdd',40),(49,'会员修改','Admin','Member','memberEdit',40),(51,'会员删除','Admin','Member','memberDelete',40),(52,'Ajax改变会员账号是否禁用','Admin','Member','ajaxChangeIsuse',40),(53,'餐厅删除','Admin','Manager','restaurantDelete',8),(54,'餐厅编辑','Admin','Manager','restaurantEdit',8),(55,'管理员中心','null','null','null',0),(56,'修改密码','Admin','Manager','managerPwdModify',55),(57,'删除菜品评论','Admin','Manager','restaurantFoodsCommentDelete',10),(58,'修改菜品评论','Admin','Manager','restaurantFoodsCommentEdit',10),(59,'删除订单','Admin','Order','orderDelete',42),(60,'菜品销售记录','Admin','Order','foodsSalesRecord',37);
/*!40000 ALTER TABLE `yd_privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_reply`
--

DROP TABLE IF EXISTS `yd_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_reply` (
  `reply_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(1000) NOT NULL COMMENT '回复的内容',
  `addtime` int(10) unsigned NOT NULL COMMENT '回复时间',
  `member_id` mediumint(8) unsigned NOT NULL COMMENT '订餐用户会员ID',
  `comment_id` mediumint(8) unsigned NOT NULL COMMENT '评论的ID',
  PRIMARY KEY (`reply_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='餐厅回复';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_reply`
--

LOCK TABLES `yd_reply` WRITE;
/*!40000 ALTER TABLE `yd_reply` DISABLE KEYS */;
INSERT INTO `yd_reply` VALUES (1,'okok',0,3,23),(2,'好好',0,3,23),(3,'1111',1492950084,3,23),(4,'哦哦哦',1492951510,35,10),(5,'好的',1492952110,35,10),(6,'1111',1492952156,35,6),(7,'还可以',1492959868,3,24),(8,'可以',1492959884,3,24),(9,'11',1492959969,3,24),(10,'可以',1492960066,3,24),(11,'23',1492960096,3,24),(12,'111',1492960178,3,24),(13,'11',1492960201,3,24),(14,'777',1494255281,3,24);
/*!40000 ALTER TABLE `yd_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_restaurant`
--

DROP TABLE IF EXISTS `yd_restaurant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_restaurant` (
  `restaurant_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_name` varchar(60) NOT NULL COMMENT '餐厅登录名称',
  `password` char(32) NOT NULL COMMENT '密码',
  `person_leader` varchar(20) NOT NULL DEFAULT '' COMMENT '餐厅负责人',
  `restaurant_email` varchar(60) NOT NULL COMMENT '餐厅邮箱',
  `restaurant_mobile` char(11) NOT NULL DEFAULT '' COMMENT '餐厅手机号',
  `restaurant_tel` varchar(10) NOT NULL DEFAULT '' COMMENT '餐厅固定号',
  `restaurant_address` varchar(100) NOT NULL DEFAULT '' COMMENT '餐厅地址',
  `addtime` int(10) unsigned NOT NULL COMMENT '餐厅注册时间',
  `email_code` char(32) NOT NULL DEFAULT '' COMMENT '邮件验证的验证码，餐厅验证通过之后，会把这个字段清空，所以如果这个字段为空就说明餐厅已经通过email验证了',
  `restaurant_state` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '餐厅状态 0：未注册 1：餐厅已激活 2:餐厅为通过管理员验证',
  `restaurant_logo` varchar(100) DEFAULT NULL COMMENT '餐厅logo',
  `sm_restaurant_logo` varchar(100) DEFAULT NULL,
  `houston` int(10) NOT NULL DEFAULT '0' COMMENT '卖家进账',
  `totalsales` int(10) NOT NULL DEFAULT '0' COMMENT '餐厅总销量',
  PRIMARY KEY (`restaurant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='餐厅表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_restaurant`
--

LOCK TABLES `yd_restaurant` WRITE;
/*!40000 ALTER TABLE `yd_restaurant` DISABLE KEYS */;
INSERT INTO `yd_restaurant` VALUES (5,'好周到餐厅','cc867572202ebd8cda14cf40c6145ba0','小鸟','1273398724@qq.com','18380591433','7318110','北京',1490935409,'',1,'/Public/Upload/Restaurant/2017-04-02/58e0e5ed0071c.gif','/Public/Upload/Restaurant/2017-04-02/small_0-58e0e5ed0071c.gif',8773,113),(7,'GoodsLucy','cc867572202ebd8cda14cf40c6145ba0','Mary','1273398724@qq.com','18380581435','7318108','四川绵阳涪江',1490936558,'',1,'/Public/Upload/Restaurant/2017-04-01/58df4e7c443f7.png','/Public/Upload/Restaurant/2017-04-01/small_0-58df4e7c443f7.png',2710,71),(9,'好运来','cc867572202ebd8cda14cf40c6145ba0','','1273398724@qq.com','','','',1492262879,'',1,NULL,NULL,0,0);
/*!40000 ALTER TABLE `yd_restaurant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_restaurant_sales`
--

DROP TABLE IF EXISTS `yd_restaurant_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_restaurant_sales` (
  `restaurant_id` int(10) unsigned NOT NULL,
  `foods_id` int(10) unsigned NOT NULL COMMENT '餐厅菜品名',
  `num` int(10) unsigned NOT NULL COMMENT '菜品销售额',
  `sales_time` int(11) unsigned NOT NULL COMMENT '销售时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='餐厅销售情况表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_restaurant_sales`
--

LOCK TABLES `yd_restaurant_sales` WRITE;
/*!40000 ALTER TABLE `yd_restaurant_sales` DISABLE KEYS */;
INSERT INTO `yd_restaurant_sales` VALUES (5,4,10,1493367771),(5,3,2,1493367772),(5,4,5,1493367854),(7,1,2,1492792604),(5,3,3,1492879059),(5,4,4,1492879059),(5,3,20,1492965952),(5,4,23,1493052393),(5,2,4,1493138820),(5,1,10,1493225252),(7,1,1,1493398793),(7,6,14,1493342038),(7,1,4,1493342038),(5,6,3,1493436906),(7,1,1,1493566584),(5,4,1,1493566767),(7,1,1,1494582207);
/*!40000 ALTER TABLE `yd_restaurant_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_role`
--

DROP TABLE IF EXISTS `yd_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_role` (
  `role_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) NOT NULL COMMENT '角色名称',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_role`
--

LOCK TABLES `yd_role` WRITE;
/*!40000 ALTER TABLE `yd_role` DISABLE KEYS */;
INSERT INTO `yd_role` VALUES (1,'超级管理员'),(2,'高级管理员'),(3,'普通管理员');
/*!40000 ALTER TABLE `yd_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yd_role_privilege`
--

DROP TABLE IF EXISTS `yd_role_privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yd_role_privilege` (
  `pri_id` smallint(5) unsigned NOT NULL COMMENT '权限的id',
  `role_id` smallint(5) unsigned NOT NULL COMMENT '角色的id',
  KEY `pri_id` (`pri_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yd_role_privilege`
--

LOCK TABLES `yd_role_privilege` WRITE;
/*!40000 ALTER TABLE `yd_role_privilege` DISABLE KEYS */;
INSERT INTO `yd_role_privilege` VALUES (41,2),(40,2),(39,2),(38,2),(36,2),(9,3),(7,3),(3,3),(1,3),(8,2),(7,2),(56,2),(55,2),(4,2),(1,1),(2,1),(28,1),(29,1),(7,1),(8,1),(43,1),(53,1),(54,1),(10,1),(57,1),(58,1),(12,1),(13,1),(18,1),(19,1),(20,1),(14,1),(21,1),(22,1),(23,1),(15,1),(17,1),(24,1),(25,1),(26,1),(36,1),(38,1),(39,1),(45,1),(46,1),(40,1),(48,1),(49,1),(51,1),(52,1),(37,1),(42,1),(44,1),(4,1),(30,1),(31,1),(32,1);
/*!40000 ALTER TABLE `yd_role_privilege` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-05-13 11:07:17
