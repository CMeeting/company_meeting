/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : yicms

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2022-05-25 11:18:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for action_logs
-- ----------------------------
DROP TABLE IF EXISTS `action_logs`;
CREATE TABLE `action_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `data` json NOT NULL COMMENT '操作内容',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '操作日志类型,1 权限操作日志, 2 登录操作日志',
  PRIMARY KEY (`id`),
  KEY `action_logs_admin_id_index` (`admin_id`),
  KEY `action_logs_type_index` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of action_logs
-- ----------------------------
INSERT INTO `action_logs` VALUES ('1', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"操作了权限分配模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 05:47:12', '2017-11-20 05:47:12');
INSERT INTO `action_logs` VALUES ('2', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"操作了权限分配模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 05:47:22', '2017-11-20 05:47:22');
INSERT INTO `action_logs` VALUES ('3', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"操作了权限分配模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 05:47:58', '2017-11-20 05:47:58');
INSERT INTO `action_logs` VALUES ('4', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 06:03:08', '2017-11-20 06:03:08');
INSERT INTO `action_logs` VALUES ('5', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 06:03:44', '2017-11-20 06:03:44');
INSERT INTO `action_logs` VALUES ('6', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 删除 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 07:53:25', '2017-11-20 07:53:25');
INSERT INTO `action_logs` VALUES ('7', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 删除 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 07:57:55', '2017-11-20 07:57:55');
INSERT INTO `action_logs` VALUES ('8', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 删除 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 08:05:40', '2017-11-20 08:05:40');
INSERT INTO `action_logs` VALUES ('9', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 14:24:43', '2017-11-20 14:24:43');
INSERT INTO `action_logs` VALUES ('10', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 14:25:03', '2017-11-20 14:25:03');
INSERT INTO `action_logs` VALUES ('13', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 15:13:17', '2017-11-20 15:13:17');
INSERT INTO `action_logs` VALUES ('14', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"科诺设计 操作了 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 15:14:01', '2017-11-20 15:14:01');
INSERT INTO `action_logs` VALUES ('15', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 15:57:34', '2017-11-20 15:57:34');
INSERT INTO `action_logs` VALUES ('16', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-20 16:09:15', '2017-11-20 16:09:15');
INSERT INTO `action_logs` VALUES ('17', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 05:57:46', '2017-11-21 05:57:46');
INSERT INTO `action_logs` VALUES ('18', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 05:57:55', '2017-11-21 05:57:55');
INSERT INTO `action_logs` VALUES ('19', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【操作日志】- 删除日志 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 06:01:49', '2017-11-21 06:01:49');
INSERT INTO `action_logs` VALUES ('20', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【操作日志】- 删除日志 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 06:02:12', '2017-11-21 06:02:12');
INSERT INTO `action_logs` VALUES ('21', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 06:22:06', '2017-11-21 06:22:06');
INSERT INTO `action_logs` VALUES ('22', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 1 ? 登录成功 : 登录失败,登录的账号为：科诺设计　密码为：123456\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:04:23', '2017-11-21 07:04:23');
INSERT INTO `action_logs` VALUES ('23', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 登录成功\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:14:25', '2017-11-21 07:14:25');
INSERT INTO `action_logs` VALUES ('24', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【操作日志】- 删除日志 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:14:37', '2017-11-21 07:14:37');
INSERT INTO `action_logs` VALUES ('25', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 登录成功\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:18:03', '2017-11-21 07:18:03');
INSERT INTO `action_logs` VALUES ('29', null, '{\"ip\": \"127.0.0.1\", \"action\": \" 登录失败,登录的账号为：科诺设计　密码为：aaaa\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:52:10', '2017-11-21 07:52:10');
INSERT INTO `action_logs` VALUES ('30', null, '{\"ip\": \"127.0.0.1\", \"action\": \" 登录失败,登录的账号为：你是傻逼　密码为：aaa\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:53:35', '2017-11-21 07:53:35');
INSERT INTO `action_logs` VALUES ('31', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 登录成功\", \"address\": \"本机地址本机地址\"}', '2017-11-21 07:53:56', '2017-11-21 07:53:56');
INSERT INTO `action_logs` VALUES ('32', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【操作日志】- 删除日志 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:08:28', '2017-11-21 08:08:28');
INSERT INTO `action_logs` VALUES ('33', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:34:30', '2017-11-21 08:34:30');
INSERT INTO `action_logs` VALUES ('34', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:40:58', '2017-11-21 08:40:58');
INSERT INTO `action_logs` VALUES ('35', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:41:10', '2017-11-21 08:41:10');
INSERT INTO `action_logs` VALUES ('36', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:47:09', '2017-11-21 08:47:09');
INSERT INTO `action_logs` VALUES ('37', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:47:20', '2017-11-21 08:47:20');
INSERT INTO `action_logs` VALUES ('38', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:54:08', '2017-11-21 08:54:08');
INSERT INTO `action_logs` VALUES ('39', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:54:10', '2017-11-21 08:54:10');
INSERT INTO `action_logs` VALUES ('40', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 08:58:59', '2017-11-21 08:58:59');
INSERT INTO `action_logs` VALUES ('41', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:00:25', '2017-11-21 09:00:25');
INSERT INTO `action_logs` VALUES ('42', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:02:00', '2017-11-21 09:02:00');
INSERT INTO `action_logs` VALUES ('43', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:02:10', '2017-11-21 09:02:10');
INSERT INTO `action_logs` VALUES ('44', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:02:23', '2017-11-21 09:02:23');
INSERT INTO `action_logs` VALUES ('45', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【角色管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:13:35', '2017-11-21 09:13:35');
INSERT INTO `action_logs` VALUES ('46', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【角色管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:14:13', '2017-11-21 09:14:13');
INSERT INTO `action_logs` VALUES ('47', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【角色管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:16:58', '2017-11-21 09:16:58');
INSERT INTO `action_logs` VALUES ('48', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【角色管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:17:02', '2017-11-21 09:17:02');
INSERT INTO `action_logs` VALUES ('49', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 09:18:31', '2017-11-21 09:18:31');
INSERT INTO `action_logs` VALUES ('50', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 10:43:16', '2017-11-21 10:43:16');
INSERT INTO `action_logs` VALUES ('51', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 10:44:00', '2017-11-21 10:44:00');
INSERT INTO `action_logs` VALUES ('52', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 12:25:07', '2017-11-21 12:25:07');
INSERT INTO `action_logs` VALUES ('53', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 12:33:23', '2017-11-21 12:33:23');
INSERT INTO `action_logs` VALUES ('54', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 12:49:39', '2017-11-21 12:49:39');
INSERT INTO `action_logs` VALUES ('55', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: 科诺设计 操作了 【权限管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2017-11-21 12:49:44', '2017-11-21 12:49:44');
INSERT INTO `action_logs` VALUES ('57', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:16:45', '2022-05-17 12:16:45');
INSERT INTO `action_logs` VALUES ('58', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:23:08', '2022-05-17 12:23:08');
INSERT INTO `action_logs` VALUES ('59', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:23:17', '2022-05-17 12:23:17');
INSERT INTO `action_logs` VALUES ('60', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:23:22', '2022-05-17 12:23:22');
INSERT INTO `action_logs` VALUES ('61', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:23:27', '2022-05-17 12:23:27');
INSERT INTO `action_logs` VALUES ('62', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:28:54', '2022-05-17 12:28:54');
INSERT INTO `action_logs` VALUES ('63', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:29:37', '2022-05-17 12:29:37');
INSERT INTO `action_logs` VALUES ('64', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 删除角色 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:34:50', '2022-05-17 12:34:50');
INSERT INTO `action_logs` VALUES ('65', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:35:45', '2022-05-17 12:35:45');
INSERT INTO `action_logs` VALUES ('66', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:35:59', '2022-05-17 12:35:59');
INSERT INTO `action_logs` VALUES ('67', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:36:38', '2022-05-17 12:36:38');
INSERT INTO `action_logs` VALUES ('68', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:36:52', '2022-05-17 12:36:52');
INSERT INTO `action_logs` VALUES ('69', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:37:19', '2022-05-17 12:37:19');
INSERT INTO `action_logs` VALUES ('70', '3', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: shuwei 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:37:36', '2022-05-17 12:37:36');
INSERT INTO `action_logs` VALUES ('71', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:38:12', '2022-05-17 12:38:12');
INSERT INTO `action_logs` VALUES ('72', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:46:12', '2022-05-17 12:46:12');
INSERT INTO `action_logs` VALUES ('73', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:46:38', '2022-05-17 12:46:38');
INSERT INTO `action_logs` VALUES ('74', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:46:51', '2022-05-17 12:46:51');
INSERT INTO `action_logs` VALUES ('75', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:46:59', '2022-05-17 12:46:59');
INSERT INTO `action_logs` VALUES ('76', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【管理员管理】- 添加数据 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:47:13', '2022-05-17 12:47:13');
INSERT INTO `action_logs` VALUES ('77', '4', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: shuwei 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:47:28', '2022-05-17 12:47:28');
INSERT INTO `action_logs` VALUES ('78', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:47:54', '2022-05-17 12:47:54');
INSERT INTO `action_logs` VALUES ('79', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 权限分配 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:48:09', '2022-05-17 12:48:09');
INSERT INTO `action_logs` VALUES ('80', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【管理员管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:49:02', '2022-05-17 12:49:02');
INSERT INTO `action_logs` VALUES ('81', '4', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: shuwei 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:49:11', '2022-05-17 12:49:11');
INSERT INTO `action_logs` VALUES ('82', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-17 12:49:52', '2022-05-17 12:49:52');
INSERT INTO `action_logs` VALUES ('83', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 登录成功\", \"address\": \"本机地址本机地址\"}', '2022-05-18 03:01:26', '2022-05-18 03:01:26');
INSERT INTO `action_logs` VALUES ('84', '1', '{\"ip\": \"127.0.0.1\", \"action\": \"管理员: admin 操作了 【角色管理】- 数据更新 模块\", \"address\": \"本机地址本机地址\"}', '2022-05-18 03:02:00', '2022-05-18 03:02:00');

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
  `avatr` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `create_ip` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '注册ip',
  `last_login_ip` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '最后登录IP',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态: 1 正常, 2=>禁止',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admins_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES ('1', 'admin', '$2y$10$jkIO148FM6Ll/fPDE6.p4OtNk9grAPCmJLr84pnJhLA7Mkr8HcNDa', '/uploads/images/avatrs/201711/13//1510542803_2gM4ffLylf.jpeg', '14', '127.0.0.1', '127.0.0.1', '1', '2017-11-12 11:21:32', '2022-05-18 03:01:26');
INSERT INTO `admins` VALUES ('3', 'guolin', '$2y$10$oD4OLDfhUtZk4aRW3nOg9uiJSjV.yLAjpQv.vMzQQbFwrQsjjPjVy', '/uploads/images/avatrs/202205/17//1652791039_6b11ROeDxA.jpg', '1', '127.0.0.1', '127.0.0.1', '1', '2022-05-17 12:37:19', '2022-05-17 12:46:12');
INSERT INTO `admins` VALUES ('4', 'shuwei', '$2y$10$BJxyZ1qYx7dK5LG7DE0iX.YeNLoQitNDofdE.to/Y8LGaUptIxLdK', '/uploads/images/avatrs/202205/17//1652791742_E0W02N2Xam.jpeg', '2', '127.0.0.1', '127.0.0.1', '1', '2022-05-17 12:47:13', '2022-05-17 12:49:11');

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_role_admin_id_index` (`admin_id`),
  KEY `admin_role_role_id_index` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_role
