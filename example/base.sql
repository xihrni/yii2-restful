/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 100325
 Source Host           : localhost:3306
 Source Schema         : www_xihrni_com

 Target Server Type    : MySQL
 Target Server Version : 100325
 File Encoding         : 65001

 Date: 24/12/2021 21:44:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for xi_admin
-- ----------------------------
DROP TABLE IF EXISTS `xi_admin`;
CREATE TABLE `xi_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL DEFAULT '' COMMENT '用户名',
  `password_hash` varchar(255) NOT NULL DEFAULT '' COMMENT '加密密码',
  `password_reset_token` varchar(64) DEFAULT NULL COMMENT '重置密码令牌',
  `auth_key` varchar(64) DEFAULT NULL COMMENT '认证密钥',
  `access_token` varchar(64) DEFAULT NULL COMMENT '访问令牌',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机号码',
  `realname` varchar(16) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>正常，1=>删除',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>正常',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_terminal` tinyint(1) NOT NULL DEFAULT 0 COMMENT '最后登录终端',
  `last_login_version` varchar(16) NOT NULL DEFAULT '' COMMENT '最后登录版本',
  `allowance` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求剩余次数',
  `allowance_updated_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `access_token` (`access_token`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `auth_key` (`auth_key`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `password_reset_token` (`password_reset_token`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `username` (`username`,`is_trash`,`deleted_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员';