-- ----------------------------
INSERT INTO `admin_role` VALUES ('3', '1', '2', null, null);
INSERT INTO `admin_role` VALUES ('6', '1', '1', '2017-11-13 03:13:36', '2017-11-13 03:13:36');
INSERT INTO `admin_role` VALUES ('7', '3', '3', '2022-05-17 12:37:19', '2022-05-17 12:37:19');
INSERT INTO `admin_role` VALUES ('8', '4', '5', '2022-05-17 12:47:13', '2022-05-17 12:47:13');

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES ('1', '2017_11_09_101232_create_admins_table', '1');
INSERT INTO `migrations` VALUES ('2', '2017_11_09_102926_create_roles_table', '1');
INSERT INTO `migrations` VALUES ('3', '2017_11_09_103632_create_rules_table', '1');
INSERT INTO `migrations` VALUES ('4', '2017_11_09_104449_create_admin_role_table', '1');
INSERT INTO `migrations` VALUES ('5', '2017_11_09_104749_create_role_auth_table', '1');
INSERT INTO `migrations` VALUES ('6', '2017_11_17_075523_create_action_logs_table', '2');
INSERT INTO `migrations` VALUES ('7', '2017_11_21_072911_add_type_to_action_logs', '3');
INSERT INTO `migrations` VALUES ('8', '2017_11_21_102920_add_fonts_to_rules', '4');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色名称',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '角色描述',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态: 1 正常, 2=>禁止',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_name_index` (`name`),
  KEY `roles_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', '超级管理员', '拥有网站最大权限', '255', '1', '2017-11-12 11:20:51', '2017-11-12 11:20:51');