-- ----------------------------
-- Records of xi_admin
-- ----------------------------
BEGIN;
INSERT INTO `xi_admin` VALUES (1, 'admin', '$2y$13$jFJP0soIv/zTTi9kPe4vl.O2y2EZ2RlsD3fMOk2f4kqPpjpS7rsjG', NULL, NULL, 'jTh0v9ddbb9C1fGYIn2wDBy0rvlcEzO2ddF1uISl6ZyDCiGHwS6q_1640229848', '18866668888', '管理员', 0, 1, '2021-12-22 17:14:26', '2021-12-23 19:57:46', NULL, '2021-12-23 11:19:15', '172.17.0.1', 1, '0.0.1', 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for xi_admin_auth_assign
-- ----------------------------
DROP TABLE IF EXISTS `xi_admin_auth_assign`;
CREATE TABLE `xi_admin_auth_assign` (
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '管理员ID',
  `role_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '角色ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  PRIMARY KEY (`admin_id`,`role_id`) USING BTREE,
  KEY `xi_admin_auth_assign_fk_role_id` (`role_id`) USING BTREE,
  KEY `xi_admin_auth_assign_fk_admin_id` (`admin_id`) USING BTREE,
  CONSTRAINT `xi_admin_auth_assign_fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `xi_admin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `xi_admin_auth_assign_fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `xi_admin_auth_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员角色分配';

-- ----------------------------
-- Records of xi_admin_auth_assign
-- ----------------------------
BEGIN;
INSERT INTO `xi_admin_auth_assign` VALUES (1, 1, '2021-12-23 12:39:11');
COMMIT;

-- ----------------------------
-- Table structure for xi_admin_auth_menu
-- ----------------------------
DROP TABLE IF EXISTS `xi_admin_auth_menu`;
CREATE TABLE `xi_admin_auth_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `front_frame_path` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架路径',
  `front_frame_name` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架名称',
  `front_frame_meta` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架可配项',
  `front_frame_component` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架组件',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`parent_id`,`name`,`is_trash`,`deleted_at`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  CONSTRAINT `xi_admin_auth_menu_fk_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `xi_admin_auth_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员后台菜单';

-- ----------------------------
-- Records of xi_admin_auth_menu
-- ----------------------------
BEGIN;
INSERT INTO `xi_admin_auth_menu` VALUES (1, NULL, '首页', '', '', '', '', 0, 0, 1, '2020-04-26 10:16:27', '2021-12-23 22:47:16', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (2, 1, '首页', '', '', '', '', 0, 0, 1, '2020-04-26 10:16:56', '2020-05-13 17:28:32', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (3, 1, '仪表盘', '', '', '', '', 0, 0, 1, '2020-04-26 10:17:05', '2020-05-13 17:28:34', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (4, NULL, '系统管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:50:45', '2021-12-22 17:17:39', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (5, 4, '账户管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:50:58', '2020-05-13 14:50:58', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (6, 4, '角色管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:51:07', '2020-05-13 14:51:07', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (7, 4, '权限管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:51:18', '2020-05-13 14:51:18', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (8, 4, '菜单管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:51:25', '2020-05-13 14:51:25', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (9, 4, '日志管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:51:30', '2020-05-13 14:51:30', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (10, NULL, '平台管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:51:50', '2021-12-22 17:21:28', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (11, NULL, '用户管理', '', '', '', '', 0, 0, 1, '2021-12-22 17:18:34', '2021-12-22 17:19:12', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (12, 11, '用户列表', '', '', '', '', 0, 0, 1, '2021-12-22 17:18:40', '2021-12-22 17:19:17', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (13, 11, '标签管理', '', '', '', '', 0, 0, 1, '2021-12-22 19:46:53', '2021-12-22 19:47:13', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (14, 11, '日志管理', '', '', '', '', 0, 0, 1, '2021-12-22 17:18:51', '2021-12-22 19:48:00', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (15, NULL, '文件管理', '', '', '', '', 0, 0, 1, '2020-05-13 14:53:47', '2021-12-22 19:47:57', NULL);
INSERT INTO `xi_admin_auth_menu` VALUES (16, 15, '文件列表', '', '', '', '', 0, 0, 1, '2020-05-13 14:53:54', '2021-12-22 19:48:13', NULL);
COMMIT;

-- ----------------------------
-- Table structure for xi_admin_auth_permission
-- ----------------------------
DROP TABLE IF EXISTS `xi_admin_auth_permission`;
CREATE TABLE `xi_admin_auth_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '菜单ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `modules` varchar(64) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(128) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(32) NOT NULL DEFAULT '' COMMENT '操作',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称（路由）',
  `method` varchar(8) NOT NULL DEFAULT '' COMMENT '方法',
  `condition` text NOT NULL COMMENT '条件（json）',
  `front_frame_path` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架路径',
  `front_frame_meta` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架可配项',
  `front_frame_name` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架名称',
  `front_frame_component` varchar(255) NOT NULL DEFAULT '' COMMENT '前端框架组件',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>正常，1=>删除',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>正常',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `title` (`menu_id`,`title`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `name` (`modules`,`controller`,`action`,`name`,`method`,`is_trash`,`deleted_at`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  CONSTRAINT `xi_admin_auth_permission_fk_menu_id` FOREIGN KEY (`menu_id`) REFERENCES `xi_admin_auth_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员权限';

-- ----------------------------
-- Records of xi_admin_auth_permission
-- ----------------------------
BEGIN;
INSERT INTO `xi_admin_auth_permission` VALUES (1, 2, '登录', 'v1/admin/index', 'index', 'login', '/v1/admin/index/index/login', 'POST', '', '', '', '', '', 0, 0, 1, '2020-04-26 10:18:14', '2021-12-23 22:45:05', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (2, 2, '访问令牌', 'v1/admin/index', 'index', 'access-token', '/v1/admin/index/index/access-token', 'POST', '', '', '', '', '', 0, 0, 1, '2020-04-26 10:21:57', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (3, 2, '个人信息', 'v1/admin/index', 'index', 'person', '/v1/admin/index/index/person', 'GET', '', '', '', '', '', 0, 0, 1, '2020-04-26 10:21:57', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (4, 2, '更改密码', 'v1/admin/index', 'index', 'password-change', '/v1/admin/index/index/password', 'PUT', '', '', '', '', '', 0, 0, 1, '2020-04-26 10:21:57', '2021-12-23 19:30:11', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (5, 3, '统计', 'v1/admin/index', 'dashboard', 'total', '/v1/admin/index/dashboard/total', 'GET', '', '', '', '', '', 0, 0, 1, '2020-04-26 10:21:57', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (6, 5, '列表', 'v1/admin/admin', 'index', 'index', '/v1/admin/admin/indices', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (7, 5, '详情', 'v1/admin/admin', 'index', 'view', '/v1/admin/admin/indices/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (8, 5, '创建', 'v1/admin/admin', 'index', 'create', '/v1/admin/admin/indices', 'POST', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (9, 5, '更新', 'v1/admin/admin', 'index', 'update', '/v1/admin/admin/indices/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (10, 5, '删除', 'v1/admin/admin', 'index', 'delete', '/v1/admin/admin/indices/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (11, 5, '状态', 'v1/admin/admin', 'index', 'status', '/v1/admin/admin/indices/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (12, 5, '重置密码', 'v1/admin/admin', 'index', 'password-reset', '/v1/admin/admin/indices/reset-password/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-12-23 22:09:10', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (13, 5, '分配角色', 'v1/admin/admin', 'index', 'role-assign', '/v1/admin/admin/indices/role/:id', 'POST', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-12-01 17:05:03', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (14, 6, '列表', 'v1/admin/admin', 'role', 'index', '/v1/admin/admin/roles', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (15, 6, '详情', 'v1/admin/admin', 'role', 'view', '/v1/admin/admin/roles/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (16, 6, '创建', 'v1/admin/admin', 'role', 'create', '/v1/admin/admin/roles', 'POST', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (17, 6, '更新', 'v1/admin/admin', 'role', 'update', '/v1/admin/admin/roles/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (18, 6, '删除', 'v1/admin/admin', 'role', 'delete', '/v1/admin/admin/roles/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (19, 6, '状态', 'v1/admin/admin', 'role', 'status', '/v1/admin/admin/roles/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (22, 7, '列表', 'v1/admin/admin', 'permission', 'index', '/v1/admin/admin/permissions', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (23, 7, '详情', 'v1/admin/admin', 'permission', 'view', '/v1/admin/admin/permissions/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (24, 7, '创建', 'v1/admin/admin', 'permission', 'create', '/v1/admin/admin/permissions', 'POST', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (25, 7, '更新', 'v1/admin/admin', 'permission', 'update', '/v1/admin/admin/permissions/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (26, 7, '删除', 'v1/admin/admin', 'permission', 'delete', '/v1/admin/admin/permissions/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (27, 7, '状态', 'v1/admin/admin', 'permission', 'status', '/v1/admin/admin/permissions/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (28, 8, '列表', 'v1/admin/admin', 'menu', 'index', '/v1/admin/admin/menus', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (29, 8, '详情', 'v1/admin/admin', 'menu', 'view', '/v1/admin/admin/menus/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (30, 8, '创建', 'v1/admin/admin', 'menu', 'create', '/v1/admin/admin/menus', 'POST', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (31, 8, '更新', 'v1/admin/admin', 'menu', 'update', '/v1/admin/admin/menus/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (32, 8, '删除', 'v1/admin/admin', 'menu', 'delete', '/v1/admin/admin/menus/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (33, 8, '状态', 'v1/admin/admin', 'menu', 'status', '/v1/admin/admin/menus/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (34, 8, '树', 'v1/admin/admin', 'menu', 'tree', '/v1/admin/admin/menus/tree', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (35, 9, '列表', 'v1/admin/admin', 'log', 'index', '/v1/admin/admin/logs', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-26 19:27:26', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (36, 9, '详情', 'v1/admin/admin', 'log', 'view', '/v1/admin/admin/logs/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-26 19:27:29', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (37, 2, '清除缓存', 'v1/admin/index', 'index', 'cache-flush', '/v1/admin/index/index/cache-flush', 'DELETE', '', '', '', '', '', 0, 0, 1, '2021-11-26 19:30:00', '2021-11-26 19:30:00', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (38, 7, '列表2（用于角色编辑页面选择权限）', 'v1/admin/admin', 'permission', 'list', '/v1/admin/admin/permissions/list', 'GET', '', '', '', '', '', 0, 0, 1, '2021-11-26 19:08:07', '2021-11-26 19:08:07', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (39, 12, '列表', 'v1/admin/user', 'index', 'index', '/v1/admin/user/indices', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (40, 12, '详情', 'v1/admin/user', 'index', 'view', '/v1/admin/user/indices/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2020-05-21 16:26:54', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (41, 12, '创建', 'v1/admin/user', 'index', 'create', '/v1/admin/user/indices', 'POST', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-24 23:51:45', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (42, 12, '更新', 'v1/admin/user', 'index', 'update', '/v1/admin/user/indices/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-24 23:51:42', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (43, 12, '删除', 'v1/admin/user', 'index', 'delete', '/v1/admin/user/indices/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-24 23:51:39', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (44, 12, '状态', 'v1/admin/user', 'index', 'status', '/v1/admin/user/indices/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-24 23:51:37', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (45, 14, '列表', 'v1/admin/user', 'log', 'index', '/v1/admin/user/logs', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-26 19:27:26', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (46, 14, '详情', 'v1/admin/user', 'log', 'view', '/v1/admin/user/logs/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2020-03-20 13:54:47', '2021-11-26 19:27:29', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (47, 16, '列表', 'v1/admin/file', 'index', 'index', '/v1/admin/file/indices', 'GET', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:39:52', '2021-11-26 14:39:52', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (48, 16, '详情', 'v1/admin/file', 'index', 'view', '/v1/admin/file/indices/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:40:55', '2021-11-26 14:40:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (49, 16, '创建', 'v1/admin/file', 'index', 'create', '/v1/admin/file/indices', 'POST', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:41:44', '2021-11-26 14:41:44', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (50, 16, '更新', 'v1/admin/file', 'index', 'update', '/v1/admin/file/indices/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:42:44', '2021-11-26 14:42:44', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (51, 16, '删除', 'v1/admin/file', 'index', 'delete', '/v1/admin/file/indices/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:43:39', '2021-11-26 14:43:39', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (52, 16, '状态', 'v1/admin/file', 'index', 'status', '/v1/admin/file/indices/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:45:11', '2021-11-26 14:45:11', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (53, 16, '批量创建', 'v1/admin/file', 'index', 'batch-create', '/v1/admin/file/indices/batch', 'POST', '', '', '', '', '', 0, 0, 1, '2021-11-26 17:54:14', '2021-11-26 19:03:39', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (54, 13, '列表', 'v1/admin/user', 'index', 'index', '/v1/admin/user/logs', 'GET', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:39:52', '2021-11-26 14:39:52', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (55, 13, '详情', 'v1/admin/user', 'index', 'view', '/v1/admin/user/logs/:id', 'GET', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:40:55', '2021-11-26 14:40:55', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (56, 13, '创建', 'v1/admin/user', 'index', 'create', '/v1/admin/user/logs', 'POST', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:41:44', '2021-11-26 14:41:44', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (57, 13, '更新', 'v1/admin/user', 'index', 'update', '/v1/admin/user/logs/:id', 'PUT', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:42:44', '2021-11-26 14:42:44', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (58, 13, '删除', 'v1/admin/user', 'index', 'delete', '/v1/admin/user/logs/:id', 'DELETE', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:43:39', '2021-11-26 14:43:39', NULL);
INSERT INTO `xi_admin_auth_permission` VALUES (59, 13, '状态', 'v1/admin/user', 'index', 'status', '/v1/admin/user/logs/status/:id', 'PATCH', '', '', '', '', '', 0, 0, 1, '2021-11-26 14:45:11', '2021-11-26 14:45:11', NULL);
COMMIT;

-- ----------------------------
-- Table structure for xi_admin_auth_role
-- ----------------------------
DROP TABLE IF EXISTS `xi_admin_auth_role`;
CREATE TABLE `xi_admin_auth_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `permission_ids` text NOT NULL COMMENT '权限ID集合（json）',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `description` varchar(64) NOT NULL DEFAULT '' COMMENT '描述',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>正常，1=>删除',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>正常',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`,`is_trash`,`deleted_at`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员角色';

-- ----------------------------
-- Records of xi_admin_auth_role
-- ----------------------------
BEGIN;
INSERT INTO `xi_admin_auth_role` VALUES (1, '[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59]', '管理员', '系统管理员', 0, 0, 1, '2021-12-22 17:15:26', '2021-12-23 11:27:23', NULL);
COMMIT;

-- ----------------------------
-- Table structure for xi_admin_behavior_log
-- ----------------------------
DROP TABLE IF EXISTS `xi_admin_behavior_log`;
CREATE TABLE `xi_admin_behavior_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL COMMENT '管理员ID',
  `module` varchar(64) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(64) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '操作',
  `route` varchar(255) NOT NULL DEFAULT '' COMMENT '路由',
  `method` varchar(8) NOT NULL DEFAULT '' COMMENT '方法',
  `headers` text NOT NULL COMMENT '请求头（json）',
  `params` text NOT NULL COMMENT '请求参数（json）',
  `body` text NOT NULL COMMENT '请求体（json）',
  `authorization` varchar(255) NOT NULL DEFAULT '' COMMENT '身份认证',
  `request_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '请求IP',
  `response` longtext NOT NULL COMMENT '响应结果（json）',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `xi_admin_behavior_log_fk_admin_id` (`admin_id`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  CONSTRAINT `xi_admin_behavior_log_fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `xi_admin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员行为日志';

-- ----------------------------
-- Records of xi_admin_behavior_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for xi_file
-- ----------------------------
DROP TABLE IF EXISTS `xi_file`;
CREATE TABLE `xi_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '操作人ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '类型，1=>图片，2=>视频，3=>文件',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `xi_file_fk_admin_id` (`admin_id`) USING BTREE,
  CONSTRAINT `xi_file_fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `xi_admin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='文件';

-- ----------------------------
-- Records of xi_file
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for xi_setting
-- ----------------------------
DROP TABLE IF EXISTS `xi_setting`;
CREATE TABLE `xi_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
  `value` text NOT NULL COMMENT '值（json）',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`,`is_trash`,`deleted_at`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='系统设置';

-- ----------------------------
-- Records of xi_setting
-- ----------------------------
BEGIN;
INSERT INTO `xi_setting` VALUES (1, 'serviceState', '服务状态', '{\"admin\":{\"status\":1,\"explain\":\"\\u7cfb\\u7edf\\u5347\\u7ea7\\u4e2d\\uff0c\\u8bf7\\u7b49\\u5f85\\u5347\\u7ea7\\u5b8c\\u6210\\uff0c\\u7ed9\\u60a8\\u9020\\u6210\\u4e0d\\u4fbf\\u656c\\u8bf7\\u8c05\\u89e3\\u3002\"},\"user\":{\"status\":1,\"explain\":\"\\u7cfb\\u7edf\\u5347\\u7ea7\\u4e2d\\uff0c\\u8bf7\\u7b49\\u5f85\\u5347\\u7ea7\\u5b8c\\u6210\\uff0c\\u7ed9\\u60a8\\u9020\\u6210\\u4e0d\\u4fbf\\u656c\\u8bf7\\u8c05\\u89e3\\u3002\"}}', 0, 1, '2021-05-01 10:00:00', '2021-05-01 10:00:00', NULL);
INSERT INTO `xi_setting` VALUES (2, 'clientSignatureSecret', '客户端签名秘钥', '[{\"id\":\"100001\",\"secret\":\"1kqa5gz4vlncdwo6\"},{\"id\":\"100002\",\"secret\":\"x4lt2rjdeubfsw7y\"}]', 0, 1, '2021-05-01 10:00:00', '2021-05-01 10:00:00', NULL);
COMMIT;

-- ----------------------------
-- Table structure for xi_user
-- ----------------------------
DROP TABLE IF EXISTS `xi_user`;
CREATE TABLE `xi_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '标签ID集合（json）',
  `username` varchar(16) NOT NULL DEFAULT '' COMMENT '用户名',
  `password_hash` varchar(255) NOT NULL DEFAULT '' COMMENT '加密密码',
  `password_reset_token` varchar(64) DEFAULT NULL COMMENT '重置密码令牌',
  `auth_key` varchar(64) DEFAULT NULL COMMENT '认证密钥',
  `access_token` varchar(64) DEFAULT NULL COMMENT '访问令牌',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机号码',
  `realname` varchar(16) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '性别，0=>未知，1=>男，2=>女',
  `birthday` date NOT NULL DEFAULT '1970-01-01' COMMENT '生日',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_terminal` tinyint(1) NOT NULL DEFAULT 0 COMMENT '最后登录终端',
  `last_login_version` varchar(16) NOT NULL DEFAULT '' COMMENT '最后登录版本',
  `allowance` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求剩余次数',
  `allowance_updated_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `access_token` (`access_token`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `auth_key` (`auth_key`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `password_reset_token` (`password_reset_token`,`is_trash`,`deleted_at`) USING BTREE,
  UNIQUE KEY `username` (`username`,`is_trash`,`deleted_at`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户';

-- ----------------------------
-- Records of xi_user
-- ----------------------------
BEGIN;
INSERT INTO `xi_user` VALUES (1, '', 'user1', '$2y$13$Nu4rgdMEIMKfuqjYIVmBMOhP/8y8ZhY59PqlitO31tgR8UA6KDh.m', NULL, NULL, NULL, '19966889900', '用户1', '用户1', '', 0, '1970-01-01', 0, 1, '2021-12-24 10:33:34', '2021-12-24 19:44:46', NULL, NULL, '', 0, '', 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for xi_user_behavior_log
-- ----------------------------
DROP TABLE IF EXISTS `xi_user_behavior_log`;
CREATE TABLE `xi_user_behavior_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `module` varchar(64) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(32) NOT NULL DEFAULT '' COMMENT '操作',
  `route` varchar(255) NOT NULL DEFAULT '' COMMENT '路由',
  `method` varchar(8) NOT NULL DEFAULT '' COMMENT '方法',
  `headers` text NOT NULL COMMENT '请求头（json）',
  `params` text NOT NULL COMMENT '请求参数（json）',
  `body` text NOT NULL COMMENT '请求体（json）',
  `authorization` varchar(255) NOT NULL DEFAULT '' COMMENT '身份认证',
  `request_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '请求IP',
  `response` text NOT NULL COMMENT '响应结果（json）',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `xi_user_behavior_log_fk_user_id` (`user_id`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  CONSTRAINT `xi_user_behavior_log_fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `xi_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户行为日志';

-- ----------------------------
-- Records of xi_user_behavior_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for xi_user_safe_log
-- ----------------------------
DROP TABLE IF EXISTS `xi_user_safe_log`;
CREATE TABLE `xi_user_safe_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `operator` int(11) unsigned DEFAULT NULL COMMENT '操作者',
  `operate` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '操作，1=>管理员创建，2=>管理员更新，3=>管理员删除，4=>管理员状态变更，5=>用户注册，6=>用户登录，7=>用户更新个人信息，8=>用户密码变更，9=>用户手机号码变更',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `xi_user_safe_log_fk_user_id` (`user_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  CONSTRAINT `xi_user_safe_log_fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `xi_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户安全日志';

-- ----------------------------
-- Records of xi_user_safe_log
-- ----------------------------
BEGIN;
INSERT INTO `xi_user_safe_log` VALUES (1, 1, 1, 1, '管理员创建 ', 0, 1, '2021-12-24 10:33:34', '2021-12-24 10:33:34', NULL);
COMMIT;

-- ----------------------------
-- Table structure for xi_user_tag
-- ----------------------------
DROP TABLE IF EXISTS `xi_user_tag`;
CREATE TABLE `xi_user_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `sort` tinyint(3) NOT NULL DEFAULT 0 COMMENT '排序',
  `is_trash` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否删除，0=>否，1=>是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '状态，0=>禁用，1=>启用',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`,`is_trash`,`deleted_at`) USING BTREE,
  KEY `is_trash` (`is_trash`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='用户标签';

-- ----------------------------
-- Records of xi_user_tag
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