INSERT INTO `roles` VALUES ('3', '运营总监', null, '255', '1', '2022-05-17 12:35:45', '2022-05-17 12:35:45');
INSERT INTO `roles` VALUES ('4', '开发部主管', null, '255', '1', '2022-05-17 12:46:38', '2022-05-17 12:46:38');
INSERT INTO `roles` VALUES ('5', '开发部-服务组', null, '255', '1', '2022-05-17 12:46:51', '2022-05-18 03:02:00');

-- ----------------------------
-- Table structure for role_auth
-- ----------------------------
DROP TABLE IF EXISTS `role_auth`;
CREATE TABLE `role_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `rule_id` int(11) NOT NULL COMMENT '权限id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_auth_role_id_index` (`role_id`),
  KEY `role_auth_rule_id_index` (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of role_auth
-- ----------------------------
INSERT INTO `role_auth` VALUES ('12', '1', '1', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('13', '1', '2', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('14', '1', '3', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('15', '1', '4', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('16', '1', '5', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('18', '1', '7', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('19', '1', '8', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('20', '1', '9', '2017-11-13 15:21:24', '2017-11-13 15:21:24');
INSERT INTO `role_auth` VALUES ('24', '1', '10', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('25', '1', '11', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('26', '1', '12', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('27', '1', '13', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('28', '1', '14', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('29', '1', '15', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('30', '1', '16', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('31', '1', '17', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('32', '1', '18', '2017-11-20 02:47:13', '2017-11-20 02:47:13');
INSERT INTO `role_auth` VALUES ('49', '1', '19', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('50', '1', '20', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('51', '1', '21', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('52', '1', '22', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('53', '1', '23', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('54', '1', '24', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('55', '1', '25', '2017-11-20 04:05:53', '2017-11-20 04:05:53');
INSERT INTO `role_auth` VALUES ('65', '1', '26', '2017-11-21 09:18:31', '2017-11-21 09:18:31');
INSERT INTO `role_auth` VALUES ('66', '1', '27', '2017-11-21 09:18:31', '2017-11-21 09:18:31');
INSERT INTO `role_auth` VALUES ('83', '1', '6', '2022-05-17 12:23:27', '2022-05-17 12:23:27');
INSERT INTO `role_auth` VALUES ('84', '3', '1', '2022-05-17 12:35:59', '2022-05-17 12:35:59');
INSERT INTO `role_auth` VALUES ('85', '3', '2', '2022-05-17 12:35:59', '2022-05-17 12:35:59');
INSERT INTO `role_auth` VALUES ('86', '5', '1', '2022-05-17 12:46:59', '2022-05-17 12:46:59');
INSERT INTO `role_auth` VALUES ('87', '5', '2', '2022-05-17 12:46:59', '2022-05-17 12:46:59');
INSERT INTO `role_auth` VALUES ('88', '5', '26', '2022-05-17 12:46:59', '2022-05-17 12:46:59');
INSERT INTO `role_auth` VALUES ('89', '5', '27', '2022-05-17 12:46:59', '2022-05-17 12:46:59');
INSERT INTO `role_auth` VALUES ('90', '5', '3', '2022-05-17 12:48:09', '2022-05-17 12:48:09');

-- ----------------------------
-- Table structure for rules
-- ----------------------------
DROP TABLE IF EXISTS `rules`;
CREATE TABLE `rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限名称',
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '权限路由',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级权限',
  `is_hidden` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `sort` int(10) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态: 1 正常, 2=>禁止',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fonts` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单fonts图标',
  PRIMARY KEY (`id`),
  KEY `rules_name_index` (`name`),
  KEY `rules_parent_id_index` (`parent_id`),
  KEY `rules_is_hidden_index` (`is_hidden`),
  KEY `rules_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of rules
-- ----------------------------
INSERT INTO `rules` VALUES ('1', '后台首页', 'index.index', '0', '0', '1', '1', '2017-11-13 05:45:27', '2017-11-21 10:43:16', 'home');
INSERT INTO `rules` VALUES ('2', '欢迎界面', 'index.main', '1', '1', '255', '1', '2017-11-13 07:41:14', '2017-11-20 03:31:52', null);
INSERT INTO `rules` VALUES ('3', '后台授权', null, '0', '0', '2', '1', '2017-11-13 08:06:50', '2017-11-21 10:44:00', 'users');
INSERT INTO `rules` VALUES ('4', '管理员管理', 'admins.index', '3', '0', '1', '1', '2017-11-13 08:09:06', '2017-11-17 13:40:33', null);
INSERT INTO `rules` VALUES ('5', '添加页面', 'admins.create', '4', '1', '255', '1', '2017-11-13 10:56:30', '2017-11-13 10:57:48', null);
INSERT INTO `rules` VALUES ('6', '添加数据', 'admins.store', '4', '1', '255', '1', '2017-11-13 10:57:46', '2017-11-13 10:58:56', null);
INSERT INTO `rules` VALUES ('7', '修改页面', 'admins.edit', '4', '1', '255', '1', '2017-11-13 10:58:44', '2017-11-13 10:59:09', null);
INSERT INTO `rules` VALUES ('8', '数据更新', 'admins.update', '4', '1', '255', '1', '2017-11-13 10:59:52', '2017-11-17 13:32:23', null);
INSERT INTO `rules` VALUES ('9', '状态修改', 'admins.status', '4', '1', '255', '1', '2017-11-17 10:05:34', '2017-11-17 13:27:06', null);
INSERT INTO `rules` VALUES ('10', '删除', 'admins.destroy', '4', '1', '255', '1', '2017-11-17 10:08:41', '2017-11-17 13:27:35', null);
INSERT INTO `rules` VALUES ('11', '角色管理', 'roles.index', '3', '0', '2', '1', '2017-11-13 11:00:47', '2017-11-17 13:40:43', null);
INSERT INTO `rules` VALUES ('12', '添加页面', 'roles.create', '11', '1', '255', '1', '2017-11-17 13:02:52', '2017-11-17 13:25:22', null);
INSERT INTO `rules` VALUES ('13', '添加数据', 'roles.store', '11', '1', '255', '1', '2017-11-17 13:03:46', '2017-11-17 13:03:46', null);
INSERT INTO `rules` VALUES ('14', '修改页面', 'roles.edit', '11', '1', '255', '1', '2017-11-17 13:06:47', '2017-11-17 13:08:30', null);
INSERT INTO `rules` VALUES ('15', '数据更新', 'roles.update', '11', '1', '255', '1', '2017-11-17 13:08:12', '2017-11-17 13:08:27', null);
INSERT INTO `rules` VALUES ('16', '权限分配页面', 'roles.access', '11', '1', '255', '1', '2017-11-17 13:09:58', '2017-11-17 13:10:31', null);
INSERT INTO `rules` VALUES ('17', '权限分配', 'roles.group-access', '11', '1', '255', '1', '2017-11-17 13:11:01', '2017-11-17 13:11:01', null);
INSERT INTO `rules` VALUES ('18', '删除角色', 'roles.destroy', '11', '1', '255', '1', '2017-11-17 13:12:22', '2017-11-17 13:12:22', null);
INSERT INTO `rules` VALUES ('19', '权限管理', 'rules.index', '3', '0', '3', '1', '2017-11-17 13:14:27', '2017-11-17 13:44:15', null);
INSERT INTO `rules` VALUES ('20', '添加页面', 'rules.create', '19', '1', '255', '1', '2017-11-17 13:16:30', '2017-11-17 13:16:30', null);
INSERT INTO `rules` VALUES ('21', '添加数据', 'rules.store', '19', '1', '255', '1', '2017-11-17 13:17:07', '2017-11-17 13:17:07', null);
INSERT INTO `rules` VALUES ('22', '修改页面', 'rules.edit', '19', '1', '255', '1', '2017-11-17 13:19:23', '2017-11-17 13:19:23', null);
INSERT INTO `rules` VALUES ('23', '数据更新', 'rules.update', '19', '1', '255', '1', '2017-11-17 13:19:55', '2017-11-17 13:19:55', null);
INSERT INTO `rules` VALUES ('24', '状态修改', 'rules.status', '19', '1', '255', '1', '2017-11-17 13:21:26', '2017-11-17 13:21:26', null);
INSERT INTO `rules` VALUES ('25', '删除权限', 'rules.destroy', '19', '1', '255', '1', '2017-11-17 13:22:30', '2017-11-17 13:22:30', null);
INSERT INTO `rules` VALUES ('26', '操作日志', 'actions.index', '3', '0', '255', '1', '2017-11-20 15:13:17', '2017-11-20 15:13:17', null);
INSERT INTO `rules` VALUES ('27', '删除日志', 'actions.destroy', '26', '1', '255', '1', '2017-11-20 15:14:01', '2017-11-20 15:14:01', null);
